<?php
require_once('inc/conexion.php');

$tpl->loadTemplateFile('sdir_formato_registral.html');
$tpl -> setVariable($arr_menu);

switch($_SESSION['nivel']){
	case 1: //usuario de nivel central

	 	$qry = "select id_ent, descripcion_ent as descripcion, 
				(case id_ent when id_ent then 
				    (select count(1) 
					 from lis_ciudadano_ciu l 
					 where l.ent_act_ciu=e.id_ent 
					       and not exists 
						       (select 1 
							    from lis_cedula_di_cdi_vig w 
								where w.consec_ciu=l.consec_ciu)
					  ) 
				 end) as total_cv,
				 
				 (case id_ent when id_ent then 
				     (select count(1) 
					  from lis_ciudadano_ciu l 
					  where l.ent_act_ciu=e.id_ent 
					        and exists 
							    (select 1 
								 from lis_cedula_di_cdi_vig w 
								 where w.consec_ciu=l.consec_ciu and edo_reg_cdi=2) 
							and not exists 
							    (select 1 
								 from lis_cedula_notif_cdn_vig x 
								 where x.consec_ciu=l.consec_ciu)
					   ) 
				  end) as total_cn,				  
			   
				   (case id_ent when id_ent then 
					     (select count(1) 
						  from lis_bajas_baj_vig l 
						  where l.ent_ant_baj=e.id_ent 
						      and not exists 
							       (select 1 
								    from lis_cedula_notif_baj_cnb_vig x 
									where x.consec_baj=l.consec_baj)) 
					 end) as total_dpi
					 
				from cat_entidades_ent e 
				where (id_ent between 1 and 32) 
				order by id_ent";
				
		$titulo = "Entidad";
	break;
	
	case 2: //usuario de nivel local
		$qry = "select e.id_ent, lpad(w.id_dis,2,'0') as descripcion,
		
				 (case id_ent when id_ent then 
				       (select count(1) 
					    from lis_ciudadano_ciu l 
						where l.ent_act_ciu=e.id_ent and l.dis_act_ciu=w.id_dis 
						      and not exists 
							  (select 1 
							   from lis_cedula_di_cdi_vig w 
							   where w.consec_ciu=l.consec_ciu)) 
					end) as total_cv,
					
				   (case id_ent when id_ent then 
				         (select count(1) 
						 from lis_ciudadano_ciu l 
						 where l.ent_act_ciu=e.id_ent and l.dis_act_ciu=w.id_dis 
						       and exists 
							   (select 1 
							    from lis_cedula_di_cdi_vig w 
								where w.consec_ciu=l.consec_ciu and edo_reg_cdi=2) 
								  and not exists 
								      (select 1 
									   from lis_cedula_notif_cdn_vig x 
									   where x.consec_ciu=l.consec_ciu)) 
				    end) as total_cn,				   
					
				   (case id_ent when id_ent then 
				        (select count(1) 
						 from lis_bajas_baj_vig l 
						 where l.ent_ant_baj=e.id_ent and l.dis_ant_baj=w.id_dis and 
						 not exists 
						     (select 1 
							 from lis_cedula_notif_baj_cnb_vig x 
							 where x.consec_baj=l.consec_baj)) 
					end) as total_dpi
					
				from cat_entidades_ent e 
				left join cat_distritos_dis w 
				using(id_ent) 
				where id_ent=$_SESSION[id_vlc] 
				      and id_dis!=0 
			    order by id_dis";
				
		$titulo = "Distrito";
	break;
	
	case 3: //usuario de nivel distrital
		$qry = "select e.id_ent, lpad(w.id_dis,2,'0') as descripcion,				 
				 (case id_ent when id_ent then 
				    (select count(1) 
				     from lis_ciudadano_ciu l 
				 	 where l.ent_act_ciu=e.id_ent and l.dis_act_ciu=w.id_dis and 
			 		 not exists 
					 (select 1 
					 from lis_cedula_di_cdi_vig w 
					 where w.consec_ciu=l.consec_ciu)) 
				  end) as total_cv,
				 
				 (case id_ent when id_ent then 
				     (select count(1) 
					  from lis_ciudadano_ciu l 
					  where l.ent_act_ciu=e.id_ent and l.dis_act_ciu=w.id_dis and 
					  exists 
					    (select 1 
						from lis_cedula_di_cdi_vig w 
						where w.consec_ciu=l.consec_ciu and edo_reg_cdi=2) and 
						not exists 
						  (select 1 
						  from lis_cedula_notif_cdn_vig x 
						  where x.consec_ciu=l.consec_ciu)) 
				   end) as total_cn,				 
				 
				   (case id_ent when id_ent then 
				     (select count(1) 
				       from lis_bajas_baj_vig l 
					   where l.ent_ant_baj=e.id_ent and l.dis_ant_baj=w.id_dis and 
					   not exists 
					   (select 1 
					   from lis_cedula_notif_baj_cnb_vig x 
					   where x.consec_baj=l.consec_baj)) 
					end) as total_dpi
				 
				from cat_entidades_ent e 
				left join cat_distritos_dis w using(id_ent) 
				where id_ent=$_SESSION[id_vlc] 
				and id_dis=$_SESSION[id_vdi];";
		$titulo = "Distrito";
		
	break;
	default: exit;
}

