<?php
function edu_api_loginwidget(WP_REST_Request $request)
{
	$surl = get_site_url();
	$cat = get_option('eduadmin-rewriteBaseUrl');

	$baseUrl = $surl . '/' . $cat;
	if(isset($_SESSION['eduadmin-loginUser']))
		$user = $_SESSION['eduadmin-loginUser'];

	if(isset($_SESSION['eduadmin-loginUser']) && !empty($_SESSION['eduadmin-loginUser']) && $_SESSION['eduadmin-loginUser']->Contact->CustomerContactID != 0)
	{
		return
		"<div class=\"eduadminLogin\"><a href=\"" . $baseUrl . "/profile/myprofile" . edu_getQueryString("?", array('eid')) . "\" class=\"eduadminMyProfileLink\">" .
		$_SESSION['eduadmin-loginUser']->Contact->ContactName .
		"</a> - <a href=\"" . $baseUrl . "/profile/logout" . edu_getQueryString("?", array('eid')) . "\" class=\"eduadminLogoutButton\">" .
		$request['logouttext'] .
		"</a>" .
		"</div>";
	}
	else
	{
		return
		"<div class=\"eduadminLogin\"><i>" .
		$request['guesttext'] .
		"</i> - " .
		"<a href=\"" . $baseUrl . "/profile/login" . edu_getQueryString("?", array('eid')) . "\" class=\"eduadminLoginButton\">" .
		$request['logintext'] .
		"</a>" .
		"</div>";
	}
}

add_action('rest_api_init', function() {
	register_rest_route(
		'eduadmin/v1',
		'/login/widget',
		array(
			'methods' => 'GET',
			'callback' => 'edu_api_loginwidget'
		)
	);
});
?>