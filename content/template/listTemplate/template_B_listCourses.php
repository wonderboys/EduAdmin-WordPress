<?php
$filtering = new XFiltering();
$f = new XFilter('ShowOnWeb','=','true');
$filtering->AddItem($f);

if($categoryID > 0)
{
	$f = new XFilter('CategoryID', '=', $categoryID);
	$filtering->AddItem($f);
}

if(isset($_REQUEST['eduadmin-category']))
{
	$f = new XFilter('CategoryID', '=', $_REQUEST['eduadmin-category']);
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

if(count($filterCourses) > 0)
{
	$f = new XFilter('ObjectID', 'IN', join(',', $filterCourses));
	$filtering->AddItem($f);
}

$sortOrder = get_option('eduadmin-listSortOrder', 'SortIndex');

$sort = new XSorting();
$s = new XSort($sortOrder, 'ASC');
$sort->AddItem($s);

$edo = $api->GetEducationObject($token, $sort->ToString(), $filtering->ToString());

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
		$subjects = $api->GetEducationSubject($token, $sorting->ToString(), '');
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

if(isset($_REQUEST['eduadmin-level']) && !empty($_REQUEST['eduadmin-level']))
{
	$edo = array_filter($edo, function($object) {
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

$objIds = array();

foreach($edo as $obj)
{
	$objIds[] = $obj->ObjectID;
}

$filtering = new XFiltering();
$f = new XFilter('ShowOnWeb','=','true');
$filtering->AddItem($f);

if($categoryID > 0)
{
	$f = new XFilter('CategoryID', '=', $categoryID);
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

if(isset($_REQUEST['eduadmin-subject']))
{
	$f = new XFilter('SubjectID', '=', $_REQUEST['eduadmin-subject']);
	$filtering->AddItem($f);
}

$f = new XFilter('PeriodStart','>',date("Y-m-d 00:00:00", strtotime("now +1 day")));
$filtering->AddItem($f);
$f = new XFilter('PeriodEnd', '<', date("Y-m-d 23:59:59", strtotime("now +1 year")));
$filtering->AddItem($f);
$f = new XFilter('StatusID','=','1');
$filtering->AddItem($f);

$f = new XFilter('LastApplicationDate','>',date("Y-m-d H:i:s"));
$filtering->AddItem($f);

if(count($objIds) > 0)
{
	$f = new XFilter('ObjectID', 'IN', join(',', $objIds));
	$filtering->AddItem($f);
}

$sorting = new XSorting();
$s = new XSort('PeriodStart', 'ASC');
$sorting->AddItem($s);

$ede = $api->GetEvent($token, $sorting->ToString(), $filtering->ToString());

if(isset($_REQUEST['eduadmin-subject']) && !empty($_REQUEST['eduadmin-subject']))
{
	$subjects = get_transient('eduadmin-subjects');
	if(!$subjects)
	{
		$sorting = new XSorting();
		$s = new XSort('SubjectName', 'ASC');
		$sorting->AddItem($s);
		$subjects = $api->GetEducationSubject($token, $sorting->ToString(), '');
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

foreach($ede as $e)
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
}

if(isset($_REQUEST['searchCourses']) && !empty($_REQUEST['searchCourses']))
{
	$ede = array_filter($ede, function($object) {
		$name = (!empty($object->PublicName) ? $object->PublicName : $object->ObjectName);

		$nameMatch = stripos($name, $_REQUEST['searchCourses']) !== FALSE;
		return $nameMatch;
	});
}

$descrField = get_option('eduadmin-layout-descriptionfield', 'CourseDescriptionShort');
if(stripos($descrField, "attr_") !== FALSE)
{
	$ft = new XFiltering();
	$f = new XFilter("AttributeID", "=", substr($descrField, 5));
	$ft->AddItem($f);
	$objectAttributes = $api->GetObjectAttribute($token, '', $ft->ToString());
}

foreach($edo as $object)
{
	$name = (!empty($object->PublicName) ? $object->PublicName : $object->ObjectName);
	$events = array_filter($ede, function($ev) use (&$object) {

		return $ev->ObjectID == $object->ObjectID;
	});

	$prices = array();
	$sortedEvents = array();
	$eventCities = array();
	foreach($pricenames as $pr)
	{
		foreach($events as $ev)
		{
			if($ev->OccationID == $pr->OccationID)
			{
				$prices[$pr->Price] = $pr;
			}
			$sortedEvents[$ev->PeriodStart] = $ev;
			$eventCities[$ev->City] = $ev;
		}
	}

	ksort($sortedEvents);
	ksort($eventCities);

	#echo "<xmp>" . print_r($sortedEvents, true) . "</xmp>";


?>
	<div class="objectBlock brick">
		<?php if($showImages && !empty($object->ImageUrl)) { ?>
		<div class="objectImage" onclick="location.href = './<?php echo makeSlugs($name); ?>__<?php echo $object->ObjectID; ?>/';" style="background-image: url('<?php echo $object->ImageUrl; ?>');"></div>
		<?php } ?>
		<div class="objectName">
			<a href="./<?php echo makeSlugs($name); ?>__<?php echo $object->ObjectID; ?>/"><?php
				echo htmlentities(getUTF8($name));
			?></a>
		</div>
		<div class="objectDescription"><?php
				if(stripos($descrField, "attr_") !== FALSE && count($objectAttributes) > 0)
				{
					$objectDescription = array_filter($objectAttributes, function($oa) use(&$object) {
						return $oa->ObjectID == $object->ObjectID;
					});

					//$descr = strip_tags(str_replace(Array('<br />', '<br>', '<br/>', "\n", "\r"), " ", htmlspecialchars_decode(current($objectDescription)->AttributeValue)));
					$descr = htmlspecialchars_decode(current($objectDescription)->AttributeValue);
				}
				else
				{
					//$descr = strip_tags(str_replace(Array('<br />', '<br>', '<br/>', "\n", "\r"), " ", $object->{$descrField}));
					$descr = $object->{$descrField};
				}

				$showNextEventDate = get_option('eduadmin-showNextEventDate', false);
				$showCourseLocations = get_option('eduadmin-showCourseLocations', false);
				$showEventPrice = get_option('eduadmin-showEventPrice', false);

				$showCourseDays = get_option('eduadmin-showCourseDays', true);
				$showCourseTimes = get_option('eduadmin-showCourseTimes', true);

				$showDescr = get_option('eduadmin-showCourseDescription', true);
				if($showDescr)
					echo "<div class\"courseDescription\">" . $descr . "</div>";

				if($showCourseLocations && count($eventCities) > 0)
				{
					$cities = join(", ", array_keys($eventCities));
					echo "<div class=\"locationInfo\">" . $cities . "</div> ";
				}

				if($showNextEventDate && count($sortedEvents) > 0)
				{
					echo "<div class=\"nextEventDate\">" . sprintf(edu__('Next event %1$s'), date("Y-m-d", strtotime(current($sortedEvents)->PeriodStart))) . " " . current($sortedEvents)->City . "</div> ";
				}

				if($showEventPrice && count($prices) > 0)
				{
					ksort($prices);
					$cheapest = current($prices);
					echo "<div class=\"priceInfo\">" . sprintf(edu__('From %1$s'), convertToMoney($cheapest->Price, get_option('eduadmin-currency', 'SEK'))) . "</div> ";
				}

				if($object->Days > 0) {
					echo
					"<div class=\"dayInfo\">" .
						($showCourseDays ? sprintf(edu_n('%1$d day', '%1$d days', $object->Days), $object->Days) . ', ' : '') .
						($showCourseTimes ? date("H:i", strtotime($object->StartTime)) .
						' - ' .
						date("H:i", strtotime($object->EndTime)) : '') .
					"</div>";
				}
		?></div>
		<div class="objectBook">
			<a class="readMoreButton" href="./<?php echo makeSlugs($name); ?>__<?php echo $object->ObjectID; ?>/"><?php edu_e("Read more"); ?></a>
		</div>
	</div>
<?php
}
?>