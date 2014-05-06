<?php
	
	require_once "../inc/conexion.php";

	global $conn;
	
	$entidad = $_POST["entidad"];
	
    $result = mysql_query("select id_dis, cabecera_dis from cat_distritos_dis where id_ent = ".$entidad." and id_dis <> 0", $conn);
	$num_rows = mysql_num_rows($result);
	
	if($num_rows != 0){
	
		echo "<option value=0>Seleccionar</option>";
		
		while($row = mysql_fetch_array($result))
		{
			$id=$row['id_dis'];
			$data=$row['cabecera_dis'];
	
			echo "<option value=".$id.">".$id." ".$data."</option>";
		}
	}else{
		echo "<option value=0>No hay registros en la Base de Datos</option>";
	}
	
    

	$conn = null;
	
?>