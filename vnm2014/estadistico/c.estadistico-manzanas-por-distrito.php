<?php $debug_pdf = false;
##Includes
$raiz = "";
require_once($raiz.'common/php/class.pdo.php');
require_once($raiz.'common/php/pdf/fpdf.php');
require_once($raiz.'common/pdf/pdf.estadistico-manzanas-por-distrito.php');
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
			,a.localidad
			,a.manzana
			FROM viviendas_seleccionadas a
			LEFT JOIN muestra b ON a.id_ent=b.estado AND a.id_dis=b.distrito AND a.id_mun=b.municipio AND a.seccion=b.seccion
			WHERE 1 $Filtro 
			GROUP BY a.id_ent, a.id_dis, a.id_mun, a.seccion, a.manzana ASC;";
	$db = new db();
	$Rows = $db->SQLQuery($sql);
	$Registros = count($Rows);
	// foreach($Rows as $Row){
	// 	print_r($Row);
	// 	echo "<br>";
	// }
	##Totales
	$sql = "SELECT sum(total) as total from (
				SELECT count(DISTINCT(manzana)) as total
				FROM viviendas_seleccionadas a
				WHERE 1 $Filtro 
				GROUP BY a.id_ent, a.id_dis, a.id_mun, a.seccion, a.manzana ASC) a;";
	$db2 = new db();
	$Tot = $db2->SQLQuery($sql);
	foreach($Tot as $Totales){}
	##--
	$rutaDocs=$raiz.'tmp/';
	$ruta = $rutaDocs;	
	##File Name		
	$fEnt = ceros($v_ent,2);
	$fDto = ceros($v_dto,2);
	$nuevoDoc='VNM2014_ESTADISTICO_E'.$fEnt.'_D'.$fDto.'_'.date('Ymd-His');
	if(strtolower($v_t)=='pdf'){
	##PDF => Crea archivo PDF
		$docPDF=$nuevoDoc.'.pdf';
		$pdf=new PDF('L','mm','letter');
	    $title="COORDINACIÓN DE OPERACIÓN EN CAMPO";
	    $pdf->SetTitle($title);
	    $pdf->SetAuthor('IFE - DDVC'); 
	    @$pdf->PrintDatos($Rows,$Totales);
	    @$pdf->Output($rutaDocs.$docPDF);
	    $Result = array($v_t, $rutaDocs, $docPDF);
	    if($debug_pdf){	
	    	//ToDebug
	    	echo "<html><head><script>document.location='".$rutaDocs.$docPDF."';</script></head></html>"; 
		}
	}
	##Print Result
	echo json_encode($Result);
}elseif($debug_pdf){
	#debug
	echo "<a href='http://localhost/ife/verificacion/vnm2014/estadistico/c.estadistico-manzanas-por-distrito.php?auth=1&t=PDF&ent=2&dto=1' target='blank'>
				   http://localhost/ife/verificacion/vnm2014/estadistico/c.estadistico-manzanas-por-distrito.php?auth=1&t=PDF&ent=2&dto=1
		 	</a>";
}else{echo false;}
/*O3M*/
?>