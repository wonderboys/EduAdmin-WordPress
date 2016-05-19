<?php
function edu_getQueryString($prepend = "?", $removeParameters = array())
{
	foreach($removeParameters as $par)
	{
		unset($_GET[$par]);
	}
	if(!empty($_GET)) { return $prepend . http_build_query($_GET); }
	return "";
}


$modules = scandir(__DIR__ . '/modules');
foreach($modules as $module)
{
	if(strpos($module, '.php') !== FALSE)
	{
		if(function_exists('opcache_compile_file'))
		{
			if(!opcache_is_script_cached(__DIR__ . '/modules/' . $module))
			{
				opcache_compile_file(__DIR__ . '/modules/' . $module);
			}
		}
		include_once(__DIR__ . '/modules/' . $module);

	}
}
?>