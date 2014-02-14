<?php /*O3M*/
##Includes
include_once('header.php');
/**
* Funciones
**/

function select_activo($value=''){
	#$sel = (empty($value))?"selected":"";
	#$options="<option value='' $sel>--Seleccionar--</option>";
	$keys = array(0,1);
	foreach($keys as $key){
		$sel = ($value==$key)?"selected":"";
		$valueTxt = ($key)?"Activado":"Desactivado";
		$options.="<option value='$key' $sel>".$valueTxt."</option>";
	}	
	return $options;
}

function select_cargo($value='', $id_area=''){
	$area = (!empty($id_area))?"and id_area IN('0','$id_area')":"";
	$sel = (empty($value))?"selected":"";
	$options="<option value='' $sel>--Seleccionar--</option>";
	$Sql="SELECT * FROM cat_cargos WHERE activo=1 $area ORDER BY cargo ASC";
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

function select_tratamiento($value=''){
	$sel = (empty($value))?"selected":"";
	$options="<option value='' $sel>--Seleccionar--</option>";
	$Sql="SELECT * FROM cat_tratamientos ORDER BY tratamiento ASC";
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

function radio_sexo($value=''){
	#$name=(empty($value))?"sexo":$value;
	$name="sexo";
	$keys = array('H','M');
	foreach($keys as $key){
		$valueTxt = ($key=='H')?"Hombre":"Mujer";
		$sel = ($value==$key)?"checked":"";
		$options.="<input type='radio' name='$name' value='$key' $sel>".$valueTxt.'&nbsp;';		
	}	
	return $options;
}

function radio_firma($value=''){
	$name="firma";
	$keys = array('S','N');
	foreach($keys as $key){
		$valueTxt = ($key=='S')?"Si":"No";
		$sel = ($value==$key)?"checked":"";
		$options.="<input type='radio' name='$name' value='$key' $sel>".$valueTxt.'&nbsp;';		
	}	
	return $options;
}

/*O3M*/
?>