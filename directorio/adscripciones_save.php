<?php
##Includes
include_once('common/php/header.php');
##Bussines
if(!empty($ins['id_adscripcion'])){
	if($ins['accion']=='UPDATE'){
		//--Update ife_ddvc_catalogos.tbl_adscripciones
		$sql="UPDATE $db_catalogos.tbl_adscripciones SET 
					calle='$ins[calle]',
					num_ext='$ins[num_ext]',
					num_int='$ins[num_int]',
					colonia='$ins[colonia]',
					mpio_desc='$ins[mpio_desc]',
					cp='$ins[cp]',
					lada='$ins[lada]',
					telefono='$ins[telefono]',
					fax='$ins[fax]',
					actualizado=NOW(),
					ID_usuario='$Usuario[id]'
					WHERE id_adscripcion='$ins[id_adscripcion]'
					LIMIT 1;";
		$sql=SQLExec($sql);
		##ETL Proccess
		$domicilio = $ins[calle].' '.$ins[num_ext].' '.$ins[num_int].', '.$ins[colonia].', '.$ins[cp].', '.$ins[mpio_desc];
		if($ins['ent']>0 && $ins['dto']==0){
		//--Update ife_dom_irre.lis_vocales_local_vl
			$sql="UPDATE $db_domirreg.lis_vocales_local_vl SET 
					calle_vl='$ins[calle]',
					num_ext_vl='$ins[num_ext]',
					num_int_vl='$ins[num_int]',
					colonia_vl='$ins[colonia]',
					municipio_vl='$ins[mpio_desc]',
					cp_vl='$ins[cp]'
					WHERE id_adscripcion='$ins[id_adscripcion]' LIMIT 1;";
			$sql=SQLExec($sql);
		}elseif($ins['ent']>0 && $ins['dto']>0){
		//--Update ife_ddvc_catalogos.lis_vocales_distrital_vd		
			$sql="UPDATE $db_domirreg.lis_vocales_distrital_vd SET 
					domicilio_vd='$domicilio',
					calle_vd='$ins[calle]',
					num_ext_vd='$ins[num_ext]',
					num_int_vd='$ins[num_int]',
					colonia_vd='$ins[colonia]',
					municipio_vd='$ins[mpio_desc]',
					cp_vd='$ins[cp]'
					WHERE id_adscripcion='$ins[id_adscripcion]';";
			$sql=SQLExec($sql);
		}
		$exito=true;
	}elseif($ins['accion']=='INSERT'){
		//--Insert new in ife_ddvc_catalogos.tbl_adscripciones
		$sql="INSERT INTO $db_catalogos.tbl_adscripciones SET 
					ent='$ins[ent]',
					dto='$ins[dto]',
					calle='$ins[calle]',
					num_ext='$ins[num_ext]',
					num_int='$ins[num_int]',
					colonia='$ins[colonia]',
					mpio_desc='$ins[mpio_desc]',
					cp='$ins[cp]',
					lada='$ins[lada]',
					telefono='$ins[telefono]',
					fax='$ins[fax]',
					actualizado=NOW(),
					ID_usuario='$Usuario[id]';";
			$sql=SQLExec($sql);
		$sql=SQLExec($sql);
		##ETL Proccess
		$domicilio = $ins[calle].' '.$ins[num_ext].' '.$ins[num_int].', '.$ins[colonia].', '.$ins[cp].', '.$ins[mpio_desc];
		if($ins['ent']>0 && $ins['dto']==0){
		//--Insert into ife_dom_irre.lis_vocales_local_vl
			$sql="INSERT INTO $db_domirreg.lis_vocales_local_vl SET 
					id_adscripcion='$ins[id_adscripcion]',
					id_ent='$ins[ent]',
					calle_vl='$ins[calle]',
					num_ext_vl='$ins[num_ext]',
					num_int_vl='$ins[num_int]',
					colonia_vl='$ins[colonia]',
					municipio_vl='$ins[mpio_desc]',
					cp_vl='$ins[cp]';";
			$sql=SQLExec($sql);
		}elseif($ins['ent']>0 && $ins['dto']>0){
		//--Insert into ife_ddvc_catalogos.lis_vocales_distrital_vd		
			$sql="INSERT INTO $db_domirreg.lis_vocales_distrital_vd SET 
					id_adscripcion='$ins[id_adscripcion]',
					id_ent='$ins[ent]',
					id_dis='$ins[dto]',
					domicilio_vd='$domicilio',
					calle_vd='$ins[calle]',
					num_ext_vd='$ins[num_ext]',
					num_int_vd='$ins[num_int]',
					colonia_vd='$ins[colonia]',
					municipio_vd='$ins[mpio_desc]',
					cp_vd='$ins[cp]';";
			$sql=SQLExec($sql);
		}
		$exito=true;	
	}
}else{$exito=false;}
echo $exito;
?>