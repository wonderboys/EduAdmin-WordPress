<?php
if(isset($_REQUEST['bookingLoginAction']) && !empty($_REQUEST['bookingLoginAction']))
{
	if($_REQUEST['bookingLoginAction'] === "checkEmail" && !empty($_REQUEST['eduadminloginEmail']))
	{
		$ft = new XFiltering();
		$f = new XFilter('Email', '=', trim($_REQUEST['eduadminloginEmail']));
		$ft->AddItem($f);

		$matchingContacts = $api->GetCustomerContact($token, '', $ft->ToString(), true);
		$_SESSION['checkEmail'] = true;
		if(!empty($matchingContacts))
		{
			foreach($matchingContacts as $con)
			{
				if(!empty($con->Loginpass) && $con->CanLogin == 1)
				{
					$_SESSION['needsLogin'] = true;
					break;
				}
			}
		}

		if(count($matchingContacts) == 1)
		{
			$con = $matchingContacts[0];
			if(!empty($con->Loginpass) && $con->CanLogin == 1)
			{
				$_SESSION['needsLogin'] = true;
				return;
			}
			$_SESSION['needsLogin'] = false;
			$filter = new XFiltering();
			$f = new XFilter('CustomerID', '=', $con->CustomerID);
			$filter->AddItem($f);
			$customers = $api->GetCustomer($token, '', $filter->ToString(), true);
			if(count($customers) == 1)
			{
				$customer = $customers[0];
				$user = new stdClass;
				$user->Contact = $con;
				$user->Customer = $customer;
				$_SESSION['eduadmin-loginUser'] = $user;
			}
			else
			{
				return;
			}
		}

		if(empty($matchingContacts))
		{
			$contact = new stdClass;
			$contact->Email = $_REQUEST['eduadminloginEmail'];
			$customer = new stdClass;

			$user = new stdClass;
			$user->Contact = $contact;
			$user->Customer = $customer;
			$_SESSION['eduadmin-loginUser'] = $user;
		}
	}
	else if($_REQUEST['bookingLoginAction'] == "loginEmail" && !empty($_REQUEST['eduadminloginEmail']) && !empty($_REQUEST['eduadminpassword']))
	{
		$user = loginContactPerson($_REQUEST['eduadminloginEmail'], $_REQUEST['eduadminpassword']);
		if($user != null)
		{
		}
		else
		{
			$_SESSION['eduadminLoginError'] = edu__("Wrong email or password.");
		}
	}
}
?>