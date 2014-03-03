<?php
##Includes
require_once('common/php/conex.php');
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
	if($v_ent){$Filtro .= " and id_ent='$v_ent'";}
	if($v_dto){$Filtro .= " and id_dis='$v_dto'";}
	if($v_folio){$Filtro .= " and folio='$v_folio'";}
	if($v_consecutivo){$Filtro .= " and consecutivo='$v_consecutivo'";}
	if($v_seccion){$Filtro .= " and seccion='$v_seccion'";}
	if($v_manzana){$Filtro .= " and manzana='$v_manzana'";}
	$sql = "SELECT 
			 folio
			,consecutivo
			,CONCAT(id_ent,' ',estado) as entidad
			,id_dis as dto
			,seccion 
			,manzana
			,CONCAT(id_mun,' ',municipio) as municipio
			,CONCAT(id_loc,' ',localidad) as localidad
			,calle
			,exterior
			,interior
			,colonia
			,es_remplazo as reemplazo
			FROM viviendas_seleccionadas
			WHERE 1 $Filtro";
	$Rows=SQLQuery($sql);
	$Registros = count($rows)-1;
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
		if($n>1){
			$Valores[$n-1] = $Row;
			$Valores[$n-1][12] = ($Row[12]==1)?'VIVIENDA DE REEMPLAZO':'';
		}
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
	    	$Line  = array_combine($Variables, $Line);
	    	@$pdf->PrintDatos($Line);
	    }    
	    $docPDF=$nuevoDoc.'.pdf';
	    @$pdf->Output($rutaDocs.$docPDF);
	    $Result = array($v_t, $rutaDocs, $docPDF);	
	    //Temporal
	    // echo "<html><head><script>document.location='".$rutaDocs.$docPDF."';</script></head></html>"; 
	}else{
	##NON
		$Result = array(0, $rutaDocs, "No se selecciono Tipo");
	}
	##Print Result
	echo json_encode($Result);
}else{echo false;}
// }else{echo "<a href='http://localhost/ife/verificacion/vnm2014/cuestionario/vnm2014_cuestionario.php?auth=1&t=pdf&ent=2&dto=3&seccion=132&manzana=8&folio=0203105_' target='blank'>http://localhost/ife/verificacion/vnm2014/cuestionario/vnm2014_pdf.php?auth=1&t=pdf&ent=2&dto=3&seccion=132&manzana=8&folio=0203105_</a>";}
/*O3M*/
?>