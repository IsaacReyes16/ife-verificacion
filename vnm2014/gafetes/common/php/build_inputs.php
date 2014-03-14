<?php 
##Includes
require_once('conex.php');
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
##
switch($v_input){
	case 'personas' : echo select_personas(); break;
	default : echo false;
}
function select_personas(){
	$Sql = "SELECT id_visitador, CONCAT(nombres,' ',apaterno,' ',amaterno) as nombre_completo FROM personal WHERE estatus=1 ORDER BY nombre_completo ASC;";
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