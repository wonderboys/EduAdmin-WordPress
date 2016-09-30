		<div class="participantView">
			<h2><?php edu_e("Participant information"); ?></h2>
			<div class="participantHolder" id="edu-participantHolder">
				<div id="contactPersonParticipant" class="participantItem contactPerson" style="display: none;">
					<h3>
						<?php edu_e("Participant"); ?>
					</h3>
					<label>
						<div class="inputLabel">
							<?php edu_e("Participant name"); ?>
						</div>
						<div class="inputHolder">
							<input type="text" readonly style="width: 49%; display: inline-block;" class="contactFirstName" placeholder="<?php edu_e("Participant first name"); ?>" />
							<input type="text" readonly style="width: 49%; display: inline-block; float: right;" class="contactLastName" placeholder="<?php edu_e("Participant surname"); ?>" />
						</div>
					</label>
					<label>
						<div class="inputLabel">
							<?php edu_e("E-mail address"); ?>
						</div>
						<div class="inputHolder">
							<input type="email" readonly class="contactEmail" placeholder="<?php edu_e("E-mail address"); ?>" />
						</div>
					</label>
					<label>
						<div class="inputLabel">
							<?php edu_e("Phone number"); ?>
						</div>
						<div class="inputHolder">
							<input type="tel" readonly class="contactPhone" placeholder="<?php edu_e("Phone number"); ?>" />
						</div>
					</label>
					<label>
						<div class="inputLabel">
							<?php edu_e("Mobile number"); ?>
						</div>
						<div class="inputHolder">
							<input type="tel" readonly class="contactMobile" placeholder="<?php edu_e("Mobile number"); ?>" />
						</div>
					</label>
					<?php if($selectedCourse->RequireCivicRegistrationNumber) { ?>
					<label>
						<div class="inputLabel">
							<?php edu_e("Civic Registration Number"); ?>
						</div>
						<div class="inputHolder">
							<input type="text" readonly class="contactCivReg" placeholder="<?php edu_e("Civic Registration Number"); ?>" />
						</div>
					</label>
					<?php } ?>
					<?php if(get_option('eduadmin-selectPricename', 'firstPublic') == "selectParticipant") { ?>
					<label>
						<div class="inputLabel">
							<?php edu_e("Price name"); ?>
						</div>
						<div class="inputHolder">
							<select name="contactPriceName" class="edudropdown participantPriceName" onchange="eduBookingView.UpdatePrice();">
								<?php foreach($prices as $price) { ?>
								<option data-price="<?php echo esc_attr($price->Price); ?>" value="<?php echo esc_attr($price->OccationPriceNameLnkID); ?>">
									<?php echo trim($price->Description); ?>
									(<?php echo convertToMoney($price->Price, get_option('eduadmin-currency', 'SEK')) . " " . edu__($incVat ? "inc vat" : "ex vat"); ?>)
								</option>
								<?php } ?>
							</select>
						</div>
					</label>
					<?php } ?>
					<?php
						/*$st = new XSorting();
						$s = new XSort('StartDate', 'ASC');
						$st->AddItem($s);
						$s = new XSort('EndDate', 'ASC');
						$st->AddItem($s);

						$ft = new XFiltering();
						$f = new XFilter('ParentEventID', '=', $event->EventID);
						$ft->AddItem($f);
						$subEvents = $eduapi->GetSubEvent($edutoken, $st->ToString(), $ft->ToString());
						if(count($subEvents) > 0) {
							echo "<h4>" . edu__("Sub events") . "</h4>\n";
							foreach($subEvents as $subEvent)
							{
								echo "<label>".
								"<input " .
									"name=\"contactSubEvent_" . $subEvent->EventID . "\" " .
									"type=\"checkbox\"" .
									($subEvent->SelectedByDefault == true || $subEvent->MandatoryParticipation == true ? " checked=\"checked\"" : "") .
									($subEvent->MandatoryParticipation == true ? " disabled=\"disabled\"" : "") .
								" value=\"" . $subEvent->EventID . "\"> " . 
									$subEvent->Description . 
									" (" . date("d/m H:i", strtotime($subEvent->StartDate)) . " - " . date("d/m H:i", strtotime($subEvent->EndDate)) . ") " .

								"</label>\n";
							}
							echo "<br />";
						}*/
					?>
				</div>
				<div class="participantItem template" style="display: none;">
					<h3>
						<?php edu_e("Participant"); ?>
						<div class="removeParticipant" onclick="eduBookingView.RemoveParticipant(this);"><?php edu_e("Remove"); ?></div>
					</h3>
					<label>
						<div class="inputLabel">
							<?php edu_e("Participant name"); ?>
						</div>
						<div class="inputHolder">
							<input type="text" style="width: 49%; display: inline-block;" class="participantFirstName" name="participantFirstName[]" placeholder="<?php edu_e("Participant first name"); ?>" />
							<input type="text" style="width: 49%; display: inline-block; float: right;" class="participantLastName" name="participantLastName[]" placeholder="<?php edu_e("Participant surname"); ?>" />
						</div>
					</label>
					<label>
						<div class="inputLabel">
							<?php edu_e("E-mail address"); ?>
						</div>
						<div class="inputHolder">
							<input type="email" name="participantEmail[]" placeholder="<?php edu_e("E-mail address"); ?>" />
						</div>
					</label>
					<label>
						<div class="inputLabel">
							<?php edu_e("Phone number"); ?>
						</div>
						<div class="inputHolder">
							<input type="tel" name="participantPhone[]" placeholder="<?php edu_e("Phone number"); ?>" />
						</div>
					</label>
					<label>
						<div class="inputLabel">
							<?php edu_e("Mobile number"); ?>
						</div>
						<div class="inputHolder">
							<input type="tel" name="participantMobile[]" placeholder="<?php edu_e("Mobile number"); ?>" />
						</div>
					</label>
					<?php if($selectedCourse->RequireCivicRegistrationNumber) { ?>
					<label>
						<div class="inputLabel">
							<?php edu_e("Civic Registration Number"); ?>
						</div>
						<div class="inputHolder">
							<input type="text" name="participantCivReg[]" placeholder="<?php edu_e("Civic Registration Number"); ?>" />
						</div>
					</label>
					<?php } ?>
					<?php if(get_option('eduadmin-selectPricename', 'firstPublic') == "selectParticipant") { ?>
					<label>
						<div class="inputLabel">
							<?php edu_e("Price name"); ?>
						</div>
						<div class="inputHolder">
							<select name="participantPriceName[]" class="edudropdown participantPriceName" onchange="eduBookingView.UpdatePrice();">
								<?php foreach($prices as $price) { ?>
								<option data-price="<?php echo esc_attr($price->Price); ?>" value="<?php echo esc_attr($price->OccationPriceNameLnkID); ?>">
									<?php echo trim($price->Description); ?>
									(<?php echo convertToMoney($price->Price, get_option('eduadmin-currency', 'SEK')) . " " . edu__($incVat ? "inc vat" : "ex vat"); ?>)
								</option>
								<?php } ?>
							</select>
						</div>
					</label>
					<?php } ?>
					<?php
					/*
						$st = new XSorting();
						$s = new XSort('StartDate', 'ASC');
						$st->AddItem($s);
						$s = new XSort('EndDate', 'ASC');
						$st->AddItem($s);

						$ft = new XFiltering();
						$f = new XFilter('ParentEventID', '=', $event->EventID);
						$ft->AddItem($f);
						$subEvents = $eduapi->GetSubEvent($edutoken, $st->ToString(), $ft->ToString());
						if(count($subEvents) > 0) {
							echo "<h4>" . edu__("Sub events") . "</h4>\n";
							foreach($subEvents as $subEvent)
							{
								echo "<label>".
								"<input " .
									"name=\"participantSubEvent_" . $subEvent->EventID . "[]\" " .
									"type=\"checkbox\"" .
									($subEvent->SelectedByDefault == true || $subEvent->MandatoryParticipation == true ? " checked=\"checked\"" : "") .
									($subEvent->MandatoryParticipation == true ? " disabled=\"disabled\"" : "") .
								" value=\"" . $subEvent->EventID . "\"> " . 
									$subEvent->Description . 
									" (" . date("d/m H:i", strtotime($subEvent->StartDate)) . " - " . date("d/m H:i", strtotime($subEvent->EndDate)) . ") " .

								"</label>\n";
							}
							echo "<br />";
						}
						#echo "<pre>" . print_r($subEvents, true) . "</pre>";
						*/
					?>
				</div>
			</div>
			<div>
				<a href="javascript://" class="addParticipantLink" onclick="eduBookingView.AddParticipant(); return false;"><?php edu_e("Add participant"); ?></a>
			</div>
			<div class="edu-modal warning" id="edu-warning-participants">
				<?php edu_e("You cannot add any more participants."); ?>
			</div>

		</div>