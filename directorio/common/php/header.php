<?php
date_default_timezone_set("America/Mexico_City");
require_once('path.php');
session_start();
$Raiz['local'] = $_SESSION['RaizLoc'];
$Raiz['url'] = $_SESSION['RaizUrl'];
$Raiz['sitefolder'] = $_SESSION['SiteFolder'];
require_once($Raiz['local'].'common/php/o3m_functions.php');
require_once($Raiz['local'].'common/php/class.template.php');
require_once($Raiz['local'].'common/php/conex.php');
require_once($Raiz['local'].'common/php/build_select.php');
$db_catalogos="ife_ddvc_catalogos";
$db_domirreg="ife_dom_irre";
$Ruta['js']=$Raiz['url'].'common/js/';
$Ruta['css']=$Raiz['url'].'common/css/';
$Ruta['tpl']='common/tpl/';
$Ruta['img']='common/img/';
parse_form_sanitizer($_GET, $_POST);
parse_form($_GET, $_POST);
##Variables de usuario
session_start();
// if (!isset($_SESSION['id_usu'])) { header("Location: ../sdir/index.php"); exit; }
$Usuario['id'] = $_SESSION['id_usu'];
$Usuario['user'] = $_SESSION['usuario'];
$Usuario['name'] = $_SESSION['nombre_completo'];
$Usuario['ent'] = $_SESSION['id_vlc'];
$Usuario['dto'] = $_SESSION['id_vdi'];
//Definicion de Nivel
if(empty($_SESSION['nivel'])){
	//Central
	if($Usuario['ent']==0 && $Usuario['dto']==0){$Usuario['nivel'] = 1;}
	//Local
	elseif($Usuario['ent']>0 && $Usuario['dto']==0){$Usuario['nivel'] = 2;}
	//Distrito
	else{$Usuario['nivel'] = 3;}	
}else{
	$Usuario['nivel'] = $_SESSION['nivel'];
}
//--Fin de nivel
if(empty($Usuario['user']) && empty($Usuario['name'])){$Usuario['user']="Usuario"; $Usuario['name']="Nombre de Usuario";}
$Css='<!--CSS Styles-->
	<link href="'.$Ruta['css'].'estilo.css'.'" rel="stylesheet" type="text/css" />';
$jQueryPlugins = '
	<!--jQuery UI-->
	<link href="'.$Ruta['js'].'jquery/jquery-ui-1.10.3.custom/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="'.$Ruta['js'].'jquery/jquery-ui-1.10.3.custom/jquery-ui-1.10.3.custom.min.js"></script>
	<!--jQuery Datepicker ES-->
	<script src="'.$Ruta['js'].'jquery/jquery-ui-1.10.3.custom/jquery.ui.datepicker-es.js"></script>
	<!--jQuery Confirm Popups-->
	<link rel="stylesheet" type="text/css" href="'.$Ruta['js'].'jquery/msgBox/Styles/msgBoxLight.css" />
	<script src="'.$Ruta['js'].'jquery/msgBox/Scripts/jquery-1.8.0.min.js"></script>
	<script src="'.$Ruta['js'].'jquery/msgBox/Scripts/jquery.msgBox.js"></script>
	<!--jQuery-->
	<script type="text/javascript" src="'.$Ruta['js'].'jquery/jquery-1.9.1.min.js"></script>';
$Javascript='<!--jQuery-->
	<script type="text/javascript" src="'.$Ruta['js'].'o3m_functions.js"></script>';
$HtmlHead = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';
?>