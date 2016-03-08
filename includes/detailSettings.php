<div class="eduadmin wrap">
	<h2><?php echo sprintf(__("EduAdmin settings - %s", "eduadmin"), __("Detail settings", "eduadmin")); ?></h2>

	<form method="post" action="options.php">
		<?php settings_fields('eduadmin-details'); ?>
		<?php do_settings_sections('eduadmin-details'); ?>
		<div class="block">
			<h3><?php _e("Template", "eduadmin"); ?></h3>
			<select name="eduadmin-detailTemplate">
				<option value="template_A"<?php echo (get_option('eduadmin-detailTemplate') === "template_A" ? " selected=\"selected\"" : ""); ?>><?php _e("One column layout", "eduadmin"); ?></option>
				<option value="template_B"<?php echo (get_option('eduadmin-detailTemplate') === "template_B" ? " selected=\"selected\"" : ""); ?>><?php _e("Two column layout", "eduadmin"); ?></option>
			</select>
			<br /><br />
			<label>
				<input type="checkbox" name="eduadmin-showDetailHeaders" value="true"<?php if(get_option('eduadmin-showDetailHeaders', true)) { echo " checked=\"checked\""; } ?> />
				<?php _e('Show headers in detail view', 'eduadmin'); ?>
			</label>
			<br />
			<i><?php _e('Uncheck to hide the headers in the course detail view', 'eduadmin'); ?></i>
			<br /><br />
			<label>
				<input type="checkbox" name="eduadmin-groupEventsByCity" value="true"<?php if(get_option('eduadmin-groupEventsByCity', false)) { echo " checked=\"checked\""; } ?> />
				<?php _e('Group events by city', 'eduadmin'); ?>
			</label>
			<br />
			<i><?php _e('Check to group the event list by city', 'eduadmin'); ?></i>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __("Save settings", "eduadmin"); ?>" />
			</p>
		</div>
	</form>
</div>