[@HtmlHead]
[@CSS_estilos]
[@Javascript]
[@jQuery]
<script>
function validar(f,accion){
    if($("#id_cargo").val()==''){
        alert("Seleccione el cargo del funcionario.");
        f.id_cargo.focus();
        return false;
    }
    if(fulltrim($("#nombre").val())==''){
        alert("Debe ingresar el nombre del funcionario.");
        f.nombre.focus();
        return false;
    }
    if(fulltrim($("#paterno").val())==''){
        alert("Debe ingresar el apellido paterno.");
        f.paterno.focus();
        return false;
    }
    if($("#id_tratamiento").val()==''){
        alert("Seleccione el tratamiento para el funcionario.");
        f.id_tratamiento.focus();
        return false;
    }
    if($('input:radio[name=sexo]:checked').length==''){
        alert("Seleccione el sexo del funcionario.");
        f.sexo.focus();
        return false;
    }
    if(fulltrim($("#telefono").val())==''){
        alert("Debe ingresar el n�mero telef�nico.");
        f.telefono.focus();
        return false;
    }
    if(fulltrim($("#telefonoip").val())==''){
        alert("Debe ingresar el n�mero IP de la red telef�nica.");
        f.telefonoip.focus();
        return false;
    }
    if(fulltrim($("#correo").val())==''){
        alert("Debe ingresar el correo electr�nico.");
        f.correo.focus();
        return false;
    }

    if(!EmailVerify($("#correo").val())){
        alert("La direcci�n de correo electr�nico no es valida, por favor, verifique.");
         f.correo.focus();
        return false;
    }

    // if($('input:radio[name=firma]:checked').length==''){
    //     alert("Seleccione si el funcionario firma oficios.");
    //     f.firma.focus();
    //     return false;
    // }
    save(accion);
}
function save(accion){
    var ajax_url = "personal_save.php";
    var accion = accion;    
    // Personal
    var id_cargo = $("#id_cargo").val();
    var cargo = $("#cargo").val();
    var id_personal = $("#id_personal").val();
    var id_adscripcion = $("#id_adscripcion").val();
    var ent = $("#ent").val();
    var dto = $("#dto").val();
    var nombre = $("#nombre").val();
    var paterno = $("#paterno").val();
    var materno = $("#materno").val();
    var id_tratamiento = $("#id_tratamiento").val();
    var tratamiento = $("#tratamiento").val();
    var sexo = $('input:radio[name=sexo]:checked').val();
    var lada = $("#lada").val();
    var telefono = $("#telefono").val();
    var telefono2 = $("#telefono2").val();
    var telefonoip = $("#telefonoip").val();
    var correo = $("#correo").val();
    var firma = $('input:radio[name=firma]:checked').val();
    // Adscripcion 
    var calle = $("#calle").val();
    var num_ext = $("#num_ext").val();
    var num_int = $("#num_int").val();
    var colonia = $("#colonia").val();
    var mpio_desc = $("#mpio_desc").val();
    var horario = $("#horario").val();
    var cp = $("#cp").val();
    $.ajax({
      type: 'POST',
      url: ajax_url,
      data: {
      accion : accion,
      id_cargo : id_cargo,
      cargo : cargo,
      id_personal : id_personal,
      id_adscripcion : id_adscripcion,
      ent : ent,
      dto : dto,
      nombre : nombre,
      paterno : paterno,
      materno : materno,
      id_tratamiento : id_tratamiento,
      tratamiento : tratamiento,
      sexo : sexo,
      lada : lada,
      telefono : telefono,
      telefono2 : telefono2,
      telefonoip : telefonoip,
      correo : correo,
      firma : firma,
      calle : calle,
      num_ext : num_ext,
      num_int : num_int,
      colonia : colonia,
      mpio_desc : mpio_desc,
      cp : cp,
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
          <input type="text" name='horario' id='horario' value='[@horario]' >
          </td>       
      </tr>
      <tr>
          <td class='table-label'>Siglas:&nbsp;</td>  
          <td class='table-field' colspan="3">[@corto]</td>   
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
        <td class='table-label'>Horario Atenci�n Vocalia RFE:&nbsp;</td>  
          <td class='table-field' colspan="3">[@horario]
            <input type="hidden" name='horario' id='horario' value='[@horario]' >
          </td>    
      </tr>
      <!-- Form -->
      <tr>
          <td class='table-label'><span class="label-required">*</span>Direcci�n:&nbsp;</td>  
          <td class='table-field' colspan="3">
          <select id="id_adscripcion" name="id_adscripcion">[@select_direccion]</select>
          </td>       
      </tr>
      <tr>
          <td class='table-label'><span class="label-required">*</span>Puesto:&nbsp;</td>  
          <td class='table-field' colspan="3">
          <select id="id_cargo" name="id_cargo">[@select_cargo]</select>
          <input type='hidden' name='cargo' id='cargo' value='[@cargo]' /> 
          </td>       
      </tr>
      <tr>
          <td class='table-label'><span class="label-required">*</span>Nombre:&nbsp;</td>  
          <td class='table-field' colspan="3"><input type="text" name='nombre' id='nombre' size='32' maxlength='32' value='[@nombre]' onkeyup="mayusc(this)" />
             <input type='hidden' name='id_personal' id='id_personal' value='[@id_personal]' onkeyup="mayusc(this)" />     
          </td>        
      </tr>
      <tr>
          <td class='table-label'><span class="label-required">*</span>Paterno:&nbsp;</td>  
          <td class='table-field' colspan="3"><input type="text" name='paterno' id='paterno' size='32' maxlength='32' value='[@paterno]' onkeyup="mayusc(this)"/></td>        
      </tr>
      <tr>
          <td class='table-label'>Materno:&nbsp;</td>  
          <td class='table-field' colspan="3"><input type="text" name='materno' id='materno' size='32' maxlength='32' value='[@materno]' onkeyup="mayusc(this)" /></td>        
      </tr>
      <tr>
          <td class='table-label'><span class="label-required">*</span>Tratamiento:&nbsp;</td>  
          <td class='table-field' colspan="3">
          <select id="id_tratamiento" name="id_tratamiento">[@select_tratamiento]</select>
          <input type='hidden' name='tratamiento' id='tratamiento' value='[@tratamiento]' />  
          </td>       
      </tr>
      <tr>
          <td class='table-label'><span class="label-required">*</span>Sexo:&nbsp;</td>  
          <td class='table-field' colspan="3">[@radio_sexo]
          </td>       
      </tr>    
      <tr>    <td class='table-label'>Lada:&nbsp;</td>    
      <td class='table-field'><input type="text" name='lada' id='lada' size='10' maxlength='10' value='[@lada]' onkeypress="return solo_num(event)" /></td>  
      <td class='table-label'><span class="label-required">*</span>Tel�fono1:&nbsp;</td>    
          <td class='table-field' ><input type="text" name='telefono' id='telefono' size='20' maxlength='20' value='[@telefono]' onkeypress="return solo_num(event)" /></td>    
      </tr>
      <tr>
          <td class='table-label'>Tel�fono2:&nbsp;</td>    
          <td class='table-field' ><input type="text" name='telefono2' id='telefono2' size='20' maxlength='20' value='[@telefono2]' onkeypress="return solo_num(event)" /></td>      
          <td class='table-label'><span class="label-required">*</span>Tel�fono IP:&nbsp;</td>    
          <td class='table-field' ><input type="text" name='telefonoip' id='telefonoip' size='20' maxlength='20' value='[@telefonoip]' onkeypress="return solo_num(event)" /></td>
      </tr>
      <tr>
          <td class='table-label'><span class="label-required">*</span>Correo:&nbsp;</td> 
          <td class='table-field' colspan="3"><input type="text" name='correo' id='correo' size='40' maxlength='80' value='[@correo]' /></td>  
      </tr>
      <tr>
          <td class='table-label'><span class="label-required">*</span>Firma Documentos:&nbsp;</td>    
          <td class='table-field' colspan="3">[@radio_firma]</td>  
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
            <div id="botones" style="display:[@botones];">
              <span id="btnCancelar" class="btn" onclick="cancelar();" title="Cancelar">Cancelar</span>
              <span id="btnAceptar" class="btn" onclick="validar(this.form,'[@btnDo]');" title="Guardar">Guardar</span>
            </div>
            <div id="botonesSrc" style="display:[@botonesSrc];">
              <span id="btnRegresar" class="btnBl" onclick="history.go(-1);" title="Regresar">Regresar</span>
            </div>
          </td>        
      </tr>
      </form>
  </table>
</div>