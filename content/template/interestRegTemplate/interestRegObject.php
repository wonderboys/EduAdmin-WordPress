<?php
ob_start();
global $wp_query;
global $eduapi;
global $edutoken;
$apiKey = get_option('eduadmin-api-key');

if(!$apiKey || empty($apiKey))
{
	echo 'Please complete the configuration: <a href="' . admin_url() . 'admin.php?page=eduadmin-settings">EduAdmin - Api Authentication</a>';
}
else
{
	$edo = get_transient('eduadmin-listCourses');
	if(!$edo)
	{
		$filtering = new XFiltering();
		$f = new XFilter('ShowOnWeb','=','true');
		$filtering->AddItem($f);

		$edo = $eduapi->GetEducationObject($edutoken, '', $filtering->ToString());
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

	?>
<div class="eduadmin">
	<a href="../" class="backLink"><?php edu_e("« Go back"); ?></a>
	<div class="title">
		<img src="<?php echo $selectedCourse->ImageUrl; ?>" class="courseImage" />
		<h1 class="courseTitle"><?php echo $name; ?> - <?php edu_e("Inquiry"); ?> <small><?php echo (!empty($courseLevel) ? $courseLevel[0]->Name : ""); ?></small></h1>
	</div>
	<hr />
	<div class="textblock">
		<?php edu_e("Please fill out the form below to send a inquiry to us about this course."); ?>
		<hr />
		<form action="" method="POST">
			<input type="hidden" name="objectid" value="<?php echo $selectedCourse->ObjectID; ?>" />
			<label>
				<div class="inputLabel">Company name</div>
				<div class="inputHolder">
					<input type="text" />
				</div>
			</label>
			<label>
				<div class="inputLabel">Contact name</div>
				<div class="inputHolder">
					<input type="text" />
				</div>
			</label>
			<label>
				<div class="inputLabel">Email address</div>
				<div class="inputHolder">
					<input type="email" />
				</div>
			</label>
			<label>
				<div class="inputLabel">Phone</div>
				<div class="inputHolder">
					<input type="phone" />
				</div>
			</label>
			<label>
				<div class="inputLabel">Mobile</div>
				<div class="inputHolder">
					<input type="phone" />
				</div>
			</label>
			<label>
				<div class="inputLabel">Notes</div>
				<div class="inputHolder">
					<textarea>
					</textarea>
				</div>
			</label>
			<label>
				<div class="inputLabel">Participants</div>
				<div class="inputHolder">
					<input type="number" min="1" />
				</div>
			</label>

			<input type="submit" class="bookButton" onclick="var validated = eduBookingView.CheckValidation(); return validated;" value="<?php edu_e("Send inquiry"); ?>" />
		</form>
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
<?php
}

$out = ob_get_clean();
return $out;
?>