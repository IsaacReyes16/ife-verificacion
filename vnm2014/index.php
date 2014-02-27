<?php #$noment='BAJA CALIFORNIA'; $txtEnt=2; $dis=3; ?>
<?php require_once('common/php/build_select.php'); 
$ent=$txtEnt; 
$dto=$dis;
?>
<html>
<head>
<!--jQuery-->
<script type="text/javascript" src="common/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="common/js/o3m_functions.js"></script>
<script>
function validar(f){
	var ent = $("#ent").val();
    var dto = $("#dto").val();
    var folio = fulltrim($("#folio").val());    
    var consecutivo = fulltrim($("#consecutivo").val());
    var seccion = $("#seccion").val();
    var manzana = $("#manzana").val();
    var folio_edsm = $("#folio_edsm").val();
    if(folio=='' && consecutivo=='' && seccion==''){
        alert("Debe ingresar algun criterio de b&uacute;squeda");
        f.folio.focus();
        return false;
    }
    generaArchivo();
}

function generaArchivo(){
	$("#divResultado").html("<img src='common/img/wait.gif' valign='middle'> Generando archivo.<br/>Este proceso puede tardar varios minutos, por favor espere...");
	var ajax_url = "vnm2014_rtf.php";
    var ent = $("#ent").val();
    var dto = $("#dto").val();
    var folio = $("#folio").val();
    var consecutivo = $("#consecutivo").val();
    var seccion = $("#seccion").val();
    var manzana = $("#manzana").val();
    var folio_edsm = $("#folio_edsm").val();
    if(folio==''){folio=folio_edsm;}
    $.ajax({
      type: 'POST',
      url: ajax_url,
      data: {
      ent : ent,
      dto : dto,
      folio : folio,
      consecutivo : consecutivo,
      seccion : seccion,
      manzana : manzana,
      auth : 1
      },
      success: function(data){  
          if(data !=''){                                    
              if(data != 0){    
                  $("#divResultado").html("Archivo Generado. <a href='tmp/"+data+"' title='Descargar'><br/><img src='common/img/zip.png' border='0' valing='middle'>"+data+"</a>");
              }else{
                  $("#divResultado").html("Error al generar archivo.");
              }
          }else{
              alert("Error al envíar datos");
          }                                
      }
    });
}

function select_manzana(seccion, onchage){ 
  if(seccion>0){
    var ajax_url = "common/php/build_select.php";
    var tipo = "select_manzana";
    var ent = "<?php echo $ent; ?>";
    var dto = "<?php echo $dto; ?>";
    var onchange = 'select_folio(this.value)';
    var seccion = seccion;
    $.ajax({
        type: 'POST',
        url: ajax_url,
        data: {
        tipo : tipo,
        ent : ent,
        dto : dto,
        seccion : seccion,
        onchange : onchange
        },
        success: function(data){ 
          if(data != 0){    
            DivContenido("select_manzana", data); 
          }else{
              alert("Error al realizar la búsqueda");
          }
        }  
    });
  }else{DivContenido("select_manzana", ""); }
}

