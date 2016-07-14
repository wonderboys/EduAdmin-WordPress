<?php
$user = $_SESSION['eduadmin-loginUser'];
$contact = $user->Contact;
$customer = $user->Customer;

global $eduapi;
global $edutoken;
?>
<div class="eduadmin">
<?php
$tab = "bookings";
include_once("login_tab_header.php");
?>
	<h2><?php edu_e("Reservations"); ?></h2>
	<?php
	$filtering = new XFiltering();
	$f = new XFilter('CustomerID', '=', $customer->CustomerID);
	$filtering->AddItem($f);

	$sorting = new XSorting();
	$s = new XSort('Created', 'DESC');
	$sorting->AddItem($s);
	$bookings = $eduapi->GetEventBooking($edutoken, $sorting->ToString(), $filtering->ToString());
	$currency = get_option('eduadmin-currency', 'SEK');
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
		if(empty($bookings)) {
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
			<td align="right"><?php echo convertToMoney($book->TotalPrice, $currency); ?></td>
		</tr>
		<?php }
		} ?>
	</table>
<?php include_once("login_tab_footer.php"); ?>
</div>