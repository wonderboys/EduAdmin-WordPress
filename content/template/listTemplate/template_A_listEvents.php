<?php
ob_start();

$surl = get_site_url();
$cat = get_option('eduadmin-rewriteBaseUrl');
$baseUrl = $surl . '/' . $cat;

{
	$filtering = new XFiltering();
	$f = new XFilter('ShowOnWeb','=','true');
	$filtering->AddItem($f);

	if($categoryID > 0)
	{
		$f = new XFilter('CategoryID', '=', $categoryID);
		$filtering->AddItem($f);
	}

	if(!empty($filterCourses))
	{
		$f = new XFilter('ObjectID', 'IN', join(',', $filterCourses));
		$filtering->AddItem($f);
	}

	if(isset($_REQUEST['eduadmin-city']))
	{
		$f = new XFilter('LocationID', '=', $_REQUEST['eduadmin-city']);
		$filtering->AddItem($f);
	}

	if(isset($_REQUEST['eduadmin-category']))
	{
		$f = new XFilter('CategoryID', '=', $_REQUEST['eduadmin-category']);
		$filtering->AddItem($f);
	}

	$edo = $eduapi->GetEducationObject($edutoken, '', $filtering->ToString());
}

if(isset($_REQUEST['searchCourses']) && !empty($_REQUEST['searchCourses']))
{
	$edo = array_filter($edo, function($object) {
		$name = (!empty($object->PublicName) ? $object->PublicName : $object->ObjectName);
		$descrField = get_option('eduadmin-layout-descriptionfield', 'CourseDescriptionShort');
		$descr = strip_tags($object->{$descrField});

		$nameMatch = stripos($name, $_REQUEST['searchCourses']) !== FALSE;
		$descrMatch = stripos($descr, $_REQUEST['searchCourses']) !== FALSE;
		return ($nameMatch || $descrMatch);
	});
}

if(isset($_REQUEST['eduadmin-subject']) && !empty($_REQUEST['eduadmin-subject']))
{
	$subjects = get_transient('eduadmin-subjects');
	if(!$subjects)
	{
		$sorting = new XSorting();
		$s = new XSort('SubjectName', 'ASC');
		$sorting->AddItem($s);
		$subjects = $eduapi->GetEducationSubject($edutoken, $sorting->ToString(), '');
		set_transient('eduadmin-subjects', $subjects, DAY_IN_SECONDS);
	}

	$edo = array_filter($edo, function($object) {
		$subjects = get_transient('eduadmin-subjects');
		foreach($subjects as $subj)
		{
			if($object->ObjectID == $subj->ObjectID && $subj->SubjectID == $_REQUEST['eduadmin-subject'])
			{
				return true;
			}
		}
		return false;
	});
}

{
	$filtering = new XFiltering();
	$f = new XFilter('ShowOnWeb','=','true');
	$filtering->AddItem($f);

	if($categoryID > 0)
	{
		$f = new XFilter('CategoryID', '=', $categoryID);
		$filtering->AddItem($f);
	}

	$fetchMonths = get_option('eduadmin-monthsToFetch', 6);
	if(!is_numeric($fetchMonths)) {
		$fetchMonths = 6;
	}

	$f = new XFilter('PeriodStart','>',date("Y-m-d 00:00:00", strtotime("now +1 day")));
	$filtering->AddItem($f);
	$f = new XFilter('PeriodEnd', '<', date("Y-m-d 23:59:59", strtotime("now +" . $fetchMonths . " months")));
	$filtering->AddItem($f);
	$f = new XFilter('StatusID','=','1');
	$filtering->AddItem($f);

	$f = new XFilter('LastApplicationDate','>',date("Y-m-d 23:59:59"));
	$filtering->AddItem($f);

	if(!empty($filterCourses))
	{
		$f = new XFilter('ObjectID', 'IN', join(',', $filterCourses));
		$filtering->AddItem($f);
	}

	if(isset($_REQUEST['eduadmin-city']))
	{
		$f = new XFilter('LocationID', '=', $_REQUEST['eduadmin-city']);
		$filtering->AddItem($f);
	}

	if(isset($_REQUEST['eduadmin-subject']))
	{
		$f = new XFilter('SubjectID', '=', $_REQUEST['eduadmin-subject']);
		$filtering->AddItem($f);
	}

	if(isset($_REQUEST['eduadmin-category']))
	{
		$f = new XFilter('CategoryID', '=', $_REQUEST['eduadmin-category']);
		$filtering->AddItem($f);
	}

	$sorting = new XSorting();
	$s = new XSort('PeriodStart', 'ASC');
	$sorting->AddItem($s);

	$ede = $eduapi->GetEvent($edutoken, $sorting->ToString(), $filtering->ToString());
}

