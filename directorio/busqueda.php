<?php 
##Includes
include_once('common/php/header.php');
##Bussines
#Validación de acceso
if($Usuario['nivel']!=1){
	header("location: inicio.php");
}
##Output
$htmlTpl = 'busqueda.tpl';
$html = new Template($Ruta['tpl'].$htmlTpl);
$html->set('HtmlHead', $HtmlHead);
$html->set('jQuery', $jQueryPlugins);
$html->set('Javascript', $Javascript);
$html->set('CSS_estilos', $Css);
$html->set('ImgPath', $Ruta['img']);
#--
$html->set('select_ent', select_ent());
$html->set('select_dto', select_dto());
$html->set('id_usuario', $UsuarioNombre);
$html=$html->output();
####### Fin de Impresión ##########
echo utf8_encode($html);
?>