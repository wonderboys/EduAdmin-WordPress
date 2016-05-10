<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );

function DecryptApiKey($key) {
	$decrypted = explode('|', base64_decode($key));
	if(count($decrypted) == 2)
	{
		$apiKey = new stdClass();
		$apiKey->UserId = $decrypted[0];
		$apiKey->Hash = $decrypted[1];
		return $apiKey;
	}
	return false;
}
?>