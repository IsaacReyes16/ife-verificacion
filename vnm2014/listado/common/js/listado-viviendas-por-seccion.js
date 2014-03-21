$(document).ready(function(){
	// Contruye select_municipios
	select_municipio();
});

function select_municipio(){
	var ajax_url = "common/php/build_inputs.php";
	var input = "municipios";
	var ent = $("#ent").val();
    var dto = $("#dto").val();
	$.ajax({
	    type: 'POST',
	    url: ajax_url,
	    dataType: "json",
	    data: {
	    	input : input,
      		ent : ent,
	    	dto : dto
	    },
	    success: function(data){ 
	      var inputName = $('#municipio');
	      inputName.empty();
	      inputName.append('<option value="">--Seleccione--</option>');
	      if(data!=null){$.each(data, function(i, valor){	      	
			inputName.append('<option value="'+valor[0]+'">'+valor[1]+'</option>');
      			});		      
	      }else{
	          alert("Error al crear input.");
	      }
	       
	    }  
	});
}

function select_seccion(){
	var ajax_url = "common/php/build_inputs.php";
	var input = "secciones";
	var ent = $("#ent").val();
    var dto = $("#dto").val();
    var mpio = $("#municipio").val();
	$.ajax({
	    type: 'POST',
	    url: ajax_url,
	    dataType: "json",
	    data: {
	    	input : input,
      		ent : ent,
	    	dto : dto,
	    	mpio : mpio
	    },
	    success: function(data){ 
	      var inputName = $('#seccion');
	      inputName.empty();
	      inputName.append('<option value="">--Seleccione--</option>');
	      if(data!=null){$.each(data, function(i, valor){
			inputName.append('<option value="'+valor[0]+'">'+valor[1]+'</option>');
      			});		      
	      }else{
	          alert("Error al crear input.");
	      }
	       
	    }  
	});
}

function generaArchivo(){	
	$("#tabla-resultados").html("<img src='common/img/wait.gif' valign='middle'> Generando archivo.<br/>Este proceso puede tardar varios minutos, por favor espere...");
	var ajax_url = "c.listado-viviendas-por-seccion.php";
    var t = 'pdf';
    var ent = $("#ent").val();
    var dto = $("#dto").val();
    var municipio = $("#municipio").val();
    var seccion = $("#seccion").val();
    var tipo = $("#tipo").val();
    $.ajax({
      type: 'POST',
      url: ajax_url,
      dataType: "json",
      data: {      
	      auth : 1,
	      t : t,
	      ent : ent,
	      dto : dto,
	      municipio : municipio,
	      seccion : seccion,
	      tipo : tipo
      },
      success: function(data){                           
      	if(data != 0){  
			if(data[0]=='pdf'){  
				$("#tabla-resultados").html("Archivo Generado. <a href='"+data[1]+data[2]+"' title='Descargar' target='_blank'><br/><img src='common/img/pdf.gif' border='0' valing='middle'>"+data[2]+"</a>");
			}else if(data[0]=='ERROR'){ 
				$("#tabla-resultados").html(''); 
				alert(data[1]);
			}else{
				$("#tabla-resultados").html("Error al generar archivo. "+data);
			}			
      	}else{
			alert("Error al envíar datos"); 
		}  
	  }
    });
}