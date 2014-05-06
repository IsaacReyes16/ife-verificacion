<?php
/*Mando llamar la clase del Template Sigma*/
require_once 'HTML/Template/Sigma.php';

$tpl = new HTML_Template_Sigma('./tpl');

/*Determinamos el tipo de usuario que esta accediendo al sistema, pueden ser de tres tipos:
 1: Es un usuario de oficinas centrales y podrá ver la información global por entidad
 2: Es un usuario de vocalía local y puede ver la información de sus vocalías distritales
 3: Es un usuario de vocalía distrital y sólo puede ver la información de su distrito
 
 Esto es importante para determinar la información y la forma en que esta se mostrará
*/
if($_SESSION['id_vlc']==0 && $_SESSION['id_vdi']==0){ $_SESSION['nivel'] = 1; }
if($_SESSION['id_vlc']!=0 && $_SESSION['id_vdi']==0){ $_SESSION['nivel'] = 2; }
if($_SESSION['id_vlc']!=0 && $_SESSION['id_vdi']!=0){ $_SESSION['nivel'] = 3; }

switch($_SESSION['nivel']){

	case 1: $entidad = "Oficinas Centrales";
			$separador = " | ";
			$separado2 = "";
	break;
	case 2: $entidad = "select descripcion_ent from cat_entidades_ent where id_ent=".$_SESSION['id_vlc'];
			$entidad = mysql_query($entidad,$conn);
			$entidad = mysql_fetch_array($entidad);
			$entidad = $entidad['descripcion_ent'];
			$separador = " | Entidad: ";
			$separador2 = "";
	break;
	case 3:	$entidad = "select descripcion_ent from cat_entidades_ent where id_ent=".$_SESSION['id_vlc'];
			$entidad = mysql_query($entidad,$conn);
			$entidad = mysql_fetch_array($entidad);
			$entidad = $entidad['descripcion_ent'];
			$separador = " | Entidad: ";
			$separador2 = " | Distrito : ";
			$distrito = $_SESSION['id_vdi'];
	break;
}

// esta función nos ayudará a hacer las condiciones de los selects
//DATOS VIGENTES ******************************************************************************
function where($tabla){ 

	unset($where);
	
	switch ($tabla){

	case 'lis_cedulas_ced':
		if($_SESSION['id_vlc']!=0){$where[] = " entidad_act_ced = ".$_SESSION['id_vlc'];}
		if($_SESSION['id_vdi']!=0){$where[] = " distrito_act_ced = ".$_SESSION['id_vdi'];}
	break;
	
	case 'lis_seg_remesa_sre':
		if($_SESSION['id_vlc']!=0){$where[] = " id_ent = ".$_SESSION['id_vlc'];}
		if($_SESSION['id_vdi']!=0){$where[] = " id_vdi = ".$_SESSION['id_vdi'];}
	break;
	case 'remesas':
		if($_SESSION['id_vlc']!=0){$where[] = " cat_entidades_ent.id_ent = ".$_SESSION['id_vlc'];}
		if($_SESSION['id_vdi']!=0){$where[] = " id_dis = ".$_SESSION['id_vdi'];}
	break;

	case 'lis_testigo_bajas_tba':
		if($_SESSION['id_vlc']!=0){$where[] = " entidad = ".$_SESSION['id_vlc'];}
		if($_SESSION['id_vdi']!=0){$where[] = " distrito = ".$_SESSION['id_vdi'];}
	break;
	
	case 'lis_ciudadano_ciu':
		if($_SESSION['id_vlc']!=0){$where[] = " ent_act_ciu = ".$_SESSION['id_vlc'];}
		if($_SESSION['id_vdi']!=0){$where[] = " dis_act_ciu = ".$_SESSION['id_vdi'];}
	break;
	
	case 'cat_entidades_ent':
		if($_SESSION['id_vlc']!=0){$where[] = " id_ent = ".$_SESSION['id_vlc'];}
	break;
	
	case 'cat_distritos_dis':
		if($_SESSION['id_vlc']!=0){$where[] = " id_ent = ".$_SESSION['id_vlc'];}
		if($_SESSION['id_vdi']!=0){$where[] = " id_dis = ".$_SESSION['id_vdi'];}
	break;
	
	case 'lis_bajas_baj':
		if($_SESSION['id_vlc']!=0){$where[] = " ent_ant_baj = ".$_SESSION['id_vlc'];}
		if($_SESSION['id_vdi']!=0){$where[] = " dis_ant_baj = ".$_SESSION['id_vdi'];}
	break;
	}
	
	if(sizeof($where)>0){ $where = " and ".implode(" and ", $where); }
	return $where;
	
}

