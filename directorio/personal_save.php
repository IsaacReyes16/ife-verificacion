<?php
##Includes
include_once('common/php/header.php');
##Bussines SQL
if(!empty($in['id_adscripcion'])){
	$domicilio = $in['calle'].' '.$in['num_ext'].' '.$in['num_int'].', '.$in['colonia'].', '.$in['cp'].', '.$in['mpio_desc'];
	$titular=($in['firma']=='S')?'t':'f';
	if($in['accion']=='UPDATE'){
		//--Update ife_ddvc_catalogos.tbl_personal
		$sql="UPDATE $db_catalogos.tbl_personal SET 
					id_adscripcion='$in[id_adscripcion]',
					id_cargo='$in[id_cargo]',
					nombre='$in[nombre]',
					paterno='$in[paterno]',
					materno='$in[materno]',
					id_tratamiento='$in[id_tratamiento]',
					sexo='$in[sexo]',
					lada='$in[lada]',
					telefono='$in[telefono]',
					telefono2='$in[telefono2]',
					telefonoip='$in[telefonoip]',
					correo='$in[correo]',
					firma='$in[firma]',
					actualizado=NOW(),
					ID_usuario='$Usuario[id]'
					WHERE id_personal='$in[id_personal]'
					LIMIT 1;";
		$sql=SQLExec($sql);
		##ETL Proccess		
		if($in['ent']>0 && $in['dto']==0){
		//--Update ife_dom_irre.lis_vocales_local_vl
			$sql="UPDATE $db_domirreg.lis_vocales_local_vl SET 
					id_adscripcion='$in[id_adscripcion]',
					puesto_vl='$in[cargo]',
					nombre_vl='$in[nombre]',
					paterno_vl='$in[paterno]',
					materno_vl='$in[materno]',
					titulo='$in[tratamiento]',
					sexo='$in[sexo]',
					correo_elec_vl='$in[correo]'
					WHERE id_personal='$in[id_personal]' LIMIT 1;";
			$sql=SQLExec($sql);
		}elseif($in['ent']>0 && $in['dto']>0){
		//--Update ife_ddvc_catalogos.lis_vocales_distrital_vd		
			$sql="UPDATE $db_domirreg.lis_vocales_distrital_vd SET 
					id_adscripcion='$in[id_adscripcion]',
					/*id_puesto_vd='$in[id_cargo]',*/
					nombre_vd='$in[nombre]',
					paterno_vd='$in[paterno]',
					materno_vd='$in[materno]',
					titulo_vd='$in[tratamiento]',
					sexo_vd='$in[sexo]',
					lada_vd='$in[lada]',
					telefono1_vd='$in[telefono]',
					telefono2_vd='$in[telefono2]',
					correo_elec_vd='$in[correo]',
					es_titular_vd='$titular',
					horario_vd='$in[horario]'
					WHERE id_personal='$in[id_personal]';";
			$sql=SQLExec($sql);
		}
		$exito=true;
	}
//-- INSERT
	elseif($in['accion']=='INSERT'){
		//--Insert new in ife_ddvc_catalogos.tbl_personal
		$sql="INSERT INTO $db_catalogos.tbl_personal SET 
					id_adscripcion='$in[id_adscripcion]',
					id_cargo='$in[id_cargo]',
					ent='$in[ent]',
					dto='$in[dto]',
					nombre='$in[nombre]',
					paterno='$in[paterno]',
					materno='$in[materno]',
					id_tratamiento='$in[id_tratamiento]',
					sexo='$in[sexo]',
					lada='$in[lada]',
					telefono='$in[telefono]',
					telefono2='$in[telefono2]',
					telefonoip='$in[telefonoip]',
					correo='$in[correo]',
					firma='$in[firma]',
					fecha_alta=CURDATE(),
					actualizado=NOW(),
					ID_usuario='$Usuario[id]';";
		$id=SQLExec($sql);
		##ETL Proccess
		$sql="SELECT 
			a.id_adscripcion
			,a.adscripcion
			,a.corto
			,a.ent
			,a.dto
			,a.id_area
			,a.calle
			,a.num_ext
			,a.num_int
			,a.colonia
			,a.mpio_desc
			,a.mpio
			,a.cp
			,a.lada
			,a.telefono
			,a.fax
			,a.activo
			,a.actualizado
			,a.id_usuario
			,b.area
			,b.organo
			,c.ent_mayusc as entidad
			FROM tbl_adscripciones a
			LEFT JOIN cat_areas b using(id_area)
			LEFT JOIN cat_entidades c on a.ent=c.id_entidad
			WHERE id_adscripcion='$in[id_adscripcion]'
			ORDER BY a.actualizado DESC LIMIT 1;";
		$Row_adsc = SQLQuery($sql,1);
		$domicilio = $Row_adsc['calle'].' '.$Row_adsc['num_ext'].' '.$Row_adsc['num_int'].', '.$Row_adsc['colonia'].', '.$Row_adsc['cp'].', '.$Row_adsc['mpio_desc'];
		$domicilio = (trim(str_replace(',', '', $domicilio))=='')?'':$domicilio;
		if($in['ent']>0 && $in['dto']==0){
			$sql="SELECT cargo FROM cat_cargos WHERE id_cargo='$in[id_cargo]';";
			$cargo=SQLQuery($sql,1);
			$cargo=utf8_encode($cargo['cargo']);
			$sql="SELECT tratamiento FROM cat_tratamientos WHERE id_tratamiento='$in[id_tratamiento]';";
			$tratamiento=SQLQuery($sql,1);
		//--Insert into ife_dom_irre.lis_vocales_local_vl
			$sql="INSERT INTO $db_domirreg.lis_vocales_local_vl SET 
					id_personal='$id',
					id_adscripcion='$in[id_adscripcion]',
					id_ent='$in[ent]',
					puesto_vl='$cargo',
					nombre_vl='$in[nombre]',
					paterno_vl='$in[paterno]',
					materno_vl='$in[materno]',
					titulo='$tratamiento[tratamiento]',
					sexo='$in[sexo]',
					correo_elec_vl='$in[correo]',
					calle_vl='$Row_adsc[calle]',
					num_ext_vl='$Row_adsc[num_ext]',
					num_int_vl='$Row_adsc[num_int]',
					colonia_vl='$Row_adsc[colonia]',
					municipio_vl='$Row_adsc[mpio_desc]',
					cp_vl='$Row_adsc[cp]';";
			$sql=SQLExec($sql);
		}elseif($in['ent']>0 && $in['dto']>0){
			$sql="SELECT tratamiento FROM cat_tratamientos WHERE id_tratamiento='$in[id_tratamiento]';";
			$tratamiento=SQLQuery($sql,1);
		//--Insert into ife_ddvc_catalogos.lis_vocales_distrital_vd		
			$sql="INSERT INTO $db_domirreg.lis_vocales_distrital_vd SET 
					id_personal='$id',
					id_adscripcion='$in[id_adscripcion]',
					id_ent='$in[ent]',
					id_dis='$in[dto]',
					id_puesto_vd='$in[id_cargo]',
					nombre_vd='$in[nombre]',
					paterno_vd='$in[paterno]',
					materno_vd='$in[materno]',
					titulo_vd='$tratamiento[tratamiento]',
					sexo_vd='$in[sexo]',
					lada_vd='$in[lada]',
					telefono1_vd='$in[telefono]',
					telefono2_vd='$in[telefono2]',
					telefono3_vd='$in[telefonoip]',
					correo_elec_vd='$in[correo]',
					es_titular_vd='$titular',
					domicilio_vd='$domicilio',
					calle_vd='$Row_adsc[calle]',
					num_ext_vd='$Row_adsc[num_ext]',
					num_int_vd='$Row_adsc[num_int]',
					colonia_vd='$Row_adsc[colonia]',
					municipio_vd='$Row_adsc[mpio_desc]',
					cp_vd='$Row_adsc[cp]',
					horario_vd='$in[horario]';";
			$sql=SQLExec($sql);
		}
		$exito=true;	
	}
//-- DELETE
	elseif($in['accion']=='DELETE'){
		$sql="UPDATE $db_catalogos.tbl_personal SET 
			activo=0,
			actualizado=NOW(),
			ID_usuario='$Usuario[id]'
			WHERE id_personal='$in[id_personal]'
			LIMIT 1;";
		$sql=SQLExec($sql);
		$exito=true;
	}
}else{$exito=false;}
echo $exito;
?>