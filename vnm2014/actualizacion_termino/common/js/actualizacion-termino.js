/*O3M*/
$(document).ready(function(){
	grupoTitulos();
});

function validar(folio){	
	var alerta = "Validación del folio: "+folio+" - ";
	// if($('input:radio[name=reemplazo_'+folio+']:checked').length==''){
 //        txt = alerta + "Seleccione si usó Reemplazo.";
	// 	message = $("<span class='error'><img src='common/img/no.png' width='30' valign='middle' align='center'>&nbsp "+txt+"</span>");
 //        showMessage(message);
 //        return false;
 //    }
	if($("#justificacion_"+folio).val()==''){
		txt = alerta + "Ingrese la Justificación.";
		message = $("<span class='error'><img src='common/img/no.png' width='30' valign='middle' align='center'>&nbsp "+txt+"</span>");
        showMessage(message);
        return false;
	}
	
	if($('#doc_'+folio).val()==''){
        txt = alerta + "Ingrese el documento digitalizado en PDF.";
		message = $("<span class='error'><img src='common/img/no.png' width='30' valign='middle' align='center'>&nbsp "+txt+"</span>");
        showMessage(message);
        return false;
    }
	return true;
}

function grupoTitulos(){
	$('#tabla-detalle > thead').append("<tr><td align='center'><img src='common/img/wait.gif' valign='middle' align='center'> Generando.<br/>Este proceso puede tardar varios minutos, por favor espere...</td></tr>");
	var ajax_url = "c.actualizacion-termino.php";
    var t = 'grupo-titulo';
    var ent = $("#ent").val();
    var dto = $("#dto").val();
    $.ajax({
      type: 'POST',
      url: ajax_url,
      async:true,   
      cache:false,
      dataType: "json",
      data: {      
	      auth : 1,
	      t : t,
	      ent : ent,
	      dto : dto,
	      soloPendientes : 1
      },
	    success: function(data){ 
	      $('#tabla-detalle > thead').empty();
	      var tableName = $('#tabla-detalle > tbody:last');	 
	      var claseLinea = 'class="grupo-label"';   
	      var claseLinea2 = 'class="grupo-titulo"';  
	      var claseValor = 'class="grupo-field"';
	      if(data!=null && data[0]!='CERO'){
	      	$.each(data, function(i, valor){	
	      		var enlace = 'class="link" onclick="desplegar(\''+valor.seccion+'-'+valor.manzana+'\')"';
	       		var rowTitles = 
						'<tr id="grupo-titulo"><td colspan="10"><div class="link" onclick="desplegar(\''+valor.seccion+'-'+valor.manzana+'\')" title="Desplegar/Ocultar"><table width="100%">'
						+'<tr id="grupo-titulo" align="center" >'
						+'	<td '+claseLinea+'>Sección</td>'
						+' 	<td '+claseLinea+'>Manzana</td>'
						+' 	<td '+claseLinea+'>Universo</td>'
						+' 	<td '+claseLinea+'>Univ-Estatus 1</span></td>'
						+' 	<td '+claseLinea+'>Univ-Estatus 2</td>'
						+' 	<td '+claseLinea+'>Univ-Estatus 3</td>'
						+' 	<td '+claseLinea+'>Capturadas</td>'
						+' 	<td '+claseLinea+'>Pendientes</td>'
						+'</tr>'
						+'<tr id="grupo-titulo" align="center" onclick="desplegar('+valor.seccion+'-'+valor.manzana+')">'
						+'	<td '+claseLinea2+'>'+valor.seccion+'</td>'
						+' 	<td '+claseLinea2+'>'+valor.manzana+'</td>'
						+' 	<td '+claseLinea2+'>'+valor.TotRegs+'</td>'
						+' 	<td '+claseLinea2+'>'+valor.TotEstatus1+'</td>'
						+' 	<td '+claseLinea2+'>'+valor.TotEstatus2+'</td>'
						+' 	<td '+claseLinea2+'>'+valor.TotEstatus3+'</td>'
						+' 	<td '+claseLinea2+'>'+valor.TotCapturas+'</td>'
						+' 	<td '+claseLinea2+'>'+valor.TotPendientes+'</td>'
						+'</tr>'
						+'</table></div></td></tr>'
						+'<tr><td >'
						+'<form id="frm_'+valor.seccion+'-'+valor.manzana+'" enctype="multipart/form-data" class="formulario">'
						+'<span id="div_'+valor.seccion+'-'+valor.manzana+'"><table id="tbl_'+valor.seccion+'-'+valor.manzana+'" width="100%"><tbody>'
						+'</tbody></table></span></form></td></tr>';      	
				tableName.append(rowTitles);
				grupoDetalle(ent, dto, valor.seccion, valor.manzana, 0);	
  			});		      
	      }else if(data[0]=='CERO'){
	      	mostrardiv('btnGenerar');
	      	ocultardiv('tabla-detalle');
	      	
	      }else{
	          alert("Error al crear grupos de tabla.");
	      }
	       
	    }  
	});
}

