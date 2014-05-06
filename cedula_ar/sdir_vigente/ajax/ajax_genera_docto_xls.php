<?php

/**
* Archivo que se encarga de leer un documento de excel, generar reportes PDF y un XLS
* Autor: Ing. Eduardo Cruz <j.edwardc@live.com>
* Fecha de cración: 17-02-2014
*
* Modificado por: Oscar Maldonado
* Última modificación: 2013-04-30
* Ojo: El codigo HTML contiene mucho HardCode <---
*/

/**Se importa el archivo de conexion a la BD*/
require_once "../inc/conexion.php";
global $conn;

error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(0);

include '../../PHPExcel/Classes/PHPExcel/IOFactory.php';// Se incluye la clase PHPExcel_IOFactory (Lectura de archivo xls)
include '../../PHPExcel/Classes/PHPExcel.php';			// Se incluye la clase PHPExcel (Creación de archivo xls)
require_once("../../dompdf/dompdf_config.inc.php");		// Se incluye la clase dompdf_config.inc (Generación de PDF)
require_once("../../ZipFile/zipfile.php");				// Se incluye la clase zipfile (Generación de zip)

/** Funcion para volver a mayusculas o minisculas*/
function utfChCase($s,$u=false){

	$s=utf8_decode($s);
	$s=$u?strtoupper($s):strtolower($s);
	return strtoupper_utf8($s);
}

/** Funcion para reemplazar los acentos en minusculas por mayusculas*/
function strtoupper_utf8($cadena) {
	$convertir_de = array("á","é","í","ñ","ó","ú");
	$convertir_a =  array("Á","É","Í","Ñ","Ó","Ú");
	return str_replace($convertir_de, $convertir_a, $cadena);
}

/**Variable default*/
$no_aplica = "<strong>NO APLICA</strong>";

/** Encabezado CSS para el reporte PDF*/
$html = '<style>

.header { position: fixed; left: 0; top: 10px; right: 47.5px; height: 0px; text-align: right; }
.page:after { content: counter(page, decimal); }

@page {
	margin-top: 30px;
	margin-left: 37.5px;
	margin-right: 37.5px;
	margin-buttom: 0px; 
}
			
table{
font-size:12px;
font-family:Arial, Helvetica, sans-serif;
}

.titulo{
font-size:16px;
}

.caja{
border: 1px solid #000000;
}

.tds{
border-bottom-color:#000000;
border-bottom-width:2px;
border-bottom-style:solid;
border-top-color:#CCCCCC;
border-top-style:solid;
border-top-width:1px; 
border-left-color:#CCCCCC; 
border-left-width:1px;
border-left-style:solid;
border-right-color:#000000; 
border-right-width:2px;
border-right-style:solid;
}

.pto_der{
border-right: 1px dotted #000000; 
}

.pto_abajo{
border-bottom: 1px dotted #000000; 
}

.pto_arriba{
border-top: 1px dotted #000000; 
}
</style>';

/** Cuando se hace una busqueda por algún Drop Down List*/
if(isset($_GET["id_ent"]) && $_GET["id_ent"] != 0){
	
	/** Asignacion de valores GET*/
	$id_ent = $_GET["id_ent"];
	$id_dis = $_GET["id_dis"];
	$id_mun = $_GET["id_mun"];
	$id_estatus = $_GET["estatus"];	
	/** Variables String para concatenar AND al query*/
	$ent="";
	$dis = "";
	$mun = "";
	
	/** Valida si es diferente de cero asigana un AND al query*/
	if($id_ent != 0 && $id_ent<=32){$ent = " and ent_act_ciu = ".$id_ent;}
	if($id_dis != 0){$dis = " and dis_act_ciu = ".$id_dis;}
	if($id_mun != 0){$mun = " and mun_act_ciu = ".$id_mun;}
	if($id_estatus != 0){$estatus = " and b.edo_reg_cdi = ".$id_estatus;}

	/** Union de las variables para formar el Query final para la selección por filtro de entidad, distrito, municipio*/
	$Validacion = "select bloque_ciu, a.consec_ciu, paterno_ciu, materno_ciu, nombre_ciu, b.edo_reg_cdi from lis_ciudadano_ciu a LEFT JOIN lis_cedula_di_cdi_vig as b ON a.consec_ciu = b.consec_ciu where 1 ".$ent;
	$Validacion = $Validacion.$dis;
	$Validacion = $Validacion.$mun;
	$Validacion = $Validacion.$estatus." /*and edo_reg_final_ciu = 2*/ order by bloque_ciu, a.consec_ciu, paterno_ciu, materno_ciu, nombre_ciu";

}else{
	/** Cuando se hace una busqueda por numero de Folio*/
	if (isset($_GET["folio"])){
		$Validacion = "select bloque_ciu, a.consec_ciu, paterno_ciu, materno_ciu, nombre_ciu, b.edo_reg_cdi from lis_ciudadano_ciu a LEFT JOIN lis_cedula_di_cdi_vig as b ON a.consec_ciu = b.consec_ciu where a.consec_ciu = ".$_GET["folio"].$estatus." /*and edo_reg_final_ciu = 2*/ order by bloque_ciu, a.consec_ciu, paterno_ciu, materno_ciu, nombre_ciu";
	}else{
		/** Cuando se hace una busqueda por medio de la carga de un archivo xls */
		/** Parametro con el nombre del archivo xls*/
		$nombre_archivo = $_GET["file"];
		
		/** Variables para crear el IN del query, obtener los folios no validos y el archivo que se leerá*/
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
		
		/** Obtiene el numero de registros leidos en el archivo XLS*/
		$no_registros = count($sheetData);
		
		/** Recorre los registros para validar que los registros sean numericos (y crea un string para hacer el query IN)*/
		foreach ($sheetData as $item => $value){
			foreach ($value as $item2 => $value2){
				
				if (is_float($value2)){
					$cadena_folios .= (int)$value2.",";
				}	
			}
		}
		
		/** Se trata la cadena creada anteriormente para quitarle la ultima ',' que se concateno y funcione el query en la BD*/
		$longitud_cadena = strlen($cadena_folios);
		$query_in = substr($cadena_folios, 0, $longitud_cadena-1);
		
		$Validacion = "select bloque_ciu, a.consec_ciu, paterno_ciu, materno_ciu, nombre_ciu, b.edo_reg_cdi from lis_ciudadano_ciu a LEFT JOIN lis_cedula_di_cdi_vig as b ON a.consec_ciu = b.consec_ciu where a.consec_ciu in (".$query_in.") ".$estatus." /*and edo_reg_final_ciu = 2*/ order by bloque_ciu, a.consec_ciu, paterno_ciu, materno_ciu, nombre_ciu";	
	}//if de folio
}//if de entidad

