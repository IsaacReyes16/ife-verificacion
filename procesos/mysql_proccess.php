<?php 
$timerefresh=5; //segundos
$url=$_SERVER['REQUEST_URI'];        
#header("Refresh: $timerefresh; URL=$url");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<style type="text/css">
.txt_script {
	font-size:9px;
	color:blue;
}
</style>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="jquery-1.9.1.min.js"></script>
<script language="javascript" src="o3m_funciones.js"></script>
<script> 
function Hora(){
	var fecha = new Date();
	var hora = fecha.getHours();
	var minuto = fecha.getMinutes();
	var segundo = fecha.getSeconds();
	if (hora < 10) {hora = "0" + hora}
	if (minuto < 10) {minuto = "0" + minuto}
	if (segundo < 10) {segundo = "0" + segundo};
	var ahora = hora + ":" + minuto + ":" + segundo;
	//tiempo = setTimeout('hora()',1000)
	return ahora;	
}
function autoRefresh(Conexion){	
//setInterval(VerTabla(Conexion,Hora()),5000);	
	VerTabla(Conexion,Hora());
	//setInterval(VerTabla(), 5000,Conexion,Hora());
	var id = setInterval("VerTabla("+Conexion+","+Hora()+")",3000);
	setTimeout("VerTabla("+Conexion+","+Hora()+")",15000);
}
function VerTabla(Conexion,Tiempo){
fajax('DivResultados','procesos_tabla.php',1,Conexion+"|",'Cargando...');
//alert(Tiempo);
//document.getElementById('Hora').innerHTML=Tiempo;
}
function Kill(IdProcess, Conexion){fajax('DivResultados','procesos_tabla.php',1,Conexion+"|"+IdProcess,'Cargando...');}
</script>
<title>Procesos de MySQL</title>


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<link href='http://fonts.googleapis.com/css?family=Shadows+Into+Light+Two' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Pontano+Sans' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Oxygen:300' rel='stylesheet' type='text/css'>

<link type="text/css" media="screen" rel="stylesheet" href="estilos.css" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>

<body>

<table width="100%" border="0" cellpadding="10" cellspacing="0" style="		-moz-box-shadow: 0 1px 5px #bbb;	-webkit-box-shadow: 0 1px 5px #bbb;	box-shadow: 0 1px 5px #bbb;	">
  <form name="f_datos" method="post" action="">
  <tr>
    <td height="30"  align="left" valign="middle" bgcolor="#5a5a5a" colspan=2>
    	

    	<font class=big>Procesos activos en servidor MySQL - DDVC-Verificacion</font>
    	</td>		
    	<td bgcolor="#5a5a5a" align=right>    	
    		<a href="logs.php" tagert="_self">[Logs]</a>
            &nbsp;&nbsp;&nbsp;
            <a href="index.php?err=1" tagert="_self"><img src='salir.png' style='vertical-align:middle;' title=salir></a>
		</td>
  </tr>
  <tr bgcolor=#ffffff>
    <td align=left colspan="2">
    	<table cellpadding=0 cellspacing=0 border=0>
    		<td height="19" align="right" valign="middle">Conexi&oacute;n:&nbsp;</td>
    <td width="286" valign="top"><label>
      <select name="conexion" id="conexion" onchange='autoRefresh(this.value)'>
        <option selected="selected"> </option>
        <option value="localhost">Localhost</option>
        <option value="produccion">Server Production</option>
        <!-- <option value="direksysmx11">direksysmx11 [Esclavo]</option> -->
                  </select>
    </label></td>
		  </table>
		</td>

  </tr> 
  <tr>

  </tr>  
  </form>
</table>



<div style="margin:15px;">
	<div id="DivResultados"></div>
</div>
</body>
</html>