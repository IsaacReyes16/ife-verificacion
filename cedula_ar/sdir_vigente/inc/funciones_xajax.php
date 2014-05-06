<?php
//funciones xajax
// set the defalut char encoding here if you don't not want to use UTF-8
define("XAJAX_DEFAULT_CHAR_ENCODING" , 'iso-8859-1'); // set before the require file
//error_reporting(0);

require_once("class/xajax/xajaxExtend.php");
require_once("inc/conexion.php");
require_once("car_manual_dup.php");

header("Content-Type: text/html; charset=".$xajax->sEncoding); 

$tpl->setVariable('encoding', $xajax->sEncoding);

$tpl->setVariable('xajax_javascript',$xajax->printJavascript("class/xajax/"));

$tpl->show();
?>