/* Función para obtener la fecha de hoy en formato: Sábado 31 de Mayo de 2008 */
function mes_hoy($mhoy) {
	switch($mhoy) {
		case 'January': $mhoy = 'Enero'; break;
		case 'February': $mhoy = 'Febrero'; break;
		case 'March': $mhoy = 'Marzo'; break;
		case 'April': $mhoy = 'Abril'; break;
		case 'May': $mhoy = 'Mayo'; break;
		case 'June': $mhoy = 'Junio'; break;
		case 'July': $mhoy = 'Julio'; break;
		case 'August': $mhoy = 'Agosto'; break;
		case 'September': $mhoy = 'Septiembre'; break;
		case 'October': $mhoy = 'Octubre'; break;
		case 'November': $mhoy = 'Noviembre'; break;
		case 'December': $mhoy = 'Dicimebre'; break;
		default: $mhoy = date('F');
	}
	return $mhoy;
}


function mes_medios($mmedio) {
	switch($mmedio) {
		case '01': $mmedio = 'enero'; break;
		case '02': $mmedio = 'febrero'; break;
		case '03': $mmedio = 'marzo'; break;
		case '04': $mmedio = 'abril'; break;
		case '05': $mmedio = 'mayo'; break;
		case '06': $mmedio = 'junio'; break;
		case '07': $mmedio = 'julio'; break;
		case '08': $mmedio = 'agosto'; break;
		case '09': $mmedio = 'septiembre'; break;
		case '10': $mmedio = 'octubre'; break;
		case '11': $mmedio = 'noviembre'; break;
		case '12': $mmedio = 'dicimebre'; break;
		default: $mmedio = date('F');
	}
	return $mmedio;
}

function fecha_hoy() {
	$dhoy = date('l');
	switch($dhoy) {
		case 'Monday': $dhoy = 'Lunes'; break;
		case 'Tuesday': $dhoy = 'Martes'; break;
		case 'Wednesday': $dhoy = 'Miércoles'; break;
		case 'Thursday': $dhoy = 'Jueves'; break;
		case 'Friday': $dhoy = 'Viernes'; break;
		case 'Saturday': $dhoy = 'Sábado'; break;
		case 'Sunday': $dhoy = 'Domingo'; break;
		default: $dhoy = date('l');
	}
	
	$mhoy = mes_hoy(date('F'));
	$nhoy = date('d');
	$ahoy = date('Y');	
	$fhoy = "$dhoy $nhoy de $mhoy de $ahoy";
	return $fhoy;	

}

function fecha_normal($fecha) { //Convertir fecha formato dd-mm-aaaa en aaaa-mm-dd

	$bloque = explode("-",$fecha);
	return $bloque[2]."-".$bloque[1]."-".$bloque[0];
}


// Funcion para mostrar la paginación de un query
// $pas : Paso que se ejecuta, (requerido)
// $qry : Query que se ejecuta para obtener el numero de filas (requerido en el paso 1)
// $tpl : template que se va utilizar para parsear los valores (requerido en el paso 2)

function paginacion($pas,$qry=null,$tpl=null, $var_qrystr=null) {

	//Inicio Variables de Paginacion
	global $conn;
	global $pag_lim;
	global $total_filas;
	global $num_filas;
	global $fila_inicio;
	global $clave_entidad;
	global $num_pagina;
	
	$num_filas = 30;
	$pag_lim = "";
	$valores_url = "";
	$clave_entidad = isset($_GET['clave_entidad']) ? intval($_GET['clave_entidad']) : "";
	$num_pagina = isset($_GET['num_pagina']) ? intval($_GET['num_pagina']) : 0;

	if(empty($total_filas)) {
		if(isset($_GET['total_filas'])) {
			$total_filas =  intval($_GET['total_filas']);
		} else {
			$qry = "select 
			        count(1) as total 
					from ($qry) as sub;";
			$res = mysql_query($qry,$conn);
			$row = mysql_fetch_array($res);
			$total_filas = $row['total'];	
		}
	}

	$fila_inicio = $num_pagina * $num_filas ;
	if (!isset($_GET['des'])) {  $pag_lim = " LIMIT $num_filas OFFSET $fila_inicio "; }

	if($pas==2){

		//Declaramos las variables que usamos en el segundo paso
		$pagina = $_SERVER["PHP_SELF"];
		$total_paginas = ceil($total_filas/$num_filas)-1;

		//Incluimos las variables GET independientes de la paginacion (provenientes del script local)
		//y tambien evitamos duplicar las variables utilizadas por la paginacion		
		
		$valores_url = "&total_filas=$total_filas" . $var_qrystr;
		
		if ($_SESSION['nivel'] == 1) {
			if($clave_entidad!=0 || $clave_entidad!="") {  $valores_url .="&clave_entidad=$clave_entidad";  }
		} else {
			$valores_url .= "&clave_entidad=$_SESSION[id_vlc]"; 
		}
	
		if (!empty($_SERVER['QUERY_STRING'])) {
			$parametros = explode("&", $_SERVER['QUERY_STRING']);
			$parametros_nuevos = array();
			foreach ($parametros as $parametro) {
				if (stristr($parametro, "num_pagina") == false && stristr($parametro, "total_filas") == false &&  stristr($parametro, "clave_entidad") == false) { array_push($parametros_nuevos, $parametro); }
			}
			if (count($parametros_nuevos)!= 0) { $valores_url .= "&" . htmlentities(implode("&", $parametros_nuevos)); }
		}
		
		//Inicio enlaces de navegación de la paginación
		$var_del = $fila_inicio+1;
		$var_al = $fila_inicio+$num_filas;
		if ($var_al > $total_filas) { $var_al = $total_filas; }
	
		if ($num_pagina > 0) {
			$nav_pri = "<a href=\"".$pagina."?num_pagina=0".$valores_url."\">&lt;&lt; Inicio</a>";
			$nav_ant = "&nbsp;&nbsp;<a href=\"".$pagina."?num_pagina=".max(0, $num_pagina - 1).$valores_url."\">&lt; Anterior</a>&nbsp;";
		} else {
			$nav_pri = "&nbsp;&nbsp;&nbsp;";
			$nav_ant = "&nbsp;&nbsp;&nbsp;";
		}
		if ($num_pagina < $total_paginas) {
			$nav_sig = "&nbsp;<a href=\"".$pagina."?num_pagina=".min($total_paginas, $num_pagina + 1).$valores_url."\">Siguiente &gt;</a>";
			$nav_ult = "&nbsp;&nbsp;<a href=\"".$pagina."?num_pagina=".$total_paginas.$valores_url."\">Fin &gt;&gt;</a>";
		} else {
			$nav_sig = "&nbsp;&nbsp;&nbsp;";
			$nav_ult = "&nbsp;&nbsp;&nbsp;";
		}
		//Fin enlaces de navegación de la paginación
	
		// Utilizamos la variable "des" para mostrar u omitir algunas etiquetas como
		// los enlaces de navegacion. Para los casos en que se quiere mandar el archivo
		// a descargar, por ser un reporte o nominativo
		
		if (isset($_GET['des']) && $_GET['des'] == 1) {
			$tpl->setVariable('estado_paginas',"<b>Mostrando $total_filas c&eacute;dulas</b>");
		}else{
			$tpl->setVariable('estado_paginas',"<b>Mostrando del $var_del al $var_al de $total_filas registros</b>");
			$tpl->setVariable('primero',$nav_pri);
			$tpl->setVariable('anterior',$nav_ant);
			$tpl->setVariable('siguiente',$nav_sig);
			$tpl->setVariable('ultimo',$nav_ult);
		}
		$valores_url .= "&descargar_xls=1"; 
		$nav_xls = "<a href=\"".$pagina."?num_pagina=0".$valores_url."\" target=\"_blank\">Descargar XLS</a>";
		$tpl->setVariable('descargaXLS', $nav_xls);
	}

}

