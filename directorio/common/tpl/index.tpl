[@HtmlHead]
[@CSS_estilos]
[@Javascript]
[@jQuery]
<script>
function quitar(id_persona, nombre){
    confirmar=confirm("¿Esta seguro de quitar al funcionario: "+nombre+", de la lista?");
    if (confirmar){save('DELETE',id_persona); }
}
function save(accion, id_persona){
    var ajax_url = "personal_save.php";
    var accion = accion;    
    var id_personal = id_persona;
    var id_adscripcion = $("#id_adscripcion").val();
    $.ajax({
      type: 'POST',
      url: ajax_url,
      data: {
      accion : accion,
      id_personal : id_personal,
      id_adscripcion : id_adscripcion
      },
      success: function(data){  
          if(data !=''){                                    
              if(data == 1){                  
                  alert("El funcionario ha sido quitado de la lista.");
                  location.reload();
              }else{
                  alert("Error al quitar funcionario");
              }
          }else{
              alert("Error al envíar datos");
          }                                
      }
    });
}
</script>
<div id="contenido">
  <table border="0" width="100%">
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
      <!-- Form -->
      <tr>
          <td class='table-label'>[@direccionOk] Dirección:&nbsp;</td> 
          <td class='table-field' Colspan="3">[@direccion]&nbsp;<a href="adscripciones.php" class="">[Editar]</a></td>         
      </tr>
      <tr>
          <th Colspan="4">FUNCIONARIOS&nbsp;</th>        
      </tr>
      [@funcionarios]
      <tr>
          <td class='table-label'>
          <a href="personal_agregar.php?id=[@id_adscripcion]" class="">[Agregar]...</a>    
          </td> 
          <td class='table-field' Colspan="3">&nbsp;</td>         
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
          <td class='table-field' Colspan="4">&nbsp;</td>        
      </tr>
      <tr>
          <td class='table-center' colspan="4" ><input type="button" name='btnGuardar' id='btnGuardar' value='Confirmar' class="boton" onclick="validar(this.form,'UPDATE');"/></td>        
      </tr>
      </form>
  </table>
</div>