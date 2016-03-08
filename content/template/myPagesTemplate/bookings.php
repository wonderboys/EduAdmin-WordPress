<?php
$user = $_SESSION['eduadmin-loginUser'];
$contact = $user->Contact;
$customer = $user->Customer;

global $api;
$token = get_transient('eduadmin-token');
if(!$token)
{
	$token = $api->GetAuthToken($apiUserId, $apiHash);
	set_transient('eduadmin-token', $token, HOUR_IN_SECONDS);
}
else
{
	$valid = $api->ValidateAuthToken($token);
	if(!$valid)
	{
		$token = $api->GetAuthToken($apiUserId, $apiHash);
		set_transient('eduadmin-token', $token, HOUR_IN_SECONDS);
	}
}
?>
<div class="eduadmin">
	<h2><?php edu_e("Reservations"); ?></h2>
	<?php
	$filtering = new XFiltering();
	$f = new XFilter('CustomerID', '=', $customer->CustomerID);
	$filtering->AddItem($f);

	$sorting = new XSorting();
	$s = new XSort('Created', 'DESC');
	$sorting->AddItem($s);
	$bookings = $api->GetEventBooking($token, $sorting->ToString(), $filtering->ToString());
	echo "<xmp>" . print_r($bookings, true) . "</xmp>";
	?>
</div>