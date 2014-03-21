<?php $noment='DISTRITO FEDERAL'; $txtEnt=9; $dis=24; ?>
<?php 
require_once('common/php/conex.php');
$ent=$txtEnt; 
$dto=$dis;
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<head>
	<title>::IFE-VNM2014-Listado de viviendas</title>
	<script type="text/javascript" src="common/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="common/js/o3m_functions.js"></script>
	<script type="text/javascript" src="common/js/listado-viviendas-por-seccion.js"></script>
	<link href="common/css/listado-viviendas-por-seccion.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div id="contenido" >
		<div id="tabla-ingreso" class="Tabla">
			<div class="tabla-top">Generación de Listado de Viviendas</div>
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
							<td class="table-label">Municipio:&nbsp;</td>
							<td class="table-field">
							 <select id="municipio" name="municipio" onchange="select_seccion(this.value)"></select>
							</td>
						</tr>
						<tr>
							<td class="table-label">Sección:&nbsp;</td>
							<td class="table-field">
							 <select id="seccion" name="seccion"></select>
							</td>
						</tr>
						<tr>
							<td class="table-label">Tipo:&nbsp;</td>
							<td class="table-field">
							 <select id="tipo" name="tipo">
							 	<option value="U" selected="selected">U</option>
							 	<option value="M">M</option>
							</select>
							</td>
						</tr>	
						<tr>
							<td colspan="2"><hr/></td>
						</tr>					
					</tbody>
					<tfoot>
						<td id="botones" colspan="2" align="center">
							<div id="btnGenerar" class="btn" onclick="generaArchivo();">:: Generar Archivo ::</div>
							</td>
					</tfoot>
				</table>
			</div>
			<div id="tabla-resultados"></div>
		</div>		
	</div>	
</body>
</html>