function grupoDetalle(ent, dto, secc, mza, reemplazo){
	var ajax_url = "c.actualizacion-termino.php";
    var t = 'grupo-detalle';
    var ent = ent;
    var dto = dto;
    var secc = secc;
    var mza = mza;
    $.ajax({
      type: 'POST',
      url: ajax_url,
      async:false,   
      cache:false,
      dataType: "json",
      data: {      
	      auth : 1,
	      t : t,
	      ent : ent,
	      dto : dto,
	      secc : secc,
	      mza : mza,
	      soloPendientes : 1
      },
	    success: function(data){ 
	      var tableName = $('#tbl_'+secc+'-'+mza+' > tbody:last');	 
	      var claseLinea = 'class="grupo-detalle"';   
	      var claseValor = 'class="folio"';
	      var reemplazoField = "";
	      if(data!=null){
	      	
	      	$.each(data, function(i, valor){
	      		if(reemplazo==0){
		      		reemplazoField = 'Si<input id="reemplazo-si" name="reemplazo_'+valor.folio+'" type="radio" value="1" onfocus="pintaFilaON(this.id, '+valor.folio+')" onclick="pintaFilaON(this.id, '+valor.folio+')" onblur="pintaFilaOFF(this.id, '+valor.folio+')"> No<input id="reemplazo-no" name="reemplazo_'+valor.folio+'" type="radio" value="0" onfocus="pintaFilaON(this.id, '+valor.folio+')" onclick="pintaFilaON(this.id, '+valor.folio+')" onblur="pintaFilaOFF(this.id, '+valor.folio+')">';
		      	}else{reemplazoField = '<label '+claseValor+' style="font-size:9px;">Fué utilizado en otro folio</label><input id="reemplazo-no" name="reemplazo_'+valor.folio+'" type="radio" value="0" checked>';}
	       		var rowData = '<tr id="tr_'+valor.folio+'" class="grupo-detalle">'
								+'	<td '+claseLinea+' width="30">&nbsp;</td>'
								+'	<td '+claseLinea+'>Consecutivo:&nbsp;<label '+claseValor+'>'+valor.folio+'</label><input type="hidden" id="folio" name="folio" value="'+valor.folio+'"></td>'
								// +'	<td '+claseLinea+' >Usó reemplazo?<br/>'+reemplazoField+'</td>'
								+'	<td '+claseLinea+' >Justificación:<br/> <textarea id="justificacion_'+valor.folio+'" name="justificacion_'+valor.folio+'" size="20" rows="2" onkeyup="mayusc(this)" onfocus="pintaFilaON(this.id, '+valor.folio+')" onblur="pintaFilaOFF(this.id, '+valor.folio+')"></textarea></td>'
								+'	<td '+claseLinea+' >Documento:<br/> <input type="file" id="doc_'+valor.folio+'" name="doc_'+valor.folio+'" onclick="pintaFilaON(this.id, '+valor.folio+')" onfocus="pintaFilaON(this.id, '+valor.folio+')" onblur="pintaFilaOFF(this.id, '+valor.folio+')"> </td>'
								+'	<td '+claseLinea+' align="center" valign="middle"><div id="ok_'+valor.folio+'" width="15" style="display:none;"><img src="common/img/ok.png" valign="middle" align="center" title="Guardada"></div><div id="btn_'+valor.folio+'" class="btn" onclick="verificaArchivo('+valor.folio+',\''+valor.seccion+'-'+valor.manzana+'\', '+ent+', '+dto+', '+secc+', '+mza+'); pintaFilaON(this.id, '+valor.folio+');" onblur="pintaFilaOFF(this.id, '+valor.folio+')">Guardar</span></td>'
								+'  <td>&nbsp;</td>'
								+'</tr>';      	
				tableName.append(rowData);
  			});		      
	      }else{
	          alert("Error al agregar detalles.");
	      }
	       
	    }  
	});
}

