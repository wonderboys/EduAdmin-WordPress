<?php
$user = $_SESSION['eduadmin-loginUser'];
$contact = $user->Contact;
$customer = $user->Customer;

global $eduapi;
global $edutoken;
?>
<div class="eduadmin">
<?php
$tab = "limitedDiscount";
include_once("login_tab_header.php");
?>
<h2><?php edu_e("Discount Cards"); ?></h2>
<?php include_once("login_tab_footer.php"); ?>
</div>