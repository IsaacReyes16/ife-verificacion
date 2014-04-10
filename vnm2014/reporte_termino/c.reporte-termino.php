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
	if(strtolower($v_t)!='grupo-titulo'){
	##DETALLE		
		$sql = "SELECT
				 a.estado
				,a.id_ent
				,a.id_dis
				,a.seccion
				,a.localidad
				,a.manzana
				,a.folio
				,a.consecutivo
				,'Date' AS termino
				,IF(b.folio IS NOT NULL, 1,0) as capturada
				,b.reemplazo
				,b.id_remplaza_a
				FROM viviendas_seleccionadas AS a
				LEFT JOIN captura_vivienda_urbana_a AS b ON a.folio = b.folio
				WHERE 1 $Filtro 
				HAVING capturada=0 ;";
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
				,SUM(IF(b.folio IS NOT NULL,1,0)) as TotCapturadas 
				,(SUM(IF(b.folio IS NOT NULL,1,0)) - COUNT(b.id_remplaza_a)) as TotCapRequeridos 
				,COUNT(b.id_remplaza_a) as TotCapReemplazadas 
				,(COUNT(*) - SUM(IF(b.folio IS NOT NULL,1,0))) as TotPendientes
				,(SUM(IF(IFNULL(a.es_remplazo,0)=0,1,0)) - SUM(IF(b.folio IS NOT NULL,1,0))) as TotPenRequeridos 
				FROM viviendas_seleccionadas AS a
				LEFT JOIN captura_vivienda_urbana_a AS b ON a.folio = b.folio
				WHERE 1 $Filtro
				GROUP BY a.id_ent, a.id_dis, a.seccion, a.manzana ASC 
				HAVING TotPenRequeridos>0 
				LIMIT 2;";
		$db = new db();
		$Rows = $db->SQLQuery($sql);
		$Result = $Rows;
	}
	$Registros = count($Rows);
	if($Registros){		
		$rutaDocs=$raiz.'tmp/';
		$ruta = $rutaDocs;	
		##File Name		
		$fEnt = ceros($v_ent,2);
		$fDto = ceros($v_dto,2);
		$fSecc = ceros($v_seccion,4);
		$fMzna = ceros($v_manzana,4);
		$nuevoDoc='VNM2014_LISTADO_E'.$fEnt.'_D'.$fDto.'_S'.$fSecc.'_'.date('Ymd-His');
		if(strtolower($v_t)=='pdf'){
		##PDF => Crea archivo PDF
			$docPDF=$nuevoDoc.'.pdf';
			$pdf=new PDF('L','mm','letter');
		    $title="COORDINACIÓN DE OPERACIÓN EN CAMPO";
		    $pdf->SetTitle($title);
		    $pdf->SetAuthor('INE - DDVC'); 
		    $pdf->AliasNbPages('TotalPages');
		    @$pdf->PrintDatos($Rows,$Totales);
		    @$pdf->Output($rutaDocs.$docPDF);
		    $Result = array($v_t, $rutaDocs, $docPDF);
		    if($debug_pdf){	
		    	//ToDebug
		    	echo "<html><head><script>document.location='".$rutaDocs.$docPDF."';</script></head></html>"; 
			}
		}		
	}else{
		$Result = array('ERROR', 'No existen registros para generar este documento');
	}
	##Print Result
	header('Content-Type: application/json; charset=utf-8'); 
	echo json_encode($Result);
}elseif($v_auth && $Action==$actionList[save]){	
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		if($v_archivo){
			uploadFile($v_folio,'tmp/');
			sleep(1);	
		}
		echo 1;			
	}else{
		throw new Exception("Error Processing Request", 1);	
		echo false;			
	}	
}elseif($debug_pdf){
	#debug
	$url = "c.reporte-termino.php?auth=1&t=lista&ent=24&dto=2";
	echo "<a href='".$url."' target='blank'>".$url."</a>";
}else{echo false;}


function uploadFile($folio, $ruta="tmp/"){
/**
*	Descripción:	Recibe archivo vía Ajax y lo sube al servidor
*	@author:		Oscar Maldonado
*/
	$fileName = 'doc_'.$folio;
	$file = $_FILES[$fileName]['name'];
	$tipo=explode('.',$file);
	$newName = 'VNM2014_J_'.$folio.'.'.$tipo[count($tipo)-1];
	if(!is_dir($ruta)){
	    mkdir($ruta, 0777);
	}
	if ($file && move_uploaded_file($_FILES[$fileName]['tmp_name'],$ruta.$newName)){
	   sleep(3);
	   return 1;
	}else{ 
		return 0; 
	}
}
/*O3M*/
?>