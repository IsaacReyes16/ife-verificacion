<?php 
##Includes
require_once('conex.php');
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
##
switch($v_input){
	case 'municipios' : echo select_municipios($v_ent, $v_dto); break;
	case 'secciones' : echo select_secciones($v_ent, $v_dto, $v_mpio); break;
	case 'manzanas' : echo select_manzanas($v_ent, $v_dto, $v_mpio, $v_secc); break;
	default : echo false;
}
function select_municipios($ent='', $dto=''){
	$Filtro .= (!empty($ent))?" and id_ent='$ent'":"";
	$Filtro .= (!empty($dto))?" and id_dis='$dto'":"";
	$Sql = "SELECT id_mun, CONCAT(id_mun,' - ',municipio) as descripcion FROM viviendas_seleccionadas WHERE 1 $Filtro GROUP BY id_ent,id_dis,id_mun ASC;";
	$Rows = SQLQuery($Sql);	
	foreach($Rows as $Row){
		++$n;
		if($n>1){
			$Valores[$n-1] = $Row;
		}
	}
	$Result = json_encode($Valores);
	return $Result;
}
function select_secciones($ent='', $dto='', $mpio=''){
	$Filtro .= (!empty($ent))?" and id_ent='$ent'":"";
	$Filtro .= (!empty($dto))?" and id_dis='$dto'":"";
	$Filtro .= (!empty($mpio))?" and id_mun='$mpio'":"";
	$Sql = "SELECT seccion,  seccion FROM viviendas_seleccionadas WHERE 1 $Filtro GROUP BY id_ent,id_dis,id_mun,seccion ASC;";
	$Rows = SQLQuery($Sql);	
	foreach($Rows as $Row){
		++$n;
		if($n>1){
			$Valores[$n-1] = $Row;
		}
	}
	$Result = json_encode($Valores);
	return $Result;
}
function select_manzanas($ent='', $dto='', $mpio='', $secc=''){
	$Filtro .= (!empty($ent))?" and id_ent='$ent'":"";
	$Filtro .= (!empty($dto))?" and id_dis='$dto'":"";
	$Filtro .= (!empty($mpio))?" and id_mun='$mpio'":"";
	$Filtro .= (!empty($secc))?" and seccion='$secc'":"";
	$Sql = "SELECT manzana,  manzana FROM viviendas_seleccionadas WHERE 1 $Filtro GROUP BY id_ent,id_dis,id_mun,seccion,manzana ASC;";
	$Rows = SQLQuery($Sql);	
	foreach($Rows as $Row){
		++$n;
		if($n>1){
			$Valores[$n-1] = $Row;
		}
	}
	$Result = json_encode($Valores);
	return $Result;
}
/*O3M*/
?>