if(isset($_REQUEST['eduadmin-subject']) && !empty($_REQUEST['eduadmin-subject']))
{
	$subjects = get_transient('eduadmin-subjects');
	if(!$subjects)
	{
		$sorting = new XSorting();
		$s = new XSort('SubjectName', 'ASC');
		$sorting->AddItem($s);
		$subjects = $eduapi->GetEducationSubject($edutoken, $sorting->ToString(), '');
		set_transient('eduadmin-subjects', $subjects, DAY_IN_SECONDS);
	}

	$ede = array_filter($ede, function($object) {
		$subjects = get_transient('eduadmin-subjects');
		foreach($subjects as $subj)
		{
			if($object->ObjectID == $subj->ObjectID && $subj->SubjectID == $_REQUEST['eduadmin-subject'])
			{
				return true;
			}
		}
		return false;
	});
}

if(isset($_REQUEST['eduadmin-level']) && !empty($_REQUEST['eduadmin-level']))
{
	$ede = array_filter($ede, function($object) {
		$cl = get_transient('eduadmin-courseLevels');
		foreach($cl as $subj)
		{
			if($object->ObjectID == $subj->ObjectID && $subj->EducationLevelID == $_REQUEST['eduadmin-level'])
			{
				return true;
			}
		}
		return false;
	});
}

$occIds = array();
$evIds = array();

foreach($ede as $e)
{
	$occIds[] = $e->OccationID;
	$evIds[] = $e->EventID;
}

$ft = new XFiltering();
$f = new XFilter('EventID', 'IN', join(",", $evIds));
$ft->AddItem($f);

$eventDays = $eduapi->GetEventDate($edutoken, '', $ft->ToString());

$eventDates = array();
foreach($eventDays as $ed)
{
	$eventDates[$ed->EventID][] = $ed->StartDate;
}

