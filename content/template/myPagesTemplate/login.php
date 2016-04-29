<?php
ob_start();
global $wp_query;
$q = $wp_query->query;
$apiKey = get_option('eduadmin-api-key');

if(isset($_SESSION['eduadmin-loginUser']))
{
	if(isset($q['edu-login']) || isset($q['edu-profile']))
	{
		include_once("profile.php");
	}
	else if(isset($q['edu-bookings']))
	{
		include_once("bookings.php");
	}
	else if(isset($q['edu-limiteddiscount']))
	{
		include_once("limitedDiscount.php");
	}
	else if(isset($q['edu-certificates']))
	{
		include_once("certificates.php");
	}
	else if(isset($q['edu-logout']))
	{
		logoutUser();
	}
}
else
{
	if(isset($q['edu-login']))
	{
		include_once("loginPage.php");
	}
	else if(isset($q['edu-logout']))
	{
		logoutUser();
	}
	else
	{
		include_once("loginPage.php");
	}
}

$out = ob_get_clean();
return $out;
?>