/* 
* Funcion para dar formato a las variables antes de ingresar los valores a la base de datos. 
* $t = Tipo de variable  (1:numero,2:texto)
* $v = Valor
* return valor con formato 'cadena' ó entero ó 'NULL'
***/

function conv($t,$v) {
	
	$valor = "";
	
	switch($t) {
		case 1: // numeros
			$valor = ($v=="") ? 'NULL' : addslashes($v); //addslashes.— Añade barras invertidas a una cadena
		break;
		case 2: //texto
			$valor = ($v=="") ? 'NULL' : "'".addslashes($v)."'";
		break;
		case 3: //booleanos
			$valor = ($v=="") ? 'NULL' : "'".addslashes($v)."'";
		break;
		case 4: //arreglos en mysql
			$valor = ($v=="") ? 'NULL' : "('{".implode(",",$v)."}')"; 
		break;
		default: $valor = 'NULL';
	}
	
	return $valor;
}



$efectoi = "class=\"menu_c_b\" onMouseOver=\"this.style.backgroundImage = 'url(img/menu/rosa_c.gif)'; document.getElementById('imenu').src = 'img/menu/rosa_i.gif';\" onMouseOut=\"this.style.backgroundImage = 'url(img/menu/gris_c.gif)'; document.getElementById('imenu').src = 'img/menu/gris_i.gif';\"";

$efecto = "class=\"menu_c_b\" onMouseOver=\"this.style.backgroundImage = 'url(img/menu/rosa_c.gif)';\" onMouseOut=\"this.style.backgroundImage = 'url(img/menu/gris_c.gif)';\"";

$efectod = "class=\"menu_c\" onMouseOver=\"this.style.backgroundImage = 'url(img/menu/rosa_c.gif)'; document.getElementById('dmenu').src = 'img/menu/rosa_d.gif';\" onMouseOut=\"this.style.backgroundImage = 'url(img/menu/gris_c.gif)'; document.getElementById('dmenu').src = 'img/menu/gris_d.gif';\"";

$colorea = "onMouseOver=\"color(this,'1');\" onMouseOut=\"color(this,'0');\"";

//Incluimos las variables del encabezado dentro de un arreglo para parsearlas en conjunto
$arr_menu= array('fecha_hoy'=>fecha_hoy(),'efectoi'=>$efectoi,'efecto'=>$efecto,'efectod'=>$efectod,'nombre_usu'=>$_SESSION['nombre_completo'],'separador'=>$separador,'separador2'=>$separador2,'entidad'=>$entidad,'distrito'=>$distrito);

