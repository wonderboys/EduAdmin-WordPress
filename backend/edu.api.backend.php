<?php
$modules = scandir(__DIR__ . '/modules');
foreach($modules as $module)
{
	if(strpos($module, '.php') !== FALSE)
	{
		include_once(__DIR__ . '/modules/' . $module);
	}
}
?>