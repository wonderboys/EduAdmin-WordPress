<?php
include_once(__DIR__ . "/../../includes/loApiClient.php");

if(isset($_GET['key']))
{
	$eduapi = new EduAdminClient();
	$info = edu_DecryptApiKey($_GET['key']);
	print_r($info);
}
?>