#txtlog($Validacion, '../tmp/');

/** Se procesa el query obtenido dependiendo de los parametros enviados*/
$result = mysql_query($Validacion, $conn);
$num_rows = mysql_num_rows($result);

/** Si el resultado del query principal trae datos*/		
if($num_rows != 0){
	/** Variables para controlar cuantas hojas serán mostradas en un archivo PDF 
	(tomando en cuenta que por numero de FOLIO son 2 hojas, entonces si requerimos 250 hojas tendremos que asignar el valor de 125 a la variable $hojas)*/
	$reg_pag = $num_rows;
	$num_doctos = 0;
	$hojas = 1;
	$i = 1;

	/** Variables para generar una carpeta aleatoria y el zip correspondiente*/
	$conta = 1;
	$zipfile = new zipfile();

	// Crea un nuevo objeto PHPExcel
	$objPHPExcel_create = new PHPExcel();
	// Establecer propiedades
	$objPHPExcel_create->getProperties()
			->setCreator("Cattivo")
			->setLastModifiedBy("Cattivo")
			->setTitle("Documento Excel")
			->setSubject("Documento Excel")
			->setKeywords("Excel Office 2007 openxml php");
			
			// $fila_excel vale 2 por que el encabezado del XLS ocupa la fila 1
			$fila_excel = 1;

	//Generar encabezado del XLS
	$objPHPExcel_create->setActiveSheetIndex(0)
			->setCellValue('A1', "NO. BLOQUE")
			->setCellValue('B1', "FOLIO")
			->setCellValue('C1', "NOMBRE")
			->setCellValue('D1', "PATERNO")
			->setCellValue('E1', "MATERNO");

	/** Creamos un directorio temporal que contendra los PDF's y el XLS que seran comprimidos en un ZIP*/
	$tmpPath = "../tmp/";	
	$aleatorio = rand(0, 32767);
	$directorio = $tmpPath."PDF_".$aleatorio; 
	$dirmake = mkdir($directorio, 0777);

	while($row = mysql_fetch_array($result))
	{
		// Contador de filas de excel.
		$fila_excel++;
		/** Asiganacion de valores a variables del query principal las cuales serán tomadas para la información del XLS (nominativo)*/
		$bloque_ciu = 	(int)$row['bloque_ciu'];
		$folio = 		(int)$row['consec_ciu'];
		$paterno_ciu = 	(string)$row['paterno_ciu'];
		$materno_ciu = 	(string)$row['materno_ciu'];
		$nombre_ciu = 	(string)$row['nombre_ciu'];
		$estatus_ciu =	$row['edo_reg_cdi'];

		/** Agregar Informacion al XLS (nominativo)*/
		$objPHPExcel_create->setActiveSheetIndex(0)
		->setCellValue('A'.$fila_excel, $bloque_ciu)
		->setCellValue('B'.$fila_excel, $folio)
		->setCellValue('C'.$fila_excel, $nombre_ciu)
		->setCellValue('D'.$fila_excel, $paterno_ciu)
		->setCellValue('E'.$fila_excel, $materno_ciu);
		
		
		
		/** Query con variable igual al nombre de la tabla*/
		$lis_ciudadano_ciu = "select consec_ciu,
									   referencia_ciu,
									   nom_comp_ciu,
									   clave_elec_ciu,
									   ent_act_ciu,
									   dis_act_ciu,
									   mun_act_ciu,
									   seccion_act_ciu,
									   manzana_act_ciu,
									   calle_act_ciu,
									   num_ext_act_ciu,
									   num_int_act_ciu,
									   colonia_act_ciu,
									   cp_act_ciu,
									   ent_ant_ciu,
									   dis_ant_local_ciu,
									   mun_ant_ciu,
									   seccion_ant_ciu,
									   manzana_ant_ciu,
									   calle_ant_ciu,
									   num_ext_ant_ciu,
									   num_int_ant_ciu,
									   colonia_ant_ciu,
									   cp_ant_ciu
								  from lis_ciudadano_ciu
								 where consec_ciu = ".$folio;		 

		$qry = mysql_query($lis_ciudadano_ciu, $conn);
	
		while($row = mysql_fetch_array($qry))
		{
			/** Asignacion de valores resultado del query anterior (Formato mayusculas, relleno de caracteres a la izq. con ceros)*/
			$referencia_ciu = 		utfChCase($row['referencia_ciu'], true);
			$nom_comp_ciu = 		utfChCase($row['nom_comp_ciu'], true);
			$clave_elec_ciu = 		utfChCase($row['clave_elec_ciu'], true);
			$ent_act_ciu = 			str_pad((string)$row['ent_act_ciu'], 2, "0", STR_PAD_LEFT);
			$dis_act_ciu = 			str_pad((string)$row['dis_act_ciu'], 2, "0", STR_PAD_LEFT);
			$mun_act_ciu = 			str_pad((string)$row['mun_act_ciu'], 3, "0", STR_PAD_LEFT);
			$seccion_act_ciu = 		str_pad((string)$row['seccion_act_ciu'], 4, "0", STR_PAD_LEFT);
			$manzana_act_ciu = 		str_pad((string)$row['manzana_act_ciu'], 4, "0", STR_PAD_LEFT);
			$calle_act_ciu = 		utfChCase($row['calle_act_ciu'], true);
			$num_ext_act_ciu = 		utfChCase($row['num_ext_act_ciu'], true);
			$num_int_act_ciu = 		utfChCase($row['num_int_act_ciu'], true);
			$colonia_act_ciu = 		utfChCase($row['colonia_act_ciu'], true);
			$cp_act_ciu = 			$row['cp_act_ciu'];
			$ent_ant_ciu = 			(!empty($row['ent_ant_ciu'])) ? str_pad((string)$row['ent_ant_ciu'], 2, "0", STR_PAD_LEFT) : $no_aplica;
			$dis_ant_local_ciu = 	(!empty($row['dis_ant_local_ciu'])) ? str_pad((string)$row['dis_ant_local_ciu'], 2, "0", STR_PAD_LEFT) :  $no_aplica;
			$mun_ant_ciu = 			(!empty($row['mun_ant_ciu'])) ? str_pad((string)$row['mun_ant_ciu'], 3, "0", STR_PAD_LEFT) :  $no_aplica;
			$seccion_ant_ciu = 		(!empty($row['seccion_ant_ciu'])) ? str_pad((string)$row['seccion_ant_ciu'], 4, "0", STR_PAD_LEFT) :  $no_aplica;
			$manzana_ant_ciu = 		(!empty($row['manzana_ant_ciu'])) ? str_pad((string)$row['manzana_ant_ciu'], 4, "0", STR_PAD_LEFT) :  $no_aplica;
			$calle_ant_ciu = 		(!empty($row['calle_ant_ciu'])) ? utfChCase($row['calle_ant_ciu'], true) :  $no_aplica;
			$num_ext_ant_ciu = 		utfChCase($row['num_ext_ant_ciu'], true);
			$num_int_ant_ciu = 		utfChCase($row['num_int_ant_ciu'], true);
			$colonia_ant_ciu = 		utfChCase($row['colonia_ant_ciu'], true);
			$cp_ant_ciu = 			$row['cp_ant_ciu'];	

			$con_registro_anterior = (empty($row['ent_ant_ciu']) || $estatus_ciu==2) ? 0 : 1;		

			if(!$con_registro_anterior){
				$ent_ant_ciu = 			$no_aplica;
				$dis_ant_local_ciu = 	$no_aplica;
				$mun_ant_ciu = 			$no_aplica;
				$seccion_ant_ciu = 		$no_aplica;
				$manzana_ant_ciu = 		$no_aplica;
				$calle_ant_ciu = 		$no_aplica;
				$num_ext_ant_ciu = 		"";
				$num_int_ant_ciu = 		"";
				$colonia_ant_ciu = 		"";
				$cp_ant_ciu =			"";
			}
		}
		
		/** Query con variable igual al nombre de la tabla*/
		$lis_cedula_medios_vig = "select resultado_mi,
									   resultado_mif,
									   resultado_cd,
									   DATE_FORMAT(fecha, '%d/%m/%Y') fecha_cmv
								  from lis_cedula_medios_vig
								 where consec_ciu = ".$folio;	
		
		$qry2 = mysql_query($lis_cedula_medios_vig, $conn);
	
		while($row2 = mysql_fetch_array($qry2))
		{
			/** Asignacion de valores resultado del query anterior*/
			$resultado_mi = 	utfChCase($row2['resultado_mi'], true);
			$resultado_mif = 	utfChCase($row2['resultado_mif'], true);
			$resultado_cd = 	utfChCase($row2['resultado_cd'], true);
			$fecha_cmv = 		$row2['fecha_cmv'];		
		}
		$resultado_mi = 	(!empty($resultado_mi)) ? $resultado_mi : $no_aplica;
		$resultado_mif = 	(!empty($resultado_mif)) ? $resultado_mif : $no_aplica;
		$resultado_cd = 	(!empty($resultado_cd)) ? $resultado_cd : $no_aplica;
		$fecha_cmv = 		(!empty($fecha_cmv)) ? $fecha_cmv : $no_aplica;

		/** Query con variable igual al nombre de la tabla*/
		$lis_cedula_di_cdi_vig = "select case
										 when v3_fecha_cdi = '0000-00-00' then
										  case
											when v2_fecha_cdi = '0000-00-00' then
											 case
											   when v1_fecha_cdi = '0000-00-00' then
												'No hay fecha'
											   else
												DATE_FORMAT(v1_fecha_cdi, '%d/%m/%Y')
											 end
											else
											 DATE_FORMAT(v2_fecha_cdi, '%d/%m/%Y')
										  end
										 else
										  DATE_FORMAT(v3_fecha_cdi, '%d/%m/%Y')
									   end as fecha_cdcv,
									   case
										 when local_dom_cdi = 't' then
										  'DOMICILIO LOCALIZADO'
										 else
										  case
											when local_dom_cdi = 'f' then
											 'DOMICILIO NO LOCALIZADO'
											else
											 case
											   when local_dom_cdi is null then
												(select desripcion_ccn
												   from cat_ced_nofif_ccn
												  where id_ccn = (select id_ccd
																	from lis_cedula_notif_cdn_vig
																   where consec_ciu = ".$folio."))
											 end
										  end
									   end as local_dom_cdi,
									   case
										 when reconoce_ciu_cdi = 't' then
										  'CUIDADANO RECONOCIDO'
										 else
										  case
											when reconoce_ciu_cdi = 'f' then
											 'CUIDADANO NO RECONOCIDO'
											else
											 case
											   when reconoce_ciu_cdi is null then
												case
												  when reconoce_ciu_dom_cdi = 't' then
												   'CUIDADANO RECONOCIDO'
												  else
												   case
													 when reconoce_ciu_dom_cdi = 'f' then
													  'CUIDADANO NO RECONOCIDO'
												   end
												end
											 end
										  end
									   end reconoce_ciu_cdi,
									   case
										 when vive_ciu_dom_cdi = 't' then
										  'VIVE EN EL DOMICILIO'
										 else
										  case
											when vive_ciu_dom_cdi = 'f' then
											 'NO VIVE EN EL DOMICILIO'
										  end
									   end vive_ciu_dom_cdi
								  from lis_cedula_di_cdi_vig
								 where consec_ciu = ".$folio;
		
		$qry3 = mysql_query($lis_cedula_di_cdi_vig, $conn);
	
		while($row3 = mysql_fetch_array($qry3))
		{
			/** Asignacion de valores resultado del query anterior*/
			$fecha_cdcv = 		$row3['fecha_cdcv'];
			$local_dom_cdi = 	utfChCase($row3['local_dom_cdi'], true);
			$reconoce_ciu_cdi = utfChCase($row3['reconoce_ciu_cdi'], true);
			$vive_ciu_dom_cdi = utfChCase($row3['vive_ciu_dom_cdi'], true);
		}
			
		/** Query con variable igual al nombre de la tabla*/
		$lis_cedula_di_cdi_ant = "select case
										 when v3_fecha_cdi = '0000-00-00' then
										  case
											when v2_fecha_cdi = '0000-00-00' then
											 case
											   when v1_fecha_cdi = '0000-00-00' then
												'No hay fecha'
											   else
												DATE_FORMAT(v1_fecha_cdi, '%d/%m/%Y')
											 end
											else
											 DATE_FORMAT(v2_fecha_cdi, '%d/%m/%Y')
										  end
										 else
										  DATE_FORMAT(v3_fecha_cdi, '%d/%m/%Y')
									   end as fecha_cdca_ant,
									   case
										 when local_dom_cdi = 't' then
										  'DOMICILIO LOCALIZADO'
										 else
										  case
											when local_dom_cdi = 'f' then
											 'DOMICILIO NO LOCALIZADO'
											else
											 case
											   when local_dom_cdi is null then
												(select desripcion_ccn
												   from cat_ced_nofif_ccn
												  where id_ccn = (select id_ccd
																	from lis_cedula_notif_cdn_ant
																   where consec_ciu = ".$folio."))
											 end
										  end
									   end as local_dom_cdi_ant,
									   case
										 when reconoce_ciu_cdi = 't' then
										  'CUIDADANO RECONOCIDO'
										 else
										  case
											when reconoce_ciu_cdi = 'f' then
											 'CUIDADANO NO RECONOCIDO'
											else
											 case
											   when reconoce_ciu_cdi is null then
												case
												  when reconoce_ciu_dom_cdi = 't' then
												   'CUIDADANO RECONOCIDO'
												  else
												   case
													 when reconoce_ciu_dom_cdi = 'f' then
													  'CUIDADANO NO RECONOCIDO'
												   end
												end
											 end
										  end
									   end reconoce_ciu_cdi_ant,
									   case
										 when vive_ciu_dom_cdi = 't' then
										  'VIVE EN EL DOMICILIO'
										 else
										  case
											when vive_ciu_dom_cdi = 'f' then
											 'NO VIVE EN EL DOMICILIO'
										  end
									   end vive_ciu_dom_cdi_ant
								  from lis_cedula_di_cdi_ant
								 where consec_ciu = ".$folio;
	
		$qry4 = mysql_query($lis_cedula_di_cdi_ant, $conn);
	
		while($row4 = mysql_fetch_array($qry4))
		{
			/** Asignacion de valores resultado del query anterior*/			
			$fecha_cdca_ant = 		$row4['fecha_cdca_ant'];
			$local_dom_cdi_ant = 	utfChCase($row4['local_dom_cdi_ant'], true);
			$reconoce_ciu_cdi_ant = utfChCase($row4['reconoce_ciu_cdi_ant'], true);
			$vive_ciu_dom_cdi_ant = utfChCase($row4['vive_ciu_dom_cdi_ant'], true);		
		}
		if(!$con_registro_anterior){
				$fecha_cdca_ant = 		 $no_aplica;
				$local_dom_cdi_ant = 	 $no_aplica;
		}
				
		/** Query con variable igual al nombre de la tabla*/
		$lis_cedula_notif_cdn_vig = "select DATE_FORMAT(cnv.fec_not_cdn, '%d/%m/%Y') as fec_not_cdn,
										   cnv.id_ccd,
										   concat_ws(' ', cn.desripcion_ccn, cnv.txt_ccd_cdn) as resultado_cncv
									  from lis_cedula_notif_cdn_vig cnv, cat_ced_nofif_ccn cn
									 where cnv.consec_ciu = ".$folio."
									   and cn.id_ccn = cnv.id_ccd";		
									   
		$qry5 = mysql_query($lis_cedula_notif_cdn_vig, $conn);
	
		while($row5 = mysql_fetch_array($qry5))
		{
			/** Asignacion de valores resultado del query anterior*/
			$fec_not_cdn = 		$row5['fec_not_cdn'];
			$id_ccd = 			$row5['id_ccd'];
			$resultado_cncv = 	utfChCase($row5['resultado_cncv'], true);
		}		
					
		/** Query con variable igual al nombre de la tabla*/
		$lis_cedula_notif_cdn_ant = "select DATE_FORMAT(cna.fec_not_cdn, '%d/%m/%Y') as fec_not_cdn_ant,
										   cna.id_ccd as id_ccd_ant ,
										   concat_ws(' ', cn.desripcion_ccn, cna.txt_ccd_cdn) as resultado_cncv_ant
									  from lis_cedula_notif_cdn_ant cna, cat_ced_nofif_ccn cn
									 where cna.consec_ciu = ".$folio."
									   and cn.id_ccn = cna.id_ccd";

		$qry6 = mysql_query($lis_cedula_notif_cdn_ant, $conn);
	
		while($row6 = mysql_fetch_array($qry6))
		{
			/** Asignacion de valores resultado del query anterior*/
			$fec_not_cdn_ant = 		$row6['fec_not_cdn_ant'];
			$id_ccd_ant =			$row6['id_ccd_ant'];
			$resultado_cncv_ant = 	utfChCase($row6['resultado_cncv_ant'], true);
		}
		if(!$con_registro_anterior){
				$fec_not_cdn_ant 		= $no_aplica;
				$id_ccd_ant 			= $no_aplica;
				$resultado_cncv_ant 	= $no_aplica;
		}
		
		/** Query con variable igual al nombre de la tabla*/							   
		$lis_cuestionario_aclara_cua_ant = "select DATE_FORMAT(fec_entrevista_cua, '%d/%m/%Y') as fec_entrevista_cua,
													case
													 when comprobante_dom_cua = 't' then
													  'SE REALIZÓ ENTREVISTA CON EL CIUDADANO.'
													 else
													  'NO SE PRESENTÓ EL CIUDADANO, SE LEVANTÓ ACTA ADMINISTRATIVA.'
												   end resultado_entrevista_ant,
												   case
													 when comprobante_dom_cua = 't' then
													  concat_ws(' ', 'ACREDITÓ DOMICILIO', txt_comprobante_dom_cua)
													 else
													  'NO ACREDITÓ DOMICILIO'
												   end comprobante_dom_cua
											  from lis_cuestionario_aclara_cua_ant
											 where consec_ciu = ".$folio;
		
		$qry7 = mysql_query($lis_cuestionario_aclara_cua_ant, $conn);
	
		$num_rows3 = mysql_num_rows($qry7);
		
		/**Si el query obtiene registros se tomaran los valores del mismo, de lo contrario se hara la busqueda en otra tabla diferente*/
		if($num_rows3 != 0){
			while($row7 = mysql_fetch_array($qry7))
			{
				/** Asignacion de valores resultado del query anterior*/
				$resultado_entrevista_ant = $row7['resultado_entrevista_ant'];
				$fec_entrevista_cua = 		$row7['fec_entrevista_cua'];
				$comprobante_dom_cua = 		$row7['comprobante_dom_cua'];
			}
		}else{
			/** Query con variable igual al nombre de la tabla*/
			$lis_acta_adm_ant = "select 'NO SE PRESENTÓ EL CIUDADANO, SE LEVANTÓ ACTA ADMINISTRATIVA.' as resultado_entrevista_ant,
										 DATE_FORMAT(fecha_inicio_adm, '%d/%m/%Y') as fec_entrevista_cua,
										 'NO ACREDITÓ DOMICILIO' as comprobante_dom_cua
								  from lis_acta_adm_ant
								 where consec_ciu = ".$folio; 	
			
			$qry8 = mysql_query($lis_acta_adm_ant, $conn);
		
			while($row8 = mysql_fetch_array($qry8))
			{
				/** Asignacion de valores resultado del query anterior*/
				$resultado_entrevista_ant = $row8['resultado_entrevista_ant'];
				$fec_entrevista_cua = 		$row8['fec_entrevista_cua'];
				$comprobante_dom_cua = 		$row8['comprobante_dom_cua'];
			}
		
		}
		if(!$con_registro_anterior){
				$resultado_entrevista_ant =	$no_aplica;
				$fec_entrevista_cua =		$no_aplica;
				$comprobante_dom_cua = 		$no_aplica;
		}

		/** Query con variable igual al nombre de la tabla*/
		$lis_cuestionario_aclara_cua_vig = "select DATE_FORMAT(fec_entrevista_cua, '%d/%m/%Y') as fec_entrevista_cua_vig,
												   case
													 when comprobante_dom_cua = 't' then
													  'SE REALIZÓ ENTREVISTA CON EL CIUDADANO'
													 else
													  'NO SE PRESENTÓ EL CIUDADANO, SE LEVANTÓ ACTA ADMINISTRATIVA.'
												   end resultado_entrevista_vig,
												   case
													 when comprobante_dom_cua = 't' then
													  concat_ws(' ', 'ACREDITÓ DOMICILIO', txt_comprobante_dom_cua)
													 else
													  'NO ACREDITÓ DOMICILIO'
												   end comprobante_dom_cua_vig
											  from lis_cuestionario_aclara_cua_vig
											 where consec_ciu = ".$folio;
			
		$qry9 = mysql_query($lis_cuestionario_aclara_cua_vig, $conn);
		$num_rows2 = mysql_num_rows($qry9);
		
		/**Si el query obtiene registros se tomaran los valores del mismo, de lo contrario se hara la busqueda en otra tabla diferente*/
		if($num_rows2 != 0){
	
			while($row9 = mysql_fetch_array($qry9))
			{
				/** Asignacion de valores resultado del query anterior*/
				$resultado_entrevista_vig = $row9['resultado_entrevista_vig'];
				$fec_entrevista_cua_vig = 	$row9['fec_entrevista_cua_vig'];
				$comprobante_dom_cua_vig = 	$row9['comprobante_dom_cua_vig'];
			}
		}else{
			
			/** Query con variable igual al nombre de la tabla*/
			$lis_acta_adm_vig = "select 'NO SE PRESENTÓ EL CIUDADANO, SE LEVANTÓ ACTA ADMINISTRATIVA.' as resultado_entrevista_vig,
										DATE_FORMAT(fecha_inicio_adm, '%d/%m/%Y') as fec_entrevista_cua_vig,
										'NO ACREDITÓ DOMICILIO' as comprobante_dom_cua_vig
								  from lis_acta_adm_vig
								 where consec_ciu = ".$folio;		
			
			$qry10 = mysql_query($lis_acta_adm_vig, $conn);
		
			while($row10 = mysql_fetch_array($qry10))
			{
				/** Asignacion de valores resultado del query anterior*/
				$resultado_entrevista_vig = $row10['resultado_entrevista_vig'];
				$fec_entrevista_cua_vig = 	$row10['fec_entrevista_cua_vig'];
				$comprobante_dom_cua_vig = 	$row10['comprobante_dom_cua_vig'];

			}
		}

		/**Definicion de leyenda de Estatus y Dictamen*/
		switch ($estatus_ciu) {
			case 1:
				#Si es Regular:
				$dictamen_result = "SE ENVÍA EL EXPEDIENTE PARA ARCHIVO";
				$dictamen_estatus = "REGULAR";
				break;
			case 2:
				#Si es presuntamente Irregular se manda a STN:
				$dictamen_result = "SE ENVÍA EL EXPEDIENTE A LA SECRETARÍA TÉCNICA NORMATIVA PARA EL ANÁLISIS JURÍDICO";
				$dictamen_estatus = "PRESUNTAMENTE IRREGULAR";
				break;
		}	
		

		/** Variable contenedora del código HTML para crear el reporte PDF*/
		$html .= '
<table width="100%" border="0">
  <tr>
    <td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td colspan="3">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				  <tr>
					<td width="170px"><img src="ine.jpg" /></td>
					<td align="center"><strong class="titulo">C&Eacute;DULA PARA EL AN&Aacute;LISIS DE REGISTROS CON DOMICILIO <br />PRESUNTAMENTE IRREGULAR Y MEDIOS DE IDENTIDAD</strong> </td>
				  </tr>
				  <tr>
					<td colspan="2">
						<strong class="titulo">I. DATOS GENERALES DEL REGISTRO</strong>
						<br />
						<br />
						<label>1. FOLIO: '.$folio.' </label><br /><br />
						<label>2. REFERENCIA: '.$referencia_ciu.'</label><br /><br />
						<label>3. NOMBRE COMPLETO: <strong>'.$nom_comp_ciu.'</strong></label><br /><br />
						<label>4. CLAVE DE ELECTOR: '.$clave_elec_ciu.' </label><br /><br /><br />
					</td>
				  </tr>
				</table>
			</td>
		  </tr>
		  <tr>
			<td width="100%" valign="top" colspan="3" >
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				  <tr>
					<td width="49%" valign="top" class="caja">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" >
						  <tr>
							<td rowspan="6" width="20px">&nbsp;</td>
							<td colspan="5" align="center"><strong class="titulo">II. DATOS DE DOMICILIO VIGENTE</strong></td>
							<td rowspan="6" width="5px">&nbsp;</td>
						  </tr>
						  <tr>
							<td width="25%">Entidad:</td>
							<td class="tds">'.$ent_act_ciu.'</td>
							<td>&nbsp;</td>
							<td align="right">Distrito:</td>
							<td class="tds">'.$dis_act_ciu.'</td>
						  </tr>
						  <tr>
							<td>Municipio:</td>
							<td class="tds">'.$mun_act_ciu.'</td>
							<td>&nbsp;</td>
							<td align="right">Secci&oacute;n:</td>
							<td class="tds">'.$seccion_act_ciu.'</td>
						  </tr>
						  <tr>
							<td>Manzana Electoral: </td>
							<td class="tds">'.$manzana_act_ciu.'</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td colspan="5">Domicilio: '.$calle_act_ciu.' '.$num_ext_act_ciu.' '.$num_int_act_ciu.' '.$colonia_act_ciu.' '.$cp_act_ciu.' </td>
						  </tr>
						  <tr>
							<td colspan="5">&nbsp;</td>
						  </tr>
						</table>
					</td>
					<td width="2%">&nbsp;</td>
					<td width="49%" valign="top" class="caja">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" >
						  <tr>
							<td rowspan="6" width="5px">&nbsp;</td>
							<td colspan="5" align="center"><strong class="titulo">III. DATOS DE DOMICILIO ANTERIOR</strong></td>
							<td rowspan="6" width="20px">&nbsp;</td>
						  </tr>
						  <tr>
							<td width="25%">Entidad:</td>
							<td class="tds">'.$ent_ant_ciu.'</td>
							<td>&nbsp;</td>
							<td align="right">Distrito:</td>
							<td class="tds">'.$dis_ant_local_ciu.'</td>
						  </tr>
						  <tr>
							<td>Municipio:</td>
							<td class="tds">'.$mun_ant_ciu.'</td>
							<td>&nbsp;</td>
							<td align="right">Secci&oacute;n:</td>
							<td class="tds">'.$seccion_ant_ciu.'</td>
						  </tr>
						  <tr>
							<td>Manzana Electoral: </td>
							<td class="tds">'.$manzana_ant_ciu.'</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td colspan="5">Domicilio: '.$calle_ant_ciu.' '.$num_ext_ant_ciu.' '.$num_int_ant_ciu.' '.$colonia_ant_ciu.' '.$cp_ant_ciu.'</td>
						  </tr>
						  <tr>
							<td colspan="5">&nbsp;</td>
						  </tr>
						</table>
					</td>
				  </tr>
				</table>
			</td>
		  </tr>
		  <tr>
			<td colspan="3" style="height:10px"></td>
		  </tr>
		  <tr>
			<td colspan="3">
				<table width="100%" cellpadding="2" cellspacing="0" frame="box" rules="groups" class="caja">
				  <tr>
					<td colspan="4" align="center"><strong class="titulo">IV. AN&Aacute;LISIS DE MEDIOS DE IDENTIDAD Y COMPROBANTE DE DOMICILIO</strong></td>
				  </tr>
				  <tr>
					<td colspan="4">&nbsp;</td>
				  </tr>
				  <tr>
					<td rowspan="5" width="10%"><strong>REVISI&Oacute;N T&Eacute;CNICA</strong></td>
					<td align="center" width="30%"><u>MEDIO DE IDENTIFICACI&Oacute;N</u></td>
					<td align="center" width="30%"><u>IDENTIFICACI&Oacute;N CON FOTOGRAF&Iacute;A</u></td>
					<td align="center" width="30%"><u>COMPROBANTE DE DOMICILIO </u></td>
				  </tr>
				  <tr>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td>Referencia: '.$referencia_ciu.' </td>
					<td>&nbsp;</td>
					<td>Fecha: '.$fecha_cmv.' </td>
				  </tr>
				  <tr>
					<td class="pto_der">Resultado: '.$resultado_mi.' </td>
					<td class="pto_der">Resultado: '.$resultado_mif.' </td>
					<td>Resultado: '.$resultado_cd.' </td>
				  </tr>
				  <tr>
					<td class="pto_der">&nbsp;</td>
					<td class="pto_der">&nbsp;</td>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
					<td rowspan="2" class="pto_arriba"><br /><strong>OPINI&Oacute;N JURIDICA</strong></td>
					<td class="pto_arriba"><br />Referencia: <strong>NO APLICA</strong> </td>
					<td class="pto_arriba"><br />No. Opini&oacute;n: <strong>NO APLICA</strong> </td>
					<td class="pto_arriba"><br />Fecha: <strong>NO APLICA</strong> </td>
				  </tr>
				  
				  <tr>
					<td class="pto_der">Resultado: <strong>NO APLICA</strong><br /><br /> </td>
					<td class="pto_der">Resultado: <strong>NO APLICA</strong><br /><br /> </td>
					<td>Resultado: <strong>NO APLICA</strong> </td>
				  </tr>
				  <tr>
					<td colspan="4">&nbsp;</td>
				  </tr>
				</table>
			</td>
		  </tr>
		  <tr>
			<td colspan="3" style="height:10px"></td>
		  </tr>
		  <tr>
			<td colspan="3">
				<table width="100%" cellpadding="5" cellspacing="0" frame="box" rules="groups" class="caja">
				  <tr>
					<td align="center"><strong class="titulo">V. VISITA EN DOMICILIO VIGENTE</strong></td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
					<td>1. Fecha de entrevista: '.$fecha_cdcv.'</td>
				  </tr>
				  <tr>
					<td>2. Resultado: '.$local_dom_cdi.' '.$reconoce_ciu_cdi.' '.$vive_ciu_dom_cdi.'</td>
				  </tr>
				</table>
			</td>
		  </tr>
		  <tr>
			<td colspan="3" style="height:10px"></td>
		  </tr>
		  <tr>
			<td colspan="3">
				<table width="100%" cellpadding="5" cellspacing="0" frame="box" rules="groups" class="caja">
				  <tr>
					<td align="center"><strong class="titulo">VI. VISITA EN DOMICILIO ANTERIOR</strong></td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
					<td>1. Fecha de entrevista: '.$fecha_cdca_ant.'</td>
				  </tr>
				  <tr>
					<td>2. Resultado: '.$local_dom_cdi_ant.' '.$reconoce_ciu_cdi_ant.' '.$vive_ciu_dom_cdi_ant.'</td>
				  </tr>
				</table>
			</td>
		  </tr>
		</table>
	</td>
  </tr>
</table>

<div style="page-break-after:always;"></div>

<table width="100%" border="0">
  <tr>
    <td>
		<table width="100%" border="0">
		  <tr>
			<td>
				<table width="100%" cellpadding="2" cellspacing="0" frame="box" rules="groups" class="caja">
				  <tr>
					<td colspan="6" align="center"><strong class="titulo">VII. NOTIFICACIÓN PARA ACLARACIÓN DE DATOS</strong></td>
				  </tr>
				  <tr>
					<td colspan="6">&nbsp;</td>
				  </tr>
				  <tr>
					<td colspan="3" align="center" class="pto_der"><strong><u>DOMICILIO VIGENTE</u></strong></td>
					<td colspan="3" align="center"><strong><u>DOMICILIO ANTERIOR</u></strong></td>
				  </tr>
				  <tr>
					<td colspan="3" class="pto_der" height="5px"></td>
					<td colspan="3" height="5px"></td>
				  </tr>
				  <tr>
					<td rowspan="4" width="15%"><strong>DOMICILIO</strong></td>
					<td width="20%">Fecha de notificación:</td>
					<td width="20%" class="pto_der">'.$fec_not_cdn.'</td>
					<td>&nbsp;</td>
					<td width="20%">Fecha de notificación:</td>
					<td width="20%">'.$fec_not_cdn_ant.'</td>
				  </tr>
				  <tr>
					<td colspan="2" height="5px" class="pto_der"></td>
					<td colspan="3" height="5px"></td>
				  </tr>
				  <tr>
					<td>Resultado:</td>
					<td class="pto_der">'.$resultado_cncv.'</td>
					<td>&nbsp;</td>
					<td>Resultado:</td>
					<td>'.$resultado_cncv_ant.'</td>
				  </tr>
				  <tr>
					<td colspan="2" height="5px" class="pto_der"></td>
					<td colspan="3" height="5px"></td>
				  </tr>
				  <tr>
					<td rowspan="4"><strong>MEDIOS DE <br />IDENTIFICACIÓN</strong></td>
					<td>Fecha de notificación:</td>
					<td class="pto_der"><strong>NO APLICA</strong></td>
					<td>&nbsp;</td>
					<td>Fecha de notificación:</td>
					<td><strong>NO APLICA</strong></td>
				  </tr>
				  <tr>
					<td colspan="2" height="5px" class="pto_der"></td>
					<td colspan="3" height="5px"></td>
				  </tr>
				  <tr>
					<td>Resultado:</td>
					<td class="pto_der"><strong>NO APLICA</strong></td>
					<td>&nbsp;</td>
					<td>Resultado:</td>
					<td><strong>NO APLICA</strong></td>
				  </tr>
				  <tr>
					<td colspan="2" height="5px" class="pto_der"></td>
					<td colspan="3" height="5px"></td>
				  </tr>
				</table>
			</td>
		  </tr>
		  <tr>
			<td height="5px"></td>
		  </tr>
		  <tr>
			<td>
				<table width="100%" cellpadding="2" cellspacing="0" frame="box" rules="groups" class="caja">
				  <tr>
					<td colspan="4" align="center"><strong class="titulo">VIII. ENTREVISTA DE ACLARACIÓN DEL DOMICILIO EN OFICINA DISTRITAL</strong></td>
				  </tr>
				  <tr>
					<td colspan="4">&nbsp;</td>
				  </tr>
				  <tr>
					<td rowspan="4" width="15%" class="pto_abajo"  valign="top"><strong>ACLARACIÓN<br />DEL DOMICILIO<br />ANTERIOR</strong></td>
					<td colspan="3">Resultado de la entrevista: '.$resultado_entrevista_ant.'</td>
				  </tr>
				  <tr>
					<td colspan="3">Fecha: '.$fec_entrevista_cua.'</td>
				  </tr>
				  <tr>
					<td colspan="3">Acreditación del domicilio actual:'.$comprobante_dom_cua.'</td>
				  </tr>
				  <tr>
					<td colspan="3" class="pto_abajo">&nbsp;</td>
				  </tr>
				  <tr>
					<td colspan="4">&nbsp;</td>
				  </tr>
				  <tr>
					<td rowspan="3" valign="top"><strong>ACLARACIÓN<br />DEL DOMICILIO<br />VIGENTE</strong></td>
					<td colspan="3">Resultado de la entrevista: '.$resultado_entrevista_vig.'</td>
				  </tr>
				  <tr>
					<td colspan="3">Fecha: '.$fec_entrevista_cua_vig.'</td>
				  </tr>
				  <tr>
					<td colspan="3">Acreditación del domicilio actual: '.$comprobante_dom_cua_vig.'</td>
				  </tr>
				  <tr>
					<td colspan="4">&nbsp;</td>
				  </tr>
				  <tr>
					<td rowspan="6"><strong>ACLARACIÓN<br />DEL MEDIO DE<br />IDENTIFICACIÓN</strong></td>
				    <td class="pto_der" width="20%"><u>MEDIO DE<br />IDENTIFICACIÓN</u></td>
				    <td class="pto_der">&nbsp;&nbsp;<u>IDENTIFICACIÓN CON FOTOGRAFÍA</u></td>
				    <td>&nbsp;&nbsp;<u>COMPROBANTE DE DOMICILIO</u></td>
				  </tr>
				  <tr>
				    <td class="pto_der">&nbsp;</td>
			        <td class="pto_der">&nbsp;</td>
				    <td>&nbsp;</td>
				  </tr>
				  <tr>
				    <td class="pto_der">Resultado: <strong>NO APLICA</strong></td>
			        <td class="pto_der">&nbsp;&nbsp;Resultado: <strong>NO APLICA</strong></td>
				    <td>&nbsp;&nbsp;Resultado: <strong>NO APLICA</strong></td>
				  </tr>
				  <tr>
				    <td>&nbsp;</td>
			        <td>&nbsp;</td>
				    <td>&nbsp;</td>
				  </tr>
				  <tr>
				    <td>Fecha: <strong>NO APLICA</strong></td>
			        <td>&nbsp;</td>
				    <td>&nbsp;</td>
				  </tr>
				  <tr>
				    <td>&nbsp;</td>
			        <td>&nbsp;</td>
				    <td>&nbsp;</td>
				  </tr>
				  <tr>
					<td colspan="4">&nbsp;</td>
				  </tr>
				</table>
			</td>
		  </tr>
		  <tr>
			<td height="5px"></td>
		  </tr>
		  <tr>
			<td valign="top">
				<table width="100%" cellpadding="0" cellspacing="0" frame="box" rules="groups" class="caja">
				  <tr>
					<td align="center"><strong class="titulo">IX. ANÁLISIS REGISTRAL</strong></td>
				  </tr>
				</table>
			</td>
		  </tr>
		  <tr>
			<td valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				  <tr>
					<td width="49%" valign="top" class="caja">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						  <tr>
							<td>Resultado:</td>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td colspan="2" height="10px"></td>
						  </tr>
						  <tr>
							<td>DOMICILIO:</td>
							<td><strong>'.$dictamen_estatus.'</strong></td>
						  </tr>
						  <tr>
							<td colspan="2" height="10px"></td>
						  </tr>
						  <tr>
							<td>MEDIO DE IDENTIFICACIÓN:</td>
							<td>'.$resultado_mi.'</td>
						  </tr>
						  <tr>
							<td colspan="2" height="10px"></td>
						  </tr>
						  <tr>
							<td>IDENTIFICACIÓN CON FOTOGRAFÍA:</td>
							<td>'.$resultado_mif.'</td>
						  </tr>
						  <tr>
							<td colspan="2" height="10px"></td>
						  </tr>
						  <tr>
							<td>COMPROBANTE DE DOMICILIO:</td>
							<td>'.$resultado_cd.'</td>
						  </tr>
						  <tr>
							<td colspan="2" height="10px" class="pto_abajo"></td>
						  </tr>
						  <tr>
							<td colspan="2" height="10px"></td>
						  </tr>
						  <tr>
							<td colspan="2" style="text-align : justify;"><strong>'.$dictamen_result.'</strong></td>
						  </tr>
						</table>
					</td>
					<td width="2%">&nbsp;</td>
					<td width="49%" valign="top" class="caja">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td align="center">C. JULIO RIVERO ANTUNA</td>
						  </tr>
						  <tr>
							<td align="center">SUBDIRECTOR DE VERIFICACIÓN EN CAMPO</td>
						  </tr>
						</table>
					</td>
				  </tr>
				</table>
			</td>
		  </tr>
		  <tr>
			<td><br />
			<strong class="titulo">FOLIO:'.$folio.'</strong></td>
		  </tr>
		</table>
	</td>
  </tr>
</table>';

		/** Regla para imprimir el reporte segun la cantidad de Hojas.*/
		if($reg_pag <= $hojas){
			//echo $reg_pag." mayor que ".$hojas."<br>";
			$num_doctos++;
			if($num_doctos == $reg_pag){
				
				/** Instancia de la Clase DOMPDF (Objeto)*/
				$dompdf = new DOMPDF();	
				$nombre_archivo = "DomIrreg_AR_".$folio.".pdf";
				
				$dompdf->load_html($html);
				$dompdf->render();
				$pdf = $dompdf->output();
				/** Asi se guarda el PDF en algún directorio del servidor*/
				file_put_contents($directorio."/".$nombre_archivo, $pdf);
				/** Se agrega el archivo PDF al ZIP*/
				$zipfile->add_file(implode("",file($directorio."/".$nombre_archivo)), $nombre_archivo);
				/**Limpia variable - libera memoria*/
				unset($dompdf);
				/** Para interpretar la salida del archivo ZIP*/
				header("Content-type: application/octet-stream");
				header("Content-disposition: attachment; filename=DomIrreg_AR_"."E".$id_ent."_D".$id_dis."_M".$id_mun.".zip");
				$conta++;
				$html = '
<style>

.header { position: fixed; left: 0; top: 10px; right: 47.5px; height: 0px; text-align: right; }
.page:after { content: counter(page, decimal); }

@page {
	margin-top: 30px;
	margin-left: 37.5px;
	margin-right: 37.5px;
	margin-buttom: 0px; 
}
			
table{
font-size:12px;
font-family:Arial, Helvetica, sans-serif;
}

.titulo{
font-size:16px;
}

.caja{
border: 1px solid #000000;
}

.tds{
border-bottom-color:#000000;
border-bottom-width:2px;
border-bottom-style:solid;
border-top-color:#CCCCCC;
border-top-style:solid;
border-top-width:1px; 
border-left-color:#CCCCCC; 
border-left-width:1px;
border-left-style:solid;
border-right-color:#000000; 
border-right-width:2px;
border-right-style:solid;
}

.pto_der{
border-right: 1px dotted #000000; 
}

.pto_abajo{
border-bottom: 1px dotted #000000; 
}

.pto_arriba{
border-top: 1px dotted #000000; 
}
</style>';

			}
		}else{
			//echo $reg_pag." mayor que ".$hojas."<br>" ;
			$num_doctos++;
			if($num_doctos == $hojas){
				//echo "documento ".$num_doctos." hoja".$hojas."<br>";
				/** Instancia de la Clase DOMPDF (Objeto)*/
				$dompdf = new DOMPDF();	
				$nombre_archivo = "DomIrreg_AR_".$folio.".pdf";
				
				$dompdf->load_html($html);
				$dompdf->render();
				$pdf = $dompdf->output();
				/** Asi se guarda el PDF en algún directorio del servidor*/
				file_put_contents($directorio."/".$nombre_archivo, $pdf);
				/** Se agrega el archivo PDF al ZIP*/
				$zipfile->add_file(implode("",file($directorio."/".$nombre_archivo)), $nombre_archivo);
				/**Limpia variable - libera memoria*/
				unset($dompdf);
				/** Para interpretar la salida del archivo ZIP*/
				header("Content-type: application/octet-stream");
				header("Content-disposition: attachment; filename=DomIrreg_AR_"."E".$id_ent."_D".$id_dis."_M".$id_mun.".zip");
				$conta++;
				$html = '<style>

.header { position: fixed; left: 0; top: 10px; right: 47.5px; height: 0px; text-align: right; }
.page:after { content: counter(page, decimal); }

@page {
	margin-top: 30px;
	margin-left: 37.5px;
	margin-right: 37.5px;
	margin-buttom: 0px; 
}
			
table{
font-size:12px;
font-family:Arial, Helvetica, sans-serif;
}

.titulo{
font-size:16px;
}

.caja{
border: 1px solid #000000;
}

.tds{
border-bottom-color:#000000;
border-bottom-width:2px;
border-bottom-style:solid;
border-top-color:#CCCCCC;
border-top-style:solid;
border-top-width:1px; 
border-left-color:#CCCCCC; 
border-left-width:1px;
border-left-style:solid;
border-right-color:#000000; 
border-right-width:2px;
border-right-style:solid;
}

.pto_der{
border-right: 1px dotted #000000; 
}

.pto_abajo{
border-bottom: 1px dotted #000000; 
}

.pto_arriba{
border-top: 1px dotted #000000; 
}
</style>';
				
				//Logica de regla de hojas mostradas.
				$num_doctos = 0;
				$reg_pag = $reg_pag - $hojas;
			}
		}		
	}// while
	
	// Renombrar Hoja del XLS
	$objPHPExcel_create->getActiveSheet()->setTitle('Nominativo');
	 
	// Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
	$objPHPExcel_create->setActiveSheetIndex(0);
	
	/** Arreglo con el estilo de la tabla de XLS que se implementara posteriormente*/
	$styleArray = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          )
      );
	  
	/** Estilo del encabezado de la tabla del archivo XLS*/
	$objPHPExcel_create->getActiveSheet()->getStyle('A1:E1')->applyFromArray( array( 'font' => array( 'name' => 'Arial', 'bold' => true, 'italic' => false, 'strike' => false, 'color' => array( 'rgb' => 'FFFFFF' ) ), 'borders' => array('allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) ), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '000033')) ) );
	/** Estilo de la tabla del archivo XLS*/
	$objPHPExcel_create->getActiveSheet()->getStyle('A2:E'.$fila_excel)->applyFromArray($styleArray);
	  	 
	/** Se especifica el tipo de archivo a guardar*/
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel_create, 'Excel2007');
	$objWriter->save($directorio."/Nominativo.xls");
	
	/** Se agrega el documento XLS al ZIP*/
	$zipfile->add_file(implode("",file($directorio."/Nominativo.xls")), "Nominativo.xls");
	
	/** Forzamos su descarga del ZIP*/
	echo $zipfile->file();

	foreach(glob($directorio."/*.*") as $archivos_carpeta){  
		unlink($archivos_carpeta);     // Eliminamos todos los archivos de la carpeta hasta dejarla vacia 
	}  
	
	rmdir($directorio);         // Eliminamos la carpeta vacia  
	
}else{
	/** Si no encuentra ningun registro para crear un PDF*/
	echo  $html = '<script language="javascript" type="text/javascript"> alert("No se encuentran registros en la Base de Datos"); window.close(); </script>';

}// if

$conn = null; // Cerramos la conexion persistente


function txtlog($data, $dir=''){
	$file = 'tmp_'.date('Yma-His').'.txt';
	$fp=fopen($dir.$file,"x");
	fwrite($fp,$data);
	fclose($fp);
	return true;
/*O3M*/
}
?>