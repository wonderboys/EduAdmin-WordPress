<div class="checkEmailForm">
	<input type="hidden" name="bookingLoginAction" value="checkEmail" />
	<h3><?php edu_e("Please enter your e-mail address to continue."); ?></h3>
	<label>
		<div class="inputLabel"><?php edu_e("E-mail address"); ?></div>
		<div class="inputHolder">
			<input type="email" name="eduadminloginEmail" required title="<?php echo esc_attr(edu__("Please enter your e-mail address here")); ?>" placeholder="<?php echo esc_attr(edu__("E-mail address")); ?>" value="<?php echo esc_attr($_REQUEST["eduadminloginEmail"]); ?>" />
		</div>
	</label>
	<input type="submit" class="bookingLoginButton" value="<?php echo esc_attr(edu__("Continue")); ?>" />
</div>