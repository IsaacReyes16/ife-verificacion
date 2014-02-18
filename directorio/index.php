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
switch($_SESSION['nivel']){
	case 1: //usuario de nivel central
	 	$Filtro = "and a.ent=0 and a.dto=0";
		break;	
	case 2: //usuario de nivel local
		$Filtro = "and a.ent='$v_ent' and a.dto=0";	
		break;	
	case 3: //usuario de nivel distrital
		$Filtro = "and a.ent='$v_ent' and a.dto='$v_dto'";
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
	FROM tbl_adscripciones a
	LEFT JOIN cat_areas b using(id_area)
	LEFT JOIN cat_entidades c on a.ent=c.id_entidad
	WHERE 1 $Filtro
	LIMIT 1;";
$Row = SQLQuery($sql,1);

$sql = "SELECT 
	a.id_personal
	,f.tratamiento
	,a.nombre
	,a.paterno
	,a.materno	
	,e.cargo
	,e.cargo_ab
	,a.id_tratamiento
	,a.actualizado
	FROM tbl_personal a
	LEFT JOIN cat_cargos e USING(id_cargo)
	LEFT JOIN cat_tratamientos f USING(id_tratamiento)
	WHERE 1 and firma='S' and a.activo=1 $Filtro ;";
$Row_personal = SQLQuery($sql);
#Distrito
$Dtto=($Row['dto']==0)?'N/A':$Row['dto'];
#Direccion
$direccion = $Row['calle'].' '.$Row['num_ext'].' '.$Row['num_int'].', <br/>'.$Row['colonia'].', '.$Row['mpio_desc'].', <br/> C.P. '.$Row['cp'].', Tel. ('.$Row['lada'].') '.$Row['telefono'];
#Usuario
$vUsuario = SQLUser($Row['id_usuario'], 'ife_dom_irre', 'cat_usuarios_usu', 'id_usu');
$UsuarioNombre = $vUsuario['nombre_usu'].' '.$vUsuario['paterno_usu'].' '.$vUsuario['materno_usu'];
#Icono OK & Not OK
$iOk = "<img src='".$Path['img'].'ok.png'."' border='0' alt='Actualizado' valign='middle' title='Actualizado'>";
$iNotOk = "<img src='".$Path['img'].'not.png'."' border='0' alt='Revisar' valign='middle' title='Actualizar Datos'>";
#Direccion
$direccionOk = (!empty($Row['actualizado']))?$iOk:$iNotOk;
#Personas
$Total=count($Row_personal);
for($i=1; $i<=$Total-1; $i++){
	$Nombre = $Row_personal[$i][1].' '.$Row_personal[$i][2].' '.$Row_personal[$i][3].' '.$Row_personal[$i][4];	
	$personaOk = (!empty($Row_personal[$i][8]))?$iOk:$iNotOk;
	$Funcionarios .= '<tr>
			        <td class="table-label">'.$personaOk.'&nbsp;'.$Row_personal[$i][6].':&nbsp;</td> 
			        <td class="table-field" Colspan="3">'.$Nombre.'&nbsp;
			        <span id="btnEditar" class="btn" onclick="location.href=\'personal.php?id='.$Row_personal[$i][0].'\'" title="Editar">Editar</span>&nbsp;
			        <span id="btnQuitar" class="btn" onclick="quitar(\''.$Row_personal[$i][0].'\',\''.$Nombre.'\');" title="Quitar">Quitar</span>
			        </td>         
			    </tr>';
}
##Output
$htmlTpl = 'index.tpl';
$html = new Template($Path['tpl'].$htmlTpl);
$html->set('HtmlHead', $HtmlHead);
$html->set('jQuery', $jQueryPlugins);
$html->set('Javascript', $Javascript);
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
$html->set('direccionOk', $direccionOk);
$html->set('icoOK', $iOk);
$html->set('icoNotOK', $iNotOk);
//-- Form
$html->set('direccion', $direccion);
$html->set('funcionarios', $Funcionarios);
$html->set('actualizado', $Row['actualizado']);
$html->set('id_usuario', $UsuarioNombre);
$html=$html->output();
####### Fin de Impresión ##########
echo utf8_encode($html);
?>