<?php
ob_start();
global $wp_query;
global $api;
$apiKey = get_option('eduadmin-api-key');

if(!$apiKey || empty($apiKey))
{
	echo 'Please complete the configuration: <a href="' . admin_url() . 'admin.php?page=eduadmin-settings">EduAdmin - Api Authentication</a>';
}
else
{
	//$api = new EduAdminClient();
	$key = DecryptApiKey($apiKey);
	if(!$key)
	{
		echo 'Please complete the configuration: <a href="' . admin_url() . 'admin.php?page=eduadmin-settings">EduAdmin - Api Authentication</a>';
		return;
	}
	$token = get_transient('eduadmin-token');
	if(!$token)
	{
		$token = $api->GetAuthToken($key->UserId, $key->Hash);
		set_transient('eduadmin-token', $token, HOUR_IN_SECONDS);
	}
	else
	{
		$valid = $api->ValidateAuthToken($token);
		if(!$valid)
		{
			$token = $api->GetAuthToken($key->UserId, $key->Hash);
			set_transient('eduadmin-token', $token, HOUR_IN_SECONDS);
		}
	}

	$edo = get_transient('eduadmin-listCourses');
	if(!$edo)
	{
		$filtering = new XFiltering();
		$f = new XFilter('ShowOnWeb','=','true');
		$filtering->AddItem($f);

		$edo = $api->GetEducationObject($token, '', $filtering->ToString());
		set_transient('eduadmin-listCourses', $edo, 6 * HOUR_IN_SECONDS);
	}

	$selectedCourse = false;
	$name = "";
	foreach($edo as $object)
	{
		$name = (!empty($object->PublicName) ? $object->PublicName : $object->ObjectName);
		$id = $object->ObjectID;
		if(makeSlugs($name) == $wp_query->query_vars['courseSlug'] && $id == $wp_query->query_vars["courseId"])
		{
			$selectedCourse = $object;
			break;
		}
	}
	if(!$selectedCourse)
	{
		?>
		<script>history.go(-1);</script>
		<?php
		die();
	}

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

	$ft = new XFiltering();
	$f = new XFilter('PublicPriceName', '=', 'true');
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

	$courseLevel = get_transient('eduadmin-courseLevel-' . $selectedCourse->ObjectID);
	if(!$courseLevel)
	{
		$ft = new XFiltering();
		$f = new XFilter('ObjectID', '=', $selectedCourse->ObjectID);
		$ft->AddItem($f);
		$courseLevel = $api->GetEducationLevelObject($token, '', $ft->ToString());
		set_transient('eduadmin-courseLevel-' . $selectedCourse->ObjectID, $courseLevel, HOUR_IN_SECONDS);
	}

	$lastCity = "";

	$showHeaders = get_option('eduadmin-showDetailHeaders', true);
?>
<!-- mfunc -->
<div class="eduadmin">
	<a href="../" class="backLink"><?php edu_e("« Go back"); ?></a>
	<div class="title">
		<img src="<?php echo $selectedCourse->ImageUrl; ?>" style="max-width: 8em; max-height: 8em; margin-right: 2em;" />
		<h1 style="display: inline-block;"><?php echo $name; ?> <small><?php echo $courseLevel[0]->Name; ?></small></h1>
	</div>
	<hr />
	<div class="textblock">
		<?php if(!empty($selectedCourse->CourseDescription)) { ?>
			<?php if($showHeaders) { ?>
		<h3><?php edu_e("Course description"); ?></h3>
			<?php } ?>
		<div>
		<?php
			echo $selectedCourse->CourseDescription;
		?>
		</div>
		<?php } ?>
		<?php if(!empty($selectedCourse->CourseGoal)) { ?>
			<?php if($showHeaders) { ?>
		<h3><?php edu_e("Course goal"); ?></h3>
			<?php } ?>
		<div>
		<?php
			echo $selectedCourse->CourseGoal;
		?>
		</div>
		<?php } ?>
		<?php if(!empty($selectedCourse->TargetGroup)) { ?>
			<?php if($showHeaders) { ?>
		<h3><?php edu_e("Target group"); ?></h3>
			<?php } ?>
		<div>
		<?php
			echo $selectedCourse->TargetGroup;
		?>
		</div>
		<?php } ?>
		<?php if(!empty($selectedCourse->Prerequisites)) { ?>
			<?php if($showHeaders) { ?>
		<h3><?php edu_e("Prerequisites"); ?></h3>
			<?php } ?>
		<div>
		<?php
			echo $selectedCourse->Prerequisites;
		?>
		</div>
		<?php } ?>
		<?php if(!empty($selectedCourse->CourseAfter)) { ?>
			<?php if($showHeaders) { ?>
		<h3><?php edu_e("After the course"); ?></h3>
			<?php } ?>
		<div>
		<?php
			echo $selectedCourse->CourseAfter;
		?>
		</div>
		<?php } ?>
		<?php if(!empty($selectedCourse->Quote)) { ?>
			<?php if($showHeaders) { ?>
		<h3><?php edu_e("Quotes"); ?></h3>
			<?php } ?>
		<div>
		<?php
			echo $selectedCourse->Quote;
		?>
		</div>
		<?php } ?>
	</div>
	<div class="eventInformation">
		<?php if(!empty($selectedCourse->StartTime) && !empty($selectedCourse->EndTime)) { ?>
		<h3><?php edu_e("Time"); ?></h3>
		<?php
			echo ($selectedCourse->Days > 0 ? sprintf(edu_n('%1$d day', '%1$d days', $selectedCourse->Days), $selectedCourse->Days) . ', ' : '') . date("H:i", strtotime($selectedCourse->StartTime)) . ' - ' . date("H:i", strtotime($selectedCourse->EndTime));
		?>
		<?php } ?>
		<?php

		$occIds = Array();
		$occIds[] = -1;
		foreach($events as $ev)
		{
			$occIds[] = $ev->OccationID;
		}

		$ft = new XFiltering();
		$f = new XFilter('PublicPriceName', '=', 'true');
		$ft->AddItem($f);
		$f = new XFilter('ObjectID', '=', $selectedCourse->ObjectID);
		$ft->AddItem($f);

		$prices = $api->GetObjectPriceName($token, '', $ft->ToString());
		$uniquePrices = Array();
		foreach($prices as $price)
		{
			$uniquePrices[$price->Description] = $price;
		}

		if(count($prices) > 0) {
		?>
		<h3><?php edu_e("Price"); ?></h3>
		<?php
			$currency = get_option('eduadmin-currency', 'SEK');
			if(count($uniquePrices) >= 1) {
		?>
		<?php echo sprintf('%1$s %2$s', current($uniquePrices)->Description, convertToMoney(current($uniquePrices)->Price, $currency)); ?>
		<?php
			}
		} ?>
	</div>
	<div class="eventDays">
	<?php
	$i = 0;
	foreach($events as $ev)
	{
		if($groupByCity && $lastCity != $ev->City)
		{
			$i = 0;
			echo '<div class="eventSeparator">' . $ev->City . '</div>';
		}
		if(isset($_REQUEST['eid']))
		{
			if($ev->EventID != $_REQUEST['eid'])
			{
				continue;
			}
		}
	?>
		<div class="eventItem">
			<div class="eventDate<?php echo $groupByCityClass; ?>">
				<?php echo GetStartEndDisplayDate($ev->PeriodStart, $ev->PeriodEnd); ?>,
				<?php echo date("H:i", strtotime($ev->PeriodStart)); ?> - <?php echo date("H:i", strtotime($ev->PeriodEnd)); ?>
			</div>
			<?php if(!$groupByCity) { ?>
			<div class="eventCity">
				<?php
				echo $ev->City;
				?>
			</div>
			<?php } ?>
			<div class="eventStatus<?php echo $groupByCityClass; ?>">
			<?php
				$spotsLeft = ($ev->MaxParticipantNr - $ev->TotalParticipantNr);
				echo getSpotsLeft($spotsLeft, $ev->MaxParticipantNr);
			?>
			</div>
			<div class="eventBook<?php echo $groupByCityClass; ?>">
			<?php
			if($ev->MaxParticipantNr == 0 ||$spotsLeft > 0) {
			?>
				<a class="book-link" href="./book/?eid=<?php echo $ev->EventID; ?><?php echo edu_getQueryString("&"); ?>" style="text-align: center;"><?php edu_e("Book"); ?></a>
			<?php
			} else {
			?>
				<i class="fullBooked"><?php edu_e("Full"); ?></i>
			<?php } ?>
			</div>
		</div>
	<?php
		$lastCity = $ev->City;
		$i++;
	}

	if(count($events) == 0)
	{
	?>
	<div class="noDatesAvailable">
		<i><?php edu_e("No available dates for the selected course"); ?></i>
	</div>
	<?php
	}
	?>
	</div>
</div>
<?php
$originalTitle = get_the_title();
$newTitle = $name . " | " . $originalTitle;
?>
<script type="text/javascript">
(function() {
	var title = document.title;
	title = title.replace('<?php echo $originalTitle; ?>', '<?php echo $newTitle; ?>');
	document.title = title;
})();
</script>
<!-- /mfunc -->
<?php
}
$out = ob_get_clean();
return $out;
?>