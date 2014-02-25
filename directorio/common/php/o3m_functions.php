<?php
function parse_form_sanitizer($g,$p){
#Load form information ($_GET/$_POST) into array $in[], $cmd[] with sanitizer
#ejem: parse_form($_GET, $_POST);
	global $ins, $cmd;
	if(!empty($g)){
		$tvars = count($g);
		$vnames = array_keys($g);
		$vvalues = array_values($g);
	}elseif(!empty($p)){
		$tvars = count($p);
		$vnames = array_keys($p);
		$vvalues = array_values($p);
	}
	for($i=0;$i<$tvars;$i++){
		if($vnames[$i]=='cmd'){$cmd=$vvalues[$i];}
		$ins[$vnames[$i]]=sanitizer_url($vvalues[$i]);
	}
}
function parse_form($g,$p){
#Load form information ($_GET/$_POST) into array $in[], $cmd[] without sanitizer
#ejem: parse_form($_GET, $_POST);
	global $in, $cmd;
	if(!empty($g)){
		$tvars = count($g);
		$vnames = array_keys($g);
		$vvalues = array_values($g);
	}elseif(!empty($p)){
		$tvars = count($p);
		$vnames = array_keys($p);
		$vvalues = array_values($p);
	}
	for($i=0;$i<$tvars;$i++){
		if($vnames[$i]=='cmd'){$cmd=$vvalues[$i];}
		$in[$vnames[$i]]=$vvalues[$i];
	}
}
function sanitizer_url($param) {
#Sanitizes a url param
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]","}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;","â€”", "â€“", ",", "<", ".", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($param)));
	return $clean;
}
function fecha_larga_hoy(){
$dia=date("l");
if ($dia=="Monday") $dia="Lunes";
if ($dia=="Tuesday") $dia="Martes";
if ($dia=="Wednesday") $dia="Miercoles";
if ($dia=="Thursday") $dia="Jueves";
if ($dia=="Friday") $dia="Viernes";
if ($dia=="Saturday") $dia="Sabado";
if ($dia=="Sunday") $dia="Domingo";
$dia2=date("d");
$mes=date("F");
if ($mes=="January") $mes="Enero";
if ($mes=="February") $mes="Febrero";
if ($mes=="March") $mes="Marzo";
if ($mes=="April") $mes="Abril";
if ($mes=="May") $mes="Mayo";
if ($mes=="June") $mes="Junio";
if ($mes=="July") $mes="Julio";
if ($mes=="August") $mes="Agosto";
if ($mes=="September")$mes="Septiembre";
if ($mes=="October") $mes="Octubre";
if ($mes=="November") $mes="Noviembre";
if ($mes=="December") $mes="Diciembre";
$ano=date("Y");
return "$dia $dia2 de $mes del $ano";
}
/*O3M*/
?>