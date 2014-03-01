<?php
##Includes
require_once('common/php/conex.php');
##Delete tmp folder
limpiar_tmp('tmp/','zip',5);
limpiar_tmp('tmp/','rtf',5);
##Business
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
if($v_auth && $v_ent && $v_dto){		 
	if($v_ent){$Filtro .= "and id_ent='$v_ent'";}
	if($v_dto){$Filtro .= "and id_dis='$v_dto'";}
	if($v_folio){$Filtro .= "and folio='$v_folio'";}
	if($v_consecutivo){$Filtro .= "and consecutivo='$v_consecutivo'";}
	if($v_seccion){$Filtro .= "and seccion='$v_seccion'";}
	if($v_manzana){$Filtro .= "and manzana='$v_manzana'";}
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
	$totVars = count($Variables);
	foreach($Rows as $Row){
		++$n;
		if($n>1){
			$Valores[$n-1] = $Row;
			$Valores[$n-1][12] = ($Row[12]==1)?'VIVIENDA DE REEMPLAZO':'';
		}
	}
	$plantillaRTF="VNM2014_10012014_3.rtf";
	$rutaDocs='tmp/';
	$plantilla='common/rtf/'.$plantillaRTF;
	$ruta = $rutaDocs;	
	$fEnt = ($v_ent<10)?'0'.$v_ent:$v_ent;
	$fDto = ($v_dto<10)?'0'.$v_dto:$v_dto;
	$fSecc = ($v_seccion<1000)?'0'.$v_seccion:$v_seccion;
	$fSecc = ($v_seccion<100)?'00'.$v_seccion:$v_seccion;
	$fSecc = ($v_seccion<10)?'000'.$v_seccion:$v_seccion;
	$fMzna = ($v_manzana<1000)?'0'.$v_manzana:$v_manzana;
	$fMzna = ($v_manzana<100)?'00'.$v_manzana:$v_manzana;
	$fMzna = ($v_manzana<10)?'000'.$v_manzana:$v_manzana;
	$nuevoDoc='VNM2014_CUESTIONARIO_E'.$fEnt.'_D'.$fDto.'_S'.$fSecc.'_M'.$fMzna.'_'.date('Ymd-His');
	$docRFT=$nuevoDoc.'.rtf';
	$archivo=Plantilla_RTF1($plantilla,$ruta,$docRFT,$Variables,"\$",'',$Valores); 
	#Zip	
	chdir($ruta);
	$docZip = $nuevoDoc.'.zip';
	$Zip = new ZipArchive;
	$Zip->open($docZip, ZipArchive::CREATE);	
	$Zip->addFile($docRFT);
	$Zip->close();
	unlink($docRFT);
	#Exit
	echo $docZip;	
}else{echo false;}
/*O3M*/
?>