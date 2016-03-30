<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );
if (!function_exists('normalize_empty_atts')) {
    function normalize_empty_atts ($atts) {
    	if($atts == "")
			return $atts;
        foreach ($atts as $attribute => $value) {
            if (is_int($attribute)) {
                $atts[strtolower($value)] = true;
                unset($atts[$attribute]);
            }
        }
        return $atts;
    }
}

function eduadmin_get_list_view($attributes)
{
	$selectedTemplate = get_option('eduadmin-listTemplate', 'template_A');
	$attributes = shortcode_atts(
		array(
			'template' => $selectedTemplate,
			'category' => null,
			'subject' => null,
			'hidesearch' => false
		),
		normalize_empty_atts($attributes),
		'eduadmin-listview'
	);
	include_once(plugin_dir_path(__FILE__) . "../content/template/listTemplate/" . $attributes['template'] . ".php");
}

function eduadmin_get_detail_view($attributes)
{
	$selectedTemplate = get_option('eduadmin-detailTemplate', 'template_A');
	$attributes = shortcode_atts(
		array(
			'template' => $selectedTemplate,
			'courseid' => null,
			'customtemplate' => null
		),
		normalize_empty_atts($attributes),
		'eduadmin-detailview'
	);
	if(!isset($attributes['customtemplate']) || $attributes['customtemplate'] != 1)
	{
		include_once(plugin_dir_path(__FILE__) . "../content/template/detailTemplate/" . $attributes['template'] . ".php");
	}
}

function eduadmin_get_booking_view($attributes)
{
	$selectedTemplate = get_option('eduadmin-bookingTemplate', 'template_A');
	$attributes = shortcode_atts(
		array(
			'template' => $selectedTemplate,
			'courseid' => null
		),
		normalize_empty_atts($attributes),
		'eduadmin-bookingview'
	);
	include_once(plugin_dir_path(__FILE__) . "../content/template/bookingTemplate/" . $attributes['template'] . ".php");
}

