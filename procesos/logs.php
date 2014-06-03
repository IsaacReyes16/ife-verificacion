<?php
function explore_files($ruta){
   if (is_dir($ruta)) {
      if ($dh = opendir($ruta)) {
         while (($file = readdir($dh)) !== false) {
			if (is_file($ruta . $file)){
				echo "<a href='".$ruta.$file."' target='_blank'>".$file."</a><br>";
			}   
         }
      closedir($dh);
      }
   }else
      echo "<br>No es ruta valida";
} 

?>
<table width="100%" border="0" cellpadding="10" cellspacing="0" style="		-moz-box-shadow: 0 1px 5px #bbb;	-webkit-box-shadow: 0 1px 5px #bbb;	box-shadow: 0 1px 5px #bbb;	">
<tr>
    <td height="30"  align="left" valign="middle" bgcolor="#5a5a5a" colspan=2>
    	

    	<font class=big>Logs de Procesos eliminados en servidor MySQL - Direksys MX</font>
    	</td>		
    	<td bgcolor="#5a5a5a" align=right>    	
    		<a href="mysql_proccess.php" tagert="_self">[Regresar]</a>
            &nbsp;&nbsp;&nbsp;
            <a href="index.php?err=1" tagert="_self"><img src='salir.png' style='vertical-align:middle;' title=salir></a>
		</td>
  </tr>
</table>
<br />
<?php explore_files("./logs/"); ?>