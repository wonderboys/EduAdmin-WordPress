<?php
if(!function_exists('edu_api_listview_courselist'))
{
	function edu_api_listview_courselist($request)
	{
		global $eduapi;

		$edutoken = edu_decrypt("edu_js_token_crypto", getallheaders()["Edu-AuthToken"]);
		$_SESSION['eduadmin-phrases'] = $request['phrases'];
	}
}
?>