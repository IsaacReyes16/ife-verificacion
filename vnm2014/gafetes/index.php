<?php $noment='BAJA CALIFORNIA'; $txtEnt=2; $dis=1; ?>
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
	<script type="text/javascript" src="common/js/jquery.mask.min.js"></script>
	<script type="text/javascript" src="common/js/o3m_functions.js"></script>
	<script type="text/javascript" src="common/js/javascript.js"></script>
	<link href="common/css/estilo.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="contenido">
	<div id="tabla-ingreso" class="Tabla">
		<div class="tabla-top">Impresión de Gafete</div>
		<table border="0" width="100%">
			<tbody>		
				<tr>
					<td class="table-label">Personas: &nbsp; </td>
					<td class="table-field">
						<select id="personas" name="personas" onchange="editar(this.value,0);">
							<option value='' selected="true">--Seleccione--</option>
						</select>
					</td>
				</tr>		
				<tr>
					<td class="table-label">Operativo: &nbsp; </td>
					<td class="table-field">
						<span id="tipotxt"></span>
						<input type="hidden" id="tipo" name="tipo" value="" />
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
						<span id="puestotxt"></span>
						<input type="hidden" id="puesto" name="puesto" value="" />
						<input type="hidden" id="puesto_old" name="puesto_old" value="" />
					</td>
				</tr>
				<tr>
					<td class="table-label">Nombre(s): &nbsp; </td>
					<td class="table-field">
						<input type="text" id="nombre" name="nombre" size="25" maxlength="32" onkeyup="mayusc(this)" readonly="true"/>
					</td>
				</tr>
				<tr>
					<td class="table-label">Apellido Paterno: &nbsp; </td>
					<td class="table-field">
						<input type="text" id="paterno" name="paterno" size="25" maxlength="32" onkeyup="mayusc(this)" readonly="true"/>
					</td>
				</tr>
				<tr>
					<td class="table-label">Apellido Materno: &nbsp; </td>
					<td class="table-field">
						<input type="text" id="materno" name="materno" size="25" maxlength="32" onkeyup="mayusc(this)" readonly="true"/>
					</td>
				</tr>
				<tr>
					<td class="table-label">Clave de elector: &nbsp; </td>
					<td class="table-field">
						<input type="text" id="cve_elector" name="cve_elector" size="25" maxlength="18"  onkeyup="mayusc(this)" readonly="true"/>
					</td>
				</tr>				
				<tr>
					<td class="table-label">Nombre <br/>(Vocal): &nbsp; </td>
					<td class="table-field">
						<input type="text" id="vocal_nombre" name="vocal_nombre" size="30" maxlength="150" onkeyup="mayusc(this)" readonly="true"/>
					</td>
				</tr>
				<tr>
					<td class="table-label">Puesto <br/>(Vocal): &nbsp; </td>
					<td class="table-field">
						<input type="text" id="vocal_puesto" name="vocal_puesto" size="30" maxlength="150" onkeyup="mayusc(this)" readonly="true"/>
					</td>
				</tr>
				<tr>
					<td class="table-label">Vigencia: &nbsp; </td>
					<td class="table-field">
						DEL 18 DE MARZO AL 15 DE MAYO DE 2014 
						<input type="hidden" id="vigencia" name="vigencia" value="DEL 18 DE MARZO AL 15 DE MAYO DE 2014 " />
						<input type="hidden" id="clave" name="clave" value="" />
						<input type="hidden" id="id_gafete" name="id_gafete" value="" />
					</td>
				</tr>
				<tr>
					<td class="table-label" colspan="2"><hr/></td>
				</tr>
			</tbody>
			<tfoot>
				<td id="botones" colspan="2" align="center">
					<div id="btnAgregar" class="btn" onclick="imprimir(id_gafete.value,'RTF');">:: Imprimir ::</div>
					&nbsp;&nbsp;
					<div id="btnAgregar" class="btn" onclick="hrefNuevo();">:: Nuevo ::</div>
				</td>
			</tfoot>
		</table>
	</div>
</div>
</body>
</html>