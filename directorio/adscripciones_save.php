<?php
##Includes
include_once('common/php/header.php');
##Bussines
if(!empty($ins['id_adscripcion'])){
	$domicilio = $in['calle'].' '.$in['num_ext'].' '.$in['num_int'].', '.$in['colonia'].', '.$in['cp'].', '.$in['mpio_desc'];
	if($in['accion']=='UPDATE'){
		//--Update ife_ddvc_catalogos.tbl_adscripciones
		$sql="UPDATE $db_catalogos.tbl_adscripciones SET 
					calle='$in[calle]',
					num_ext='$in[num_ext]',
					num_int='$in[num_int]',
					colonia='$in[colonia]',
					mpio_desc='$in[mpio_desc]',
					cp='$in[cp]',
					lada='$in[lada]',
					telefono='$in[telefono]',
					fax='$in[fax]',
					horario='$in[horario]',
					actualizado=NOW(),
					ID_usuario='$Usuario[id]'
					WHERE id_adscripcion='$in[id_adscripcion]'
					LIMIT 1;";
		$sql=SQLExec($sql);
		##ETL Proccess
		if($ins['ent']>0 && $ins['dto']==0){
		//--Update ife_dom_irre.lis_vocales_local_vl
			$sql="UPDATE $db_domirreg.lis_vocales_local_vl SET 
					calle_vl='$in[calle]',
					num_ext_vl='$in[num_ext]',
					num_int_vl='$in[num_int]',
					colonia_vl='$in[colonia]',
					municipio_vl='$in[mpio_desc]',
					cp_vl='$in[cp]'
					WHERE id_adscripcion='$in[id_adscripcion]' LIMIT 1;";
			$sql=SQLExec($sql);
		}elseif($ins['ent']>0 && $ins['dto']>0){
		//--Update ife_ddvc_catalogos.lis_vocales_distrital_vd		
			$sql="UPDATE $db_domirreg.lis_vocales_distrital_vd SET 
					domicilio_vd='$domicilio',
					calle_vd='$in[calle]',
					num_ext_vd='$in[num_ext]',
					num_int_vd='$in[num_int]',
					colonia_vd='$in[colonia]',
					municipio_vd='$in[mpio_desc]',
					cp_vd='$in[cp]',
					horario_vd='$in[horario]'
					WHERE id_adscripcion='$ins[id_adscripcion]';";
			$sql=SQLExec($sql);
		}
		$exito=true;
	}
//INSERT
	elseif($in['accion']=='INSERT'){
		//--Insert new in ife_ddvc_catalogos.tbl_adscripciones
		$sql="INSERT INTO $db_catalogos.tbl_adscripciones SET 
					ent='$in[ent]',
					dto='$in[dto]',
					corto='$in[corto]',
					adscripcion='$in[adscripcion]',
					id_area='$in[id_area]',
					calle='$in[calle]',
					num_ext='$in[num_ext]',
					num_int='$in[num_int]',
					colonia='$in[colonia]',
					mpio_desc='$in[mpio_desc]',
					cp='$in[cp]',
					lada='$in[lada]',
					telefono='$in[telefono]',
					fax='$in[fax]',
					horario='$in[horario]',
					activo='1',
					actualizado=NOW(),					
					ID_usuario='$Usuario[id]';";
		$sql=SQLExec($sql);
		##ETL Proccess		
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
					horario_vd='$in[horario]',
					cp_vd='$ins[cp]';";
			$sql=SQLExec($sql);
		}
		$exito=true;	
	}
//-- DELETE
	elseif($in['accion']=='DELETE'){
		$sql="UPDATE $db_catalogos.tbl_adscripciones SET 
			activo=0,
			actualizado=NOW(),
			ID_usuario='$Usuario[id]'
			WHERE id_adscripcion='$in[id_adscripcion]'
			LIMIT 1;";
		$sql=SQLExec($sql);
		$exito=true;
	}
}else{$exito=false;}
echo $exito;
?>