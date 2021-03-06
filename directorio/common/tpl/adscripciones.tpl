[@HtmlHead]
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
        alert("Debe ingresar el n�mero exterior.");
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
        alert("Debe ingresar el c�digo postal.");
        f.cp.focus();
        return false;
    }
    if(fulltrim($("#horario").val())==''){
        alert("Debe ingresar el Horario de atenci�n del la Vocal�a RFE.");
        f.horario.focus();
        return false;
    }
    save(accion);
}
function save(accion){
    var ajax_url = "adscripciones_save.php";
    var accion = accion;
    var id_adscripcion = $("#id_adscripcion").val();
    var adscripcion = $("#adscripcion").val();
    var ent = $("#ent").val();
    var dto = $("#dto").val();
    var corto = $("#corto").val();
    var id_area = $("#id_area").val();
    var calle = $("#calle").val();
    var num_ext = $("#num_ext").val();
    var num_int = $("#num_int").val();
    var colonia = $("#colonia").val();
    var mpio_desc = $("#mpio_desc").val();
    var cp = $("#cp").val();
    var lada = $("#lada").val();
    var telefono = $("#telefono").val();
    var fax = $("#fax").val();
    var horario = $("#horario").val();
    $.ajax({
      type: 'POST',
      url: ajax_url,
      data: {
      accion : accion,
      id_adscripcion : id_adscripcion,
      adscripcion : adscripcion,
      corto : corto,
      id_area : id_area,
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
      fax : fax,
      horario : horario
      },
      success: function(data){    
          if(data !=''){                                    
              if(data == 1){
                  alert("Informaci�n guardada correctamente.")
                  // location.reload();  
                  location.href="inicio.php";
              }else{
                  alert("Error al guardar");
              }
          }else{
              alert("Error al env�ar datos");
          }                                
      }
    });
}

function cancelar(){    
  confirmar=confirm("�Esta seguro de cancelar? \nNo se guardara ningun cambio."); 
  if (confirmar){location.href="inicio.php";}  
}
</script>
<div id="contenido">
  <table border="0" width="100%">
      <form name="f_datos" method="POST" action="">
      <tr>
      	<td class='table-label'>Adscripci�n:&nbsp;</td>	
          <td class='table-field' colspan="3">[@adscripcion]
          <input type='hidden' name='id_adscripcion' id='id_adscripcion' value='[@id_adscripcion]' />
          <input type='hidden' name='adscripcion' id='adscripcion' value='[@adscripcion]' />
          </td>		
  	</tr>
      <tr>
      	<td class='table-label'>Siglas:&nbsp;</td>	
          <td class='table-field' colspan="3">[@corto]
            <input type='hidden' name='corto' id='corto' value='[@corto]' />
          </td>	
  	</tr>    
      <tr>
      	<td class='table-label'>Organo:&nbsp;</td>	
          <td class='table-field' >[@organo]</td>		
      	<td class='table-label'>�rea:&nbsp;</td>	
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
      	<td class='table-label'>Tel�fono:&nbsp;</td>	
          <td class='table-field' ><input type="text" name='telefono' id='telefono' size='20' maxlength='20' value='[@telefono]' onkeypress="return solo_num(event)"/></td>		
      	<td class='table-label'>Fax:&nbsp;</td>	
          <td class='table-field' ><input type="text" name='fax' id='fax' size='10' maxlength='10' value='[@fax]' onkeypress="return solo_num(event)"/></td>		
  	</tr>
     <tr>
        <td class='table-label'><span class="label-required">*</span>Horario Atenci�n Vocalia RFE:&nbsp;</td>  
          <td class='table-field' colspan="3"><input type="text" name='horario' id='horario' size='50' maxlength='120' value='[@horario]' onkeyup="mayusc(this)" ></td>    
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
          <span id="btnCancelar" class="btn" onclick="cancelar();" title="Cancelar">Cancelar</span>
          <span id="btnAceptar" class="btn" onclick="validar(this.form,'[@btnDo]');" title="Guardar">Guardar</span>
          </td>
  	</tr>
      </form>
  </table>
  <br/>
  <table border="0" width="100%">
    <tr>
        <th Colspan="4">Funcionarios en est� direcci�n&nbsp;</th>        
    </tr>
    [@funcionarios]
  </table>
</div>