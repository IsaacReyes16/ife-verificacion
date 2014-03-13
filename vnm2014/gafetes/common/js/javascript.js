$(document).ready(function(){	
	listado();
	$('#cve_elector').mask('SSSSSS00000000Z000', {translation:  {'Z': {pattern: /[H,M]/, optional: false}}}); 
});

function listado(){
	var ajax_url = "imp_gafete.php";
	var t = "lista";
	var ent = $("#ent").val();
    var dto = $("#dto").val();
	$.ajax({
	    type: 'POST',
	    url: ajax_url,
	    dataType: "json",
	    data: {
	    	t : t,
      		auth : 1,
      		ent : ent,
	    	dto : dto
	    },
	    success: function(data){ 
	      if(data!=null){
	      		$('#tbl_resultados tbody').empty();
	      		$('#tbl_resultados tfoot').html('<tr><td colspan="2"></td></tr>');
	       		$.each(data, function(i, valor){
	       			var cons = i+1;
	       			if(cons<10){cons = '0'+cons;}
	       			var tipo  =valor.tipo.substr(0,3);
      				$('#tbl_resultados > tbody:last').append('<tr><td class="table-label-l"><a href="#tabla-ingreso"><span id="editar" name="editar" class="enlace" onclick="editar('+valor.id_gafete+')"><img src="common/img/edit.png" valign="middle" border="0" title="Editar"></span></a> '+cons+'- '+tipo+' | '+valor.clave+' - '+valor.nombre_completo+'</td><td class="table-label-c"><!--span class="btn"  onclick="imprimir('+valor.id_gafete+','+"'PDF'"+');">PDF</span--><span class="btn"  onclick="imprimir('+valor.id_gafete+','+"'RTF'"+');">Descargar</span></td></tr>');
      				$("#tbl_resultados tbody tr:even").css("background-color", "#EEE");
					$("#tbl_resultados tbody tr:odd").css("background-color", "#FFF");
					$("#vocal_nombre").val(valor.vocal_nombre);
					$("#vocal_puesto").val(valor.vocal_puesto);
      			});		      
	      }else{
	          // alert("Sin datos.");
	      }
	       
	    }  
	});
}

function imprimir(id,t){ 
	$("#divResultado").html("<img src='common/img/wait.gif' valign='middle'> Generando archivo.<br/>Este proceso puede tardar varios minutos, por favor espere...");
	var ajax_url = "imp_gafete.php";
	var t = t;
	var ent = $("#ent").val();
    var dto = $("#dto").val();
	var id = id;	
	$.ajax({
	    type: 'POST',
	    url: ajax_url,
	    dataType: "json",
	    data: {
	    	auth : 1,
	    	t : t,
	    	ent : ent,
	    	dto : dto,
	    	id : id      		
	    },
	    success: function(data){ 
	      if(data != 0){    
	      	// if(data[0]=='pdf'){
	        	// $("#divResultado").html("Archivo Generado. <a href='"+data[1]+data[2]+"' title='Descargar'><br/><img src='common/img/pdf.gif' border='0' valing='middle'>"+data[2]+"</a>"); 
	        	window.open(data[1]+data[2],'_self');
	       	// }
	      }else{
	          alert("Error al generar archivo.");
	      }
	    }  
	});
}

function agregar(){ 
	validar();
if(confirm("Se guardarán los datos con el tipo: "+$("#tipo").val()+" \r\n¿Desea continuar?")) {
		$("#divResultado").html("<img src='common/img/wait.gif' valign='middle'> Guardando información, por favor, espere...");
		var ajax_url = "imp_gafete.php";
		var t = "ADD";
		var tipo = $("#tipo").val();
		var ent = $("#ent").val();
	    var dto = $("#dto").val();
	    var puesto = $("#puesto").val();
	    var nombre = $("#nombre").val();
	    var paterno = $("#paterno").val();
	    var materno = $("#materno").val();	
	    var cve_elector = $("#cve_elector").val();
	    // var clave = $("#clave").val();
	    var vocal_nombre = $("#vocal_nombre").val();
	    var vocal_puesto = $("#vocal_puesto").val();
	    var vigencia = $("#vigencia").val();
		$.ajax({
		    type: 'POST',
		    url: ajax_url,
		    data: {
		    	auth : 1,
		    	t : t,
		    	tipo : tipo,
		    	ent : ent,
		    	dto : dto,
		    	puesto : puesto,
		    	nombre : nombre,
		    	paterno : paterno,
		    	materno : materno,
		    	cve_elector : cve_elector,
		    	// clave : clave,
		    	vocal_nombre : vocal_nombre,
		    	vocal_puesto : vocal_puesto,
		    	vigencia : vigencia
		    },
		    success: function(data){ 
		      if(data != 0){    
		        	listado();
		      }else{
		          alert("Error al guardar datos.");
		      }
		    }  
		});
	}
}

