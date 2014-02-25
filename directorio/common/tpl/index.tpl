[@HtmlHead]
[@CSS_estilos]
[@Javascript]
[@jQuery]
<div id="fondoPagina" >
  <div id="contenidoPagina">
    <table class="Tabla" border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td id="header"><img src="[@ImgPath]back/logo.gif" width="235" height="118" /></td>
        <td width="70%" align="center" class="titulo" id="header">[@Titulo]</td>
    </tr>
    <tr>
      <td height="500px" colspan="2">
        <div id="btnSalir" class="btn" onclick="location.href='../index.php'">Salir</div><br/>
        <iframe id="popup_page" src="inicio.php" width=100% height=100% scroll="auto" frameborder="0"></iframe>

      </td>
    </tr>
    <tr>
      <td id="footer" align="left">Direcci&oacute;n de Depuraci&oacute;n y Verificaci&oacute;n en Campo </td>
      <td id="footer" align="right">[@fecha_hoy]</td>
    </tr>
    </table>
  </div>
</div>