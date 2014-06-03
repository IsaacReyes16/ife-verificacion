<?php
if(empty($conexion)){$conexion="localhost";}
switch($conexion){
case 'localhost' : $host="localhost"; $user="root"; $pass="";	break;
case 'produccion' : $host="verificacion.derfe.ife.org.mx"; $user="Israel"; $pass="angel";	break;
// case 'produccion' : $host="verificacion.derfe.ife.org.mx"; $user="root"; $pass="ife";	break;
// case 'produccion' : $host="verificacion.derfe.ife.org.mx"; $user="Oscar"; $pass="Oscar445.";	break;

}
$link=mysql_connect($host,$user,$pass)or die(mysql_error());

?>