$ft = new XFiltering();
$f = new XFilter('PublicPriceName', '=', 'true');
$ft->AddItem($f);
$f = new XFilter('OccationID', 'IN', join(",", $occIds));
$ft->AddItem($f);
$pricenames = $eduapi->GetPriceName($edutoken,'',$ft->ToString());
set_transient('eduadmin-publicpricenames', $pricenames, HOUR_IN_SECONDS);


	if(!empty($pricenames))
	{
		$ede = array_filter($ede, function($object) {
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

$showCourseDays = get_option('eduadmin-showCourseDays', true);
$showCourseTimes = get_option('eduadmin-showCourseTimes', true);
$incVat = $eduapi->GetAccountSetting($edutoken, 'PriceIncVat') == "yes";

$showEventPrice = get_option('eduadmin-showEventPrice', false);
$currency = get_option('eduadmin-currency', 'SEK');

$ft = new XFiltering();
$f = new XFilter('EventID', 'IN', join(",", $evIds));
$ft->AddItem($f);

foreach($ede as $object)
{
	foreach($edo as $course)
	{
		$id = $course->ObjectID;
		if($id == $object->ObjectID)
		{
			$object->Days = $course->Days;
			$object->StartTime = $course->StartTime;
			$object->EndTime = $course->EndTime;
			$object->CategoryID = $course->CategoryID;
			$object->PublicName = $course->PublicName;
			break;
		}
	}

	foreach($pricenames as $pn)
	{
		$id = $pn->OccationID;
		if($id == $object->OccationID)
		{
			$object->Price = $pn->Price;
			$object->PriceNameVat = $pn->PriceNameVat;
			break;
		}
	}
}

if(isset($_REQUEST['searchCourses']) && !empty($_REQUEST['searchCourses']))
{
	$ede = array_filter($ede, function($object) {
		$name = (!empty($object->PublicName) ? $object->PublicName : $object->ObjectName);

		$nameMatch = stripos($name, $_POST['searchCourses']) !== FALSE;
		return $nameMatch;
	});
}
?>
<div class="eventListTable"
	data-eduwidget="listview-eventlist"
	data-template="A"
	data-subject="<?php echo @esc_attr($attributes['subject']); ?>"
	data-category="<?php echo @esc_attr($attributes['category']); ?>"
	data-city="<?php echo @esc_attr($attributes['city']); ?>"
	data-spotsleft="<?php echo @get_option('eduadmin-spotsLeft', 'exactNumbers'); ?>"
	data-spotsettings="<?php echo @get_option('eduadmin-spotsSettings', "1-5\n5-10\n10+"); ?>"
	data-fewspots="<?php echo @get_option('eduadmin-alwaysFewSpots', "3"); ?>"
	data-showcoursedays="<?php echo @esc_attr($showCourseDays); ?>"
	data-showcoursetimes="<?php echo @esc_attr($showCourseTimes); ?>"
	data-showcourseprices="<?php echo @esc_attr($showEventPrice); ?>"
	data-currency="<?php echo @esc_attr($currency); ?>"
	data-search="<?php echo @esc_attr($_REQUEST['searchCourses']); ?>"
	data-showimages="<?php echo @esc_attr($showImages); ?>"
	data-numberofevents="<?php echo @esc_attr($attributes['numberofevents']); ?>"
	data-fetchmonths="<?php echo @esc_attr($fetchMonths); ?>"
>
<?php

$numberOfEvents = $attributes['numberofevents'];
$currentEvents = 0;

foreach($ede as $object)
{
	if($numberOfEvents != null && $numberOfEvents > 0 && $currentEvents >= $numberOfEvents)
	{
		break;
	}
	$name = (!empty($object->PublicName) ? $object->PublicName : $object->ObjectName);
?>
	<div class="objectItem">
		<?php if($showImages && !empty($object->ImageUrl)) { ?>
		<div class="objectImage" onclick="location.href = '<?php echo $baseUrl; ?>/<?php echo makeSlugs($name); ?>__<?php echo $object->ObjectID; ?>/?eid=<?php echo $object->EventID; ?><?php echo edu_getQueryString("&"); ?>';" style="background-image: url('<?php echo $object->ImageUrl; ?>');"></div>
		<?php } ?>
		<div class="objectInfoHolder">
			<div class="objectName">
				<a href="<?php echo $baseUrl; ?>/<?php echo makeSlugs($name); ?>__<?php echo $object->ObjectID; ?>/?eid=<?php echo $object->EventID; ?><?php echo edu_getQueryString("&"); ?>"><?php
					echo htmlentities(getUTF8($name));
				?></a>
			</div>
			<div class="objectDescription"><?php

		$spotsLeft = ($object->MaxParticipantNr - $object->TotalParticipantNr);
		echo isset($eventDates[$object->EventID]) ? GetLogicalDateGroups($eventDates[$object->EventID]) : GetStartEndDisplayDate($object->PeriodStart, $object->PeriodEnd, true);

		if(!empty($object->City))
		{
			echo ", <span class=\"cityInfo\">" . $object->City . "</span>";
		}

		if($object->Days > 0) {
			echo
			"<div class=\"dayInfo\">" .
				($showCourseDays ? sprintf(edu_n('%1$d day', '%1$d days', $object->Days), $object->Days) . ($showCourseTimes ? ', ' : '') : '') .
				($showCourseTimes ? date("H:i", strtotime($object->StartTime)) .
				' - ' .
				date("H:i", strtotime($object->EndTime)) : '') .
			"</div>";
		}

		if($showEventPrice) {
			echo "<div class=\"priceInfo\">" . sprintf(edu__('From %1$s'), convertToMoney($object->Price, $currency)) . " " . edu__($incVat ? "inc vat" : "ex vat") . "</div> ";
		}
		echo getSpotsLeft($spotsLeft, $object->MaxParticipantNr)


?></div>
			<div class="objectBook">
				<a class="readMoreButton" href="<?php echo $baseUrl; ?>/<?php echo makeSlugs($name); ?>__<?php echo $object->ObjectID; ?>/?eid=<?php echo $object->EventID; ?><?php echo edu_getQueryString("&"); ?>"><?php edu_e("Read more"); ?></a><br />
					<?php
				if($spotsLeft > 0 || $object->MaxParticipantNr == 0) {
				?>
					<a class="bookButton" href="<?php echo $baseUrl; ?>/<?php echo makeSlugs($name); ?>__<?php echo $object->ObjectID;?>/book/?eid=<?php echo $object->EventID; ?><?php echo edu_getQueryString("&"); ?>"><?php edu_e("Book"); ?></a>
				<?php
				} else {
				?>
					<i class="fullBooked"><?php edu_e("Full"); ?></i>
				<?php } ?>
			</div>
		</div>
	</div>
<?php
	$currentEvents++;
}
?>
</div><!-- /eventlist -->
<?php
$out = ob_get_clean();
return $out;
?>