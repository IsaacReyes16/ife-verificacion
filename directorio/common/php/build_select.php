<?php /*O3M*/
##Includes
include_once('header.php');
/**
* Funciones
**/

//SELECTS
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

function select_srcAdscripcion($value=''){
	$sel = (empty($value))?"selected":"";
	$options="<option value='0' $sel>--Todas--</option>";
	$Sql="SELECT id_adscripcion, CONCAT(id_adscripcion,' - ',adscripcion) FROM tbl_adscripciones WHERE activo=1 ORDER BY id_adscripcion ASC;";
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

function select_direccion($value='', $ent='', $dto=''){
	$Sql="SELECT id_adscripcion, CONCAT(calle,', ',num_ext,' ',num_int,', ',colonia,', ',mpio_desc,'...') FROM tbl_adscripciones WHERE activo=1 and ent='$ent' and dto='$dto'ORDER BY id_adscripcion ASC;";
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

function select_ent($value=''){
	$sel = (empty($value))?"selected":"";
	$options="<option value='' $sel>--Todas--</option>";
	$options.="<option value='0'>0 - Oficinas Centrales</option>";
	$Sql="SELECT id_entidad, CONCAT(id_entidad,' - ',ent_minusc) FROM cat_entidades ORDER BY id_entidad ASC;";
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

function select_dto($value='', $ent=''){
	$sel = (empty($value))?"selected":"";
	$options="<option value='' $sel>--Todos--</option>";
	$options.="<option value='0'>0 - Vocalia Local</option>";
	$Sql="SELECT dto, dto_corto FROM cat_distritos WHERE ent='$ent' ORDER BY ent, dto ASC;";
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

//RADIOS
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
	$keys = array('S');
	foreach($keys as $key){
		$valueTxt = ($key=='S')?"Si":"No";
		#$sel = ($value==$key)?"checked='checked'":"";
		$sel = "checked='checked'";
		$options.="<input type='radio' name='$name' value='$key' $sel>".$valueTxt.'&nbsp;';		
	}	
	return $options;
}

// Dinamicos
if($in['tipo']=='select_dto'){
#Crea select con distritos de acuerdo al la entidad seleccionada
	$ent=$in['ent'];
	$select='<select id="id_dto" name="id_dto">';
	$local=($ent==0)?"<option value='0'>0 - Vocalia Local</option>":"<option value='' selected>--Todos--</option>";
	$options.=$local;
	$Sql="SELECT dto, dto_corto FROM cat_distritos WHERE ent='$ent' ORDER BY ent, dto ASC;";
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
/*O3M*/
?>