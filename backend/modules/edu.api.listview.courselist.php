<?php
if(!function_exists('edu_api_listview_courselist'))
{
	function edu_api_listview_courselist($request)
	{
		global $eduapi;

		$edutoken = edu_decrypt("edu_js_token_crypto", getallheaders()["Edu-AuthToken"]);
		$_SESSION['eduadmin-phrases'] = $request['phrases'];

		$objectIds = $request['objectIds'];

		$filtering = new XFiltering();
		$f = new XFilter('ShowOnWeb','=','true');
		$filtering->AddItem($f);

		$f = new XFilter('PeriodStart','>',date("Y-m-d 00:00:00", strtotime("now +1 day")));
		$filtering->AddItem($f);
		$f = new XFilter('PeriodEnd', '<', date("Y-m-d 23:59:59", strtotime("now +1 year")));
		$filtering->AddItem($f);
		$f = new XFilter('StatusID','=','1');
		$filtering->AddItem($f);

		$f = new XFilter('LastApplicationDate','>',date("Y-m-d H:i:s"));
		$filtering->AddItem($f);

		if(!empty($objectIds))
		{
			$f = new XFilter('ObjectID', 'IN', join(',', $objectIds));
			$filtering->AddItem($f);
		}

		$sorting = new XSorting();
		$s = new XSort('PeriodStart', 'ASC');
		$sorting->AddItem($s);

		$ede = $eduapi->GetEvent($edutoken, $sorting->ToString(), $filtering->ToString());

		print_r($ede);
	}
}

if(isset($_REQUEST['module']) && $_REQUEST['module'] == "listview_courselist")
{
	echo edu_api_listview_courselist($_REQUEST);
}
?>