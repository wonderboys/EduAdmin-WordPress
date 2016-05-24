<?php
include_once(__DIR__ . "/../../includes/loApiClient.php");

if(isset($_GET['authenticate']) && isset($_GET['key']))
{
	$eduapi = new EduAdminClient();

	$info = edu_DecryptApiKey($_GET['key']);

	if(!isset($_SESSION['edu-usertoken']))
	{
		$_SESSION['edu-usertoken'] = $eduapi->GetAuthToken($info->UserId, $info->Hash);
	}
	else
	{
		$valid = $eduapi->ValidateAuthToken($edutoken);
		if(!$valid)
		{
			$_SESSION['edu-usertoken'] = $eduapi->GetAuthToken($info->UserId, $info->Hash);
		}
	}

	echo edu_encrypt("edu_js_token_crypto", $_SESSION['edu-usertoken']);
}
?>