function generaArchivo(){	
	// $("#tabla-resultados").html("<img src='common/img/wait.gif' valign='middle'> Generando archivo.<br/>Este proceso puede tardar varios minutos, por favor espere...");
	var ajax_url = "c.actualizacion-termino.php";
    var t = 'pdf';
    var ent = $("#ent").val();
    var dto = $("#dto").val();
    $.ajax({
      type: 'POST',
      url: ajax_url,
      dataType: "json",
      data: {      
	      auth : 1,
	      t : t,
	      ent : ent,
	      dto : dto,
	      soloPendientes : 0
      },
      beforeSend: function(){    
			txt = "Generando documento, por favor espere...";
	        message = $("<span class='before'><img src='common/img/loader2.gif' valign='middle' align='center'>&nbsp "+txt+"</span>");
	        showMessage(message);      
	    },
      success: function(data){                           
      	if(data != 0){	
			if(data[0]=='pdf'){        			
    			txt = "Archivo generado correctamente.";
	            message = $("<span class='success'><img src='common/img/yes.png' width='30' valign='middle' align='center'>&nbsp "+txt+"</span>");
	            $("#tabla-resultados").html("Archivo Generado. <a href='"+data[1]+data[2]+"' title='Descargar' target='_blank'><br/><img src='common/img/pdf.gif' border='0' valing='middle'>"+data[2]+"</a>");
	        }else if(data[0]=='ERROR'){ 
	        	txt = data[1];
	        	message = $("<span class='error'><img src='common/img/no.png' width='30' valign='middle' align='center'>&nbsp "+txt+"</span>");
	    	}else{
	    		txt = "Error al generar archivo. "+data;
	        	message = $("<span class='error'><img src='common/img/no.png' width='30' valign='middle' align='center'>&nbsp "+"Ha ocurrido un error al guardar los datos.</span>");
	        }
	        showMessage(message); 	
      	}else{
			alert("Error al envíar datos"); 
		}  
	  }
    });
}

function desplegar(id){   
    $('#tabla-detalle > tbody span').hide();
    $('#div_'+id).toggle("slow");
}

function pintaFilaON(inputField, folio){
	$('#tr_'+folio).closest('#tr_'+folio).addClass('fila-actual');
}
function pintaFilaOFF(inputField, folio){
	$('#tr_'+folio).closest('#tr_'+folio).removeClass('fila-actual');
}

