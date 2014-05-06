<?php

/**
* Archivo que se encarga de leer un documento de excel y validar que los numeros de folio sean correctos.
* Autor: Ing. Eduardo Cruz <j.edwardc@live.com>
* Fecha de cración: 13-02-2014
*
* Modificado por: Oscar Maldonado
* Última modificación: 2013-04-30
*/

/**Se importa el archivo de conexion a la BD*/
require_once "../inc/conexion.php";
global $conn;

error_reporting(E_ALL);
set_time_limit(0);

/** Se carga la clase PHPExcel_IOFactory */
include_once '../../PHPExcel/Classes/PHPExcel/IOFactory.php';

/**Parametro con el nombre del archivo xls*/
$nombre_archivo = $_POST["file"];

/** Variables para crear el IN del query, obtener los folios no validos y el archivo que se leerrá*/
$cadena_folios = "";
$create_table = "";
$Inserts = "";
$drop_table = "";
$folios_no_validos = array();
$todo_bien = "";
$inputFileType = 'Excel5';
$inputFileName = '../archivos_xls/'.$nombre_archivo;

/** Manipulación del Objeto para leer el XLS*/
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objReader->setReadDataOnly(true);
$objPHPExcel = $objReader->load($inputFileName);

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

/**Obtiene el numero de registros leidos en el archivo XLS*/
$no_registros = count($sheetData);

$aleatorio = rand(0, 10000);

$create_table = "CREATE TEMPORARY TABLE TablaTemporal_".$aleatorio."( campo1 int); ";
if(!mysql_query($create_table, $conn)){
	echo "Error creando tabla temporal: " . mysql_error($conn);
}


/**Recorre los registros para validar que los registros sean numericos (y crea un string para hacer el query IN)
 si no son numericos se acumularán en los no validos*/
foreach ($sheetData as $item => $value){
	foreach ($value as $item2 => $value2){
		
		if (is_float($value2)){
			$cadena_folios .= (int)$value2.",";
			$Inserts = "INSERT TablaTemporal_".$aleatorio."(campo1)VALUES(".$value2.")";
			if(mysql_query($Inserts, $conn)){
			
			}else{
				echo "Error insertando en la tabla temporal: " . mysql_error($conn);
			}
		}else{
			$folios_no_validos[] = $value2;
		}	
	}
}

/**Se trata la cadena creada enteriormente para quitarle la ultima ',' que se concateno y funcione el query en la BD*/
$longitud_cadena = strlen($cadena_folios);
$query_in = substr($cadena_folios, 0, $longitud_cadena-1);


$Validacion = "select * from  TablaTemporal_".$aleatorio." where campo1 not in (select consec_ciu from lis_ciudadano_ciu where consec_ciu in (".$query_in."))";
$drop_table = "drop table TablaTemporal_".$aleatorio;

$result = mysql_query($Validacion, $conn);
$num_rows = mysql_num_rows($result);

if($num_rows != 0){
	
	while($row = mysql_fetch_array($result))
	{
		$id = $row['campo1'];
		
		$folios_no_encontrados[] = $id;
		
	}
	
}else{
	$todo_bien = '<div class="contenedor-columna">
					Los registros si existen en la BD
				 </div>';
}

if(mysql_query($drop_table, $conn)){

}else{
	echo "Error eliminando la tabla temporal: " . mysql_error($conn);
}

echo '<div class="contenedor-tabla">
		<div class="contenedor-fila">';
		
if (!empty($folios_no_validos)) {
    echo '<div class="contenedor-columna">
			Folios que fueron mal generados<br>';
			foreach ($folios_no_validos as $item3 => $value3){
				echo $value3."<br>";
			}
	echo '</div>';
}

if (!empty($folios_no_encontrados)) {
    echo '<div class="contenedor-columna">
			Folios no encontrados en la BD<br>';
			foreach ($folios_no_encontrados as $item4 => $value4){
				echo $value4."<br>";
			}
	echo '</div>';
}

echo $todo_bien;
echo'<div class="contenedor-columna">
			El archivo contiene '.$no_registros.' registro(s)
	</div>';


echo '</div>
	</div>';

$conn = null;

?>