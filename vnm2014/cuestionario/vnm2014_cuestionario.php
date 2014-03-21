<?php $debug_pdf = false;
##Includes
// require_once('common/php/conex.php');
require_once('common/php/class.pdo.php');
require_once('common/php/pdf/fpdf.php');
require_once('common/pdf/vnm2014_pdf_template.php');
##Delete tmp folder
limpiar_tmp('tmp/','zip',5);
limpiar_tmp('tmp/','rtf',5);
limpiar_tmp('tmp/','pdf',5);
##Business
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
if($v_auth && $v_ent && $v_dto && $v_t){	
	##SQL	 
	if($v_ent){$Filtro .= " and a.id_ent='$v_ent'";}
	if($v_dto){$Filtro .= " and a.id_dis='$v_dto'";}
	if($v_folio){$Filtro .= " and a.folio='$v_folio'";}
	if($v_consecutivo){$Filtro .= " and a.consecutivo='$v_consecutivo'";}
	if($v_seccion){$Filtro .= " and a.seccion='$v_seccion'";}
	if($v_manzana){$Filtro .= " and a.manzana='$v_manzana'";}
	$sql = "SELECT
		    a.folio
		   ,a.consecutivo
		   ,CONCAT(a.id_ent,' ',a.estado) as entidad
		   ,a.id_dis as distrito
		   ,a.seccion 
		   ,a.manzana
		   ,CONCAT(a.id_mun,' ',a.municipio) as municipio
		   ,CONCAT(a.id_loc,' ',a.localidad) as localidad
		   ,a.calle
		   ,a.exterior as num_ext
		   ,a.interior as num_int
		   ,IF(b.colonia='-', CONCAT('LOC. ',b.localidad),CONCAT('COL. ', b.colonia)) as colonia
		   ,a.es_remplazo as reemplazo
		   FROM viviendas_seleccionadas a
		   LEFT JOIN cedula b ON a.id_ent = b.estado AND a.id_dis = b.distrito AND a.manzana = b.manzana AND a.seccion = b.seccion
		   WHERE 1 $Filtro ;";
	// $Rows=SQLQuery($sql);
	$db = new db();
	$Rows = $db->SQLQuery($sql);
	$Registros = count($Rows)-1;
	##Vars
	$Variables = array(
				 'folio'
				,'consecutivo'
				,'entidad'
				,'distrito'
				,'seccion'
				,'manzana'
				,'municipio'
				,'localidad'
				,'calle'
				,'num_ext'
				,'num_int'
				,'colonia'
				,'reemplazo');
	foreach($Rows as $Row){
		++$n;
		// if($n>1){
			$Valores[$n-1] = $Row;
			$Valores[$n-1][reemplazo] = ($Row[reemplazo]==1)?'VIVIENDA DE REEMPLAZO':'';			
		// }
	}	
	$rutaDocs='tmp/';
	$ruta = $rutaDocs;	
	##File Name		
	$fEnt = ceros($v_ent,2);
	$fDto = ceros($v_dto,2);
	$fSecc = ceros($v_seccion,4);
	$fMzna = ceros($v_manzana,4);
	$nuevoDoc='VNM2014_CUESTIONARIO_E'.$fEnt.'_D'.$fDto.'_S'.$fSecc.'_M'.$fMzna.'_'.date('Ymd-His');
	if(strtolower($v_t)=='rtf'){
	##RTF
		$docRFT=$nuevoDoc.'.rtf';
		$plantillaRTF="VNM2014_10012014_3.rtf";
		$plantilla='common/rtf/'.$plantillaRTF;	
		$archivo=Plantilla_RTF1($plantilla,$ruta,$docRFT,$Variables,"\$",'',$Valores); 
		$docZip = zipFile($docRFT, $rutaDocs, true);
		$Result = array($v_t, $rutaDocs, $docZip);
	}elseif(strtolower($v_t)=='pdf'){
	##PDF
		$pdf=new PDF('P','mm','letter');
	    $title="COORDINACIÓN DE OPERACIÓN EN CAMPO";
	    $pdf->SetTitle($title);
	    $pdf->SetAuthor('IFE - DDVC');	    
	    foreach($Valores as $Line){
	    	// $Line  = array_combine($Variables, $Line);
	    	@$pdf->PrintDatos($Line);
	    }    
	    $docPDF=$nuevoDoc.'.pdf';
	    @$pdf->Output($rutaDocs.$docPDF);
	    $Result = array($v_t, $rutaDocs, $docPDF);	
	    if($debug_pdf){	
	    	//ToDebug
	    	//echo "<html><head><script>document.location='".$rutaDocs.$docPDF."';</script></head></html>"; 
		}
	}else{
	##NON
		$Result = array(0, $rutaDocs, "No se selecciono Tipo");
	}
	##Print Result
	echo json_encode($Result);
}elseif($debug_pdf){
	#debug
	$url = "vnm2014_cuestionario.php?auth=1&t=pdf&ent=24&dto=2";
	echo "<a href='".$url."' target='blank'>".$url."</a>";
}else{echo false;}
/*O3M*/
?>