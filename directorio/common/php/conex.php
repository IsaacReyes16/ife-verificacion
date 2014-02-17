<?php /*O3M*/
###Conexión Data###
function SQLLink(){
##Conexion to MySQL
	$Server = "localhost";
	$User = "root";
	$Password = "osc445";
	$DataBase = "ife_ddvc_catalogos"; 
	$DBIConex = array('Server'=>$Server,'User'=>$User,'Password'=>$Password,'DataBase'=>$DataBase);
	##
	$Link=mysql_connect($DBIConex['Server'], $DBIConex['User'], $DBIConex['Password']) or die(mysql_error());
	mysql_select_db($DBIConex['DataBase'],$Link);
	mysql_query("SET NAMES 'utf8'", $Link);
	return $Link;
}
function SQLQuery($Sql='',$One=0, $Table=0){
##Excecute a SELECT query and return the results
	if(!empty($Sql)){
		$Cmd=array('SELECT');
		$vSql=explode(' ',$Sql);
		if(in_array(strtoupper($vSql[0]),$Cmd)){
			$Link=SQLLink();
			$Con=mysql_query($Sql, $Link)or die(mysql_error());	
			$TotRows=mysql_num_rows($Con);
			$TotCols=mysql_num_fields($Con);
			if($TotRows){		
				$y=0;
				$rKeys=array_keys(mysql_fetch_array($Con));	
				foreach($rKeys as $rkey){	
				##Table Titles in $Rows[0]
					if($z){$Rows[$y][$x] = $rKeys[$x]; $z=0;}else{$z++;}	
					$x++;
				}
				$y++;
				mysql_data_seek($Con,0);
				while($Row=mysql_fetch_array($Con, MYSQL_BOTH)){
				##First record in $Rows[1]...$Rows[n]
					for($x=0; $x<$TotCols; $x++){$Rows[$y][$x] = utf8_decode($Row[$x]);}
					$y++;
				}			
				if($Table){
				##Debug mode - Print HTML table with query results.
					$Result .= "<table class='tablaSQL' >";
					foreach($Rows as $Row){
						$label1 = (!$l)?"<th>":"<td>";
						$label2 = (!$l)?"</th>":"</td>";
						$Result .= "<tr>";
						foreach($Row as $Cell){$Result .= $label1.$Cell.$label2;}
						$Result .= "</tr>";
						$l++;
					}
					$Result .= "</table>";
				}else{
					if(!$One){
						$Result = $Rows;
					}else{
					##If return only one rows
						$rowsResutls=$Rows;
						$titles=$rowsResutls[0];
						unset($rowsResutls[0]);
						$Result  = array_combine($titles, $rowsResutls[1]);
					}
				}
			}else{$Result = 0;}
			mysql_free_result($Con); 
			mysql_close($Link);
		}else{$Result = "Error: Wrong SQL instruction";}
	}else{$Result = "Error: Empty sel-query";}
	return $Result;
}
function SQLExec($Sql=''){
##Execute a query and return a message
	global $Usuario;
	if(!empty($Sql)){
		$Cmd=array('INSERT', 'UPDATE', 'DELETE');
		$vSql=explode(' ',$Sql);
		if(in_array(strtoupper($vSql[0]),$Cmd)){
			$Link=SQLLink();
			$Con=mysql_query($Sql, $Link)or die(mysql_error());	
			$Id=mysql_insert_id($Link);
			$TotRows=mysql_affected_rows();
			if($TotRows){
				$Result = $TotRows;					
				#Identify table in query
				$idtable=mysql_insert_id();
				$action=strtoupper($vSql[0]);
				if($action=='INSERT'){
					$t=explode('INTO ',strtoupper($Sql));
					$t2=explode(' ',$t[1]);
					$table=strtolower($t2[0]);
					$Result=$Id;
				}				
				if($action=='UPDATE'){
					$t=explode(' ',strtoupper($Sql));
					$table=strtolower($t[1]);
				}				
				if($action=='DELETE'){
					$t=explode('FROM ',strtoupper($Sql));
					$t2=explode(' ',$t[1]);
					$table=strtolower($t2[0]);
				}		
				$Log=SQLLogs($table,$idtable,$action,$Sql,'',$Usuario['id']);	
			}else{
				$Result = 0;
			}
			mysql_close($Link);
		}else{$Result = false;}
	}else{$Result = false;}
	return $Result;
}
function SQLLogs($table='',$idtable='',$action='', $query='', $desc='', $iduser=''){
##Write logs in DB
	if(!empty($table)){
		$timestamp = date('Y-m-d H:i:s');
		$query=addslashes($query);
		$tbl_logs = "admin_logs_ddvc_catalogos";
		$Sql = "INSERT INTO $tbl_logs SET
				tablename='$table',
				id_table='$idtable',
				accion='$action',
				query='$query',
				txt='$desc',
				timestamp='$timestamp',
				id_usuario='$iduser';";			
		$Link=SQLLink();
		$Con=mysql_query($Sql, $Link)or die("Error en Tbl Logs: ".mysql_error());
	}else{return "Error al grabar logs en $tbl_logs";}
}
function SQLUser($ID=0, $Database='', $Table='', $IdField=''){
##Return data user from: Database.Table
	if($ID){
		$Sql = "SELECT * FROM $Database.$Table WHERE $IdField='$ID'";
		$User = SQLQuery($Sql,1);
		return $User;
	}
}
/*O3M*/
?>