// Funcion que despliega un aviso en pantalla, generalmente un error
// la variable $arr es un arreglo que puede contener:
// $arr['titulo']	: El tipo de aviso, por defecto es "Error"
// $arr['mensaje'] 	: La descripcion del error que se mostrara en pantalla, por defecto "Ha ocurrido un error inesperado"
// $arr['enlace']   : La direccion a la que apuntara el enlace de "Continuar", este valor es opcional

function muestra_error($tpl=null,$arr=null) {
	
	global $arr_menu;
	if(empty($tpl)) { global $tpl;}
	
	$arr['titulo']  = (@empty($arr['titulo']))  ? "Error" : $arr['titulo'];
	$arr['mensaje'] = (@empty($arr['mensaje'])) ? "Ha ocurrido un error inesperado" : $arr['mensaje'];
	
	$tpl->loadTemplateFile("mensaje.html");
	$tpl->setVariable($arr_menu);
	$tpl->setVariable($arr);
	$tpl->show();
	exit;

}

/**
 * Función para los  <SELECT> </SELECT>  de los html cuando vengan d ela BD (Puede aplicar a los radio y checkbox)
 * $bloque  =  Bloque dep template a desplegar
 * $campos  =  Id y descripcion PE.  "id_est, descripcion_est"
 * $tabla   =  Tabla de la BDE que ocntiewne le catálogo
 * $where   =  Restricción Where
 * $order   =  Ordenamiento de la búsqueda
 * $val     =  Valor por defecto del option  (SELECTED)
 * */

function muestra_listado($bloque,$campos = "*",$tabla,$where=null,$order=null, $val=null){
	global $tpl;
	global $conn;
	if(!empty($where)) { $where = " WHERE $where "; }
	if(!empty($order)) { $order = " ORDER BY $order "; }
	if(!empty($val)) { $val = ", CASE WHEN $val THEN 'SELECTED' ELSE '' END AS sel_$tabla "; }
	
	
	$qry = "SELECT $campos $val FROM $tabla $where $order ;";
	$qry = mysql_query($qry,$conn);
	
	$tpl->setCurrentBlock($bloque);
	while ($res = mysql_fetch_array($qry)){
		foreach($res as $campo => $valor){
			$tpl -> setVariable($campo, utf8_decode(utf8_encode($valor)));
		}
		$tpl -> parseCurrentBlock();
	}	
}


/*
 * Función para generar archivos csv
 * $query : consulta con la que se generará el archivo
 * $archivo: Archivo destino donde se guardaran la consulta
 * */

function generar_csv($query, $archivo){
	global $conn;
	$result = mysql_query($query,$conn);
		
	if (!$result){
		print "Error al generar el archivo <b>$archivo </b> con el query :<br/> <b>$query </b>";
  		exit;
	}
		
	$fp = fopen($archivo, 'w');	
	for ($i = 0; $i < mysql_num_fields($result); $i++) {
		$encabezado[]= mysql_field_name ( $result , $i );
	}
	
	if (mysql_num_rows($result) > 0) {
		$bandera= 0;
		while ($row = mysql_fetch_assoc($result)) {

			if ($bandera == 0) {
				$txt_encabezado = '"'.implode('","', $encabezado).'"'."\n";
				fwrite($fp, $txt_encabezado);
				$bandera = 1;
				//print $txt_encabezado;
			}
	
			$separador = '"'.implode('","', $row).'"'."\n";
			fwrite($fp,utf8_decode($separador));
			//print utf8_decode($separador);
		}
		fclose($fp);
	}
	
} 

function texto_anio($anio) {
	$texto="";
	switch ($anio) {
		case '00': $texto=""; break;
		case '01': $texto="UNO"; break;
		case '02': $texto="DOS"; break;
		case '03': $texto="TRES"; break;
		case '04': $texto="CUATRO"; break;
		case '05': $texto="CINCO"; break;
		case '06': $texto="SEIS"; break;
		case '07': $texto="SIETE"; break;
		case '08': $texto="OCHO"; break;
		case '09': $texto="NUEVE"; break;
		case '10': $texto="DIEZ"; break;
		case '11': $texto="ONCE"; break;
		case '12': $texto="DOCE"; break;
		case '13': $texto="TRECE"; break;
		case '14': $texto="CATORCE"; break;
		case '15': $texto="QUINCE"; break;
		case '16': $texto="DIECISEIS"; break;
		case '17': $texto="DIECISIETE"; break;
		case '18': $texto="DIECIOCHO"; break;
		case '19': $texto="DIECINUEVE"; break;
		case '20': $texto="VEINTE"; break;
		default:   $texto="UNDEFINED";
	}
	return $texto;
}


