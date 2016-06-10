<?php
@session_start();
include_once("edu.api.functions.php");

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

if(isset($_REQUEST['localDecrypt']))
{
	echo edu_decrypt("edu_js_token_crypto", "1pa76lkmo9D8C0wp1nXejdwwjFpXFtmoKqs9oag1c6K4eUK43NEZjbQt2gMEmPnPMZwEIVsDD/++FSS1IycxSg==");
}
?>