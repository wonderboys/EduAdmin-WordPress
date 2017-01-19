<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );
if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
           $headers = array();
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               @$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
}
include_once("_apiFunctions.php");
include_once("_translationFunctions.php");
include_once("_questionFunctions.php");
include_once("_attributeFunctions.php");
include_once("_textFunctions.php");
include_once("_loginFunctions.php");
?>