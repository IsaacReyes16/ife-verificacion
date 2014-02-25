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
	WHERE 1 and a.activo=1 $Filtro ORDER BY a.id_adscripcion ASC;";
$Row = SQLQuery($sql);

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
	,a.sexo
	,a.telefono
	,a.telefonoip
	,a.correo
	,a.firma
	FROM tbl_personal a
	LEFT JOIN cat_cargos e USING(id_cargo)
	LEFT JOIN cat_tratamientos f USING(id_tratamiento)
	WHERE 1 and firma='S' and a.activo=1 $Filtro ;";

$Row_personal = SQLQuery($sql);
#Entidad
$Ent=($Row[1][3]==0)?'N/A':$Row[1][21];
#Distrito
$Dtto=($Row[1][4]==0)?'N/A':$Row[1][4];
#Usuario
$vUsuario = SQLUser($Row[1][18], 'ife_dom_irre', 'cat_usuarios_usu', 'id_usu');
$UsuarioNombre = $vUsuario['nombre_usu'].' '.$vUsuario['paterno_usu'].' '.$vUsuario['materno_usu'];
#Icono OK & Not OK
$iOk = "<img src='".$Path['img'].'ok.png'."' border='0' alt='Actualizado' valign='middle' title='Actualizado'>";
$iNotOk = "<img src='".$Path['img'].'not.png'."' border='0' alt='Revisar' valign='middle' title='Actualizar Datos'>";
#Direccion
$TotalDirs = count($Row);
for($i=1; $i<=$TotalDirs-1; $i++){
	$direccion = $Row[$i][6].' '.$Row[$i][7].' '.$Row[$i][8].', <br/>'.$Row[$i][9].', '.$Row[$i][10].', <br/> C.P. '.$Row[$i][12].', Tel. ('.$Row[$i][13].') '.$Row[$i][14];

	$direccionOk = (!empty($Row[$i][17]) && !empty($Row[$i][6]) && !empty($Row[$i][7]) && !empty($Row[$i][9])&& !empty($Row[$i][10]) && !empty($Row[$i][12]))?$iOk:$iNotOk;
	$Direcciones .= '<tr>
            <td class="table-label">'.$direccionOk.' Dirección '.$i.':&nbsp;</td> 
            <td class="table-field" Colspan="3">'.$direccion.'&nbsp;<span id="btnEditar" class="btnBl" onclick="location.href=\'adscripciones.php?id='.$Row[$i][0].'\'">Editar</span>
            <span id="btnQuitar" class="btnBl" onclick="quitar(\''.$Row[$i][0].'\',\''.$direccion.'\', \'direccion\');" title="Quitar">Quitar</span>
            </td>         
        </tr>';
}
#Personas
$Total=count($Row_personal);
for($i=1; $i<=$Total-1; $i++){
	$Nombre = $Row_personal[$i][1].' '.$Row_personal[$i][2].' '.$Row_personal[$i][3].' '.$Row_personal[$i][4];	
	$personaOk = (!empty($Row_personal[$i][8]) && !empty($Row_personal[$i][7]) && !empty($Row_personal[$i][2]) && !empty($Row_personal[$i][3]) && !empty($Row_personal[$i][9]) && !empty($Row_personal[$i][10]) && !empty($Row_personal[$i][11]) && !empty($Row_personal[$i][12]) && !empty($Row_personal[$i][13]))?$iOk:$iNotOk;
	$Funcionarios .= '<tr>
			        <td class="table-label">'.$personaOk.'&nbsp;'.$Row_personal[$i][6].':&nbsp;</td> 
			        <td class="table-field" Colspan="3">'.$Nombre.'&nbsp;
			        <span id="btnEditar" class="btnBl" onclick="location.href=\'personal.php?id='.$Row_personal[$i][0].'\'" title="Editar">Editar</span>&nbsp;
			        <span id="btnQuitar" class="btnBl" onclick="quitar(\''.$Row_personal[$i][0].'\',\''.$Nombre.'\', \'personal\');" title="Quitar">Quitar</span>
			        </td>         
			    </tr>';
}
#divSearch
$divSearch=($Usuario['nivel']==1)?'block':'none';
##Output
$htmlTpl = 'inicio.tpl';
$html = new Template($Path['tpl'].$htmlTpl);
$html->set('HtmlHead', $HtmlHead);
$html->set('jQuery', $jQueryPlugins);
$html->set('Javascript', $Javascript);
$html->set('CSS_estilos', $Css);
$html->set('ImgPath', $Path['img']);
#--
$html->set('id_adscripcion', $Row[1][0]);
$html->set('adscripcion', $Row[1][1]);
$html->set('corto', $Row[1][2]);
$html->set('id_ent', $Row[1][3]);
$html->set('entidad', $Ent);
$html->set('id_dto', $Row[1][4]);
$html->set('dto', $Dtto);
$html->set('id_area', $Row[1][5]);
$html->set('area', $Row[1][19]);
$html->set('organo', $Row[1][20]);
$html->set('direccionOk', $direccionOk);
$html->set('icoOK', $iOk);
$html->set('icoNotOK', $iNotOk);
$html->set('divSearch', $divSearch);
//-- Form
$html->set('direcciones', $Direcciones);
$html->set('funcionarios', $Funcionarios);
$html->set('actualizado', $Row[17]);
$html->set('id_usuario', $UsuarioNombre);
$html=$html->output();
####### Fin de Impresión ##########
echo utf8_encode($html);
?>