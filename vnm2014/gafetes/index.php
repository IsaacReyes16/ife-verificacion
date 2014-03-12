<?php $noment='BAJA CALIFORNIA'; $txtEnt=2; $dis=3; ?>
<?php 
require_once('common/php/conex.php');
$ent=$txtEnt; 
$dto=$dis;
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<head>
	<title>::IFE-VNM2014-Impresión de gafetes</title>
	<script type="text/javascript" src="common/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="common/js/o3m_functions.js"></script>
	<link href="common/css/estilo.css" rel="stylesheet" type="text/css" />

<script>
$(document).ready(function(){	
	listado();
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
      				$('#tbl_resultados > tbody:last').append('<tr><td class="table-label-l">'+cons+'.- '+tipo+' | '+valor.clave+' - '+valor.nombre_completo+'</td><td class="table-label-c"><span class="btn"  onclick="imprimir('+valor.id_gafete+','+"'PDF'"+');">PDF</span><span class="btn"  onclick="imprimir('+valor.id_gafete+','+"'RTF'"+');">RTF</span></td></tr>');
      				$("#tbl_resultados tbody tr:even").css("background-color", "#EEE");
					$("#tbl_resultados tbody tr:odd").css("background-color", "#FFF");
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
    if(cve_elector==''){
        alert("Debe ingresar la Clave de elector");
        cve_elector.focus();
        return false;
    }
    if(vocal_nombre==''){
        alert("Debe ingresar el nombre completo del Vocal Ejecutivo de VD");
        vocal_nombre.focus();
        return false;
    }    
}
</script>
</head>
<body>
<div id="contenido">
	<div id="tabla-ingreso" class="Tabla">
		<div class="tabla-top">Impresión de Gafete</div>
		<table border="0" width="100%">
			<tbody>				
				<tr>
					<td class="table-label">Tipo: &nbsp; </td>
					<td class="table-field">
						<select id="tipo" name="tipo">
							<option value="ENUMERACION" selected="selected">ENUMERACION</option>
							<option value="COBERTURA">COBERTURA</option>
							<option value="ACTUALIZACION">ACTUALIZACION</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="table-label">Entidad: &nbsp; </td>
					<td class="table-field"><?php echo $noment; ?>
						<input type="hidden" id="ent" name="ent" value="<?php echo $ent; ?>" />
					</td>
				</tr>
				<tr>
					<td class="table-label">Distrito: &nbsp; </td>
					<td class="table-field"><?php echo $dto; ?>
						<input type="hidden" id="dto" name="dto" value="<?php echo $dto; ?>" />
					</td>
				</tr>
				<tr>
					<td class="table-label">Puesto: &nbsp; </td>
					<td class="table-field">
						<select id="puesto" name="puesto">
							<option value="">--Seleccione--</option>
							<option value="VALIDADOR">VALIDADOR</option>
							<option value="VISITADOR">VISITADOR DOMICILIARIO</option>
							<option value="SUPERVISOR DE CAMPO">SUPERVISOR DE CAMPO</option>
							<option value="ENUMERADOR">ENUMERADOR</option>
							<option value="REVISOR">REVISOR</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="table-label">Nombre(s): &nbsp; </td>
					<td class="table-field">
						<input type="text" id="nombre" name="nombre" size="20" maxlength="32" onkeyup="mayusc(this)" />
					</td>
				</tr>
				<tr>
					<td class="table-label">Apellido Paterno: &nbsp; </td>
					<td class="table-field">
						<input type="text" id="paterno" name="paterno" size="20" maxlength="32" onkeyup="mayusc(this)" />
					</td>
				</tr>
				<tr>
					<td class="table-label">Apellido Materno: &nbsp; </td>
					<td class="table-field">
						<input type="text" id="materno" name="materno" size="20" maxlength="32" onkeyup="mayusc(this)"/>
					</td>
				</tr>
				<tr>
					<td class="table-label">Clave de elector: &nbsp; </td>
					<td class="table-field">
						<input type="text" id="cve_elector" name="cve_elector" size="20" maxlength="18"  onkeyup="mayusc(this)"/>
					</td>
				</tr>				
				<tr>
					<td class="table-label">Nombre <br/>(Vocal Ejecutivo): &nbsp; </td>
					<td class="table-field">
						<input type="text" id="vocal_nombre" name="vocal_nombre" size="30" maxlength="150" onkeyup="mayusc(this)"/>
					</td>
				</tr>
				<tr>
					<td class="table-label">Puesto <br/>(Vocal Ejecutivo): &nbsp; </td>
					<td class="table-field">
						<input type="text" id="vocal_puesto" name="vocal_puesto" size="30" maxlength="150" onkeyup="mayusc(this)"/>
					</td>
				</tr>
				<!-- <tr>
					<td class="table-label">Clave: &nbsp; </td>
					<td class="table-field">
						<input type="text" id="clave" name="clave" size="15" readonly="true" />
					</td>
				</tr> -->
				<tr>
					<td class="table-label">Vigencia: &nbsp; </td>
					<td class="table-field">
						DEL 18 DE MARZO AL 15 DE MAYO DE 2014 
						<input type="hidden" id="vigencia" name="vigencia" value="DEL 18 DE MARZO AL 15 DE MAYO DE 2014 " />
					</td>
				</tr>
				<tr>
					<td class="table-label" colspan="2"><hr/></td>
				</tr>
			</tbody>
			<tfoot>
				<td colspan="2" align="center">
					<div id="btnAgregar" class="btn" onclick="agregar();">:: Agregar ::</div>
				</td>
			</tfoot>
		</table>
	</div>
	<div id="tabla-ingreso">&nbsp;</div>
	<div id="tabla-resultados" class="Tabla">
		<div class="tabla-top">Listado de Gafetes</div>
		<table id="tbl_resultados" border="0" width="100%" cellpadding="3" cellspacing="0">
			<thead>
				<tr>
					<td colspan="2">
						Para realizar la impresión correctamente, debe configurarse el tamaño de papel a 10.4cm x 12.8cm
					</td>
				</tr>	
			</thead>
			<tbody>	
				
			<!--Dinamico-->
			</tbody>
			<tfoot>
				<td colspan="2" align="center">
					<div id="divResultado"></div>
				</td>				
			</tfoot>
		</table>		
	</div>
</div>
</body>
</html>