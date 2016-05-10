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
	<input type="submit" class="bookingLoginButton" value="<?php echo esc_attr(edu__("Log in")); ?>" />
</div>
	<?php if(isset($_SESSION['eduadminLoginError'])) { ?>
	<div class="edu-modal warning" style="display: block; clear: both;">
		<?php echo $_SESSION['eduadminLoginError']; ?>
	</div>
	<?php unset($_SESSION['eduadminLoginError']); } ?>