function detalle_cedula_dom($row_ced){
	global $tpl;

	$tpl -> setCurrentBlock("datos");
	
	$tpl -> setVariable("consec_ciu",utf8_decode($row_ced['consec_ciu']));
	$tpl -> setVariable("clave_elec_ciu",utf8_decode($row_ced['clave_elec_ciu']));
	$tpl -> setVariable("paterno_ciu",utf8_decode($row_ced['paterno_ciu']));
	$tpl -> setVariable("materno_ciu",utf8_decode($row_ced['materno_ciu']));
	$tpl -> setVariable("nombre_ciu",utf8_decode($row_ced['nombre_ciu']));
	$tpl -> setVariable("fuar_ciu",utf8_decode($row_ced['fuar_ciu']));
	
	$tpl -> setVariable("tipo_tramite_ciu",utf8_decode($row_ced['tipo_tramite_ciu']));
	$tpl -> setVariable("fec_ult_tram_ciu",utf8_decode($row_ced['fec_ult_tram_ciu']));
	$tpl -> setVariable("ent_nac_ciu",utf8_decode($row_ced['ent_nac_ciu']));
	$tpl -> setVariable("nom_ent_nac_ciu",($row_ced['nom_ent_nac_ciu']));	
	$tpl -> setVariable("edad_ciu",utf8_decode($row_ced['edad_ciu']));
	
	###################### ent,dis
	$tpl -> setVariable("ent_act_ciu",utf8_decode($row_ced['ent_act_ciu']));
	$tpl -> setVariable("nom_ent_act_ciu",utf8_decode($row_ced['nom_ent_act_ciu']));
	$tpl -> setVariable("dis_act_ciu",utf8_decode($row_ced['dis_act_ciu']));	
	$tpl -> setVariable("mun_act_ciu",utf8_decode($row_ced['mun_act_ciu']));
	$tpl -> setVariable("nom_mun_act_ciu",utf8_decode($row_ced['nom_mun_act_ciu']));
	$tpl -> setVariable("seccion_act_ciu",utf8_decode($row_ced['seccion_act_ciu']));
	##############################
	
	$fec_gene = explode(' ', utf8_decode($row_ced['fec_gene']) );	
	$tpl -> setVariable("fec_gene", $fec_gene[1]); //fec de ent de cred del ciu
	
	$tpl -> setVariable("mecanismo",utf8_decode($row_ced['mecanismo'])); //Flujo 2014
	$tpl -> setVariable("instancia",utf8_decode($row_ced['instancia'])); //DERFE/COC
	
	$est_cv = $row_ced['est_cv']; //cedula verificacion
	$est_cv_ant = $row_ced['est_cv_ant']; //cedula verificacion	
	$est_ar = $row_ced['est_ar']; //edo_reg_cdi del registro
	$est_ar_final = $row_ced['est_ar_final']; //estatus final del registro
	$est_cn = $row_ced['est_cn']; //cedula notificacion
	$res_cn = $row_ced['res_cn']; //id_ccd de la cedula de notificacion
	$est_ac = $row_ced['est_ac']; //acta circunstanciada	
	$est_cm = $row_ced['est_cm']; //cedula medios
	
	$est_estrado_col = $row_ced['est_estrado_col']; //estrados colocacion
	$est_estrado_exi = $row_ced['est_estrado_exi']; //estrados exhibicion
	$est_estrado_pub = $row_ced['est_estrado_pub']; //estrados publicacion
	
    $res_ec = $row_ced['res_ec']; //fecha de estrados de colocacion
	$res_ee = $row_ced['res_ee']; //fecha de estrados de exhibicion
	$res_ep = $row_ced['res_ep']; //fecha de estrados de publicacion	
	
	$est_aac = $row_ced['est_aac']; //acta administrativa
	$est_ca = $row_ced['est_ca']; //cuestionario
	$est_aac_ant = $row_ced['est_aac_ant']; //acta administrativa_ant
	$est_ca_ant = $row_ced['est_ca_ant']; //cuestionario_ant
		
    $envio  = $row_ced['envio_ciu'];
	
	$res_fec_cn = $row_ced['res_fec_cn']; //fecha de la cedula de notificacion	aaaa-mm-dd	
	
	$ban_ac = ""; 
	
    ## Validaciones para informe de captura ##
	if($est_ar==1) { //Regular
		$est_cn = "NO APLICA";
		$est_ac = "NO APLICA";
		$est_estrado_col = "NO APLICA";
		$est_estrado_exi = "NO APLICA";
		$est_estrado_pub = "NO APLICA";
		$est_aac = "NO APLICA";
		$est_ca = "NO APLICA";
		$est_cm = "NO APLICA";
	}	

    if ($est_ar_final == 1){ //Regular
	   $est_cm = "NO APLICA";
	}	
	else if ($est_ar_final != 1){//Irregular
		if($est_cm =='LISTO') { //Cedula de Medios
			$est_cm = "<a href='sdir_guarda_cedula_medios.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_cm</a>";				
		}else if($est_cm == 'PENDIENTE') {
		
	//		if(($est_cv == "LISTO" and $est_cv_ant == "LISTO") ){
			   $est_cm = "<a href='sdir_captura_medios.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_cm</a>";              					
	    }			
		//	else{
		//	   $est_cm = "NO DISPONIBLE";
		//	}
    }
		 	
	if($est_cv=='PENDIENTE') {
		$est_cv = "<a href='sdir_captura.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_cv</a>";
		$est_cn = "NO DISPONIBLE";
		$est_ac = "NO DISPONIBLE";
		$est_estrado_col =  "NO DISPONIBLE";
		$est_estrado_exi =  "NO DISPONIBLE";
		$est_estrado_pub =  "NO DISPONIBLE";
		$est_aac =  "NO DISPONIBLE";		
		$est_ca = "NO DISPONIBLE";
		//$est_cm = "NO DISPONIBLE";
				
	} else if($est_cv=='LISTO') {
   		$est_cv = "<a href='sdir_guarda_cedula.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_cv</a>";
	}		

	if($est_cn=='PENDIENTE') {
		$est_cn = "<a href='sdir_captura_n.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_cn</a>";
		$est_ac = "NO DISPONIBLE";
		$est_estrado_col =  "NO DISPONIBLE";
		$est_estrado_exi =  "NO DISPONIBLE";
		$est_estrado_pub =  "NO DISPONIBLE";		
		$est_aac =  "NO DISPONIBLE";		
		$est_ca = "NO DISPONIBLE";						
				
	} else if($est_cn=='LISTO') {
   		$est_cn = "<a href='sdir_guarda_notif.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_cn</a>";   				
				
		if($res_cn>=1 and $res_cn<=16){ //Proceso normal		                            
	   	        $est_ac = "NO APLICA";
		}		
       
        else if($res_cn==17){ //Se suspendio el operativo
				if($est_ac =='LISTO') { //Acta circunstanciada
					$est_ac = "<a href='sdir_guarda_acta.php?consec=".$row_ced['consec_ciu']."'>$est_ac</a>";
					$ban_ac = 1;
									
				}else if($est_ac=='PENDIENTE') {
					$est_ac = "<a href='sdir_captura_acta.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_ac</a>";              					
				}				
		  		  
	     }	

		if($est_ca =='LISTO') { //Cuestionario

			$est_ca = "<a href='sdir_guarda_cuestionario.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_ca</a>";
							
		}else if($est_ca=='PENDIENTE') {
			if($res_cn==17){ //Se suspendio el operativo

				if($ban_ac == 1) { //Acta circunstanciada
			          $est_ca = "<a href='sdir_captura_c.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_ca</a>";              					
				}

				else if($ban_ac != 1) { //Acta circunstanciada
				        $est_ca="NO DISPONIBLE";				
				}		
			}	
			else if($res_cn!=17){
			    $est_ca = "<a href='sdir_captura_c.php?consec_ciu=".$row_ced['consec_ciu']."'>$est_ca</a>";              					
			}
		}		
	}	
    
	$tpl -> setVariable("est_cv",$est_cv);
	$tpl -> setVariable("est_cn",$est_cn);
	$tpl -> setVariable("est_ac",$est_ac);	
	$tpl -> setVariable("est_ca",$est_ca);
	
	if($_SESSION[nivel]==1 or $_SESSION[nivel]==2){
	   $tpl -> setVariable("est_cm",$est_cm);
       $tpl -> setVariable("medios","Cédula de Medios");
	}
	
	$tpl -> parse('datos');
	return true;
}

