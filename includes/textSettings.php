<div class="eduadmin wrap">
	<h2><?php echo sprintf(__("EduAdmin settings - %s", "eduadmin"), __("Translation", "eduadmin")); ?></h2>

	<form method="post" action="options.php">
		<?php settings_fields('eduadmin-phrases'); ?>
		<?php do_settings_sections('eduadmin-phrases'); ?>
		<div class="block">
			<table>
				<tr>
					<td><h3><?php _e("Key text", "eduadmin"); ?></h3></td>
					<td><h3><?php _e("Translation", "eduadmin"); ?></h3></td>
				</tr>
<?php
	$phrases = get_option('eduadmin-phrases');
	$phrasestr = $phrases;
	$file = file_get_contents(( dirname( __FILE__ ) ) . '/defaultPhrases.json');
	$originalPhrases = json_decode($file, true);

	if(!$phrases || $phrases == "{}" || $phrases == "null")
	{
		$phrasestr = $file;
		$phrases = json_decode($file, true);
		update_option('eduadmin-phrases', json_encode($phrases));
	}
	else
	{
		$phrases = json_decode($phrases, true);
	}

	ksort($phrases, SORT_NATURAL | SORT_FLAG_CASE);

	foreach($phrases as $phrase => $translation)
	{
		?>
		<tr><td><?php echo $phrase; ?></td><td><input type="text" class="form-control" style="width: 300px;" onblur="update_phrase(event);" data-key="<?php echo htmlentities($phrase); ?>" placeholder="<?php echo htmlentities($originalPhrases[$phrase]); ?>" value="<?php echo htmlentities($phrases[$phrase]); ?>" /></td></tr>
		<?php
	}
?>
			</table>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__("Save settings", "eduadmin"); ?>" />
		</div>
		<input type="hidden" id="eduadmin-phrases" name="eduadmin-phrases" value='' />

	</form>
	<script type="text/javascript">
	var loadedPhrases;
	(function() {
		loadedPhrases = <?php echo $phrasestr; ?>;
	})();

	function update_phrase(item)
	{
		var t = item.target;
		var key = t.attributes['data-key'].value;
		var translation = t.value;
		if(translation == '') {
			delete loadedPhrases[key];
		} else {
			loadedPhrases[key] = translation;
		}
		document.getElementById('eduadmin-phrases').value = JSON.stringify(loadedPhrases);
	}
	</script>
</div>