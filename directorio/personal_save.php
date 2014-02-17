<?php
##Includes
include_once('common/php/header.php');
##Bussines
if(!empty($ins['id_adscripcion'])){
	if($ins['accion']=='UPDATE'){
		//--Update ife_ddvc_catalogos.tbl_adscripciones
		$sql="UPDATE $db_catalogos.tbl_personal SET 
					id_cargo='$ins[id_cargo]',
					nombre='$in[nombre]',
					paterno='$in[paterno]',
					materno='$in[materno]',
					id_tratamiento='$ins[id_tratamiento]',
					sexo='$ins[sexo]',
					lada='$ins[lada]',
					telefono='$ins[telefono]',
					telefono2='$ins[telefono2]',
					telefonoip='$ins[telefonoip]',
					correo='$in[correo]',
					firma='$in[firma]',
					actualizado=NOW(),
					ID_usuario='$Usuario[id]'
					WHERE id_personal='$ins[id_personal]'
					LIMIT 1;";
		$sql=SQLExec($sql);
		##ETL Proccess
		$domicilio = $ins[calle].' '.$ins[num_ext].' '.$ins[num_int].', '.$ins[colonia].', '.$ins[cp].', '.$ins[mpio_desc];
		if($ins['ent']>0 && $ins['dto']==0){
		//--Update ife_dom_irre.lis_vocales_local_vl
			$sql="UPDATE $db_domirreg.lis_vocales_local_vl SET 
					puesto_vl='$in[cargo]',
					nombre_vl='$in[nombre]',
					paterno_vl='$in[paterno]',
					materno_vl='$in[materno]',
					titulo='$in[tratamiento]',
					sexo='$ins[sexo]',
					correo_elec_vl='$in[correo]'
					WHERE id_personal='$ins[id_personal]' LIMIT 1;";
			$sql=SQLExec($sql);
		}elseif($ins['ent']>0 && $ins['dto']>0){
		//--Update ife_ddvc_catalogos.lis_vocales_distrital_vd		
			$sql="UPDATE $db_domirreg.lis_vocales_distrital_vd SET 
					/*id_puesto_vd='$ins[id_cargo]',*/
					nombre_vd='$in[nombre]',
					paterno_vd='$in[paterno]',
					materno_vd='$in[materno]',
					titulo_vd='$in[tratamiento]',
					sexo_vd='$ins[sexo]',
					lada_vd='$ins[lada]',
					telefono1_vd='$ins[telefono]',
					telefono2_vd='$ins[telefono2]',
					correo_elec_vd='$in[correo]'
					WHERE id_personal='$ins[id_personal]';";
			$sql=SQLExec($sql);
		}
		$exito=true;
	}
//-- INSERT
	elseif($ins['accion']=='INSERT'){
		//--Insert new in ife_ddvc_catalogos.tbl_adscripciones
		$sql="INSERT INTO $db_catalogos.tbl_adscripciones SET 
					id_cargo='$ins[id_cargo]',
					nombre='$in[nombre]',
					paterno='$in[paterno]',
					materno='$in[materno]',
					id_tratamiento='$ins[id_tratamiento]',
					sexo='$ins[sexo]',
					lada='$ins[lada]',
					telefono='$ins[telefono]',
					telefono2='$ins[telefono2]',
					telefonoip='$ins[telefonoip]',
					correo='$in[correo]',
					firma='$in[firma]',
					fecha_alta=CUDATE(),
					actualizado=NOW(),
					ID_usuario='$Usuario[id]';";
			$sql=SQLExec($sql);
		$sql=SQLExec($sql);
		##ETL Proccess
		$domicilio = $ins[calle].' '.$ins[num_ext].' '.$ins[num_int].', '.$ins[colonia].', '.$ins[cp].', '.$ins[mpio_desc];
		if($ins['ent']>0 && $ins['dto']==0){
		//--Insert into ife_dom_irre.lis_vocales_distrital_vd
			$sql="INSERT INTO $db_domirreg.lis_vocales_distrital_vd SET 
					puesto_vl='$ins[id_cargo]',
					nombre_vl='$ins[nombre]',
					paterno_vl='$ins[paterno]',
					materno_vl='$ins[materno]',
					titulo='$ins[tratamiento]',
					sexo='$ins[sexo]',
					correo_elec_vl='$ins[correo]';";
			$sql=SQLExec($sql);
		}elseif($ins['ent']>0 && $ins['dto']>0){
		//--Insert into ife_ddvc_catalogos.lis_vocales_distrital_vd		
			$sql="INSERT INTO $db_domirreg.lis_vocales_distrital_vd SET 
					id_puesto_vd='$ins[id_cargo]',
					nombre_vd='$ins[nombre]',
					paterno_vd='$ins[paterno]',
					materno_vd='$ins[materno]',
					titulo_vd='$ins[tratamiento]',
					sexo_vd='$ins[sexo]',
					lada_vd='$ins[lada]',
					telefono1_vd='$ins[telefono]',
					telefono2_vd='$ins[telefono2]',
					telefono3_vd='$ins[telefonoip]',
					correo_elec_vd='$ins[correo]';";
			$sql=SQLExec($sql);
		}
		$exito=true;	
	}
}else{$exito=false;}
echo $exito;
?>