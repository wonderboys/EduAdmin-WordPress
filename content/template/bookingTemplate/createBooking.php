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
	$singlePersonBooking = get_option('eduadmin-singlePersonBooking', false);
	if($singlePersonBooking) {
		include_once("__bookSingleParticipant.php");
	} else {
		include_once("__bookMultipleParticipants.php");
	}
}
?>