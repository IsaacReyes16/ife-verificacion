<?php #import_request_variables("g,p","v_");
extract($_GET, EXTR_PREFIX_ALL, "v");
extract($_POST, EXTR_PREFIX_ALL, "v"); 
$v=explode("|",$v_v);
$ids=explode("-",$v[1]);
$ids_tot=count($ids);
if(!empty($v[0])){
	$conexion=$v[0];
	include("conex.php");
	if($ids_tot>0){
		for($x=0; $x<$ids_tot; $x++){
			if($ids[$x]>0){								
			// 	$sql="select * from information_schema.processlist where ID='$ids[$x]'";
			// 	$con=mysql_query($sql, $link)or die(mysql_error());
			// 	$row=mysql_fetch_array($con);
			// 	if($row>0){
			// 		##Log File
			// 		unset($logTxt);
			// 		foreach($row as $datalog){
			// 			$logTxt.=$datalog.'|';
			// 		}				
			// 		$logTxt=date('Y-m-d H:i:s')."-> ".$logTxt."\r\n";
			// 		$fileName="logs/mysql_kill_proccess_".date('Ymd').".txt";
			// 		if(file_exists($fileName)){chmod($fileName, 0777);}
			// 		$file = fopen($fileName, "a")or die("Error al crear archivo de logs");
			// 		fwrite($file, $logTxt);
			// 		fclose($file);
			// 		if(file_exists($fileName)){chmod($fileName, 0777);}
			// 		## End Log File
			// 		$sqlKill="KILL $ids[$x]";
			// 		$con=mysql_query($sqlKill, $link)or die(mysql_error());
			// 		$msj.="Se ha eliminado el Proceso Sleep ID $ids[$x]<br>";
			// 	}
				## End Log File
				$sqlKill="KILL $ids[$x]";			
				$con=mysql_query($sqlKill, $link)or die(mysql_error());
				$msj.="Se ha eliminado el Proceso Sleep ID $ids[$x]<br>";
			}
			
		}
	}
}
// $timerefresh=2; //segundos
// $url=$_SERVER['REQUEST_URI'];        
// header("Refresh: $timerefresh; URL=$url");
$sql="SHOW FULL PROCESSLIST;";
// $sql="select * from information_schema.processlist order by TIME desc";
$con=mysql_query($sql, $link)or die(mysql_error());
echo "<a href=\"javascript:VerTabla('$conexion')\" class=pag><img src='b_reload.gif' style='vertical-align:middle;'> Actualizar </a> &nbsp; &nbsp;".Date('[H:i:s]');
echo "<br><br><table border='0' cellspacing='0' cellpadding='0' width=100% style='border: 1px solid #333;'>";
echo "<tr bgcolor=#333>";
echo "<td><font color=#ffffff >ID</td>";
echo "<td><font color=#ffffff >User</td>";
echo "<td><font color=#ffffff >Host</td>";
echo "<td><font color=#ffffff >DB</td>";
echo "<td><font color=#ffffff >Command</td>";
echo "<td><font color=#ffffff >Time</td>";
echo "<td><font color=#ffffff width=200px>State</td>";
echo "<td><font color=#ffffff width=300px>Info</td>";
echo "<td><font color=#ffffff >Acci&oacute;n</td>";
echo "</tr>";
while($row=mysql_fetch_array($con)){
	$filterQuery=explode(' ',$row[7]);	
	$style="style='border-bottom:1px #ddd solid; '";
	echo "<tr>";
	for($x=0; $x<8; $x++){if(empty($row[$x])){$row[$x]="-";} echo "<td $style><abbr title='$row[$x]'>$row[$x]</abbr></td>";}
	if($row[4]=='Sleep'){ 
		echo "<td $style><a href=\"javascript:Kill($row[0],'$conexion')\" class=kill>[Kill]</a></td>"; $Ids.=$row[0]."-";
	}
	elseif($filterQuery[0]=='SELECT' && $row[5]>300){ 
		echo "<td $style><a href=\"javascript:Kill($row[0],'$conexion')\" class=kill>[Kill]</a></td>"; $Idsel.=$row[0]."-";
	}
	else{echo "<td $style>-</td>";}
	echo "</tr>";	
}
echo "</table><br><br>";
echo "<a href=\"javascript:VerTabla('$conexion')\" class=pag><img src='b_reload.gif' style='vertical-align:middle;'> Actualizar </a> &nbsp; &nbsp; <input type='button' class='inputs' name='b_sleep' id='b_sleep' value='Eliminar Todos los Procesos' onClick=\"Kill('$Ids','$conexion')\" />";
echo "<br><br>".$msj;
mysql_free_result($con);
mysql_close($link);
/*O3M*/
?>