<?php $debug_pdf = false;
##Includes
$raiz = "";
require_once($raiz.'common/php/class.pdo.php');
require_once($raiz.'common/php/pdf/fpdf.php');
require_once($raiz.'common/pdf/pdf.listado-viviendas-por-seccion.php');
##Delete tmp folder
limpiar_tmp('tmp/','zip',5);
limpiar_tmp('tmp/','rtf',5);
limpiar_tmp('tmp/','pdf',5);
##Business
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
if($v_auth && $v_t){	
	##SQL	 
	if($v_ent){$Filtro .= " and a.id_ent='$v_ent'";}
	if($v_dto){$Filtro .= " and a.id_dis='$v_dto'";}
	if($v_municipio){$Filtro .= " and a.id_mun='$v_municipio'";}
	if($v_seccion){$Filtro .= " and a.seccion='$v_seccion'";}
	if($v_tipo){$Filtro .= " and b.tipo_seccion='$v_tipo'";}
	##Nominativo
	$sql = "SELECT 
			 a.id_ent as ent
			,a.estado as entidad
			,a.id_dis as dto
			,a.id_mun as mpio
			,a.municipio
			,a.seccion
			,b.tipo_seccion as seccion_tipo
			,a.id_loc as loc
			,a.manzana
			,a.folio
			,a.consecutivo
			FROM viviendas_seleccionadas a
			LEFT JOIN muestra b ON a.id_ent=b.estado AND a.id_dis=b.distrito AND a.id_mun=b.municipio AND a.seccion=b.seccion
			WHERE 1 $Filtro 
			GROUP BY a.id_ent,a.id_dis,a.id_mun,a.seccion,b.tipo_seccion,a.folio ASC;";
	$db = new db();
	$Rows = $db->SQLQuery($sql);
	$Registros = count($Rows);
	if($Registros){
		##Totales
		$sql = "SELECT
				 count(DISTINCT(a.manzana)) as tot_manazanas
				,count(DISTINCT(a.folio)) as tot_folios
				FROM viviendas_seleccionadas a
				LEFT JOIN muestra b ON a.id_ent=b.estado AND a.id_dis=b.distrito AND a.id_mun=b.municipio AND a.seccion=b.seccion
				WHERE 1 $Filtro
				GROUP BY a.id_ent,a.id_dis,a.id_mun,a.seccion,b.tipo_seccion ASC;";			
		$db2 = new db();
		$Tot = $db2->SQLQuery($sql);
		foreach($Tot as $Totales){}
		##--
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
		    $pdf->SetAuthor('IFE - DDVC'); 
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
	echo json_encode($Result);
}elseif($debug_pdf){
	#debug
	// $url = "c.listado-viviendas-por-seccion.php?auth=1&t=PDF&ent=9&dto=24&municipio=3&seccion=528&tipo=U";
	$url = "c.listado-viviendas-por-seccion.php?auth=1&t=PDF&ent=2&dto=1&municipio=2&seccion=363&tipo=U";
	echo "<a href='".$url."' target='blank'>".$url."</a>";
}else{echo false;}
/*O3M*/
?>