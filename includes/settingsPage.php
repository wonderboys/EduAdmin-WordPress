<?php
if(get_option('eduadmin-credentials_have_changed'))
{
	delete_transient('eduadmin-token');
	delete_transient('eduadmin-listCourses');
	delete_transient('eduadmin-events-object');
	delete_transient('eduadmin-courseSubject');
	delete_transient('eduadmin-listEvents');
	delete_transient('eduadmin-courseLevels');
	delete_transient('eduadmin-levels');
	delete_transient('eduadmin-categories');
	delete_transient('eduadmin-locations');
	delete_transient('eduadmin-subjects');

	update_option('eduadmin-credentials_have_changed', false);
}
?>
<div class="eduadmin wrap">
	<h2><?php echo sprintf(__("EduAdmin settings - %s", "eduadmin"), __("Api authentication", "eduadmin")); ?></h2>

	<form method="post" action="options.php">
		<?php settings_fields('eduadmin-credentials'); ?>
		<?php do_settings_sections('eduadmin-credentials'); ?>
		<input type="hidden" name="eduadmin-credentials_have_changed" value="true" />
		<div class="block">
			<p>
			<?php echo __("Enter the provided Api User Id and Api Hash to connect to EduAdmin", "eduadmin"); ?>
			</p>
			<p>
				<?php echo sprintf(__("You can get these details by contacting %s", "eduadmin"), sprintf("<a href=\"http://support.multinet.se\" target=\"_blank\">%s</a>", __("our support", "eduadmin"))); ?>
			</p>
			<input type="number" readonly class="form-control api_user_id" name="eduadmin-api_user_id" id="eduadmin-api_user_id" value="<?php echo get_option('eduadmin-api_user_id'); ?>" placeholder="<?php echo esc_attr__("Api User Id", "eduadmin"); ?>" />
			<input type="text" readonly class="form-control api_hash" name="eduadmin-api_hash" id="eduadmin-api_hash" value="<?php echo get_option('eduadmin-api_hash'); ?>" placeholder="<?php echo esc_attr__("Api Hash", "eduadmin"); ?>" />
			<span id="edu-unlockButton" title="<?php esc_attr_e("Click here to unlock the Api Authentication-fields", "eduadmin"); ?>" class="dashicons dashicons-lock" onclick="EduAdmin.UnlockApiAuthentication();"></span>
			<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__("Save settings", "eduadmin"); ?>" />
			</p>
		</div>
	</form>
</div>