function eduadmin_get_detailinfo($attributes)
{
	global $wp_query;
	$attributes = shortcode_atts(
		array(
			'courseid' => null,
			'coursename' => null,
			'coursepublicname' => null,
			'courselevel' => null,
			'courseimage' => null,
			'courseimagetext' => null,
			'coursedays' => null,
			'coursestarttime' => null,
			'courseendtime' => null,
			'courseprice' => null,
			'coursedescriptionshort' => null,
			'coursedescription' => null,
			'coursegoal' => null,
			'coursetarget' => null,
			'courseprerequisites' => null,
			'courseafter' => null,
			'coursequote' => null,
			'courseeventlist' => null,
			'showmore' => null,
			'courseattributeid' => null,
			'courseeventlistfiltercity' => null
			//'coursesubject' => null
		),
		normalize_empty_atts($attributes),
		'eduadmin-detailinfo'
	);

	$retStr = '';

	$courseId = 0;

	if(empty($attributes['courseid']) || $attributes['courseid'] <= 0)
	{
		if(isset($wp_query->query_vars["courseId"]))
		{
			$courseId = $wp_query->query_vars["courseId"];
		}
		else
			return 'Missing courseId in attributes';
	}
	else
	{
		$courseId = $attributes['courseid'];
	}

	$apiUserId = get_option('eduadmin-api_user_id');
	$apiHash = get_option('eduadmin-api_hash');

	if(!$apiUserId || !$apiHash || (empty($apiUserId) || empty($apiHash)))
	{
		return 'Please complete the configuration: <a href="' . admin_url() . 'admin.php?page=eduadmin-settings">EduAdmin settings - Api authentication</a>';
	}
	else
	{
		$api = new EduAdminClient();

		$token = get_transient('eduadmin-token');
		if(!$token)
		{
			$token = $api->GetAuthToken($apiUserId, $apiHash);
			set_transient('eduadmin-token', $token, HOUR_IN_SECONDS);
		}
		else
		{
			$valid = $api->ValidateAuthToken($token);
			if(!$valid)
			{
				$token = $api->GetAuthToken($apiUserId, $apiHash);
				set_transient('eduadmin-token', $token, HOUR_IN_SECONDS);
			}
		}

		$filtering = new XFiltering();
		$f = new XFilter('ObjectID','=',$courseId);
		$filtering->AddItem($f);

		$f = new XFilter('ShowOnWeb','=','true');
		$filtering->AddItem($f);

		$edo = $api->GetEducationObject($token, '', $filtering->ToString());

		$selectedCourse = false;
		$name = "";
		foreach($edo as $object)
		{
			$id = $object->ObjectID;
			if($id == $courseId)
			{
				$selectedCourse = $object;
				break;
			}
		}

		if(!$selectedCourse)
		{
			return 'Course with ID ' . $courseId . ' could not be found.';
		}
		else
		{
			 if(isset($attributes['coursename']))
			 {
			 	$retStr .= $selectedCourse->ObjectName;
			 }
			 if(isset($attributes['coursepublicname']))
			 {
			 	$retStr .= $selectedCourse->PublicName;
			 }
			 if(isset($attributes['courseimage']))
			 {
			 	$retStr .= $selectedCourse->ImageUrl;
			 }
			 if(isset($attributes['coursedays']))
			 {
			 	$retStr .= $selectedCourse->Days;
			 }
			 if(isset($attributes['coursestarttime']))
			 {
			 	$retStr .= $selectedCourse->StartTime;
			 }
			 if(isset($attributes['courseendtime']))
			 {
			 	$retStr .= $selectedCourse->EndTime;
			 }
			 if(isset($attributes['coursedescriptionshort']))
			 {
			 	$retStr .= $selectedCourse->CourseDescriptionShort;
			 }
			 if(isset($attributes['coursedescription']))
			 {
			 	$retStr .= $selectedCourse->CourseDescription;
			 }
			 if(isset($attributes['coursegoal']))
			 {
			 	$retStr .= $selectedCourse->CourseGoal;
			 }
			 if(isset($attributes['coursetarget']))
			 {
			 	$retStr .= $selectedCourse->TargetGroup;
			 }
			 if(isset($attributes['courseprerequisites']))
			 {
			 	$retStr .= $selectedCourse->Prerequisites;
			 }
			 if(isset($attributes['courseafter']))
			 {
			 	$retStr .= $selectedCourse->CourseAfter;
			 }
			 if(isset($attributes['coursequote']))
			 {
			 	$retStr .= $selectedCourse->Quote;
			 }
			 if(isset($attributes['coursesubject']))
			 {

			 	$ft = new XFiltering();
				$f = new XFilter('ObjectID', '=', $selectedCourse->ObjectID);
				$ft->AddItem($f);
			 	$courseSubject = $api->GetEducationSubject($token, '', $ft->ToString());
				$retStr .= print_r($courseSubject, true);
			 }
			 if(isset($attributes['courselevel']))
			 {

				$ft = new XFiltering();
				$f = new XFilter('ObjectID', '=', $selectedCourse->ObjectID);
				$ft->AddItem($f);
				$courseLevel = $api->GetEducationLevelObject($token, '', $ft->ToString());

				if(count($courseLevel) > 0)
				{
			 		$retStr .= $courseLevel[0]->Name;
				}
			 }
			 if(isset($attributes['courseattributeid']))
			 {
			 	$attrid = $attributes['courseattributeid'];
				$ft = new XFiltering();
				$f = new XFilter('ObjectID', '=', $selectedCourse->ObjectID);
				$ft->AddItem($f);
				$f = new XFilter('AttributeID', '=', $attrid);
				$ft->AddItem($f);
				$objAttr = $api->GetObjectAttribute($token, '', $ft->ToString());
				if(count($objAttr) > 0)
				{
					$attr = $objAttr[0];
					switch($attr->AttributeTypeID)
					{
						case 5:
							$value = $attr->AttributeAlternative;
						/*case 7:
							$value = $attr->AttributeDate;*/
						default:
							$value = $attr->AttributeValue;
						break;
					}
					$retStr .=  $value;
				}
			 }

			 if(isset($attributes['courseprice']))
			 {
				$ft = new XFiltering();
				$f = new XFilter('PublicPriceName', '=', 'true');
				$ft->AddItem($f);
				$f = new XFilter('ObjectID', 'IN', $selectedCourse->ObjectID);
				$ft->AddItem($f);
				$prices = $api->GetObjectPriceName($token, '', $ft->ToString());
				$uniquePrices = Array();
				foreach($prices as $price)
				{
					$uniquePrices[$price->Description] = $price;
				}

				$currency = get_option('eduadmin-currency', 'SEK');
			 	$retStr .= convertToMoney(current($uniquePrices)->Price, $currency);
			 }

			 if(isset($attributes['courseeventlist']))
			 {
			 	$ft = new XFiltering();
				$f = new XFilter('PeriodStart', '>=', date("Y-m-d 00:00:00", strtotime('now +1 day')));
				$ft->AddItem($f);
				$f = new XFilter('ShowOnWeb', '=', 'true');
				$ft->AddItem($f);
				$f = new XFilter('StatusID', '=', '1');
				$ft->AddItem($f);
				$f = new XFilter('ObjectID', '=', $selectedCourse->ObjectID);
				$ft->AddItem($f);
				$f = new XFilter('LastApplicationDate', '>=', date("Y-m-d H:i:s"));
				$ft->AddItem($f);

				if(!empty($attributes['courseeventlistfiltercity']))
				{
					$f = new XFilter('City', '=', $attributes['courseeventlistfiltercity']);
					$ft->AddItem($f);
				}

				$st = new XSorting();
				$groupByCity = get_option('eduadmin-groupEventsByCity', FALSE);
				$groupByCityClass = "";
				if($groupByCity)
				{
					$s = new XSort('City', 'ASC');
					$st->AddItem($s);
					$groupByCityClass = " noCity";
				}
				$s = new XSort('PeriodStart', 'ASC');
				$st->AddItem($s);

				$events = $api->GetEvent(
					$token,
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
				$pricenames = $api->GetPriceName($token,'',$ft->ToString());
				set_transient('eduadmin-publicpricenames', $pricenames, HOUR_IN_SECONDS);

				if(count($pricenames) > 0)
				{
					$events = array_filter($events, function($object) {
						$pn = get_transient('eduadmin-publicpricenames');
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

				$surl = get_site_url();
				$cat = get_option('eduadmin-rewriteBaseUrl');

				$lastCity = "";

				$showMore = isset($attributes['showmore']) && !empty($attributes['showmore']) ? $attributes['showmore'] : -1;
				$spotLeftOption = get_option('eduadmin-spotsLeft', 'exactNumbers');

				$baseUrl = $surl . '/' . $cat;
				$name = (!empty($selectedCourse->PublicName) ? $selectedCourse->PublicName : $selectedCourse->ObjectName);
				$retStr .= '<div class="eduadmin"><div class="event-table eventDays">';
				$i = 0;
				$hasHiddenDates = false;

				foreach($events as $ev)
				{
					$spotsLeft = ($ev->MaxParticipantNr - $ev->TotalParticipantNr);

					if(isset($_REQUEST['eid']))
					{
						if($ev->EventID != $_REQUEST['eid'])
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

					$retStr .= '<div data-groupid="eduev' . ($groupByCity ? "-" . $ev->City : "") . '" class="eventItem' . ($i % 2 == 0 ? " evenRow" : " oddRow") . ($showMore > 0 && $i >= $showMore ? " showMoreHidden" : "") . '">';
					$retStr .= '
					<div class="eventDate' . $groupByCityClass . '">
						' . GetStartEndDisplayDate($ev->PeriodStart, $ev->PeriodEnd, true) . ',
						' . date("H:i", strtotime($ev->PeriodStart)) . ' - ' . date("H:i", strtotime($ev->PeriodEnd)) . '
					</div>
					'. (!$groupByCity ?
					'<div class="eventCity">
						' . $ev->City . '
					</div>' : '') .
					'<div class="eventStatus' . $groupByCityClass . '">
					' .
						getSpotsLeft($spotsLeft, $ev->MaxParticipantNr)
					 . '
					</div>
					<div class="eventBook' . $groupByCityClass . '">
					' . ($ev->MaxParticipantNr == 0 || $spotsLeft > 0 ?

						'<a class="book-link" href="' . $baseUrl . '/' . makeSlugs($name) . '__' . $selectedCourse->ObjectID . '/book/?eid=' . $ev->EventID . '" style="text-align: center;">' . edu__("Book") . '</a>'
					:
						'<i class="fullBooked">' . edu__("Full") . '</i>'
					) . '
					</div>';
					$retStr .= '</div><!-- /eventitem -->';
					$lastCity = $ev->City;
					$i++;
				}
				if(count($events) == 0)
				{
					$retStr.= '<div class="noDatesAvailable"><i>' . edu__("No available dates for the selected course") . '</i></div>';
				}
				if($hasHiddenDates)
				{
					$retStr .= "<div class=\"eventShowMore\"><a href=\"javascript://\" onclick=\"eduDetailView.ShowAllEvents('eduev" . ($groupByCity ? "-" . $ev->City : "") . "', this);\">" . edu__("Show all events") . "</a></div>";
				}
				$retStr .= '</div></div>';
			 }
		}
	}
	return $retStr;
}

function eduadmin_get_login_widget($attributes)
{
	$attributes = shortcode_atts(
		array(
			'loginText' => edu__("Log in"),
			'logoutText' => edu__("Log out"),
			'guestText' => edu__("Guest"),
			'subject' => null
		),
		normalize_empty_atts($attributes),
		'eduadmin-loginwidget'
	);

	$surl = get_site_url();
	$cat = get_option('eduadmin-rewriteBaseUrl');

	$baseUrl = $surl . '/' . $cat;

	if(isset($_SESSION['eduadmin-loginUser']) && !empty($_SESSION['eduadmin-loginUser']))
	{
		echo
		"<div class=\"eduadminLogin\"><a href=\"" . $baseUrl . "/profile/myprofile\" class=\"eduadminMyProfileLink\">" .
		$_SESSION['eduadmin-loginUser']->Contact->ContactName .
		"</a> - <a href=\"" . $baseUrl . "/profile/bookings\" class=\"eduadminBookingsLink\">" .
		edu__("Reservations") .
		"</a> - <a href=\"" . $baseUrl . "/profile/logout\" class=\"eduadminLogoutButton\">" .
		$attributes['logoutText'] .
		"</a>" .
		"</div>";
	}
	else
	{
		echo
		"<div class=\"eduadminLogin\"><i>" .
		$attributes['guestText'] .
		"</i> - " .
		"<a href=\"" . $baseUrl . "/profile/login\" class=\"eduadminLoginButton\">" .
		$attributes['loginText'] .
		"</a>" .
		"</div>";
	}
}

function eduadmin_get_login_view($attributes)
{
	$attributes = shortcode_atts(
		array(
			'loginText' => edu__("Log in"),
			'logoutText' => edu__("Log out"),
			'guestText' => edu__("Guest"),
			'subject' => null
		),
		normalize_empty_atts($attributes),
		'eduadmin-loginview'
	);

	include_once(plugin_dir_path(__FILE__) . "../content/template/myPagesTemplate/login.php");
}

add_shortcode("eduadmin-listview", "eduadmin_get_list_view");
add_shortcode("eduadmin-detailview", "eduadmin_get_detail_view");
add_shortcode("eduadmin-bookingview", "eduadmin_get_booking_view");
add_shortcode('eduadmin-detailinfo', 'eduadmin_get_detailinfo');
add_shortcode('eduadmin-loginwidget', 'eduadmin_get_login_widget');
add_shortcode('eduadmin-loginview', 'eduadmin_get_login_view');
?>