function datos_drr ($consec){ //Informe de captura
	global $conn;
	$where = where('lis_ciudadano_ciu');

	        $qry = "SELECT consec_ciu, 
		               clave_elec_ciu, 
					   paterno_ciu, 
					   materno_ciu, 
					   nombre_ciu, 
					   fuar_ciu, 
					   tipo_tramite_ciu, 
					   fec_ult_tram_ciu,
		               ent_nac_ciu, 
					   edad_ciu, 
					   
					   cp_act_ciu, 
					   ent_act_ciu, 
					   nom_ent_act_ciu, 
					   dis_act_ciu, 
					   mun_act_ciu, 
					   nom_mun_act_ciu, 
		               seccion_act_ciu,
					    
					   fec_ent_cred_ciu AS fec_gene, 
					   'OBSERVACIONES_PEL 2014: 01/04/2014' AS mecanismo, 
					   'DERFE/COC' AS instancia,
		               ent_nac.descrip_acento_ent AS nom_ent_nac_ciu, 
					   envio_ciu,			   
		
			(case when 
			   (select count(1) 
				   from lis_cedula_di_cdi_vig w 
				   where w.consec_ciu=a.consec_ciu) = 1 then 'LISTO' else 'PENDIENTE' 
			 end) as est_cv,
			 
			(case 1 when 1 then 
			   (select edo_reg_cdi 
					from lis_cedula_di_cdi_vig w 
					where w.consec_ciu=a.consec_ciu) 
			end) as est_ar,
									
			(case when 
				(select count(1) 
					 from lis_cedula_notif_cdn_vig w 
					 where w.consec_ciu=a.consec_ciu) = 1 then 'LISTO' else 'PENDIENTE' 
			end) as est_cn,
			
			(case 1 when 1 then 
				(select id_ccd 
					from lis_cedula_notif_cdn_vig w 
					where w.consec_ciu=a.consec_ciu) 
			end) as res_cn,
			
			(case 1 when 1 then 
				(select fec_not_cdn 
					from lis_cedula_notif_cdn_vig w 
					where w.consec_ciu=a.consec_ciu) 
				 end) as res_fec_cn,				 
	
			(case when 
				   (select count(1) 
					  from lis_acta_cir_aci_vig w 
					  where w.consec_ciu=a.consec_ciu) = 1 then 'LISTO' else 'PENDIENTE' 
			end) as est_ac,
			
			(case when 
				   (select count(1) 
					  from lis_estrados_col_vig w 
					  where w.consec_ciu=a.consec_ciu) = 1 then 'LISTO' else 'PENDIENTE' 
			end) as est_estrado_col,
	
			(case when 
				   (select count(1) 
					  from lis_estrados_exi_vig w 
					  where w.consec_ciu=a.consec_ciu) = 1 then 'LISTO' else 'PENDIENTE' 
			end) as est_estrado_exi,
					
			(case when 
				   (select count(1) 
					  from lis_estrados_pub_vig w 
					  where w.consec_ciu=a.consec_ciu) = 1 then 'LISTO' else 'PENDIENTE' 
			end) as est_estrado_pub,
	
			(case 1 when 1 then 
				(select fec_cap_es_col
				from lis_estrados_col_vig w 
				where w.consec_ciu=a.consec_ciu) 
			 end) as res_ec,			
		
			(case 1 when 1 then 
				(select fec_cap_es_exi
				from lis_estrados_exi_vig w 
				where w.consec_ciu=a.consec_ciu) 
			 end) as res_ee,
	
			(case 1 when 1 then 
				(select fec_cap_es_pub 
				from lis_estrados_pub_vig w 
				where w.consec_ciu=a.consec_ciu) 
			 end) as res_ep,
			 
			(case when 
				   (select count(1) 
					from lis_acta_adm_vig w 
					where w.consec_ciu=a.consec_ciu) = 1 then 'LISTO' else 'PENDIENTE' 
			end) as est_aac,
	
			(case when 
				    (select count(1) 
					from lis_cuestionario_aclara_cua_vig w 
					where w.consec_ciu=a.consec_ciu) = 1 then 'LISTO' else 'PENDIENTE' 
			end) as est_ca,			

			(case when 
				    (select count(1) 
					from lis_cedula_medios_vig w 
					where w.consec_ciu=a.consec_ciu) = 1 then 'LISTO' else 'PENDIENTE' 
			end) as est_cm					
				
			FROM lis_ciudadano_ciu a
			JOIN cat_entidades_ent ent_nac ON (ent_nac_ciu = ent_nac.id_ent)
			WHERE consec_ciu= $consec $where ";

   
	$qry = mysql_query($qry,$conn);
	$res = mysql_fetch_assoc ($qry);
	return $res;
}