// #############################
// Funciones para subir archivo
// #############################
function verificaArchivo(folio, id, ent, dto, secc, mza){    
    var fileExtension = "";
	var inputFile = '#doc_'+folio;
	var archivo = 0;
	var alto = 0;
    if(validar(folio)){
    	if($(inputFile).val()!=''){	    	 		
		    //array con los datos del archivo
		    var file = $(inputFile)[0].files[0];
		    //nombre del archivo
		    var fileName = file.name;
		    //extensión del archivo
		    fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
		    //tamaño del archivo
		    var fileSize = file.size;
		    //tipo de archivo image/png ejemplo
		    var fileType = file.type; 
	    	if(isCorrect(fileExtension) ){
	    		showMessage("<span class='info'>Archivo para subir: "+fileName+", peso total: "+fileSize+" bytes.</span>");
	    		alto=0;
	    	}else{
	    		showMessage("<span class='error'><img src='common/img/no.png' width='30' valign='middle' align='center'>&nbsp El tipo de archivo es inválido - " + fileName + "</span>");
	    		alto=1;
	    	}
	    	archivo=1;
	    }	    	
    	if(!alto){
    		enviaFormulario(id, folio, archivo, ent, dto, secc, mza);
    	}

    }
}

function enviaFormulario(id, folio, archivo, ent, dto, secc, mza){	
    //campos de formulario
    var alerta = "Folio: "+folio+" - ";
    var ajax_url = "c.actualizacion-termino.php";
    var formData = new FormData($('#frm_'+id)[0]);    
    formData.append("auth", 1);
    formData.append("t", "save");
    formData.append("ent", ent);
    formData.append("dto", dto);
    formData.append("secc", secc);
    formData.append("mza", mza);    
    formData.append("archivo", archivo);
    formData.append("tipo", 'CAPTURA'); 
    formData.append("zona", 'URBANA'); 
    formData.append("folio", folio);    
    formData.append("reemplazo", 0);
    formData.append("justificacion", $("#justificacion_"+folio).val());
    var message = "";    
    $.ajax({
        url: ajax_url,  
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function(){        	
        	if(archivo){
    			txt = "Guardando archivo y datos, por favor espere...";
    		}else{
    			txt = "Guardando datos, por favor espere...";
    		}
            message = $("<span class='before'><img src='common/img/loader2.gif' valign='middle' align='center'>&nbsp "+alerta+txt+"</span>");
            showMessage(message);      
        },
        success: function(data){
        	if(data==1){
        		if(archivo){
        			txt = "El archivo y los datos se han guardado correctamente.";
        		}else{
        			txt = "Los datos se han guardado correctamente.";
        		}
	            message = $("<span class='success'><img src='common/img/yes.png' width='30' valign='middle' align='center'>&nbsp "+alerta+txt+"</span>");
	        }else if(data=='duplicado'){
	        	txt = "Este folio ya se capturó anteriormente.";
	        	message = $("<span class='error'><img src='common/img/no.png' width='30' valign='middle' align='center'>&nbsp "+alerta+txt+"</span>");
	    	}else{
	        	message = $("<span class='error'><img src='common/img/no.png' width='30' valign='middle' align='center'>&nbsp "+alerta+"Ha ocurrido un error al guardar los datos.</span>");
	        }
	        showMessage(message); 
	         $("#btn_"+folio).hide();
	         $("#ok_"+folio).show();
	         setTimeout(function(){	location.reload(true);}, 3000);
        },
        error: function(){
            message = $("<span class='error'><img src='common/img/no.png' width='30' valign='middle' align='center'>&nbsp "+alerta+"Ha ocurrido un error al subir el archivo.</span>");
            showMessage(message);
        }
    });
}
 
function showMessage(message){
	$("#popup_modal").show();
    $(".messages").show();
    $(".messages").html(message);
    setTimeout(function(){	hideMessage();}, 5000);
}

function hideMessage(){
	$("#popup_modal").hide();
    $(".messages").hide();
    $(".messages").empty();
}
 
function isCorrect(extension){
//Validación de tipo de archivo
    switch(extension.toLowerCase()){
        case 'pdf': case 'doc': case 'xdoc': case 'jpg':
            return true;
        break;
        default:
            return false;
        break;
    }
}

/*O3M*/