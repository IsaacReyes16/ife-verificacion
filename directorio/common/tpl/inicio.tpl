[@HtmlHead]
[@CSS_estilos]
[@Javascript]
[@jQuery]
<script>
function quitar(id, nombre, tipo){
  if(tipo=='personal'){
    confirmar=confirm("¿Esta seguro de quitar al funcionario: "+nombre+", de la lista?");
    if (confirmar){save_personal('DELETE',id); }
  }else if(tipo=='direccion'){
    confirmar=confirm("¿Esta seguro de quitar la dirección: "+nombre+", de la lista?");
    if (confirmar){save_direccion('DELETE',id); }
  }
}
function save_personal(accion, id_persona){
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

function save_direccion(accion, id_adscripcion){
    var ajax_url = "adscripciones_save.php";
    var accion = accion;    
    var id_adscripcion = id_adscripcion;
    $.ajax({
      type: 'POST',
      url: ajax_url,
      data: {
      accion : accion,
      id_adscripcion : id_adscripcion
      },
      success: function(data){  
          if(data !=''){                                    
              if(data == 1){                  
                  alert("La dirección ha sido quitada de la lista.");
                  location.reload();
              }else{
                  alert("Error al quitar la dirección");
              }
          }else{
              alert("Error al envíar datos");
          }                                
      }
    });
}
</script>
<div id="contenido">
  <div id="btnSearch" class="btnBl" style="display:[@divSearch];" onclick="location.href='busqueda.php'"><img src="[@ImgPath]find.gif" valign="middle">&nbsp;Búsqueda</div>
  <div id="verDetalle" style="display:block;">
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
            <td class='table-label'>Horario Atención:&nbsp;</td>  
            <td class='table-field' colspan="3">[@horario]</td>   
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
            <th Colspan="4">DIRECCIONES&nbsp;</th>        
        </tr>
        [@direcciones]
        <tr>
            <td class='table-label'>
            <span id="btnAgregar" class="btn" onclick="location.href='adscripciones_agregar.php?id=[@id_adscripcion]'" title="Agregar dirección">Agregar...</span> 
            </td> 
            <td class='table-field' Colspan="3">&nbsp;</td>         
        </tr>
        <tr>
            <th Colspan="4">FUNCIONARIOS&nbsp;</th>        
        </tr>
        [@funcionarios]
        <tr>
            <td class='table-label'>
            <span id="btnAgregar" class="btn" onclick="location.href='personal_agregar.php?id=[@id_adscripcion]'" title="Agregar funcionario">Agregar...</span> 
            </td> 
            <td class='table-field' Colspan="3">&nbsp;</td>         
        </tr>
         <tr>
            <td class='table-field' Colspan="4">&nbsp;</td>        
        </tr>
        <tr>
            <td class='table-center' colspan="4" >
            <span id="btnCancelar" class="btn" onclick="ocultar();">Cerrar</span> 
            </td>        
        </tr>
        </form>
    </table>
    <div id="msjPie"><b>IMPORTANTE:</b> La información aquí presentada, es la que aparecerá en <b>toda la documentación</b> generada por el sistema.<br/><br/>
    <table align="right">
      <tr>
        <td>[@icoOK]</td>
        <td>Información actualizada.</td>
      </tr>
      <tr>
        <td>[@icoNotOK]</td>
        <td>Necesita ser actualizado.</td>
      </tr>
    </table>
    </div>
  </div>
</div>