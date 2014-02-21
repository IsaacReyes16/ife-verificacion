[@HtmlHead]
[@CSS_estilos]
[@Javascript]
[@jQuery]
<script>
function buscar(){ 
  var ajax_url = "busqueda_src.php";
  var nombre = $("#nombre").val();
  var paterno = $("#paterno").val();
  var materno = $("#materno").val();
  var ent = $("#id_ent").val();
  var dto = $("#id_dto").val();
  var auth = 1;
  $.ajax({
      type: 'POST',
      url: ajax_url,
      data: {
      auth : auth,
      nombre : nombre,
      paterno : paterno,
      materno : materno,
      ent : ent,
      dto : dto
      },
      success: function(data){                                
        if(data != 0){    
          DivContenido("resultados", data); 
        }else{
            alert("Error al realizar la búsqueda");
        }
      }  
  });
}
function select_dto(ent){ 
  if(ent>0){
    var ajax_url = "common/php/build_select.php";
    var tipo = 'select_dto';
    $.ajax({
        type: 'POST',
        url: ajax_url,
        data: {
        tipo : tipo,
        ent : ent
        },
        success: function(data){ 
          if(data != 0){    
            DivContenido("select_dto", data); 
          }else{
              alert("Error al realizar la búsqueda");
          }
        }  
    });
  }else{DivContenido("select_dto", ""); }
}
function cancelar(){ 
  location.href="index.php";
}
</script>
<div id="contenido">
  <div id="btnSearch" class="btnBl" style="display:[@divSearch];" onclick="location.href='index.php'">Regresar</div>
  <div id="verDetalle" style="display:block;">
    <table border="0" width="100%">
        <form name="f_datos" method="POST" action="">
        <tr>
            <th Colspan="4">BÚSQUEDA&nbsp;</th>        
        </tr>
        <tr>
          <td class='table-label'>Nombre:&nbsp;</td>  
          <td class='table-field' colspan="3"><input type="text" name='nombre' id='nombre' size='32' maxlength='32' value='' onkeyup="mayusc(this)" />    
          </td>        
      </tr>
      <tr>
          <td class='table-label'>Paterno:&nbsp;</td>  
          <td class='table-field' colspan="3"><input type="text" name='paterno' id='paterno' size='32' maxlength='32' value='' onkeyup="mayusc(this)"/></td>        
      </tr>
      <tr>
          <td class='table-label'>Materno:&nbsp;</td>  
          <td class='table-field' colspan="3"><input type="text" name='materno' id='materno' size='32' maxlength='32' value='' onkeyup="mayusc(this)" /></td>        
      </tr>
        <tr>
            <td class='table-label'>Ent:&nbsp;</td> 
            <td class='table-field' >
            <select id="id_ent" name="id_ent" onchange="select_dto(this.value)">[@select_ent]</select>
            </td>   
        </tr>
        <tr>
            <td class='table-label'>Dtto:&nbsp;</td>    
            <td class='table-field' >
            <span id="select_dto"></span>
            </td>       
        </tr>
        <tr>
          <td class='table-center' colspan="4" >
          <span id="btnCancelar" class="btn" onclick="cancelar();" title="Cancelar">Cancelar</span>
          <span id="btnAceptar" class="btn" onclick="buscar();" title="Buscar">Buscar</span>
          </td>        
      </tr>
        </form>
    </table>    
  </div>
  <div id="resultados"></div>
</div>