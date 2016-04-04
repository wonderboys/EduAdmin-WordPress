<?php
$user = $_SESSION['eduadmin-loginUser'];
$contact = $user->Contact;
$customer = $user->Customer;

global $api;
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
	#echo "<xmp>" . print_r($bookings, true) . "</xmp>";
	?>
	<table class="myReservationsTable">
		<tr>
			<th align="left"><?php edu_e("Booked"); ?></th>
			<th align="left"><?php edu_e("Course"); ?></th>
			<th align="left"><?php edu_e("Dates"); ?></th>
			<th align="right"><?php edu_e("Participants"); ?></th>
			<th align="right"><?php edu_e("Price"); ?></th>
		</tr>
		<?php
		if(count($bookings) == 0) {
		?>
		<tr><td colspan="5" align="center"><i><?php edu_e("No courses booked"); ?></i></td></tr>
		<?php
		} else {
			foreach($bookings as $book) {
		?>
		<tr>
			<td><?php echo getDisplayDate($book->Created, true); ?></td>
			<td><?php echo $book->EventDescription; ?></td>
			<td><?php echo GetStartEndDisplayDate($book->PeriodStart, $book->PeriodEnd, true); ?></td>
			<td align="right"><?php echo $book->ParticipantNr; ?></td>
			<td align="right"><?php echo convertToMoney($book->TotalPrice); ?></td>
		</tr>
		<?php }
		} ?>
	</table>
</div>