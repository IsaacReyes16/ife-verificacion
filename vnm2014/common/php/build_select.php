<?php
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
require_once('conex.php');
function select_seccion($value='', $ent='', $dto=''){
	$sel = (empty($value))?"selected":"";
	$options="<option value='' $sel>-- Seleccione --</option>";
	$Sql="SELECT seccion, seccion FROM viviendas_seleccionadas WHERE id_ent='$ent' and id_dis='$dto' GROUP BY id_ent, id_dis, seccion ASC;";
	echo $Sql;
	$Row=SQLQuery($Sql);
	$Total=count($Row);
	for($i=1; $i<=$Total; $i++){
		$keys[$i-1]=$Row[$i][0];
		$valueTxt[$i-1]=$Row[$i][1];
	}
	$x=0;
	foreach($keys as $key){
		if(!empty($valueTxt[$x])){
			$sel = ($value==$key)?"selected":"";
			$options.="<option value='$key' $sel>".$valueTxt[$x]."</option>";
		}
		$x++;
	}	
	return $options;
}
// Dinamicos
if($v_tipo=='select_manzana'){
#Crea select con manzanas de acuerdo a la sección seleccionada
	$name='manzana';
	$ent=$v_ent;
	$dto=$v_dto;
	$seccion=$v_seccion;
	$onChange=$v_onchange;
	$select='<select id="'.$name.'" name="'.$name.'" onchange="'.$onChange.'">';
	$local="<option value='' selected>--Todas--</option>";
	$options.=$local;
	$Sql="SELECT manzana, manzana FROM viviendas_seleccionadas WHERE id_ent='$ent' and id_dis='$dto' and seccion='$seccion' GROUP BY id_ent, id_dis, seccion, manzana ASC;";
	$Row=SQLQuery($Sql);
	$Total=count($Row);
	for($i=1; $i<=$Total; $i++){
		$keys[$i-1]=$Row[$i][0];
		$valueTxt[$i-1]=$Row[$i][1];
	}
	$x=0;
	foreach($keys as $key){
		if(!empty($valueTxt[$x])){
			$options.="<option value='$key'>".$valueTxt[$x]."</option>";
		}
		$x++;
	}
	$select .= $options.'</select>';
	echo $select;
}

if($v_tipo=='select_folio'){
#Crea select con folios de acuerdo a la manzana seleccionada
	$name='folio_edsm';
	$ent=$v_ent;
	$dto=$v_dto;
	$seccion=$v_seccion;
	$manzana=$v_manzana;
	$onChange=$v_onchange;
	$select='<select id="'.$name.'" name="'.$name.'" onchange="'.$onChange.'">';
	$local="<option value='' selected>--Todos--</option>";
	$options.=$local;
	$Sql="SELECT folio, folio FROM viviendas_seleccionadas WHERE id_ent='$ent' and id_dis='$dto' and seccion='$seccion' and manzana='$manzana' ORDER BY folio ASC;";
	$Row=SQLQuery($Sql);
	$Total=count($Row);
	for($i=1; $i<=$Total; $i++){
		$keys[$i-1]=$Row[$i][0];
		$valueTxt[$i-1]=$Row[$i][1];
	}
	$x=0;
	foreach($keys as $key){
		if(!empty($valueTxt[$x])){
			$options.="<option value='$key'>".$valueTxt[$x]."</option>";
		}
		$x++;
	}
	$select .= $options.'</select>';
	echo $select;
}
?>