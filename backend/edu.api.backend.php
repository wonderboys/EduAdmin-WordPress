<?php
include_once("edu.api.functions.php");
@session_start();
date_default_timezone_set('UTC');

$modules = scandir(__DIR__ . '/modules');
foreach($modules as $module)
{
	if(strpos($module, '.php') !== FALSE)
	{
		if(function_exists('opcache_compile_file') && function_exists('opcache_is_script_cached'))
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