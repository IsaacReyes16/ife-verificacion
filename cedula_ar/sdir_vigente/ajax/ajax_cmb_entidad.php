<?php
	
	require_once "../inc/conexion.php";

	global $conn;
	
    $qry = mysql_query("select id_ent,descripcion_ent from cat_entidades_ent where id_ent not in (0, 86, 87, 88)", $conn);
	
	echo "<option value=0>Seleccionar</option>";
	echo "<option value=99>TODAS</option>";
	
    while($row = mysql_fetch_array($qry))
    {
        $id=$row['id_ent'];
        $data=$row['descripcion_ent'];

        echo "<option value=$id>$data</option>";
    }

	$conn = null;
	
?>