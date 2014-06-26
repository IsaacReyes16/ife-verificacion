<?php 
##Auth
session_start();
if (!isset($_SESSION['id_usu'])) { header("Location: ../sdir/index.php"); exit; }
##Includes
include_once('common/php/header.php');
##Bussines
$v_ent = $Usuario['ent'];
$v_dto = $Usuario['dto'];
##Output
$htmlTpl = 'index.tpl';
$html = new Template($Ruta['tpl'].$htmlTpl);
$html->set('HtmlHead', $HtmlHead);
$html->set('jQuery', $jQueryPlugins);
$html->set('Javascript', $Javascript);
$html->set('CSS_estilos', $Css);
$html->set('ImgPath', $Ruta['img']);
$html->set('fecha_hoy', fecha_larga_hoy());
#--
$html->set('Titulo', 'ACTUALIZACIÓN DE DIRECTORIO');
$html=$html->output();
####### Fin de Impresión ##########
echo utf8_encode($html);
?>