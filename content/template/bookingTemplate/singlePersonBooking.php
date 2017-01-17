		<div class="contactView">
			<h2><?php edu_e("Contact information"); ?></h2>
			<label>
				<div class="inputLabel">
					<?php edu_e("Contact name"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" style="width: 48%; display: inline;" required onchange="eduBookingView.ContactAsParticipant();" id="edu-contactFirstName" name="contactFirstName" placeholder="<?php edu_e("Contact first name"); ?>" value="<?php echo @esc_attr(explode(' ', $contact->ContactName)[0]); ?>" />
					<input type="text" style="width: 48%; display: inline;" required onchange="eduBookingView.ContactAsParticipant();" id="edu-contactLastName" name="contactLastName" placeholder="<?php edu_e("Contact surname"); ?>" value="<?php echo @esc_attr(str_replace(explode(' ', $contact->ContactName)[0], '', $contact->ContactName)); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("E-mail address"); ?>
				</div>
				<div class="inputHolder">
					<input type="email" id="edu-contactEmail" required name="contactEmail" onchange="eduBookingView.ContactAsParticipant();" placeholder="<?php edu_e("E-mail address"); ?>" value="<?php echo @esc_attr($contact->Email); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Phone number"); ?>
				</div>
				<div class="inputHolder">
					<input type="tel" id="edu-contactPhone" name="contactPhone" onchange="eduBookingView.ContactAsParticipant();" placeholder="<?php edu_e("Phone number"); ?>" value="<?php echo @esc_attr($contact->Phone); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Mobile number"); ?>
				</div>
				<div class="inputHolder">
					<input type="tel" id="edu-contactMobile" name="contactMobile" onchange="eduBookingView.ContactAsParticipant();" placeholder="<?php edu_e("Mobile number"); ?>" value="<?php echo @esc_attr($contact->Mobile); ?>" />
				</div>
			</label>
			<?php $selectedLoginField = get_option('eduadmin-loginField', 'Email'); ?>
			<?php if($selectedCourse->RequireCivicRegistrationNumber || $selectedLoginField == 'CivicRegistrationNumber') { ?>
			<label>
				<div class="inputLabel">
					<?php edu_e("Civic Registration Number"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" id="edu-contactCivReg" required name="contactCivReg" onchange="eduBookingView.ContactAsParticipant();" placeholder="<?php edu_e("Civic Registration Number"); ?>" value="<?php echo @esc_attr($contact->CivicRegistrationNumber); ?>" />
				</div>
			</label>
			<?php } ?>
			<?php if(get_option('eduadmin-useLogin', false) && empty($contact->Loginpass)) { ?>
			<label>
				<div class="inputLabel">
					<?php edu_e("Please enter a password"); ?>
				</div>
				<div class="inputHolder">
					<input type="password" required name="contactPass" placeholder="<?php edu_e("Please enter a password"); ?>" />
				</div>
			</label>
			<?php } ?>
			<div class="edu-modal warning" id="edu-warning-participants-contact">
				<?php edu_e("You cannot add any more participants."); ?>
			</div>
		</div>
<div class="customerView">

			<label>
				<div class="inputLabel">
					<?php edu_e("Address 1"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerAddress1" placeholder="<?php edu_e("Address 1"); ?>" value="<?php echo @esc_attr($customer->Address1); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Address 2"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerAddress2" placeholder="<?php edu_e("Address 2"); ?>" value="<?php echo @esc_attr($customer->Address2); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Postal code"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerPostalCode" placeholder="<?php edu_e("Postal code"); ?>" value="<?php echo @esc_attr($customer->Zip); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Postal city"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerPostalCity" placeholder="<?php edu_e("Postal city"); ?>" value="<?php echo @esc_attr($customer->City); ?>" />
				</div>
			</label>
		</div>
		<br />