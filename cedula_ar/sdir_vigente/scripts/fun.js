// JavaScript Document
/**
* Archivo que contiene las acciones de los botones, programados con AJAX y JQUERY
* Creado por: Ing. Eduardo Cruz <j.edwardc@live.com>
*
* Modificado por: Oscar Maldonado
* Última modificación: 2013-05-06
*/

$(document).ready(function() {
	
	var id_ent = 0;
	var id_dis = 0;
	var id_mun = 0;
	
	$("#file").pekeUpload();
	
	$.ajax({
		type: "POST",
		url: "ajax/ajax_cmb_entidad.php",
		data: {},
		cache: false,
		success: function(html)
		{
			$("#slc_entidad").html(html);
		} 
	});
	
	$("#txt_folio").keydown(function(event) {

		if(event.shiftKey){event.preventDefault();}
		
		if (event.keyCode == 46 || event.keyCode == 8){
		}else{
			if (event.keyCode < 95) {
				if (event.keyCode < 48 || event.keyCode > 57){
				event.preventDefault();
				}
			}else{
				if (event.keyCode < 96 || event.keyCode > 105){
					event.preventDefault();
				}
			}
		}
	});
	
	$("#slc_entidad").change(function()
	{
		id_ent = $(this).val(); 
		
		if(id_ent == 0){
			$("#slc_distrito").html("<option value=0>Seleccionar</option>");
			$("#slc_distrito").attr("disabled", "disabled");
			$("#slc_municipio").html("<option value=0>Seleccionar</option>");
			$("#slc_municipio").attr("disabled", "disabled");
			return;
		}
		$.ajax({
			type: "POST",
			url: "ajax/ajax_cmb_distrito.php",
			data: { entidad: id_ent},
			cache: false,
			success: function(html)
			{
				$("#slc_distrito").html(html);
				$("#slc_distrito").removeAttr('disabled');
				$("#slc_municipio").html("<option value=0>Seleccionar</option>");
				$("#slc_municipio").attr("disabled", "disabled");
				$("#txt_folio").val("");
			} 
		});
	});
	
	$("#slc_distrito").change(function()
	{
		id_dis = $(this).val(); 
		
		if(id_dis == 0){
			$("#slc_municipio").html("<option value=0>Seleccionar</option>");
			$("#slc_municipio").attr("disabled", "disabled");
			return;
		}
		$.ajax({
			type: "GET",
			url: "ajax/ajax_cmb_municipio.php",
			data: { entidad: id_ent, distrito: id_dis},
			cache: false,
			success: function(html)
			{
				$("#slc_municipio").html(html);
				$("#slc_municipio").removeAttr('disabled');
				$("#txt_folio").val("");
			} 
		});
	});
	
	$("#slc_distrito").change(function()
	{
		id_mun = $(this).val(); 
	});
	
	$("#slc_municipio").change(function()
	{
		$("#txt_folio").val("");
	});
	
	$("#limpiar").click(function(e){
		
		$("#slc_entidad").val(0);
		$("#slc_distrito").html("<option value=0>Seleccionar</option>");
		$("#slc_distrito").attr("disabled", "disabled");
		$("#slc_municipio").html("<option value=0>Seleccionar</option>");
		$("#slc_municipio").attr("disabled", "disabled");
		$("#slc_estatus").val(0);
		$("#txt_folio").val("");
		$("#div_mensajes").html("");
		$("#div_mensajes").val("");
		$(".bar-pekeupload").html("");
		$(".filename").html("");
		$("#file").removeAttr('disabled');
		
		
	});

	$("#btn_precarga").click(function(e){
		
		var pocent = $("#subida").text();
		var mensaje = $("#div_mensajes").val();
		
		if(pocent != "100%"){
			alert("No hay archivo para leer");
			return;
		}else{
//			alert("aqui"+mensaje+"final");
			$("#slc_entidad").val(0);
			$("#slc_distrito").html("<option value=0>Seleccionar</option>");
			$("#slc_distrito").attr("disabled", "disabled");
			$("#slc_municipio").html("<option value=0>Seleccionar</option>");
			$("#slc_municipio").attr("disabled", "disabled");
			$("#txt_folio").val("");
			
			if (mensaje == ""){
				// var nombre_archivo = $(".filename").text();
				var nombre_archivo = filename($("#file").val());
				$.ajax({
					type: "POST",
					url: "ajax/ajax_lee_docto_xls.php",
					data: {file : nombre_archivo},
					cache: false,
					success: function(html)
					{
						$("#div_mensajes").html(html);
						$("#div_mensajes").val(1);
					} 
				});	
			}else{
				alert("Ya tiene una carga valida, debe de limpiar el formulario. Gracias");
			}
		}
	});
	
	$("#btn_generar").click(function(e){
		
		var entidad = $("#slc_entidad").val();
		var distrito = $("#slc_distrito").val();
		var municipio = $("#slc_municipio").val();
		var estatus = $("#slc_estatus").val();
		var folio = $("#txt_folio").val();
		
		if(entidad != 0){
			window.open("ajax/ajax_genera_docto_xls.php?id_ent="+entidad+"&id_dis="+distrito+"&id_mun="+municipio+"&estatus="+estatus);
			return;
		}
		
		if(folio != ""){
			window.open("ajax/ajax_genera_docto_xls.php?folio="+folio+"&estatus="+estatus);
			return;
		}
		
		var pocent = $("#subida").text();
		var mensaje = $("#div_mensajes").val();
		
		if(pocent != "100%"){
			alert("No hay archivo para generar");
			return;
		}else{
			
			if(mensaje == "" && pocent == "100%"){
				alert("Debe de validar el archivo para poder generar su reporte");
				return;
			}else{
				
				// var nombre_archivo = $(".filename").text();	
				var nombre_archivo = filename($("#file").val());
				window.open("ajax/ajax_genera_docto_xls.php?file="+nombre_archivo);
				
			}			
			
		}
	});

function filename(fic) {
  fic = fic.split('\\');  
  var filename = fic[fic.length-1];
  // alert(fic[fic.length-1]);
  return filename
  /*O3M*/
}
	
	
	
	
});
