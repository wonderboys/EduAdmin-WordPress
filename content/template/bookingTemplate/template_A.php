<?php
global $wp_query;
$apiKey = get_option('eduadmin-api-key');

if(!$apiKey || empty($apiKey))
{
	echo 'Please complete the configuration: <a href="' . admin_url() . 'admin.php?page=eduadmin-settings">EduAdmin - Api Authentication</a>';
}
else
{
	$api = new EduAdminClient();
	$key = DecryptApiKey($apiKey);
	if(!$key)
	{
		echo 'Please complete the configuration: <a href="' . admin_url() . 'admin.php?page=eduadmin-settings">EduAdmin - Api Authentication</a>';
		return;
	}
	$token = get_transient('eduadmin-token');
	if(!$token)
	{
		$token = $api->GetAuthToken($key->UserId, $key->Hash);
		set_transient('eduadmin-token', $token, HOUR_IN_SECONDS);
	}
	else
	{
		$valid = $api->ValidateAuthToken($token);
		if(!$valid)
		{
			$token = $api->GetAuthToken($key->UserId, $key->Hash);
			set_transient('eduadmin-token', $token, HOUR_IN_SECONDS);
		}
	}

	$edo = get_transient('eduadmin-listCourses');
	if(!$edo)
	{
		$filtering = new XFiltering();
		$f = new XFilter('ShowOnWeb','=','true');
		$filtering->AddItem($f);

		$edo = $api->GetEducationObject($token, '', $filtering->ToString());
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
	$ft = new XFiltering();
	if(isset($_REQUEST['eid']))
	{
		$eventid = $_REQUEST['eid'];
		$f = new XFilter('EventID', '=', $eventid);
		$ft->AddItem($f);
	}
	$f = new XFilter('ShowOnWeb', '=', 'true');
	$ft->AddItem($f);
	$f = new XFilter('ObjectID', '=', $selectedCourse->ObjectID);
	$ft->AddItem($f);
	$f = new XFilter('LastApplicationDate', '>=', date("Y-m-d 00:00:00"));
	$ft->AddItem($f);
	$f = new XFilter('StatusID','=','1');
	$ft->AddItem($f);

	$st = new XSorting();
	$s = new XSort('PeriodStart', 'ASC');
	$st->AddItem($s);

	$events = $api->GetEvent(
		$token,
		$st->ToString(),
		$ft->ToString()
	);

	$event = $events[0];

	if(!$event)
	{
		?>
		<script>history.go(-1);</script>
		<?php
		die();
	}

	if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'bookCourse')
	{
		$eventId = $_REQUEST['eid'];
		$requiredFields = array();
		$requiredFields[] = 'customerName';

		$missingFields = false;
		foreach($requiredFields as $field)
		{
			if(empty($_REQUEST[$field]))
			{
				$missingFields = true;
			}
		}

		if($missingFields)
		{
			// TODO: Show an error message that some fields are missing
			// Should not be able to happen, since we should validate the fields first
		}
		else
		{
			$customer = new Customer();
			$customer->CustomerName = trim($_POST['customerName']);
			$customer->CustomerGroupID = get_option('eduadmin-customerGroupId', NULL);
			$customer->InvoiceOrgnr = trim($_POST['customerVatNo']);
			$customer->Address1 = trim($_POST['customerAddress1']);
			$customer->Address2 = trim($_POST['customerAddress2']);
			$customer->Zip = trim($_POST['customerPostalCode']);
			$customer->City = trim($_POST['customerPostalCity']);
			$customer->CustomerReference = trim($_POST['invoiceReference']);

			if(isset($_POST['alsoInvoiceCustomer']) && $_POST['alsoInvoiceCustomer'] == "true")
			{
				$customer->InvoiceName = trim($_POST['customerName']);
				$customer->InvoiceAddress1 = trim($_POST['customerAddress1']);
				$customer->InvoiceAddress2 = trim($_POST['customerAddress2']);
				$customer->InvoiceZip = trim($_POST['customerPostalCode']);
				$customer->InvoiceCity = trim($_POST['customerPostalCity']);
			}
			else
			{
				$customer->InvoiceName = trim($_POST['invoiceName']);
				$customer->InvoiceAddress1 = trim($_POST['invoiceAddress1']);
				$customer->InvoiceAddress2 = trim($_POST['invoiceAddress2']);
				$customer->InvoiceZip = trim($_POST['invoicePostalCode']);
				$customer->InvoiceCity = trim($_POST['invoicePostalCity']);
			}

			$ft = new XFiltering();
			$f = new XFilter('CustomerName', '=', $customer->CustomerName);
			$ft->AddItem($f);
			$f = new XFilter('Zip', '=', $customer->Zip);
			$ft->AddItem($f);
			$matchingCustomer = $api->GetCustomer($token, '', $ft->ToString());
			if(count($matchingCustomer) == 0)
			{
				$cres = $api->SetCustomer($token, array($customer));
				$customer->CustomerID = $cres[0];
			}
			else
			{
				$customer = $matchingCustomer[0];
			}
			$contact = new CustomerContact();
			$contact->CustomerID = $customer->CustomerID;

			if(!empty($_POST['contactFirstName']))
			{
				$contact->ContactName = trim($_POST['contactFirstName']) . ";" . trim($_POST['contactLastName']);
				$contact->Phone = trim($_POST['contactPhone']);
				$contact->Mobile = trim($_POST['contactMobile']);
				$contact->Email = trim($_POST['contactEmail']);
				if(isset($_POST['contactCivReg']))
				{
					$contact->CivicRegistrationNumber = trim($_POST['contactCivReg']);
				}
				$contact->CanLogin = 'false';
				$contact->PublicGroup = 'true';

				$ft = new XFiltering();
				$f = new XFilter('CustomerID', '=', $customer->CustomerID);
				$ft->AddItem($f);
				$f = new XFilter('ContactName', '=', trim(str_replace(';', ' ', $contact->ContactName)));
				$ft->AddItem($f);
				$f = new XFilter('Email', '=', $contact->Email);
				$ft->AddItem($f);
				$matchingContacts = $api->GetCustomerContact($token, '', $ft->ToString());
				if(count($matchingContacts) == 0)
				{
					$contact->CustomerContactID = $api->SetCustomerContact($token, array($contact))[0];
				}
				else
				{
					$contact = $matchingContacts[0];
				}
			}

			$persons = array();
			$personEmail = array();
			if(!empty($contact->Email) && !in_array($contact->Email, $personEmail))
			{
				$personEmail[] = $contact->Email;
			}

			$pArr = array();
			foreach($_POST['participantFirstName'] as $key => $value)
			{
				if($key === "0")
				{
					continue;
				}

				if(!empty($_POST['participantFirstName'][$key]))
				{
					$person = new Person();
					$person->CustomerID = $customer->CustomerID;
					$person->PersonName = trim($_POST['participantFirstName'][$key]) . ";" . trim($_POST['participantLastName'][$key]);
					$person->PersonEmail = trim($_POST['participantEmail'][$key]);
					$person->PersonPhone = trim($_POST['participantPhone'][$key]);
					$person->PersonMobile = trim($_POST['participantMobile'][$key]);


					$ft = new XFiltering();
					$f = new XFilter('CustomerID', '=', $customer->CustomerID);
					$ft->AddItem($f);
					$f = new XFilter('PersonName', '=', trim(str_replace(';', ' ', $person->PersonName)));
					$ft->AddItem($f);
					$f = new XFilter('PersonEmail', '=', $person->PersonEmail);
					$ft->AddItem($f);
					$matchingPersons = $api->GetPerson($token, '', $ft->ToString());
					if(count($matchingPersons) > 0)
					{
						$person = $matchingPersons[0];
					}

					$person->PersonEmail = trim($_POST['participantEmail'][$key]);
					$person->PersonPhone = trim($_POST['participantPhone'][$key]);
					$person->PersonMobile = trim($_POST['participantMobile'][$key]);

					if(isset($_POST['participantCivReg'][$key]))
					{
						$person->PersonCivicRegistrationNumber = trim($_POST['participantCivReg'][$key]);
					}
					$pArr[] = $person;

					if(!empty($person->PersonEmail) && !in_array($person->PersonEmail, $personEmail))
					{
						$personEmail[] = $person->PersonEmail;
					}
				}
			}

			if(isset($_POST['contactIsAlsoParticipant']) && $contact->CustomerContactID > 0)
			{
				$person = new Person();
				$person->CustomerID = $customer->CustomerID;
				$person->CustomerContactID = $contact->CustomerContactID;
				$person->PersonName = $contact->ContactName;
				$person->PersonEmail = $contact->Email;
				$person->PersonPhone = $contact->Phone;
				$person->PersonMobile = $contact->Mobile;
				$person->PersonCivicRegistrationNumber = $contact->CivicRegistrationNumber;
				$ft = new XFiltering();
				$f = new XFilter('CustomerID', '=', $customer->CustomerID);
				$ft->AddItem($f);
				$f = new XFilter('CustomerContactID', '=', $contact->CustomerContactID);
				$ft->AddItem($f);
				$matchingPersons = $api->GetPerson($token, '', $ft->ToString());
				if(count($matchingPersons) > 0)
				{
					$person = $matchingPersons[0];
				}
				$pArr[] = $person;
			}

			if(count($pArr) == 0)
			{
				// Deltagare saknas, avbryt
			}

			$persons = $api->SetPerson($token, $pArr);

			$eventCustomerLnkID = $api->BookIncCustomerReference(
				$token,
				$eventId,
				$customer->CustomerID,
				$contact->CustomerContactID,
				(!empty($_POST['invoiceReference']) ? trim($_POST['invoiceReference']) : $contact->ContactName),
				$persons
			);

			$answers = array();
			// TODO: Add loop to push in all Answers from Questions
			foreach($_POST as $input => $value)
			{
				if(stripos($input, "question_") !== FALSE)
				{
					$question = explode('_', $input);
					$answerID = $question[1];
					$type = $question[2];

					switch($type)
					{
						case 'radio':
						case 'check':
						case 'dropdown':
							$answerID = $value;
							break;
					}
					if($type == "time")
					{
						$answers[$answerID]['AnswerTime'] = trim($value);
					}
					else
					{
						$answers[$answerID] =
						array(
							'AnswerID' => $answerID,
							'AnswerText' => trim($value),
							'EventID' => $eventId,
							'EventCustomerLnkID' => $eventCustomerLnkID
						);
					}
				}
			}

			// Spara alla frågor till eventcustomeranswerv2
			if(count($answers) > 0)
			{
				$sanswers = array();
				foreach($answers as $answer)
				{
					$sanswers[] = $answer;
				}
				$api->SetEventCustomerAnswerV2($token, $sanswers);
			}

			$ai = $api->GetAccountInfo($token);
			$senderEmail = $ai->Email;
			if(empty($senderEmail))
			{
				$senderEmail = "no-reply@legaonline.se";
			}
			if(!empty($personEmail))
			{
				$api->SendConfirmationEmail($token, $eventCustomerLnkID, $senderEmail, $personEmail);
			}

			$_SESSION['eduadmin-printJS'] = true;

			die("<script type=\"text/javascript\">location.href = '" . get_page_link(get_option('eduadmin-thankYouPage','/')) . "';</script>");
		}
	}

$contact = new CustomerContact();
$customer = new Customer();

if(isset($_SESSION['eduadmin-loginUser']))
{
	$user = $_SESSION['eduadmin-loginUser'];
	$contact = $user->Contact;
	$customer = $user->Customer;
}
?>
<div class="eduadmin">
	<a href="../" class="backLink"><?php edu_e("« Go back"); ?></a>
	<div class="title">
		<img src="<?php echo $selectedCourse->ImageUrl; ?>" style="max-width: 8em; max-height: 8em; margin-right: 2em; float: left;" />
		<h1 style="float: left; clear: right;"><?php echo $name; ?></h1>
			<?php if(count($events) > 1) { ?>
<select name="eid" class="dateInfo">
<?php
				foreach($events as $ev)
				{
					?>				<option value="<?php echo $ev->EventID; ?>"><?php
						echo wp_strip_all_tags(GetStartEndDisplayDate($ev->PeriodStart, $ev->PeriodEnd)) . ", ";
						echo date("H:i", strtotime($ev->PeriodStart)); ?> - <?php echo date("H:i", strtotime($ev->PeriodEnd));
						$addresses = get_transient('eduadmin-location-' . $ev->LocationAddressID);
						if(!$addresses)
						{
							$ft = new XFiltering();
							$f = new XFilter('LocationAddressID', '=', $ev->LocationAddressID);
							$ft->AddItem($f);
							$addresses = $api->GetLocationAddress($token, '', $ft->ToString());
							set_transient('eduadmin-location-' . $ev->LocationAddressID, $addresses, DAY_IN_SECONDS);
						}

						foreach($addresses as $address)
						{
							if($address->LocationAddressID === $ev->LocationAddressID)
							{
								echo ", " . $ev->AddressName . ", " . $address->Address . ", " . $address->City;
								break;
							}
						}
					?></option>
<?php
				}
?>
			</select>
			<?php
			} else {
				echo "<div class=\"dateInfo\">" . GetStartEndDisplayDate($event->PeriodStart, $event->PeriodEnd) . ", ";
				echo date("H:i", strtotime($event->PeriodStart)); ?> - <?php echo date("H:i", strtotime($event->PeriodEnd));
				$addresses = get_transient('eduadmin-location-' . $event->LocationAddressID);
				if(!$addresses)
				{
					$ft = new XFiltering();
					$f = new XFilter('LocationAddressID', '=', $event->LocationAddressID);
					$ft->AddItem($f);
					$addresses = $api->GetLocationAddress($token, '', $ft->ToString());
					set_transient('eduadmin-location-' . $event->LocationAddressID, $addresses, DAY_IN_SECONDS);
				}

				foreach($addresses as $address)
				{
					if($address->LocationAddressID === $event->LocationAddressID)
					{
						echo ", " . $event->AddressName . ", " . $address->Address . ", " . $address->City;
						break;
					}
				}
				echo "</div>";
			}
?>
</div>
	<hr />
	<form action="" method="post">
		<input type="hidden" name="act" value="bookCourse" />
		<div class="customerView">
			<h2><?php edu_e("Customer information"); ?></h2>
			<label>
				<div class="inputLabel">
					<?php edu_e("Customer name"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" required name="customerName" placeholder="<?php edu_e("Customer name"); ?>" value="<?php echo esc_attr($customer->CustomerName); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Org.No."); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerVatNo" placeholder="<?php edu_e("Org.No."); ?>" value="<?php echo esc_attr($customer->InvoiceOrgnr); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Address"); ?> 1
				</div>
				<div class="inputHolder">
					<input type="text" name="customerAddress1" placeholder="<?php edu_e("Address"); ?> 1" value="<?php echo esc_attr($customer->Address1); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Address"); ?> 2
				</div>
				<div class="inputHolder">
					<input type="text" name="customerAddress2" placeholder="<?php edu_e("Address"); ?> 2" value="<?php echo esc_attr($customer->Address2); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Postal code"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerPostalCode" placeholder="<?php edu_e("Postal code"); ?>" value="<?php echo esc_attr($customer->Zip); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Postal city"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerPostalCity" placeholder="<?php edu_e("Postal city"); ?>" value="<?php echo esc_attr($customer->City); ?>" />
				</div>
			</label>
			<div id="invoiceView" class="invoiceView" style="display: none;">
				<h2><?php edu_e("Invoice information"); ?></h2>
				<label>
					<div class="inputLabel">
						<?php edu_e("Customer name"); ?>
					</div>
					<div class="inputHolder">
						<input type="text" name="invoiceName" placeholder="<?php edu_e("Customer name"); ?>" value="<?php echo esc_attr($customer->InvoiceName); ?>" />
					</div>
				</label>
				<label>
					<div class="inputLabel">
						<?php edu_e("Address"); ?> 1
					</div>
					<div class="inputHolder">
						<input type="text" name="invoiceAddress1" placeholder="<?php edu_e("Address"); ?> 1" value="<?php echo esc_attr($customer->InvoiceAddress1); ?>" />
					</div>
				</label>
				<label>
					<div class="inputLabel">
						<?php edu_e("Address"); ?> 2
					</div>
					<div class="inputHolder">
						<input type="text" name="invoiceAddress2" placeholder="<?php edu_e("Address"); ?> 2" value="<?php echo esc_attr($customer->InvoiceAddress2); ?>" />
					</div>
				</label>
				<label>
					<div class="inputLabel">
						<?php edu_e("Postal code"); ?>
					</div>
					<div class="inputHolder">
						<input type="text" name="invoicePostalCode" placeholder="<?php edu_e("Postal code"); ?>" value="<?php echo esc_attr($customer->InvoiceZip); ?>" />
					</div>
				</label>
				<label>
					<div class="inputLabel">
						<?php edu_e("Postal city"); ?>
					</div>
					<div class="inputHolder">
						<input type="text" name="invoicePostalCity" placeholder="<?php edu_e("Postal city"); ?>" value="<?php echo esc_attr($customer->InvoiceCity); ?>" />
					</div>
				</label>
			</div>
			<label>
				<div class="inputLabel">
					<?php edu_e("Invoice reference"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="invoiceReference" placeholder="<?php edu_e("Invoice reference"); ?>" value="<?php echo esc_attr($customer->CustomerReference); ?>" />
				</div>
			</label>
			<label>
				<div class="inputHolder">
					<input type="checkbox" name="alsoInvoiceCustomer" value="true" onchange="eduBookingView.UpdateInvoiceCustomer();" />
					<?php edu_e("Use other information for invoicing"); ?>
				</div>
			</label>
		</div>

		<div class="contactView">
			<h2><?php edu_e("Contact information"); ?></h2>
			<label>
				<div class="inputLabel">
					<?php edu_e("Contact name"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" style="width: 49%; display: inline-block;" name="contactFirstName" placeholder="<?php edu_e("Contact first name"); ?>" value="<?php echo esc_attr(split(' ', $contact->ContactName)[0]); ?>" />
					<input type="text" style="width: 49%; display: inline-block; float: right;" name="contactLastName" placeholder="<?php edu_e("Contact surname"); ?>" value="<?php echo esc_attr(str_replace(split(' ', $contact->ContactName)[0], '', $contact->ContactName)); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("E-mail address"); ?>
				</div>
				<div class="inputHolder">
					<input type="email" name="contactEmail" placeholder="<?php edu_e("E-mail address"); ?>" value="<?php echo esc_attr($contact->Email); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Phone number"); ?>
				</div>
				<div class="inputHolder">
					<input type="tel" name="contactPhone" placeholder="<?php edu_e("Phone number"); ?>" value="<?php echo esc_attr($contact->Phone); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Mobile number"); ?>
				</div>
				<div class="inputHolder">
					<input type="tel" name="contactMobile" placeholder="<?php edu_e("Mobile number"); ?>" value="<?php echo esc_attr($contact->Mobile); ?>" />
				</div>
			</label>
			<?php if($selectedCourse->RequireCivicRegistrationNumber) { ?>
			<label>
				<div class="inputLabel">
					<?php edu_e("Civic Registration Number"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" required name="contactCivReg" placeholder="<?php edu_e("Civic Registration Number"); ?>" value="<?php echo esc_attr($contact->CivicRegistrationNumber); ?>" />
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
		<div class="participantView">
			<h2><?php edu_e("Participant information"); ?></h2>
			<div class="participantHolder" id="edu-participantHolder">
				<div class="participantItem contactPerson" style="display: none;">
					<h3>
						<?php edu_e("Participant"); ?>
					</h3>
					<label>
						<div class="inputLabel">
							<?php edu_e("Participant name"); ?>
						</div>
						<div class="inputHolder">
							<input type="text" readonly style="width: 49%; display: inline-block;" class="contactFirstName" name="participantFirstName[]" placeholder="<?php edu_e("Participant first name"); ?>" />
							<input type="text" readonly style="width: 49%; display: inline-block; float: right;" class="contactLastName" name="participantLastName[]" placeholder="<?php edu_e("Participant surname"); ?>" />
						</div>
					</label>
					<label>
						<div class="inputLabel">
							<?php edu_e("E-mail address"); ?>
						</div>
						<div class="inputHolder">
							<input type="email" readonly class="contactEmail" name="participantEmail[]" placeholder="<?php edu_e("E-mail address"); ?>" />
						</div>
					</label>
					<label>
						<div class="inputLabel">
							<?php edu_e("Phone number"); ?>
						</div>
						<div class="inputHolder">
							<input type="tel" readonly class="contactPhone" name="participantPhone[]" placeholder="<?php edu_e("Phone number"); ?>" />
						</div>
					</label>
					<label>
						<div class="inputLabel">
							<?php edu_e("Mobile number"); ?>
						</div>
						<div class="inputHolder">
							<input type="tel" readonly class="contactMobile" name="participantMobile[]" placeholder="<?php edu_e("Mobile number"); ?>" />
						</div>
					</label>
					<?php if($selectedCourse->RequireCivicRegistrationNumber) { ?>
					<label>
						<div class="inputLabel">
							<?php edu_e("Civic Registration Number"); ?>
						</div>
						<div class="inputHolder">
							<input type="text" readonly class="contactCivReg" name="participantCivReg[]" placeholder="<?php edu_e("Civic Registration Number"); ?>" />
						</div>
					</label>
					<?php } ?>
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
							<input type="text" style="width: 49%; display: inline-block;" name="participantFirstName[]" placeholder="<?php edu_e("Participant first name"); ?>" />
							<input type="text" style="width: 49%; display: inline-block; float: right;" name="participantLastName[]" placeholder="<?php edu_e("Participant surname"); ?>" />
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
							<input type="text" required name="participantCivReg[]" placeholder="<?php edu_e("Civic Registration Number"); ?>" />
						</div>
					</label>
					<?php } ?>
				</div>
			</div>
			<div>
				<a href="javascript://" onclick="eduBookingView.AddParticipant(); return false;"><?php edu_e("Add participant"); ?></a>
			</div>
			<div class="edu-modal warning" id="edu-warning-participants">
				<?php edu_e("You cannot add any more participants."); ?>
			</div>

		</div>
		<br />
		<div class="questionPanel">
		<?php
		$ft = new XFiltering();
		$f = new XFilter("ShowExternal", "=", 'true');
		$ft->AddItem($f);
		/*$f = new XFilter("CategoryID", "=", $selectedCourse->CategoryID);
		$ft->AddItem($f);*/
		$st = new XSorting();
		$s = new XSort('SortIndex', 'ASC');
		$st->AddItem($s);
		$objCatQuestion = $api->GetObjectCategoryQuestion($token, $st->ToString(), $ft->ToString());

		$groupedQuestions = array();

		foreach($objCatQuestion as $q)
		{
			$groupedQuestions[$q->QuestionID][] = $q;
		}

		if(count($groupedQuestions) > 0)
		{
			$lastQuestionId = -1;
			foreach($groupedQuestions as $question)
			{
				if($lastQuestionId != $question[0]->QuestionID)
				{
					renderQuestion($question);
				}

				$lastQuestionId = $question[0]->QuestionID;
			}
		}


		?>
		</div>
		<div class="sumTotal"><?php edu_e('Total sum:'); ?> <span id="sumValue" class="sumValue"></span></div>

		<?php
		if(get_option('eduadmin-useBookingTermsCheckbox', false) && !empty(get_option('eduadmin-bookingTermsLink')))
		{
			?>
			<div>
			<label>
				<input type="checkbox" id="confirmTerms" name="confirmTerms" value="agree" />
				<?php echo sprintf(edu__('I agree to the %sTerms and Conditions%s'), '<a href="' . get_option('eduadmin-bookingTermsLink') . '" target="_blank">', '</a>'); ?>
			</label>
			</div>
			<?php
		}
		?>
		<div class="edu-modal warning" id="edu-warning-terms">
			<?php edu_e("You must accept Terms and Conditions to continue."); ?>
		</div>
		<div class="edu-modal warning" id="edu-warning-no-participants">
				<?php edu_e("You must add some participants."); ?>
			</div>
		<div>
		<div class="edu-modal warning" id="edu-warning-missing-participants">
				<?php edu_e("One or more participants is missing a name."); ?>
			</div>
		<div>
		<input type="submit" class="bookButton" onclick="var validated = eduBookingView.CheckValidation(); return validated;" value="<?php edu_e("Book now"); ?>" />

		</div>
	</form>
</div>
<?php
$originalTitle = get_the_title();
$newTitle = $name . " | " . $originalTitle;

$occIds = Array();
$occIds[] = -1;
foreach($events as $ev)
{
	$occIds[] = $ev->OccationID;
}

$ft = new XFiltering();
$f = new XFilter('PublicPriceName', '=', 'true');
$ft->AddItem($f);
$f = new XFilter('OccationID', 'IN', join(',', $occIds));
$ft->AddItem($f);

$prices = $api->GetPriceName($token, '', $ft->ToString());
$uniquePrices = Array();
foreach($prices as $price)
{
	$uniquePrices[$price->Description] = $price;
}
$firstPrice = current($uniquePrices);
?>
<script type="text/javascript">
var pricePerParticipant = <?php echo $firstPrice->Price; ?>;
var currency = '<?php echo get_option('eduadmin-currency', 'SEK'); ?>';
(function() {
	var title = document.title;
	title = title.replace('<?php echo $originalTitle; ?>', '<?php echo $newTitle; ?>');
	document.title = title;
	eduBookingView.MaxParticipants = <?php echo ($event->MaxParticipantNr == 0 ? -1 : ($event->MaxParticipantNr - $event->TotalParticipantNr)); ?>;

	eduBookingView.AddParticipant();
	eduBookingView.UpdatePrice();
})();
</script>
<?php
}
?>