function editar(id){
	var ajax_url = "imp_gafete.php";
	var t = "editar";
	var ent = $("#ent").val();
    var dto = $("#dto").val();
	var id = id;	
	$.ajax({
	    type: 'POST',
	    url: ajax_url,
	    dataType: "json",
	    data: {
	    	auth : 1,
	    	t : t,
	    	ent : ent,
	    	dto : dto,
	    	id : id      		
	    },
	    success: function(data){ 
	      if(data != 0){    
	      		$.each(data, function(i, valor){
	      			$("#id_gafete").val(valor.id_gafete);
	      			$("#tipo").val(valor.tipo);
	      			$("#puesto").val(valor.puesto);
	      			$("#puesto_old").val(valor.puesto);
	      			$("#nombre").val(valor.nombre);
	      			$("#paterno").val(valor.paterno);
	      			$("#materno").val(valor.materno);
	      			$("#cve_elector").val(valor.cve_elector);
	      			$("#vocal_nombre").val(valor.vocal_nombre);
	      			$("#vocal_puesto").val(valor.vocal_puesto);
	      			$("#clave").val(valor.clave);
	      			$("#vigencia").val(valor.vigencia);
	      			$('#botones').html('<div id="btnActualizar" class="btn" onclick="actualizar();">:: Actualizar ::</div>&nbsp;<div id="btnActualizar" class="btn" onclick="recargar();">:: Cancelar ::</div>');
	      		});
	      }else{
	          alert("Error accesar a la información.");
	      }
	    }  
	});
}

function recargar(){
	location.reload(true);
}

function actualizar(){
validar();
if(confirm("Se actualizaran los datos del registro con clave: "+$("#clave").val()+" \r\n¿Desea continuar?")) {
		$("#divResultado").html("<img src='common/img/wait.gif' valign='middle'> Actualizando información, por favor, espere...");
		var ajax_url = "imp_gafete.php";
		var t = "update";
		var id_gafete = $("#id_gafete").val();
		var tipo = $("#tipo").val();
		var ent = $("#ent").val();
	    var dto = $("#dto").val();
	    var puesto = $("#puesto").val();
	    var puesto_old = $("#puesto_old").val();
	    var nombre = $("#nombre").val();
	    var paterno = $("#paterno").val();
	    var materno = $("#materno").val();	
	    var cve_elector = $("#cve_elector").val();
	    var clave = $("#clave").val();
	    var vocal_nombre = $("#vocal_nombre").val();
	    var vocal_puesto = $("#vocal_puesto").val();
	    var vigencia = $("#vigencia").val();
		$.ajax({
		    type: 'POST',
		    url: ajax_url,
		    data: {
		    	auth : 1,
		    	t : t,
		    	id_gafete : id_gafete,
		    	tipo : tipo,
		    	ent : ent,
		    	dto : dto,
		    	puesto : puesto,
		    	puesto_old : puesto_old,
		    	nombre : nombre,
		    	paterno : paterno,
		    	materno : materno,
		    	cve_elector : cve_elector,
		    	clave : clave,
		    	vocal_nombre : vocal_nombre,
		    	vocal_puesto : vocal_puesto,
		    	vigencia : vigencia
		    },
		    success: function(data){ 
		      if(data != 0){    
		        	listado();
		        	alert("Información actualizada correctamente.");
		      }else{
		          alert("Error al actualizar datos.");
		      }
		    }  
		});
	}
}


function validar(){
    var puesto = $("#puesto").val();
    var nombre = $("#nombre").val();
    var paterno = $("#paterno").val();	
    var cve_elector = $("#cve_elector").val();
    var vocal_nombre = $("#vocal_nombre").val();
    if(puesto==''){
        alert("Debe seleccionar un Puesto");
        puesto.focus();
        return false;
    }
	if(nombre==''){
        alert("Debe ingresar el Nombre");
        nombre.focus();
        return false;
    }
    if(paterno==''){
        alert("Debe ingresar el apellido Paterno");
        paterno.focus();
        return false;
    }    
    if(cve_elector=='' || cve_elector.length!=18){
        alert("Debe ingresar la Clave de elector y debe contener exactamente de 18 digitos");
        cve_elector.focus();
        return false;
    }
    if(vocal_nombre==''){
        alert("Debe ingresar el nombre completo del Vocal Ejecutivo de VD");
        vocal_nombre.focus();
        return false;
    }    
}