function select_folio(manzana, onchage){ 
  if(manzana>0){
    var ajax_url = "common/php/build_select.php";
    var tipo = "select_folio";
    var ent = "<?php echo $ent; ?>";
    var dto = "<?php echo $dto; ?>";
	var seccion = $("#seccion").val();
    var manzana = manzana;
    var onchange = '';
    $.ajax({
        type: 'POST',
        url: ajax_url,
        data: {
        tipo : tipo,
        ent : ent,
        dto : dto,
        seccion : seccion,
      	manzana : manzana,
        onchange : onchange
        },
        success: function(data){
          if(data != 0){    
            DivContenido("select_folio", data); 
          }else{
              alert("Error al realizar la búsqueda");
          }
        }  
    });
  }else{DivContenido("select_folio", ""); }
}
</script>
<style>
body { 
	font-family:Arial, Helvetica, sans-serif;
	color:#333;
	font-size: 11px;
	text-align:center; 
}
table{
	font-family:Arial, Helvetica, sans-serif;
	color:#333;
	font-size: 11px;
	border: 1px solid #DDD;
	border-radius: 9px;
	-moz-border-radius: 9px;
	-webkit-border-radius: 9px;
}
input, textarea, select{
	font-size:.9em;
	border:#DDD solid 1px;
	color:#333;
	font-weight:bold;
}
input:focus, textarea:focus, select:focus{
	border:#F500AB solid 1px;
	background-color:#FBE6FA;	
}
#forma{
	position:absolute;
    top:50%;
    left: 50%;
    margin-top: -100px;
    margin-left: -100px;
	overflow:hidden;
}
.table-label{
	font-weight:bold;
	text-align: right;
	color:#555;
}
.table-field{
	font-weight:bold;
	text-align: left;
}
.table-toplabel{
	text-align: center;
	color: #CC0099;
	font-weight: bold;
}
.btn{
	text-align: center;
	background-color: #42B0EF; 
	color: #FFF;
	font-weight: bold;
	border: 1px #DDD solid;   
	width: 100%;
	text-shadow: -1px -1px 2px #618926;
	border-radius: 9px;
	-moz-border-radius: 9px;
	-webkit-border-radius: 9px;
}
.btn:hover {
	text-align: center;
	background-color: #C1E2F5;
	color: #333;
	font-weight: bold;
	border: 1px #DDD solid;   
	width: 100%;    
	text-shadow: -1px -1px 2px #465f97;
	border-radius: 9px;
	-moz-border-radius: 9px;
	-webkit-border-radius: 9px;
	cursor: pointer; 
	cursor: hand;
}
#btnGenerar{
	display:inline-block;
	width: 80px;
	height: 17px;
	vertical-align: middle;
	margin: 0 auto 0 auto;
	clear: both;
	z-index: 4;
}
#Tabla{
	position:relative;
	width:200px;
	overflow:hidden;
	float: left;
	clear: both;
	margin-left: 50px;
}
#divResultado{
	position:relative;
	overflow:hidden;
	float: left;
	clear: both;
}
</style>
</head>
<body>
	<div id="forma" style="">
		<div id="Tabla">
			<table border="0" cellpadding="0" cellpadding="3" width="100%">
				<form name="forma" method="POST" action="">
				<tr class="table-toplabel">
					<td colspan="2">Generaci&oacute;n de Cuestionario</td>
				</tr>
				<tr>
					<td colspan="2">Seleccione el criterio:</td>
				</tr>
				<tr>
					<td class="table-label"><?php echo $noment ?>&nbsp;</td>
					<td class="table-label">Dtto:&nbsp;<?php echo $dis ?></td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
				<tr>
					<td class="table-label">Folio:&nbsp;</td>
					<td class="table-field"><input type="text" id="folio" name="folio" size="10"></td>
				</tr>
				<tr>
					<td class="table-label">Consecutivo:&nbsp;</td>
					<td class="table-field"><input type="text" id="consecutivo" name="consecutivo" size="10"></td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
				<tr>
					<td class="table-label">Secci&oacute;n:&nbsp;</td>
					<td class="table-field">
					 <select id="seccion" name="seccion" onchange="select_manzana(this.value)">
					<?php echo select_seccion('', $ent, $dto); ?>
					</select>
					</td>
				</tr>
				<tr>
					<td class="table-label">Manzana:&nbsp;</td>
					<td class="table-field">
					<span id="select_manzana"></span>
					</td>
				</tr>
				<tr>
					<td class="table-label">Folio:&nbsp;</td>
					<td class="table-field">
					<span id="select_folio"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
				<tr>
					<td colspan="2"  class="table-label">
					<input type="hidden" id="ent" name="ent" value="<?php echo $txtEnt ?>">
					<input type="hidden" id="dto" name="dto" value="<?php echo $dis ?>">
					<div id="btnGenerar" class="btn" onclick="validar(this.form);">Generar</div>
					</td>
				</tr>
				</form>
			</table>			
		</div>	
		<div id="divResultado"></div>
	</div>	
</body>
</html>