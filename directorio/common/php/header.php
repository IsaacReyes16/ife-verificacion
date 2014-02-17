<?php
date_default_timezone_set("America/Mexico_City");
require_once('path.php');
$Raiz['local'] = $_SESSION['RaizLoc'];
$Raiz['url'] = $_SESSION['RaizUrl'];
$Raiz['sitefolder'] = $_SESSION['SiteFolder'];
require_once($Raiz['local'].'common/php/o3m_functions.php');
require_once($Raiz['local'].'common/php/class.template.php');
require_once($Raiz['local'].'common/php/conex.php');
require_once($Raiz['local'].'common/php/build_select.php');
$db_catalogos="ife_ddvc_catalogos";
$db_domirreg="ife_dom_irre";
$Path['js']=$Raiz['url'].'common/js/';
$Path['css']=$Raiz['url'].'common/css/';
$Path['tpl']='common/tpl/';
$Path['img']='common/img/';
parse_form_sanitizer($_GET, $_POST);
parse_form($_GET, $_POST);
##Variables de usuario
$Usuario['id'] = $_SESSION['id_usu'];
$Usuario['user'] = $_SESSION['usuario'];
$Usuario['name'] = $_SESSION['usuario'];
$Usuario['ent'] = $_SESSION['id_vlc'];
$Usuario['dto'] = $_SESSION['id_vlc'];
$Usuario['nivel'] = $_SESSION['nivel'];
if(empty($Usuario['user']) && empty($Usuario['name'])){$Usuario['user']="Usuario"; $Usuario['name']="Nombre de Usuario";}
$Css='<!--CSS Styles-->
	<link href="'.$Path['css'].'estilo.css'.'" rel="stylesheet" type="text/css" />';
$jQueryPlugins = '
	<!--jQuery UI-->
	<link href="'.$Path['js'].'jquery/jquery-ui-1.10.3.custom/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="'.$Path['js'].'jquery/jquery-ui-1.10.3.custom/jquery-ui-1.10.3.custom.min.js"></script>
	<!--jQuery Datepicker ES-->
	<script src="'.$Path['js'].'jquery/jquery-ui-1.10.3.custom/jquery.ui.datepicker-es.js"></script>
	<!--jQuery Confirm Popups-->
	<link rel="stylesheet" type="text/css" href="'.$Path['js'].'jquery/msgBox/Styles/msgBoxLight.css" />
	<script src="'.$Path['js'].'jquery/msgBox/Scripts/jquery-1.8.0.min.js"></script>
	<script src="'.$Path['js'].'jquery/msgBox/Scripts/jquery.msgBox.js"></script>
	<!--jQuery-->
	<script type="text/javascript" src="'.$Path['js'].'jquery/jquery-1.9.1.min.js"></script>';
$Javascript='<!--jQuery-->
	<script type="text/javascript" src="'.$Path['js'].'o3m_functions.js"></script>';
?>