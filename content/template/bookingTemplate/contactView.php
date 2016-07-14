		<div class="contactView">
			<h2><?php edu_e("Contact information"); ?></h2>
			<label>
				<div class="inputLabel">
					<?php edu_e("Contact name"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" style="width: 49%; display: inline-block;" required onchange="eduBookingView.ContactAsParticipant();" id="edu-contactFirstName" name="contactFirstName" placeholder="<?php edu_e("Contact first name"); ?>" value="<?php echo @esc_attr(explode(' ', $contact->ContactName)[0]); ?>" />
					<input type="text" style="width: 49%; display: inline-block; float: right;" required onchange="eduBookingView.ContactAsParticipant();" id="edu-contactLastName" name="contactLastName" placeholder="<?php edu_e("Contact surname"); ?>" value="<?php echo @esc_attr(str_replace(explode(' ', $contact->ContactName)[0], '', $contact->ContactName)); ?>" />
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
			<?php if($selectedCourse->RequireCivicRegistrationNumber) { ?>
			<label>
				<div class="inputLabel">
					<?php edu_e("Civic Registration Number"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" id="edu-contactCivReg" required name="contactCivReg" onchange="eduBookingView.ContactAsParticipant();" placeholder="<?php edu_e("Civic Registration Number"); ?>" value="<?php echo @esc_attr($contact->CivicRegistrationNumber); ?>" />
				</div>
			</label>
			<?php } ?>
			<?php if(empty($contact->Loginpass)) { ?>
			<label>
				<div class="inputLabel">
					<?php edu_e("Password"); ?>
				</div>
				<div class="inputHolder">
					<input type="password" required name="contactPass" placeholder="<?php edu_e("Please enter a password"); ?>" />
				</div>
			</label>
			<?php } ?>
			<label>
				<div class="inputHolder">
					<input type="checkbox" id="contactIsAlsoParticipant" name="contactIsAlsoParticipant" value="true" onchange="if(eduBookingView.CheckParticipantCount()) { eduBookingView.UpdatePrice(); } else { this.checked = false; return false; }" />
					<?php edu_e("Contact is also a participant"); ?>
				</div>
			</label>
			<div class="edu-modal warning" id="edu-warning-participants-contact">
				<?php edu_e("You cannot add any more participants."); ?>
			</div>
		</div>