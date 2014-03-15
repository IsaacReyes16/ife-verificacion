<?php 
##Includes
require_once('conex.php');
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
##
switch($v_input){
	case 'personas' : echo select_personas($v_ent, $v_dto, $v_filtroCargo); break;
	default : echo false;
}
function select_personas($ent='', $dto='', $filtroCargo=false){
	$filtroCargo = ($filtroCargo)?'and id_cargo IN(1,2,3,4,6)':'';
	$Sql = "SELECT id_visitador, CONCAT(nombres,' ',apaterno,' ',amaterno) as nombre_completo FROM personal WHERE id_edo='$ent' and id_dtto='$dto' and estatus=1 $filtroCargo ORDER BY nombre_completo ASC;";
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