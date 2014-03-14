<?php $debug_pdf = false;
##Includes
require_once('common/php/conex.php');
require_once('common/php/pdf/fpdf.php');
require_once('common/pdf/gafete1_pdf_template.php');
session_start();
$id_usuario=$_SESSION['id'];
##Delete tmp folder
limpiar_tmp('tmp/','pdf',5);
limpiar_tmp('tmp/','zip',5);
limpiar_tmp('tmp/','rtf',5);
##Business
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
if($v_auth && $v_t && strtolower($v_t)!='add' && strtolower($v_t)!='update'){	
	##SQL	 
	if($v_id){$Filtro .= " and id_visitador='$v_id'";}
	if($v_ent){$Filtro .= " and id_edo='$v_ent'";}
	if($v_dto){$Filtro .= " and id_dtto='$v_dto'";}
	if($v_nombre){$Filtro .= " and nombres='$v_nombre'";}
	if($v_paterno){$Filtro .= " and apaterno='$v_paterno'";}
	if($v_materno){$Filtro .= " and amaterno='$v_materno'";}
	if($v_tipo){$Filtro .= " and id_operativo='$v_tipo'";}
	$sql = "SELECT 
			 id_visitador
			,id_operativo
			,id_edo
			,id_dtto
			,CONCAT(nombres,' ',apaterno,' ',amaterno) as nombre_completo
			,nombres
			,apaterno
			,amaterno
			,id_cargo
			,clv_elec
			,clave
			,nom_vocal
			,pue_vocal
			,vigencia
			FROM personal
			WHERE 1 and estatus=1 and id_cargo IN(1,2,3,4,6) $Filtro 
			ORDER BY id_operativo, clave, nombres, apaterno, amaterno ASC";
	$Rows=SQLQuery($sql);
	$Registros = count($Rows)-1;
	##Vars
	$Variables = array(
				 'id_gafete'
				,'tipo'
				,'ent'
				,'dto'
				,'nombre_completo'
				,'nombre'
				,'paterno'
				,'materno'
				,'puesto'
				,'cve_elector'
				,'clave'
				,'vocal_nombre'
				,'vocal_puesto'
				,'vigencia');	
	if($Registros>0){
		foreach($Rows as $Row){
			++$n;
			if($n>1){
				$Valores[$n-1] = $Row;
				$Valores[$n-1][4] = utf8_encode($Row[4]);
				$Valores[$n-1][12] = utf8_encode($Row[12]);
				$pClave = $Row[10];
			}
		}
	}
	$rutaDocs='tmp/';
	$ruta = $rutaDocs;	
	##File Name		
	$fEnt = ceros($v_ent,2);
	$fDto = ceros($v_dto,2);
	$nuevoDoc='GAFETE_E'.$fEnt.'_D'.$fDto.'['.$pClave.']_'.date('Ymd-His');
	if(strtolower($v_t)=='rtf'){
	##RTF
		$docRFT=$nuevoDoc.'.rtf';
		$plantillaRTF="gafete_template.rtf";
		$plantilla='common/rtf/'.$plantillaRTF;	
		$archivo=Plantilla_RTF1($plantilla,$ruta,$docRFT,$Variables,"\$",'',$Valores); 
		$Result = array($v_t, $rutaDocs, $docRFT);
	}elseif(strtolower($v_t)=='pdf'){
	##PDF
		$pdf=new PDF('P','mm','gafete');
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
	    if($debug_pdf){	
	    	//Debug
	    	echo "<html><head><script>document.location='".$rutaDocs.$docPDF."';</script></head></html>"; 
		}
	}elseif(strtolower($v_t)=='lista'){
	##LISTA => Genera listado de registros guardados
		if($Registros>0){
			foreach($Valores as $Line){
		    	$Row = array_combine($Variables, $Line);
				$Result [] = $Row;
		    } 
		    #txt(json_encode($Result));
		}		
	}elseif(strtolower($v_t)=='editar'){
	##EDITAR => Regresa datos del ID solicitado
		if($Registros>0){
			foreach($Valores as $Line){
		    	$Row = array_combine($Variables, $Line);
				$Result [] = $Row;
		    } 
		}	
	}
	##Print Result
	echo json_encode($Result);
}elseif($v_auth && strtolower($v_t)=='add'){	
##Guardar registro
	switch($v_puesto){
		case 6 : $digito=10; break; //'SUPERVISOR DE CAMPO'
		case 4 : $digito=20; break; //'VALIDADOR'
		case 2 : $digito=20; break; //'REVISOR'
		case 3 : $digito=30; break; //'VISITADOR DOMICILIARIO'
		case 1 : $digito=30; break; //'ENUMERADOR'
		default : $digito=0;
	}
	$fEnt = ceros($v_ent,2);
	$fDto = ceros($v_dto,2);
	$sql="SELECT count(*)+$digito as clave FROM personal WHERE id_operativo='$v_tipo' and id_cargo='$v_puesto';";
	$c=SQLQuery($sql,1);
	$clave = $fEnt.$fDto.$c['clave'];
	$sql = "INSERT INTO personal SET
			 id_operativo = '$v_tipo'
			,id_edo = '$v_ent'
			,id_dtto = '$v_dto'
			,nombres = '$v_nombre'
			,apaterno = '$v_paterno'
			,amaterno = '$v_materno'
			,id_cargo = '$v_puesto'
			,clv_elec = '$v_cve_elector'
			,clave = '$clave'
			,nom_vocal = '$v_vocal_nombre'
			,pue_vocal = '$v_vocal_puesto'
			,vigencia = '$v_vigencia'
			,id = '$id_usuario'
			,fec_alta = NOW()
			,estatus = 1;";
	if(SQLExec($sql)){
		echo true;
	}else{echo false;}
}elseif($v_auth && strtolower($v_t)=='update'){	
##Actualizar registro
	if($v_puesto!=$v_puesto_old || empty($v_clave)){
		switch($v_puesto){
			case 6 : $digito=10; break; //'SUPERVISOR DE CAMPO'
			case 4 : $digito=20; break; //'VALIDADOR'
			case 2 : $digito=20; break; //'REVISOR'
			case 3 : $digito=30; break; //'VISITADOR DOMICILIARIO'
			case 1 : $digito=30; break; //'ENUMERADOR'
			default : $digito=0;
		}
		$fEnt = ceros($v_ent,2);
		$fDto = ceros($v_dto,2);
		$sql="SELECT count(*)+$digito as clave FROM personal WHERE id_operativo='$v_tipo' and id_cargo='$v_puesto' and clave!='' and clave IS NOT null;";
		$c=SQLQuery($sql,1);
		$clave = $fEnt.$fDto.$c['clave'];
	}else{$clave=$v_clave;}	
	$sql = "UPDATE personal SET
			 id_operativo = '$v_tipo'
			,id_edo = '$v_ent'
			,id_dtto = '$v_dto'
			,nombres = '$v_nombre'
			,apaterno = '$v_paterno'
			,amaterno = '$v_materno'
			,id_cargo = '$v_puesto'
			,clv_elec = '$v_cve_elector'
			,clave = '$clave'
			,nom_vocal = '$v_vocal_nombre'
			,pue_vocal = '$v_vocal_puesto'
			,vigencia = '$v_vigencia'
			,id = '$id_usuario'
			,fec_alta = NOW()
			,estatus = 1
			WHERE id_visitador='$v_id_gafete' LIMIT 1;";
	if(SQLExec($sql)){
		echo true;
	}else{echo false;}
}elseif($debug_pdf){
	#debug
	echo "<a href='http://localhost/ife/verificacion/vnm2014/gafetes/imp_gafete.php?auth=1&t=pdf&id=1' target='blank'>
					 http://localhost/ife/verificacion/vnm2014/gafetes/imp_gafete.php?auth=1&t=pdf&id=1
		 	</a>";
}else{echo false;}

function txt($contenido,$ruta='tmp/'){
	$archivo=$ruta.'tmp_'.date('Ymd-His').'.txt';
	$fp=fopen($archivo,'a');
	fwrite($fp,$contenido);
	fclose($fp);
}
/*O3M*/
?>