<?php
##Includes
include_once('common/php/header.php');
##Bussines
if($ins['auth']){
	$filtro = (!empty($ins['ent']))?"and a.ent='$ins[ent]'":"";
	$filtro .= (!empty($ins['dto']))?"and a.dto='$ins[dto]'":"";
	$filtro .= (!empty($ins['nombre']))?"and a.nombre LIKE '%$ins[nombre]%'":"";
	$filtro .= (!empty($ins['paterno']))?"and a.paterno LIKE '$ins[paterno]%'":"";
	$filtro .= (!empty($ins['materno']))?"and a.materno LIKE '$ins[materno]%'":"";

	$sql = "SELECT 
			 a.id_personal
			,IFNULL(e.ent_minusc,'N/A') as entidad
			,IFNULL(f.dto_corto,'N/A') as distrito
			,CONCAT(c.tratamiento,' ',a.nombre,' ',a.paterno,' ',a.materno) as nombre_completo
			,d.cargo_ab as puesto
			,CONCAT(a.telefono,', ',a.telefono2,', ',a.telefonoip) as telefonos
			,a.correo
			FROM tbl_personal a
			LEFT JOIN tbl_adscripciones b USING(id_adscripcion)
			LEFT JOIN cat_tratamientos c USING(id_tratamiento)
			LEFT JOIN cat_cargos d USING(id_cargo)
			LEFT JOIN cat_entidades e ON a.ent=e.id_entidad
			LEFT JOIN cat_distritos f ON a.ent=f.ent and a.dto=f.dto
			WHERE 1 $filtro
			ORDER BY a.ent, a.dto, a.nombre, a.paterno, a.materno ASC;";
	$Rows = SQLQuery($sql);
	$TotRegs = count($Rows)-1;
	$msjTotalRegs = ($TotRegs)?'-- Se encontraron '.$TotRegs.' registros. --':'-- No se encontraron registros. --';
	$HTML='<table border="0" width="100%">
        <tr>
            <th Colspan="7">Resultados</th>        
        </tr>
        <tr>
          <td class="table-toplabel" >Entidad</td>
          <td class="table-toplabel" >Distrito</td>
          <td class="table-toplabel" >Nombre Completo&nbsp;</td>  
          <td class="table-toplabel" >Puesto</td>
          <td class="table-toplabel" >Tel&eacute;fono</td>        
          <td class="table-toplabel" >Correo</td>
          <td class="table-toplabel" >M&aacute;s</td>
      </tr>
      <tr style="background-color:#F7F7E6;">
        <td Colspan="7" align="center">'.$msjTotalRegs.'</td>        
      </tr>';

    if($TotRegs){
	    $i=0;
		foreach($Rows as $Row){
			$c=($i%2==0)?'#DAEFF2':'';
			if($i){
				$HTML .= "<tr style='background-color:$c'>
				          <td class='table-filed' >".$Row[1]."</td>
				          <td class='table-filed' >".$Row[2]."</td>
				          <td class='table-filed' >".$Row[3]."</td>  
				          <td class='table-filed' >".$Row[4]."</td>
				          <td class='table-filed' >".$Row[5]."</td>        
				          <td class='table-filed' >".$Row[6]."</td>   
				          <td class='table-filed' >"."<span id='btnEditar' class='btn' onclick='location.href=\"personal.php?id=".$Row[0]."&s=1\"' title='Ver M&aacute;s'>M&aacute;s</span>"."</td>

				      </tr>";
			}
			$i++;
		}
	}
	$HTML.='<tr>
            <td Colspan="7"></td>        
        </tr>
        </table>';
	echo utf8_encode($HTML);
}else{echo 0;}
?>