function detalle_cedula_dom_baj ($row_ced){
	global $tpl;

	$tpl -> setCurrentBlock("datos");
	$tpl -> setVariable("consec_ciu",utf8_decode($row_ced['consec_baj']));
	$tpl -> setVariable("clave_elec_ciu",utf8_decode($row_ced['clave_elec_baj']));
	$tpl -> setVariable("paterno_ciu",utf8_decode($row_ced['paterno_baj']));
	$tpl -> setVariable("materno_ciu",utf8_decode($row_ced['materno_baj']));
	$tpl -> setVariable("nombre_ciu",utf8_decode($row_ced['nombre_baj']));
	$tpl -> setVariable("fuar_ciu",utf8_decode($row_ced['fuar_baj']));
	
	$tpl -> setVariable("tipo_tramite_ciu",utf8_decode($row_ced['tipo_tramite_baj']));
	$tpl -> setVariable("fec_ult_tram_ciu",utf8_decode($row_ced['fec_tram_baj']));
	$tpl -> setVariable("nom_ent_nac_ciu",($row_ced['desc_ent_nac_baj']));
	
	$tpl -> setVariable("edad_ciu",utf8_decode($row_ced['fec_nac_baj']));
	$tpl -> setVariable("ent_act_ciu",utf8_decode($row_ced['ent_ant_baj']));
	$tpl -> setVariable("nom_ent_act_ciu",utf8_decode($row_ced['nom_ent_ant_baj']));
	$tpl -> setVariable("dis_act_ciu",utf8_decode($row_ced['dis_ant_baj']));
	
	$tpl -> setVariable("mun_act_ciu",utf8_decode($row_ced['mun_ant_baj']));
	$tpl -> setVariable("nom_mun_act_ciu",utf8_decode($row_ced['nom_mun_ant_baj']));
	$tpl -> setVariable("seccion_act_ciu",utf8_decode($row_ced['seccion_ant_baj']));
	
	$tpl -> setVariable("oe_fec_orden_baj", $row_ced['oe_fec_orden_baj']);
	$tpl -> setVariable("oe_refer_baj",utf8_decode($row_ced['oe_refer_baj']));
	$tpl -> setVariable("oe_orde_exc_baj",utf8_decode($row_ced['oe_orde_exc_baj']));
	
	$tpl -> parse('datos');
	return true;
}


