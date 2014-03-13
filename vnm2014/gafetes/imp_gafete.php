<?php $debug_pdf = false;
##Includes
require_once('common/php/conex.php');
require_once('common/php/pdf/fpdf.php');
require_once('common/pdf/gafete1_pdf_template.php');
##Delete tmp folder
limpiar_tmp('tmp/','pdf',5);
limpiar_tmp('tmp/','zip',5);
limpiar_tmp('tmp/','rtf',5);
##Business
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v");
if($v_auth && $v_t && strtolower($v_t)!='add' && strtolower($v_t)!='update'){	
	##SQL	 
	if($v_id){$Filtro .= " and id_gafete='$v_id'";}
	if($v_ent){$Filtro .= " and ent='$v_ent'";}
	if($v_dto){$Filtro .= " and dto='$v_dto'";}
	if($v_nombre){$Filtro .= " and nombre='$v_nombre'";}
	if($v_paterno){$Filtro .= " and paterno='$v_paterno'";}
	if($v_materno){$Filtro .= " and materno='$v_materno'";}
	if($v_tipo){$Filtro .= " and tipo='$v_tipo'";}
	$sql = "SELECT 
			 id_gafete
			,tipo
			,ent
			,dto
			,CONCAT(nombre,' ',paterno,' ',materno) as nombre_completo
			,nombre
			,paterno
			,materno
			,puesto
			,cve_elector 
			,clave
			,vocal_nombre
			,vocal_puesto
			,vigencia
			FROM tbl_gafetes
			WHERE 1 and activo=1 $Filtro 
			ORDER BY tipo, clave, nombre, paterno, materno ASC";
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
				$Valores[$n-1][8] = utf8_encode($Row[8]);
			}
		}
	}
	$rutaDocs='tmp/';
	$ruta = $rutaDocs;	
	##File Name		
	$fEnt = ceros($v_ent,2);
	$fDto = ceros($v_dto,2);
	$nuevoDoc='GAFETE_E'.$fEnt.'_D'.$fDto.'_'.date('Ymd-His');
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
		case 'SUPERVISOR DE CAMPO': $digito=10; break;
		case 'VALIDADOR': $digito=20; break;
		case 'REVISOR': $digito=20; break;
		case 'VISITADOR DOMICILIARIO': $digito=30; break;
		case 'ENUMERADOR': $digito=30; break;
		default : $digito=0;
	}
	$fEnt = ceros($v_ent,2);
	$fDto = ceros($v_dto,2);
	$sql="SELECT count(*)+$digito as clave FROM tbl_gafetes WHERE tipo='$v_tipo' and puesto='$v_puesto';";
	$c=SQLQuery($sql,1);
	$clave = $fEnt.$fDto.$c['clave'];
	$sql = "INSERT INTO tbl_gafetes SET
			 tipo = '$v_tipo'
			,ent = '$v_ent'
			,dto = '$v_dto'
			,nombre = '$v_nombre'
			,paterno = '$v_paterno'
			,materno = '$v_materno'
			,puesto = '$v_puesto'
			,cve_elector = '$v_cve_elector'
			,clave = '$clave'
			,vocal_nombre = '$v_vocal_nombre'
			,vocal_puesto = '$v_vocal_puesto'
			,vigencia = '$v_vigencia'
			,id_usuario = '$id_usuario'
			,timestamp = NOW()
			,activo = 1;";
	if(SQLExec($sql)){
		echo true;
	}else{echo false;}
}elseif($v_auth && strtolower($v_t)=='update'){	
##Actualizar registro
	if($v_puesto!=$v_puesto_old){
		switch($v_puesto){
			case 'SUPERVISOR DE CAMPO': $digito=10; break;
			case 'VALIDADOR': $digito=20; break;
			case 'REVISOR': $digito=20; break;
			case 'VISITADOR DOMICILIARIO': $digito=30; break;
			case 'ENUMERADOR': $digito=30; break;
			default : $digito=0;
		}
		$fEnt = ceros($v_ent,2);
		$fDto = ceros($v_dto,2);
		$sql="SELECT count(*)+$digito as clave FROM tbl_gafetes WHERE tipo='$v_tipo' and puesto='$v_puesto';";
		$c=SQLQuery($sql,1);
		$clave = $fEnt.$fDto.$c['clave'];
	}else{$clave=$v_clave;}	
	$sql = "UPDATE tbl_gafetes SET
			 tipo = '$v_tipo'
			,ent = '$v_ent'
			,dto = '$v_dto'
			,nombre = '$v_nombre'
			,paterno = '$v_paterno'
			,materno = '$v_materno'
			,puesto = '$v_puesto'
			,cve_elector = '$v_cve_elector'
			,clave = '$clave'
			,vocal_nombre = '$v_vocal_nombre'
			,vocal_puesto = '$v_vocal_puesto'
			,vigencia = '$v_vigencia'
			,id_usuario = '$id_usuario'
			,timestamp = NOW()
			,activo = 1
			WHERE id_gafete='$v_id_gafete' LIMIT 1;";
	if(SQLExec($sql)){
		echo true;
	}else{echo false;}
}elseif($debug_pdf){
	#debug
	echo "<a href='http://localhost/ife/verificacion/vnm2014/gafetes/imp_gafete.php?auth=1&t=pdf&id=1' target='blank'>
					 http://localhost/ife/verificacion/vnm2014/gafetes/imp_gafete.php?auth=1&t=pdf&id=1
		 	</a>";
}else{echo false;}
/*O3M*/
?>