<div class="checkLoginForm">
	<input type="hidden" name="bookingLoginAction" value="loginEmail" />
	<h3><?php edu_e("Please login to continue."); ?></h3>
	<label>
		<div class="inputLabel"><?php edu_e("E-mail address"); ?></div>
		<div class="inputHolder">
			<input type="email" name="eduadminloginEmail" required title="<?php echo esc_attr(edu__("Please enter your e-mail address here")); ?>" placeholder="<?php echo esc_attr(edu__("E-mail address")); ?>" value="<?php echo esc_attr($_REQUEST["eduadminloginEmail"]); ?>" />
		</div>
	</label>
	<label>
		<div class="inputLabel"><?php edu_e("Password"); ?></div>
		<div class="inputHolder">
			<input type="password" name="eduadminpassword" required title="<?php echo esc_attr(edu__("Please enter your password here")); ?>" placeholder="<?php echo esc_attr(edu__("Password")); ?>" />
		</div>
	</label>
	<!--<input type="submit" class="bookingLoginButton" value="<?php echo esc_attr(edu__("Log in")); ?>" />-->
	<button class="bookingLoginButton" onclick="this.form.bookingLoginAction.value = 'login';"><?php edu_e("Log in"); ?></button>
	<button class="bookingforgotPasswordButton" onclick="this.form.eduadminpassword.value = '-'; this.form.bookingLoginAction.value = 'forgot';"><?php edu_e("Forgot password"); ?></button>
</div>
	<?php if(isset($_SESSION['eduadminLoginError'])) { ?>
	<div class="edu-modal warning" style="display: block; clear: both;">
		<?php echo $_SESSION['eduadminLoginError']; ?>
	</div>
	<?php unset($_SESSION['eduadminLoginError']); } ?>
	<?php if(isset($_SESSION['eduadmin-forgotPassSent']) && $_SESSION['eduadmin-forgotPassSent'] == true) {
		unset($_SESSION['eduadmin-forgotPassSent']);
	?>
	<div class="edu-modal warning" style="display: block; clear: both;">
		<?php edu_e("A new password has been sent by email."); ?>
	</div>
	<?php } else if(isset($_SESSION['eduadmin-forgotPassSent']) && $_SESSION['eduadmin-forgotPassSent'] == false) {
		unset($_SESSION['eduadmin-forgotPassSent']);
	?>
	<div class="edu-modal warning" style="display: block; clear: both;">
		<?php edu_e("Could not send a new password by email."); ?>
	</div>
	<?php } ?>