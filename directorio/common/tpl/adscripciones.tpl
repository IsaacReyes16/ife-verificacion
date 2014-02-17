[@CSS_estilos]
[@Javascript]
[@jQuery]
<script>
function validar(f,accion){
    if(fulltrim($("#calle").val())==''){
        alert("Debe ingresar el nombre de la calle.");
        f.calle.focus();
        return false;
    }
    if(fulltrim($("#num_ext").val())==''){
        alert("Debe ingresar el número exterior.");
        f.num_ext.focus();
        return false;
    }
    if(fulltrim($("#colonia").val())==''){
        alert("Debe ingresar la colonia.");
        f.colonia.focus();
        return false;
    }
    if(fulltrim($("#mpio_desc").val())==''){
        alert("Debe ingresar el municipio.");
        f.mpio_desc.focus();
        return false;
    }
    if(fulltrim($("#cp").val())==''){
        alert("Debe ingresar el código postal.");
        f.cp.focus();
        return false;
    }
    save(accion);
}
function save(accion){
    var ajax_url = "adscripciones_save.php";
    // var accion = accion;
    var id_adscripcion = $("#id_adscripcion").val();
    var ent = $("#ent").val();
    var dto = $("#dto").val();
    var calle = $("#calle").val();
    var num_ext = $("#num_ext").val();
    var num_int = $("#num_int").val();
    var colonia = $("#colonia").val();
    var mpio_desc = $("#mpio_desc").val();
    var cp = $("#cp").val();
    var lada = $("#lada").val();
    var telefono = $("#telefono").val();
    var fax = $("#fax").val();
    $.ajax({
      type: 'POST',
      url: ajax_url,
      data: {
      accion : accion,
      id_adscripcion : id_adscripcion,
      ent : ent,
      dto : dto,
      calle : calle,
      num_ext : num_ext,
      num_int : num_int,
      colonia : colonia,
      mpio_desc : mpio_desc,
      cp : cp,
      lada : lada,
      telefono : telefono,
      fax : fax
      },
      success: function(data){    
          if(data !=''){                                    
              if(data == 1){
                  alert("Información guardada correctamente.")
                  // location.reload();  
                  location.href="index.php";
              }else{
                  alert("Error al guardar");
              }
          }else{
              alert("Error al envíar datos");
          }                                
      }
    });
}

function cancelar(){    
  confirmar=confirm("¿Esta seguro de cancelar? \nNo se guardara ningun cambio."); 
  if (confirmar){location.href="index.php";}  
}
</script>
<table border="0">
    <form name="f_datos" method="POST" action="">
    <tr>
    	<td class='table-label'>Adscripción:&nbsp;</td>	
        <td class='table-field' colspan="3">[@adscripcion]
        <input type='hidden' name='id_adscripcion' id='id_adscripcion' value='[@id_adscripcion]' />
        </td>		
	</tr>
    <tr>
    	<td class='table-label'>Siglas:&nbsp;</td>	
        <td class='table-field' colspan="3">[@corto]</td>	
	</tr>    
    <tr>
    	<td class='table-label'>Organo:&nbsp;</td>	
        <td class='table-field' >[@organo]</td>		
    	<td class='table-label'>Área:&nbsp;</td>	
        <td class='table-field'>[@area]<input type='hidden' name='id_area' id='id_area' value='[@id_area]' /></td>		
	</tr>
    <tr>
    	<td class='table-label'>Ent:&nbsp;</td>	
        <td class='table-field' >[@entidad]<input type='hidden' name='ent' id='ent' value='[@id_ent]' /></td>	
    	<td class='table-label'>Dtto:&nbsp;</td>	
        <td class='table-field' >[@dto]<input type='hidden' name='dto' id='dto' value='[@id_dto]' /></td>		
	</tr>
    <tr>
    	<td class='table-label'><span class="label-required">*</span>Calle:&nbsp;</td>	
        <td class='table-field' colspan="3"><input type="text" name='calle' id='calle' size='50' maxlength='50' value='[@calle]' onkeyup="mayusc(this)" /></td>		
	</tr>
    <tr>
    	<td class='table-label'><span class="label-required">*</span>Num. Ext:&nbsp;</td>	
        <td class='table-field'><input type="text" name='num_ext' id='num_ext' size='12' maxlength='20' value='[@num_ext]' onkeyup="mayusc(this)" /></td>
    	<td class='table-label'>Num. Int:&nbsp;</td>	
        <td class='table-field'><input type="text" name='num_int' id='num_int' size='13' maxlength='20' value='[@num_int]' onkeyup="mayusc(this)" /></td>		
	</tr>
    <tr>
    	<td class='table-label'><span class="label-required">*</span>Colonia:&nbsp;</td>	
        <td class='table-field' colspan="3"><input type="text" name='colonia' id='colonia' size='50' maxlength='50' value='[@colonia]' onkeyup="mayusc(this)" /></td>		
	</tr>
    <tr>
    	<td class='table-label'><span class="label-required">*</span>Municipio:&nbsp;</td>	
        <td class='table-field' colspan="3"><input type="text" name='mpio_desc' id='mpio_desc' size='50' maxlength='50' value='[@mpio_desc]' onkeyup="mayusc(this)" ></td>		
	</tr>
    <tr>
    	<td class='table-label'><span class="label-required">*</span>CP:&nbsp;</td>	
        <td class='table-field' colspan="3"><input type="text" name='cp' id='cp' size='5' maxlength='5' value='[@cp]' onkeypress="return solo_num(event)"/></td>	</tr>
    <tr>	<td class='table-label'>Lada:&nbsp;</td>	
    <td class='table-field' colspan="3"><input type="text" name='lada' id='lada' size='10' maxlength='10' value='[@lada]' onkeypress="return solo_num(event)"/></td>		
	</tr>
    <tr>
    	<td class='table-label'>Teléfono:&nbsp;</td>	
        <td class='table-field' ><input type="text" name='telefono' id='telefono' size='20' maxlength='20' value='[@telefono]' onkeypress="return solo_num(event)"/></td>		
    	<td class='table-label'>Fax:&nbsp;</td>	
        <td class='table-field' ><input type="text" name='fax' id='fax' size='10' maxlength='10' value='[@fax]' onkeypress="return solo_num(event)"/></td>		
	</tr>
    <!-- 
    <tr>
    	<td class='table-label'>Activo:&nbsp;</td>	
        <td class='table-field' colspan="3">
        <select id="activo" name="activo">[@select_activo]</select>
        </td>		
	</tr>
    <!-- -->
    <tr>
    	<td class='table-label'>Actualizado el:&nbsp;</td>	
        <td class='table-field' >[@actualizado]</td>	
    	<td class='table-label'>Usuario:&nbsp;</td>	
        <td class='table-field' >[@id_usuario]<input type="hidden" name='id_usuario' id='id_usuario' size='11' value='[@id_usuario]' /></td>		
	</tr>
    
    <tr>
        <td class='table-center' colspan="4" >
        <input type="button" name='btncancelar' id='btncancelar' value='Cancelar' class="boton" onclick="cancelar();"/>
        <input type="button" name='btnGuardar' id='btnGuardar' value='Guardar' class="boton" onclick="validar(this.form,'UPDATE');"/></td>		
	</tr>
    </form>
</table>