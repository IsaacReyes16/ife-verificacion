<?php $noment='AGUASCALIENTES'; $txtEnt=1; $dis=1; ?>
<?php 
$ent=$txtEnt; 
$dto=$dis;
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<head>
	<title>::IFE-VNM2014-Reporte de Término</title>
	<script type="text/javascript" src="common/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="common/js/o3m_functions.js"></script>
	<script type="text/javascript" src="common/js/actualizacion-termino.js"></script>
	<link href="common/css/actualizacion-termino.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div id="contenido" >
		<div id="tabla-ingreso" class="Tabla">
			<div class="tabla-top">Generación de Reporte de Término</div>
			<table border="0" width="100%">
				<tbody>		
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
						<td colspan="2"><hr/></td>
					</tr>					
				</tbody>
				<tfoot>
					<td id="botones" colspan="2" align="center">
						<div id="btnGenerar" class="btn" onclick="generaArchivo();">:: Generar Documento ::</div>
						</td>
				</tfoot>
			</table>
		</div>
	</div>
	<div id="tabla-resultados"></div>
	<!--Detalle-->	
	<div id="tabla-detalle" class="Tabla">
		<div class="tabla-top">Capturas Pendientes</div>
		<table border="0" width="100%" id="tabla-detalle">
			<thead></thead>
			<tbody></tbody>
			<tfoot>
				<td id="botones" colspan="5" align="center">&nbsp;</td>
			</tfoot>
		</table>
	</div>
	<div id="popup_modal" style="display:none;" onclick="hideMessage()"></div>
	<div class="messages" style="display:none;" onclick="hideMessage()"></div><br /><br />		
</body>
</html>