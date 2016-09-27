<?php
if(!function_exists('edu_api_eventlist'))
{
	function edu_api_eventlist($request)
	{
		header("Content-type: text/html; charset=UTF-8");
		$retStr = '';
		global $eduapi;

		$edutoken = edu_decrypt("edu_js_token_crypto", getallheaders()["Edu-Auth-Token"]);

		$_SESSION['eduadmin-phrases'] = $request['phrases'];

		$objectId = $request['objectid'];

		$filtering = new XFiltering();
		$f = new XFilter('ShowOnWeb','=','true');
		$filtering->AddItem($f);
		$f = new XFilter('ObjectID', '=', $objectId);
		$filtering->AddItem($f);

		$edo = $eduapi->GetEducationObject($edutoken, '', $filtering->ToString());
		$selectedCourse = false;
		$name = "";
		foreach($edo as $object)
		{
			$name = (!empty($object->PublicName) ? $object->PublicName : $object->ObjectName);
			$id = $object->ObjectID;
			if($id == $objectId)
			{
				$selectedCourse = $object;
				break;
			}
		}

		$ft = new XFiltering();
		$f = new XFilter('PeriodStart', '>=', date("Y-m-d 00:00:00", strtotime('now +1 day')));
		$ft->AddItem($f);
		$f = new XFilter('PeriodEnd', '<=', date("Y-m-d 00:00:00", strtotime('now +6 months')));
		$ft->AddItem($f);
		$f = new XFilter('ShowOnWeb', '=', 'true');
		$ft->AddItem($f);
		$f = new XFilter('StatusID', '=', '1');
		$ft->AddItem($f);
		$f = new XFilter('ObjectID', '=', $objectId);
		$ft->AddItem($f);
		$f = new XFilter('LastApplicationDate', '>=', date("Y-m-d 23:59:59"));
		$ft->AddItem($f);

		if(!empty($request['city']))
		{
			$f = new XFilter('City', '=', $request['city']);
			$ft->AddItem($f);
		}

		$st = new XSorting();
		$groupByCity = $request['groupbycity'];
		$groupByCityClass = "";
		if($groupByCity)
		{
			$s = new XSort('City', 'ASC');
			$st->AddItem($s);
			$groupByCityClass = " noCity";
		}
		$s = new XSort('PeriodStart', 'ASC');
		$st->AddItem($s);

		$events = $eduapi->GetEvent(
			$edutoken,
			$st->ToString(),
			$ft->ToString()
		);

		$occIds = array();

		foreach($events as $e)
		{
			$occIds[] = $e->OccationID;
		}

		$ft = new XFiltering();
		$f = new XFilter('PublicPriceName', '=', 'true');
		$ft->AddItem($f);
		$f = new XFilter('OccationID', 'IN', join(",", $occIds));
		$ft->AddItem($f);

		$st = new XSorting();
		$s = new XSort('Price', 'ASC');
		$st->AddItem($s);

		$pricenames = $eduapi->GetPriceName($edutoken,$st->ToString(),$ft->ToString());

		if(!empty($pricenames))
		{
			$events = array_filter($events, function($object) use (&$pricenames) {
				$pn = $pricenames;
				foreach($pn as $subj)
				{
					if($object->OccationID == $subj->OccationID)
					{
						return true;
					}
				}
				return false;
			});
		}

        $surl = $request['baseUrl'];
        $cat = $request['courseFolder'];
        $baseUrl = $surl . '/' . $cat;

		$lastCity = "";

		$showMore = isset($request['showmore']) && !empty($request['showmore']) ? $request['showmore'] : -1;
		$spotLeftOption = $request['spotsleft'];
		$alwaysFewSpots = $request['fewspots'];
		$spotSettings = $request['spotsettings'];
		$showEventInquiry = isset($request['event-inquiry']) && $request['event-inquiry'] == "1";
		$name = (!empty($selectedCourse->PublicName) ? $selectedCourse->PublicName : $selectedCourse->ObjectName);
		$retStr .= '<div class="eduadmin"><div class="event-table eventDays">';
		$i = 0;
		$hasHiddenDates = false;
		if(!empty($pricenames))
		{
			foreach($events as $ev)
			{
				$spotsLeft = ($ev->MaxParticipantNr - $ev->TotalParticipantNr);

				if(isset($request['eid']))
				{
					if($ev->EventID != $request['eid'])
					{
						continue;
					}
				}

				if($groupByCity && $lastCity != $ev->City)
				{
					$i = 0;
					if($hasHiddenDates)
					{
						$retStr .= "<div class=\"eventShowMore\"><a href=\"javascript://\" onclick=\"eduDetailView.ShowAllEvents('eduev-" . $lastCity . "', this);\">" . edu__("Show all events") . "</a></div>";
					}
					$hasHiddenDates = false;
					$retStr .= '<div class="eventSeparator">' . $ev->City . '</div>';
				}

				if($showMore > 0 && $i >= $showMore)
				{
					$hasHiddenDates = true;
				}

				$removeItems = array(
					'eid',
					'phrases',
					'module',
					'baseUrl',
					'courseFolder',
					'showmore',
					'spotsleft',
					'objectid',
					'groupbycity',
					'fewspots',
					'spotsettings'
				);

				$retStr .= '<div data-groupid="eduev' . ($groupByCity ? "-" . $ev->City : "") . '" class="eventItem' . ($i % 2 == 0 ? " evenRow" : " oddRow") . ($showMore > 0 && $i >= $showMore ? " showMoreHidden" : "") . '">';
				$retStr .= '
				<div class="eventDate' . $groupByCityClass . '">
					' . edu_GetStartEndDisplayDate($ev->PeriodStart, $ev->PeriodEnd, true) . ',
					' . date("H:i", strtotime($ev->PeriodStart)) . ' - ' . date("H:i", strtotime($ev->PeriodEnd)) . '
				</div>
				'. (!$groupByCity ?
				'<div class="eventCity">
					' . $ev->City . '
				</div>' : '') .
				'<div class="eventStatus' . $groupByCityClass . '">
				' .
					edu_getSpotsLeft($spotsLeft, $ev->MaxParticipantNr, $spotLeftOption, $spotSettings, $alwaysFewSpots)
				 . '
				</div>
				<div class="eventBook' . $groupByCityClass . '">
				' .
				($ev->MaxParticipantNr == 0 || $spotsLeft > 0 ?
					'<a class="book-link" href="' . $baseUrl . '/' . makeSlugs($name) . '__' . $objectId . '/book/?eid=' . $ev->EventID . edu_getQueryString("&", $removeItems) . '" style="text-align: center;">' . edu__("Book") . '</a>'
				:
					($showEventInquiry ?
						'<a class="inquiry-link" href="' . $baseUrl . '/' . makeSlugs($name) . '__' . $objectId . '/book/interest/?eid=' . $ev->EventID . edu_getQueryString("&", $removeItems) . '">' . edu__("Inquiry") . '</a> '
					:
						''
					) .
					'<i class="fullBooked">' . edu__("Full") . '</i>'
				) . '
				</div>';
				$retStr .= '</div><!-- /eventitem -->';
				$lastCity = $ev->City;
				$i++;
			}
		}
		if(empty($pricenames) || empty($events))
		{
			$retStr.= '<div class="noDatesAvailable"><i>' . edu__("No available dates for the selected course") . '</i></div>';
		}
		if($hasHiddenDates)
		{
			$retStr .= "<div class=\"eventShowMore\"><a href=\"javascript://\" onclick=\"eduDetailView.ShowAllEvents('eduev" . ($groupByCity ? "-" . $ev->City : "") . "', this);\">" . edu__("Show all events") . "</a></div>";
		}
		$retStr .= '</div></div>';

		return $retStr;
	}
}

if(isset($_REQUEST['module']) && $_REQUEST['module'] == "detailinfo_eventlist")
{
	echo edu_api_eventlist($_REQUEST);
}
?>