function datos_drr_baj ($consec){
	global $conn;
	$where = where('lis_bajas_baj');
	$qry = "SELECT
		consec_baj, paterno_baj, materno_baj, nombre_baj , 
		desc_ent_nac_baj, fec_nac_baj, sexo_baj, clave_elec_baj, fuar_baj, 
		fec_tram_baj , tipo_tramite_baj, ent_ant_baj, 
		nom_ent_ant_baj, dis_ant_baj, mun_ant_baj, nom_mun_ant_baj, seccion_ant_baj, manzana_ant_baj,
		oe_fec_orden_baj, oe_refer_baj, oe_orde_exc_baj
		FROM lis_bajas_baj_vig
		WHERE consec_baj= $consec $where ";
	$qry = mysql_query($qry,$conn);
	$res = mysql_fetch_assoc ($qry);
	return $res;
}

function borrar_cascada_ced ($consec, $tipo_borrado){
	global $conn;
	$qry = mysql_query("BEGIN;",$conn);
	$arr_borrar = Array();
	switch ($tipo_borrado){
		case 'ver':
			$qry =  "SELECT COUNT(1) FROM lis_cedula_di_cdi_vig WHERE consec_ciu= $consec";
			$qry = mysql_query($qry,$conn);
			$num_cdi = mysql_fetch_array($qry);
			
			if ($num_cdi[0] > 0) {
				$arr_borrar [] = 'Cédula de Verificación';
				$qry =  "UPDATE lis_cedula_di_cdi_vig SET usu_del_cdi = ". $_SESSION[id_usu] ." WHERE consec_ciu= $consec";
				$qry = mysql_query($qry,$conn);
				$arr_qry_del [] = "DELETE FROM lis_cedula_di_cdi_vig WHERE consec_ciu= $consec";
			}
		case 'not':
			$qry =  "SELECT COUNT(1) FROM lis_cedula_notif_cdn_vig WHERE consec_ciu= $consec";
			$qry = mysql_query($qry,$conn);
			$num_cdn = mysql_fetch_array($qry);
			
			if ($num_cdn[0] > 0) {
				$arr_borrar [] = 'Cédula de Notificación';
				$qry =  "UPDATE lis_cedula_notif_cdn_vig SET usu_del_cdn = ". $_SESSION[id_usu] ." WHERE consec_ciu= $consec";
				$qry = mysql_query($qry,$conn);
				$arr_qry_del [] = "DELETE FROM lis_cedula_notif_cdn_vig WHERE consec_ciu= $consec";
			}
		case 'acta':
			$qry =  "SELECT COUNT(1) FROM lis_acta_cir_aci_vig WHERE consec_ciu= $consec";
			$qry = mysql_query($qry,$conn);
			$num_cdn = mysql_fetch_array($qry);
			
			if ($num_cdn[0] > 0) {
				$arr_borrar [] = 'Acta Circunstansiada';
				$qry =  "UPDATE lis_acta_cir_aci_vig SET usu_del_aci = ". $_SESSION[id_usu] ." WHERE consec_ciu= $consec";
				$qry = mysql_query($qry,$conn);
				$arr_qry_del [] = "DELETE FROM lis_acta_cir_aci_vig WHERE consec_ciu= $consec";
			}			
		case 'cue':
			$qry =  "SELECT COUNT(1) FROM lis_cuestionario_aclara_cua_vig WHERE consec_ciu= $consec";
			$qry = mysql_query($qry,$conn);
			$num_cdn = mysql_fetch_array($qry);
			
			if ($num_cdn[0] > 0) {
				$arr_borrar [] = 'Cuestionario';
				$qry =  "UPDATE lis_cuestionario_aclara_cua_vig SET usu_del_cua = ". $_SESSION[id_usu] ." WHERE consec_ciu= $consec";
				$qry = mysql_query($qry,$conn);
				$arr_qry_del [] = "DELETE FROM lis_cuestionario_aclara_cua_vig WHERE consec_ciu= $consec";
			}
		case 'actaoe':
			$qry =  "SELECT COUNT(1) FROM lis_oexclusion_oex_vig WHERE consec_ciu= $consec";
			$qry = mysql_query($qry,$conn);
			$num_cdn = mysql_fetch_array($qry);
			
			if ($num_cdn[0] > 0) {
				$arr_borrar [] = 'Acta OE';
				$qry =  "UPDATE lis_oexclusion_oex_vig SET usu_del_oex = ". $_SESSION[id_usu] ." WHERE consec_ciu= $consec";
				$qry = mysql_query($qry,$conn);
				$arr_qry_del [] = "DELETE FROM lis_oexclusion_oex_vig WHERE consec_ciu= $consec";
			}	
		break;		
	}
	if (count($arr_qry_del) > 0){
		$arr_qry_del = array_reverse($arr_qry_del);
		$qry_del = implode("; ", $arr_qry_del); //Une elementos de un arreglo en una cadena, separado por el parametro que se le indique
		$qry = mysql_query($qry_del,$conn);
	}
	$qry = mysql_query("COMMIT;",$conn);

	if (count($arr_borrar) > 0) {
		return $arr_borrar[0];
		
	}else{
		$var = "";
		return $var;
	}
}
?>