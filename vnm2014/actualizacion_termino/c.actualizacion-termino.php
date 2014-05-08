<?php $debug_pdf = false;
##Includes
$raiz = "";
require_once($raiz.'common/php/class.pdo.php');
require_once($raiz.'common/php/pdf/fpdf.php');
require_once($raiz.'common/pdf/pdf.actualizacion-termino.php');
##Delete tmp folder
limpiar_tmp('tmp/','zip',5);
limpiar_tmp('tmp/','rtf',5);
limpiar_tmp('tmp/','pdf',5);
##Business
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
#Acciones
$actionList = array(save => 'save');
$Action = strtolower($v_t);
#if(in_array(strtolower($v_t),$Actions)){
if($v_auth && $v_t && !in_array($Action,$actionList)){	
	##SQL	 
	if($v_ent){$Filtro .= " and a.id_edo='$v_ent'";}
	if($v_dto){$Filtro .= " and a.id_dtto='$v_dto'";}
	if($v_secc){$Filtro .= " and a.seccion='$v_secc'";}
	if($v_mza){$Filtro .= " and a.mz='$v_mza'";}
	if($v_soloPendientes){$soloPendientes = " HAVING capturada=0 ";}
	if(strtolower($v_t)!='grupo-titulo'){
	##DETALLE		
		$sql = "SELECT
				 a.edo_nombre AS estado
				,a.id_edo AS id_ent
				,a.edo_nombre AS entidad
				,a.id_dtto AS id_dis
				,a.seccion
				,a.loc_nombre AS localidad
				,a.loc_clave AS id_loc
				,a.mz AS manzana
				,a.consecutivo as folio
				,a.consecutivo
				,'Date' AS termino
				,IF(tmp_captura.consecutivo IS NOT NULL OR c.consecutivo IS NOT NULL,1,0) AS capturada
				,tmp_captura.nombre_ciudadano
				,tmp_captura.tbl
				,'' AS reemplazo
				,'' AS id_remplaza_a
				FROM actualizacion AS a
				LEFT JOIN (
					SELECT consecutivo, id_estado AS entidad, id_dtto AS distrito, nombre_ciudadano, 'captura_actualizacion' AS tbl
					FROM captura_actualizacion
				) AS tmp_captura ON a.consecutivo=tmp_captura.consecutivo
				LEFT JOIN rep_termino_act AS c ON a.consecutivo=c.consecutivo
				WHERE 1 $Filtro
				$soloPendientes
				ORDER BY capturada DESC, a.id_edo, a.id_dtto, a.seccion, a.mz, a.consecutivo ASC;";
		$db = new db();
		$Rows = $db->SQLQuery($sql);
		$Result = $Rows;
	}
	if(strtolower($v_t)=='grupo-titulo'){
	##GRUPOS
		$sql = "SELECT
				 a.edo_nombre AS estado
				,a.id_edo AS id_ent
				,a.id_dtto AS id_dis
				,a.seccion
				,a.loc_nombre AS localidad
				,a.mz AS manzana
				,'Date' AS termino
				,COUNT(*) as TotRegs
				,SUM(IF(a.estatus=1,1,0)) AS TotEstatus1
				,SUM(IF(a.estatus=2,1,0)) AS TotEstatus2
				,SUM(IF(a.estatus=3,1,0)) AS TotEstatus3
				,IF(tmp_captura.consecutivo IS NOT NULL  OR c.consecutivo IS NOT NULL,'Y','N') AS CapFlag
				,SUM(IF(tmp_captura.consecutivo IS NOT NULL  OR c.consecutivo IS NOT NULL,1,0)) AS TotCapturas
				,(COUNT(*) - IFNULL(SUM(IF(tmp_captura.consecutivo IS NOT NULL  OR c.consecutivo IS NOT NULL,1,0)),0)) AS TotPendientes
				FROM actualizacion AS a
				LEFT JOIN (
					SELECT consecutivo, id_estado AS entidad, id_dtto AS distrito, nombre_ciudadano, 'captura_actualizacion' AS tbl
					FROM captura_actualizacion
				) AS tmp_captura ON a.consecutivo=tmp_captura.consecutivo
				LEFT JOIN rep_termino_act AS c ON a.consecutivo=c.consecutivo
				WHERE 1 $Filtro
				GROUP BY  a.id_edo, a.id_dtto, a.seccion, a.mz ASC				
				HAVING TotPendientes>0
				;";
		$db = new db();
		$Rows = $db->SQLQuery($sql);
		$Result = $Rows;
	}
	$Registros = count($Rows);
	if($Registros){				
		##File Name		
		$rutaDocs=$raiz.'tmp/';
		$ruta = $rutaDocs;	
		$fEnt = ceros($v_ent,2);
		$fDto = ceros($v_dto,2);
		$fSecc = ceros($v_seccion,4);
		$fMzna = ceros($v_manzana,4);
		$nuevoDoc='VNM2014_ACT_CONCLUIDAS_E'.$fEnt.'_D'.$fDto.'_'.date('Ymd-His');
		if(strtolower($v_t)=='pdf'){
		##Guarda de fecha de término
			$sql ="SELECT timestamp as termino FROM rep_termino_act WHERE ent='$v_ent' AND dto='$v_dto' LIMIT 1;";
			$db = new db();
			$termino = $db->SQLQuery($sql);	
			if(!$termino){
				$sql = "INSERT INTO rep_termino_act SET 
						 tipo='REPORTE'
						,zona='URBANA'
						,ent='$v_ent'
						,dto='$v_dto'
						,secc='$v_secc'
						,mza='$v_mza'
						,id_usuario='$Usuario'
						,timestamp=NOW()
						";		
				$db = new db();
				$db->SQLDo($sql);
			}	
		##PDF => Crea archivo PDF
			$docPDF=$nuevoDoc.'.pdf';
			$pdf=new PDF('P','mm','letter');
		    $title="COORDINACIÓN DE OPERACIÓN EN CAMPO";
		    $pdf->SetTitle($title);
		    $pdf->SetAuthor('INE - DDVC'); 
		    $pdf->AliasNbPages('TotalPages');
		    @$pdf->PrintDatos($Rows, $termino);
		    @$pdf->Output($rutaDocs.$docPDF);
		    $Result = array($v_t, $rutaDocs, $docPDF);
		    if($debug_pdf){	
		    	//ToDebug
		    	echo "<html><head><script>document.location='".$rutaDocs.$docPDF."';</script></head></html>"; 
			}
		}		
	}else{
		$Result = array('CERO', 'No existen registros para generar este documento');
	}
	##Print Result
	header('Content-Type: application/json; charset=utf-8'); 
	echo json_encode($Result);
}elseif($v_auth && $Action==$actionList[save]){	
#validación de recepción de datos vía Ajax
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$sql = "SELECT consecutivo FROM rep_termino_act WHERE consecutivo='$v_folio'";
		$db = new db();
		$Rows = $db->SQLQuery($sql);
		$Duplicado = count($Rows);
		if(!$Duplicado){
			$Result = 0;
			$reemplazo = ($v_reemplazo)?'SI':'NO';
			$Usuario = "";
			#Archivo
			if($v_archivo){
				$filePath = 'docs/docs.act_termino/';
				$fileName = 'doc_'.$v_folio;
				$file = $_FILES[$fileName]['name'];
				$tipo = explode('.',$file);
				$fileTmp = $_FILES[$fileName]['tmp_name'];
				$fileRename = 'VNM2014_J_ACT_'.$v_folio.'.'.$tipo[count($tipo)-1];
			}
			$sql = "INSERT INTO rep_termino_act SET 
					 tipo='$v_tipo'
					,zona='$v_zona'
					,ent='$v_ent'
					,dto='$v_dto'
					,secc='$v_secc'
					,mza='$v_mza'
					,consecutivo='$v_folio'
					,reemplazo='$reemplazo'
					,justificacion='$v_justificacion'
					,archivo='$v_archivo'
					,archivo_nombre='$fileRename'
					,archivo_ruta='$filePath'
					,id_usuario='$Usuario'
					,timestamp=NOW()
					";		
			$db = new db();
			if($db->SQLDo($sql)){
				$Result = 1;
				if($v_archivo){
					$Result = uploadFile($fileTmp,$filePath,$fileRename);			
				}
				sleep(1);			
			}
			echo $Result;
		}else{
			echo "duplicado";
		}
	}else{
		throw new Exception("Error Processing Request", 1);	
		echo false;			
	}	
}elseif($debug_pdf){
	#debug
	$url = "c.actualizacion-termino.php?auth=1&t=lista&ent=24&dto=2";
	echo "<a href='".$url."' target='blank'>".$url."</a>";
}else{echo false;}


function uploadFile($fileTmp, $filePath='tmp/', $fileName){
/**
*	Descripción:	Recibe archivo y lo sube al servidor
*	@author:		Oscar Maldonado
*/
	if(!is_dir($filePath)){
	    mkdir($filePath, 0777);
	}
	if ($fileTmp && move_uploaded_file($fileTmp,$filePath.$fileName)){
	   sleep(3);
	   return 1;
	}else{ 
		return 0; 
	}
}
/*O3M*/
?>