$tpl->setVariable("titulo",$titulo);

$res = mysql_query($qry,$conn);

while($row=mysql_fetch_assoc($res)) {
	$suma_cv += $row['total_cv'];
	$suma_cn += $row['total_cn'];
	$suma_oe += $row['total_oe'];
	$suma_dpi += $row['total_dpi'];
	
	foreach ($row as $campo => $valor) {
		if($valor =='0') { $valor = "-"; }
		
		if($campo == 'total_cv' && $valor!=0 && $_SESSION['nivel']==1) { $valor = "<a href=\"sdir_principal_entidad.php?t=1&ent=$row[id_ent]\">$valor</a>"; }
	    if($campo == 'total_cv' && $valor!=0 && $_SESSION['nivel']==2) { $valor = "<a href=\"sdir_reportes_listado.php?ent_loc=&id_ent=$_SESSION[id_vlc]&col=3&dis_loc=".intval($row['descripcion'])."&tipo_reporte=2\">$valor</a>"; }
	    if($campo == 'total_cv' && $valor!=0 && $_SESSION['nivel']==3) { $valor = "<a href=\"sdir_reportes_listado.php?ent_loc=&id_ent=$_SESSION[id_vlc]&col=3&dis_loc=$_SESSION[id_vdi]&tipo_reporte=2\">$valor</a>"; }


		if($campo == 'total_cn' && $valor!=0 && $_SESSION['nivel']==1) { $valor = "<a href=\"sdir_principal_entidad.php?t=2&ent=$row[id_ent]\">$valor</a>"; }
	    if($campo == 'total_cn' && $valor!=0 && $_SESSION['nivel']==2) { $valor = "<a href=\"sdir_reportes_listado.php?ent_loc=&id_ent=$_SESSION[id_vlc]&col=3&dis_loc=".intval($row['descripcion'])."&tipo_reporte=4\">$valor</a>"; }
	    if($campo == 'total_cn' && $valor!=0 && $_SESSION['nivel']==3) { $valor = "<a href=\"sdir_reportes_listado.php?ent_loc=&id_ent=$_SESSION[id_vlc]&col=3&dis_loc=$_SESSION[id_vdi]&tipo_reporte=4\">$valor</a>"; }
		
		
		if($campo == 'total_oe' && $valor!=0 && $_SESSION['nivel']==1) { $valor = "<a href=\"sdir_principal_entidad.php?t=3&ent=$row[id_ent]\">$valor</a>"; }
	    if($campo == 'total_oe' && $valor!=0 && $_SESSION['nivel']==2) { $valor = "<a href=\"sdir_reportes_listado.php?ent_loc=&id_ent=$_SESSION[id_vlc]&col=3&dis_loc=".intval($row['descripcion'])."&tipo_reporte=6\">$valor</a>"; }
	    if($campo == 'total_oe' && $valor!=0 && $_SESSION['nivel']==3) { $valor = "<a href=\"sdir_reportes_listado.php?ent_loc=&id_ent=$_SESSION[id_vlc]&col=3&dis_loc=$_SESSION[id_vdi]&tipo_reporte=6\">$valor</a>"; }
		
		
		if($campo == 'total_dpi' && $valor!=0 && $_SESSION['nivel']==1) { $valor = "<a href=\"sdir_principal_entidad.php?t=4&ent=$row[id_ent]\">$valor</a>"; }
	    if($campo == 'total_dpi' && $valor!=0 && $_SESSION['nivel']==2) { $valor = "<a href=\"sdir_reportes_listado.php?ent_loc=&id_ent=$_SESSION[id_vlc]&col=3&dis_loc=".intval($row['descripcion'])."&tipo_reporte=8\">$valor</a>"; }
	    if($campo == 'total_dpi' && $valor!=0 && $_SESSION['nivel']==3) { $valor = "<a href=\"sdir_reportes_listado.php?ent_loc=&id_ent=$_SESSION[id_vlc]&col=3&dis_loc=$_SESSION[id_vdi]&tipo_reporte=8\">$valor</a>"; }
		$tpl->setVariable($campo, $valor);
	}
	$tpl->setVariable('colorea',$colorea);
	$tpl->parse('lista_pendientes');
}

$tpl->setVariable("suma_cv", $suma_cv);
$tpl->setVariable("suma_cn", $suma_cn);
$tpl->setVariable("suma_oe", $suma_oe);
$tpl->setVariable("suma_dpi", $suma_dpi);
$tpl->parse();

$tpl->show();
?>

<header>

<script language="javascript" type="text/javascript">
javascript:window.history.forward(1);
</script>

</header>