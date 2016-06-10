<?php
if(!function_exists('edu_api_listview_eventlist'))
{
	function edu_api_listview_eventlist($request)
	{
		header("Content-type: application/json; charset=UTF-8");
		global $eduapi;

		$edutoken = edu_decrypt("edu_js_token_crypto", getallheaders()["Edu-Auth-Token"]);
		$_SESSION['eduadmin-phrases'] = $request['phrases'];
	}
}
?>