<?php 
##Includes
include_once('common/php/header.php');
##Bussines
#Sesiones
// if (!isset($_SESSION)) { session_start(); }
//--Vars Temporales
@include('testvars.php');
//--FIN Vars Temporales
$v_ent = $Usuario['ent'];
$v_dto = $Usuario['dto'];
switch($Usuario['nivel']){
	case 1: //usuario de nivel central
	 	$Filtro = "";
		break;	
	case 2: //usuario de nivel local
		$Filtro = "and ent='$v_ent'";	
		break;	
	case 3: //usuario de nivel distrital
		$Filtro = "and ent='$v_ent' and dto='$v_dto'";
		break;
	default: exit;
}
#Query SQL
$sql="SELECT 
	a.id_adscripcion
	,a.adscripcion
	,a.corto
	,a.ent
	,a.dto
	,a.id_area
	,a.calle
	,a.num_ext
	,a.num_int
	,a.colonia
	,a.mpio_desc
	,a.mpio
	,a.cp
	,a.lada
	,a.telefono
	,a.fax
	,a.activo
	,a.actualizado
	,a.id_usuario
	,b.area
	,b.organo
	,c.ent_mayusc as entidad
	,a.horario
	FROM tbl_adscripciones a
	LEFT JOIN cat_areas b using(id_area)
	LEFT JOIN cat_entidades c on a.ent=c.id_entidad
	WHERE 1 $Filtro
	LIMIT 1;";
$Row = SQLQuery($sql,1);
#Distrito
$Dtto=($Row['dto']==0)?'N/A':$Row['dto'];
#Activo
$select_activo=select_activo($Row['activo']);
#Usuario
$vUsuario = SQLUser($Row['id_usuario'], 'ife_dom_irre', 'cat_usuarios_usu', 'id_usu');
$UsuarioNombre = $vUsuario['nombre_usu'].' '.$vUsuario['paterno_usu'].' '.$vUsuario['materno_usu'];
##Output
$htmlTpl = 'adscripciones.tpl';
$html = new Template($Path['tpl'].$htmlTpl);
$html->set('HtmlHead', $HtmlHead);
$html->set('jQuery', $jQueryPlugins);
$html->set('Javascript', $Javascript);
$html->set('CSS_estilos', $Css);
#--
$html->set('btnDo', 'INSERT');
$html->set('id_adscripcion', $Row['id_adscripcion']);
$html->set('adscripcion', $Row['adscripcion']);
$html->set('corto', $Row['corto']);
$html->set('id_ent', $Row['ent']);
$html->set('entidad', $Row['entidad']);
$html->set('id_dto', $Row['dto']);
$html->set('dto', $Dtto);
$html->set('id_area', $Row['id_area']);
$html->set('area', $Row['area']);
$html->set('organo', $Row['organo']);
$html->set('calle', "");
$html->set('num_ext', "");
$html->set('num_int', "");
$html->set('colonia', "");
$html->set('mpio_desc', "");
$html->set('horario', $Row['horario']);
#$html->set('mpio', $Row['mpio']);
$html->set('cp', "");
$html->set('lada', $Row['lada']);
$html->set('telefono', "");
$html->set('fax', "");
$html->set('activo', $Row[1]);
$html->set('select_activo', $select_activo);
$html->set('actualizado', date('Y-m-d H:i:s'));
$html->set('id_usuario', $UsuarioNombre);
$html->set('funcionarios', '');
$html=$html->output();
####### Fin de Impresión ##########
echo utf8_encode($html);
?>