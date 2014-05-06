<?php
	
	require_once "../inc/conexion.php";

	global $conn;

	$entidad = $_POST["entidad"];
	$distrito = $_POST["distrito"];

	$filtro = "";
	if($entidad){$filtro .= " and ent_act_ciu = ".$entidad;}
	if($entidad){$filtro .= " and dis_act_ciu = ".$distrito;}
	
	$sql = "select distinct mun_act_ciu, nom_mun_act_ciu from lis_ciudadano_ciu where 1 $filtro";
    $result = mysql_query($sql, $conn)or die(mysql_error());
    
	
	// $entidad = $_POST["entidad"];
	// $distrito = $_POST["distrito"];
	
 //    $result = mysql_query("select distinct mun_act_ciu, nom_mun_act_ciu from lis_ciudadano_ciu where ent_act_ciu = ".$entidad."  and dis_act_ciu = ".$distrito, $conn);
	$num_rows = mysql_num_rows($result);
	
	if($num_rows != 0){
	
		echo "<option value=0>Seleccionar</option>";
		
		while($row = mysql_fetch_array($result))
		{
			$id=$row['mun_act_ciu'];
			$data=$row['nom_mun_act_ciu'];
	
			echo "<option value=".$id.">".$id." ".$data."</option>";
		}
	}else{
		echo "<option value=0>No hay registros en la Base de Datos</option>";
	}
	
	$conn = null;
	
?>