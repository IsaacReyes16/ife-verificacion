<?php 
##Includes
include_once('common/php/header.php');
##Bussines
#Sesiones
if (!isset($_SESSION)) { session_start(); }
//--Vars Temporales
@include('testvars.php');
//--FIN Vars Temporales
$v_ent = $_SESSION[id_vlc];
$v_dto = $_SESSION[id_vdi];
$v_id=$ins['id'];
switch($_SESSION['nivel']){
	case 1: //usuario de nivel central
	 	$Filtro = "and a.id_personal='$v_id'";
		break;	
	case 2: //usuario de nivel local
		$Filtro = "and a.id_personal='$v_id' and a.ent='$v_ent'";	
		break;	
	case 3: //usuario de nivel distrital
		$Filtro = "and a.id_personal='$v_id' and a.ent='$v_ent' and a.dto='$v_dto'";
		break;
	default: exit;
}
#Query SQL
$sql="SELECT 
	a.id_personal
	,a.paterno
	,a.materno
	,a.nombre
	,a.id_tratamiento
	,a.sexo
	,a.lada
	,a.telefono
	,a.telefono2
	,a.telefonoip
	,a.correo
	,a.id_cargo
	,a.ent
	,a.dto
	,a.fecha_alta
	,a.id_adscripcion
	,a.firma
	,a.activo
	,a.actualizado
	,a.id_usuario
	,b.id_area
	,b.area
	,b.organo
	,c.ent_mayusc as entidad
	,d.adscripcion
	,d.corto
	,e.cargo
	,f.tratamiento
	FROM tbl_personal a
	LEFT JOIN tbl_adscripciones d USING(id_adscripcion)
	LEFT JOIN cat_areas b USING(id_area)
	LEFT JOIN cat_entidades c on a.ent=c.id_entidad
	LEFT JOIN cat_cargos e USING(id_cargo)
	LEFT JOIN cat_tratamientos f USING(id_tratamiento)
	WHERE 1 $Filtro
	LIMIT 1;";


$Row = SQLQuery($sql,1);
#Distrito
$Dtto=($Row['dto']==0)?'N/A':$Row['dto'];
#Puesto
$select_cargo = select_cargo($Row['id_cargo'],$Row['id_area']);
#Tratamientos
$select_tratamiento = select_tratamiento($Row['id_tratamiento']);
#Sexo
$radio_sexo = radio_sexo($Row['sexo']);
#Firma
$radio_firma = radio_firma($Row['firma']);
#Activo
$select_activo=select_activo($Row['activo']);
#Usuario
$vUsuario = SQLUser($Row['id_usuario'], 'ife_dom_irre', 'cat_usuarios_usu', 'id_usu');
$UsuarioNombre = $vUsuario['nombre_usu'].' '.$vUsuario['paterno_usu'].' '.$vUsuario['materno_usu'];

##Output
$htmlTpl = 'personal.tpl';
$html = new Template($Path['tpl'].$htmlTpl);
$html->set('jQuery', $jQueryPlugins);
$html->set('CSS_estilos', $Css);
#--
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
//-- Form
$html->set('id_personal', $Row['id_personal']);
$html->set('select_cargo', $select_cargo);
$html->set('cargo', $Row['cargo']);
$html->set('nombre', $Row['nombre']);
$html->set('paterno', $Row['paterno']);
$html->set('materno', $Row['materno']);
$html->set('select_tratamiento', $select_tratamiento);
$html->set('tratamiento', $Row['tratamiento']);
$html->set('radio_sexo', $radio_sexo);
$html->set('lada', $Row['lada']);
$html->set('telefono', $Row['telefono']);
$html->set('telefono2', $Row['telefono2']);
$html->set('telefonoip', $Row['telefonoip']);
$html->set('correo', $Row['correo']);
$html->set('radio_firma', $radio_firma);
$html->set('activo', $Row[1]);
$html->set('select_activo', $select_activo);
$html->set('actualizado', $Row['actualizado']);
$html->set('id_usuario', $UsuarioNombre);
$html=$html->output();
####### Fin de Impresión ##########
echo $html;
?>