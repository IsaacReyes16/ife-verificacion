<?php $debug_pdf = false;
##Includes
$raiz = "";
require_once($raiz.'common/php/class.pdo.php');
require_once($raiz.'common/php/pdf/fpdf.php');
require_once($raiz.'common/pdf/pdf.reporte-termino.php');
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
	if($v_ent){$Filtro .= " and a.id_ent='$v_ent'";}
	if($v_dto){$Filtro .= " and a.id_dis='$v_dto'";}
	if($v_secc){$Filtro .= " and a.seccion='$v_secc'";}
	if($v_mza){$Filtro .= " and a.manzana='$v_mza'";}
	if($v_soloPendientes){$soloPendientes = " HAVING capturada=0 ";}
	if(strtolower($v_t)!='grupo-titulo'){
	##DETALLE		
		$sql = "SELECT
				 a.estado
				,a.id_ent
				,a.estado as entidad
				,a.id_dis
				,a.seccion
				,a.localidad
				,a.id_loc
				,a.manzana
				,a.folio
				,a.consecutivo
				,'Date' AS termino
				,IF(b.folio IS NOT NULL OR c.folio IS NOT NULL, 1,0) as capturada
				,a.es_remplazo as reemplazo
				,b.id_remplaza_a
				FROM viviendas_seleccionadas AS a
				LEFT JOIN captura_vivienda_urbana_a AS b ON a.folio=b.folio
				LEFT JOIN rep_termino AS c ON a.folio=c.folio
				WHERE 1 $Filtro 
				GROUP BY a.FOLIO
				$soloPendientes ;";
		$db = new db();
		$Rows = $db->SQLQuery($sql);
		$Result = $Rows;
	}
	if(strtolower($v_t)=='grupo-titulo'){
	##GRUPOS
		$sql = "SELECT
				 a.estado
				,a.id_ent
				,a.id_dis
				,a.seccion
				,a.localidad
				,a.manzana
				,'Date' AS termino
				,COUNT(DISTINCT(a.folio)) as TotRegs 
				,SUM(IF(IFNULL(a.es_remplazo,0)=0,1,0)) as TotRequeridos 
				,SUM(IF(IFNULL(a.es_remplazo,0)=1,1,0)) as TotReemplazosOpt
				,SUM(IF(b.folio IS NOT NULL OR c.folio IS NOT NULL,1,0)) as TotCapturadas 
				,(SUM(IF((b.folio IS NOT NULL AND b.reemplazo=0 ) OR c.folio IS NOT NULL ,1,0))) as TotCapRequeridos 
				,(SUM(IF((b.folio IS NOT NULL OR c.folio IS NOT NULL) AND b.reemplazo=1,1,0))) as TotCapReemplazadas 
				,(COUNT(*) - SUM(IF(b.folio IS NOT NULL OR c.folio IS NOT NULL,1,0))) as TotPendientes
				,(SUM(IF(IFNULL(a.es_remplazo,0)=0,1,0)) - SUM(IF(b.folio IS NOT NULL OR c.folio IS NOT NULL,1,0))) as TotPenRequeridos 
				,SUM(IF(b.id_remplaza_a IS NOT NULL,1,0) + (IF((b.folio IS NOT NULL OR c.folio IS NOT NULL) AND b.reemplazo=1 AND b.id_remplaza_a IS NOT NULL,1,0))) as ReemplazoUsado
				FROM viviendas_seleccionadas AS a
				LEFT JOIN captura_vivienda_urbana_a AS b ON a.folio=b.folio
				LEFT JOIN rep_termino AS c ON a.folio=c.folio
				WHERE 1 $Filtro
				GROUP BY a.id_ent, a.id_dis, a.seccion, a.manzana ASC 
				HAVING TotPenRequeridos>0 
				;";
		$db = new db();
		$Rows = $db->SQLQuery($sql);
		$Result = $Rows;
	}
	$Registros = count($Rows);
	if($Registros){		
		##Validacion de fecha de término
		$sql ="SELECT timestamp as termino FROM rep_termino WHERE ent='$v_ent' AND dto='$v_dto' AND secc='$v_secc' AND mza='$v_mza' LIMIT 1;";
		$db = new db();
		$termino = $db->SQLQuery($sql);	
		if(!$termino){
			$sql = "INSERT INTO rep_termino SET 
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
		##File Name		
		$rutaDocs=$raiz.'tmp/';
		$ruta = $rutaDocs;	
		$fEnt = ceros($v_ent,2);
		$fDto = ceros($v_dto,2);
		$fSecc = ceros($v_seccion,4);
		$fMzna = ceros($v_manzana,4);
		$nuevoDoc='VNM2014_CONCLUIDAS_E'.$fEnt.'_D'.$fDto.'_'.date('Ymd-His');
		if(strtolower($v_t)=='pdf'){
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
		$sql = "SELECT folio FROM rep_termino WHERE folio='$v_folio'";
		$db = new db();
		$Rows = $db->SQLQuery($sql);
		$Duplicado = count($Rows);
		if(!$Duplicado){
			$Result = 0;
			$reemplazo = ($v_reemplazo)?'SI':'NO';
			$Usuario = "";
			#Archivo
			if($v_archivo){
				$filePath = 'docs/docs.rep_termino/';
				$fileName = 'doc_'.$v_folio;
				$file = $_FILES[$fileName]['name'];
				$tipo = explode('.',$file);
				$fileTmp = $_FILES[$fileName]['tmp_name'];
				$fileRename = 'VNM2014_J_'.$v_folio.'.'.$tipo[count($tipo)-1];
			}
			$sql = "INSERT INTO rep_termino SET 
					 tipo='$v_tipo'
					,zona='$v_zona'
					,ent='$v_ent'
					,dto='$v_dto'
					,secc='$v_secc'
					,mza='$v_mza'
					,folio='$v_folio'
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
			$sql = "UPDATE captura_vivienda_urbana_a SET
					id_remplaza_a='$v_folio'
					WHERE reemplazo=1
						AND id_edo='$v_ent'
						AND id_dtto='$v_dto'
						AND seccion='$v_secc'
						AND mz='$v_mza'
					LIMIT 1;";	
			$db = new db();	
			if($db->SQLDo($sql)){
				$Result = 1;
			}else{$Result = 0;}
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
	$url = "c.reporte-termino.php?auth=1&t=lista&ent=24&dto=2";
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