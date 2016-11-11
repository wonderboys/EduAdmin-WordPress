<?php
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
	// And then we'd have to go through the trouble to recreate all participants.
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
	$customer->Email = trim($_POST['customerEmail']);
	$customer->CustomerReference = trim($_POST['invoiceReference']);

	$purchaseOrderNumber = trim($_POST['purchaseOrderNumber']);

	if(!isset($_POST['alsoInvoiceCustomer']))
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

	$selectedMatch = get_option('eduadmin-customerMatching', 'name-zip-match');
	if($selectedMatch === "name-zip-match")
	{
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
		$matchingCustomer = $eduapi->GetCustomer($edutoken, '', $ft->ToString(), false);
		if(empty($matchingCustomer))
		{
			$customer->CustomerID = 0;
			$cres = $eduapi->SetCustomer($edutoken, array($customer));
			$customer->CustomerID = $cres[0];
		}
		else
		{
			$customer = $matchingCustomer[0];
		}
	}
	else if($selectedMatch === "name-zip-match-overwrite")
	{
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
		$matchingCustomer = $eduapi->GetCustomer($edutoken, '', $ft->ToString(), false);
		if(empty($matchingCustomer))
		{
			$customer->CustomerID = 0;
			$cres = $eduapi->SetCustomer($edutoken, array($customer));
			$customer->CustomerID = $cres[0];
		}
		else
		{
			$customer->CustomerID = $matchingCustomer[0]->CustomerID;
			$eduapi->SetCustomer($edutoken, array($customer));
		}
	}
	else if($selectedMatch === "no-match")
	{
		$customer->CustomerID = 0;
		$cres = $eduapi->SetCustomer($edutoken, array($customer));
		$customer->CustomerID = $cres[0];
	}
	else if($selectedMatch === "no-match-new-overwrite")
	{
		if(isset($_SESSION['eduadmin-loginUser']))
		{
			$user = $_SESSION['eduadmin-loginUser'];
			$contact->CustomerContactID = $user->Contact->CustomerContactID;
			$customer->CustomerID = $user->Customer->CustomerID;

			if($contact->CustomerContactID == 0)
			{
				$customer->CustomerID = 0;
				$cres = $eduapi->SetCustomer($edutoken, array($customer));
				$customer->CustomerID = $cres[0];
			}
			else
			{
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
				$matchingCustomer = $eduapi->GetCustomer($edutoken, '', $ft->ToString(), false);
				if(empty($matchingCustomer))
				{
					$customer->CustomerID = 0;
					$cres = $eduapi->SetCustomer($edutoken, array($customer));
					$customer->CustomerID = $cres[0];
				}
				else
				{
					$customer->CustomerID = $matchingCustomer[0]->CustomerID;
					$eduapi->SetCustomer($edutoken, array($customer));
				}
			}
		}
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
		if(isset($_POST['contactPass']))
		{
			$contact->Loginpass = $_POST['contactPass'];
		}
		$contact->CanLogin = 'true';
		$contact->PublicGroup = 'true';

		$ft = new XFiltering();
		$f = new XFilter('CustomerID', '=', $customer->CustomerID);
		$ft->AddItem($f);
		if($contact->CustomerContactID == 0)
		{
			$f = new XFilter('ContactName', '=', trim(str_replace(';', ' ', $contact->ContactName)));
			$ft->AddItem($f);

			$f = new XFilter('Email', '=', $contact->Email);
			$ft->AddItem($f);
		}
		else
		{
			$f = new XFilter('CustomerContactID', '=', $contact->CustomerContactID);
			$ft->AddItem($f);
		}
		$matchingContacts = $eduapi->GetCustomerContact($edutoken, '', $ft->ToString(), false);
		if(empty($matchingContacts))
		{
			$contact->CustomerContactID = 0;
			$contact->CustomerContactID = $eduapi->SetCustomerContact($edutoken, array($contact))[0];
		}
		else
		{
			if($selectedMatch === "name-zip-match-overwrite")
			{
				$contact->CustomerContactID = $matchingContacts[0]->CustomerContactID;
				$eduapi->SetCustomerContact($edutoken, array($contact));
			}
			else
			{
				$contact = $matchingContacts[0];
				if(isset($_POST['contactPass']) && empty($contact->Loginpass))
				{
					$contact->Loginpass = $_POST['contactPass'];
					$eduapi->SetCustomerContact($edutoken, array($contact));
				}
			}
		}

		$contact->ContactName = str_replace(";", " ", $contact->ContactName);
	}

	$persons = array();
	$personEmail = array();
	if(!empty($contact->Email) && !in_array($contact->Email, $personEmail))
	{
		$personEmail[] = $contact->Email;
	}

	$st = new XSorting();
	$s = new XSort('StartDate', 'ASC');
	$st->AddItem($s);
	$s = new XSort('EndDate', 'ASC');
	$st->AddItem($s);

	$ft = new XFiltering();
	$f = new XFilter('ParentEventID', '=', $eventId);
	$ft->AddItem($f);
	$subEvents = $eduapi->GetSubEvent($edutoken, $st->ToString(), $ft->ToString());

	$pArr = array();
	foreach($_POST['participantFirstName'] as $key => $value)
	{
		if($key == "0")
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
			$matchingPersons = $eduapi->GetPerson($edutoken, '', $ft->ToString(), false);
			if(!empty($matchingPersons))
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

			if(isset($_POST['participantPriceName'][$key]))
			{
				$person->OccasionPriceNameLnkID = trim($_POST['participantPriceName'][$key]);
			}

			foreach($subEvents as $subEvent)
			{
				$fieldName = "participantSubEvent_" . $subEvent->EventID;
				if(isset($_POST[$fieldName][$key]))
				{
					$fieldValue = $_POST[$fieldName][$key];
					$subEventInfo = new SubEventInfo();
					$subEventInfo->EventID = $fieldValue;
					$person->SubEvents[] = $subEventInfo;
				}
				else if($subEvent->MandatoryParticipation) {
					$subEventInfo = new SubEventInfo();
					$subEventInfo->EventID = $subEvent->EventID;
					$person->SubEvents[] = $subEventInfo;
				}
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
		$matchingPersons = $eduapi->GetPerson($edutoken, '', $ft->ToString(), false);
		if(!empty($matchingPersons))
		{
			$person = $matchingPersons[0];
		}

		if(isset($_POST['contactCivReg']))
		{
			$person->PersonCivicRegistrationNumber = trim($_POST['contactCivReg']);
		}

		if(isset($_POST['contactPriceName']))
		{
			$person->OccasionPriceNameLnkID = trim($_POST['contactPriceName']);
		}
		$person->SubEvents = array();
		foreach($subEvents as $subEvent)
		{
			$fieldName = "contactSubEvent_" . $subEvent->EventID;
			if(isset($_POST[$fieldName]))
			{
				$fieldValue = $_POST[$fieldName];
				$subEventInfo = new SubEventInfo();
				$subEventInfo->EventID = $fieldValue;
				$person->SubEvents[] = $subEventInfo;
			}
			else if($subEvent->MandatoryParticipation) {
				$subEventInfo = new SubEventInfo();
				$subEventInfo->EventID = $subEvent->EventID;
				$person->SubEvents[] = $subEventInfo;
			}
		}

		$pArr[] = $person;
	}

	if(!empty($pArr))
	{
		$bi = new BookingInfoSubEvent();
		$bi->EventID = $eventId;
		$bi->CustomerID = $customer->CustomerID;
		$bi->CustomerContactID = $contact->CustomerContactID;
		$bi->SubEventPersons = $pArr;
		$bi->PurchaseOrderNumber = $purchaseOrderNumber;
		if(isset($_POST['edu-pricename']))
		{
			$bi->OccasionPriceNameLnkID = $_POST['edu-pricename'];
		}

		$bi->CustomerReference = (!empty($_POST['invoiceReference']) ? trim($_POST['invoiceReference']) : trim(str_replace(';', ' ', $contact->ContactName)));
		$eventCustomerLnkID = $eduapi->CreateSubEventBooking(
			$edutoken,
			$bi
		);

		$answers = array();
		foreach($_POST as $input => $value)
		{
			if(strpos($input, "question_") !== FALSE)
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
				if($type === "time")
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
		if(!empty($answers))
		{
			$sanswers = array();
			foreach($answers as $answer)
			{
				$sanswers[] = $answer;
			}
			$eduapi->SetEventCustomerAnswerV2($edutoken, $sanswers);
		}

		$ai = $eduapi->GetAccountInfo($edutoken);
		$senderEmail = $ai->Email;
		if(empty($senderEmail))
		{
			$senderEmail = "no-reply@legaonline.se";
		}
		if(!empty($personEmail))
		{
			$eduapi->SendConfirmationEmail($edutoken, $eventCustomerLnkID, $senderEmail, $personEmail);
		}

		$_SESSION['eduadmin-printJS'] = true;

		$user = $_SESSION['eduadmin-loginUser'];
		$jsEncContact = json_encode($contact);
		@$user->Contact = json_decode($jsEncContact);

		$jsEncCustomer = json_encode($customer);
		@$user->Customer = json_decode($jsEncCustomer);
		$_SESSION['eduadmin-loginUser'] = $user;

		die("<script type=\"text/javascript\">location.href = '" . get_page_link(get_option('eduadmin-thankYouPage','/')) . "?edu-thankyou=" . $eventCustomerLnkID . "';</script>");
	}
}
?>