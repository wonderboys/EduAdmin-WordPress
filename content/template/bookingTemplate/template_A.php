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

			if(empty($customer->InvoiceOrgnr))
			{
				$ft = new XFiltering();
				$f = new XFilter('CustomerName', '=', $customer->CustomerName);
				$ft->AddItem($f);
			}
			else
			{
				$ft = new XFiltering();
				$f = new XFilter('InvoiceOrgnr', '=', $customer->InvoiceOrgnr);
				$ft->AddItem($f);
			}
			$f = new XFilter('Zip', '=', $customer->Zip);
			$ft->AddItem($f);
			$matchingCustomer = $api->GetCustomer($token, '', $ft->ToString(), false);
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
				$matchingContacts = $api->GetCustomerContact($token, '', $ft->ToString(), false);
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
					$person = new SubEventPerson();
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
					$matchingPersons = $api->GetPerson($token, '', $ft->ToString(), false);
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
				$person = new SubEventPerson();
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
				$matchingPersons = $api->GetPerson($token, '', $ft->ToString(), false);
				if(count($matchingPersons) > 0)
				{
					$person = $matchingPersons[0];
				}
				$pArr[] = $person;
			}

			if(count($pArr) > 0)
			{
				// Deltagare saknas, avbryt
				//$persons = $api->SetPerson($token, $pArr);

				$bi = new BookingInfoSubEvent();
				$bi->EventID = $eventId;
				$bi->CustomerID = $customer->CustomerID;
				$bi->CustomerContactID = $contact->CustomerContactID;
				$bi->SubEventPersons = $pArr;
				$bi->CustomerReference = (!empty($_POST['invoiceReference']) ? trim($_POST['invoiceReference']) : $contact->ContactName);
				//$api->debug = true;
				$eventCustomerLnkID = $api->CreateSubEventBooking(
					$token,
					$bi
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
				//$api->debug = false;
				$_SESSION['eduadmin-printJS'] = true;
				die("<script type=\"text/javascript\">location.href = '" . get_page_link(get_option('eduadmin-thankYouPage','/')) . "';</script>");
			}
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
					set_transient('eduadmin-location-' . $event->LocationAddressID, $addresses, HOUR_IN_SECONDS);
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
	<form action="" method="post">
		<input type="hidden" name="act" value="bookCourse" />
		<?php include_once("contactView.php"); ?>
		<?php include_once("customerView.php"); ?>
		<?php include_once("participantView.php"); ?>
		<br />
		<?php include_once("questionView.php"); ?>
		<div class="sumTotal"><?php edu_e('Total sum:'); ?> <span id="sumValue" class="sumValue"></span></div>

		<?php
		if(get_option('eduadmin-useBookingTermsCheckbox', false) && !empty(get_option('eduadmin-bookingTermsLink')))
		{
			?>
			<div class="confirmTermsHolder">
			<label>
				<input type="checkbox" id="confirmTerms" name="confirmTerms" value="agree" />
				<?php echo sprintf(edu__('I agree to the %sTerms and Conditions%s'), '<a href="' . get_option('eduadmin-bookingTermsLink') . '" target="_blank">', '</a>'); ?>
			</label>
			</div>
			<?php
		}
		?>

		<input type="submit" class="bookButton" onclick="var validated = eduBookingView.CheckValidation(); return validated;" value="<?php edu_e("Book now"); ?>" />
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