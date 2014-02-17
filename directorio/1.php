<style>
#popup_modal{
	/*display:{popup_diplay};*/
	position:absolute;
	top:0%;
	left:0%;
	width:100%;
	height:100%;
	background-color:black;
	z-index:1001;
	-moz-opacity:0.3-;
	opacity:.30;
	filter: alpha(opacity=30);
}
#popup_msj{
	/*display:{popup_diplay};*/
	position:absolute;
	top:25%;
	left:25%;
	width:50%;
	height:50%;
	padding:16px;
	background-color:#FFF;
	z-index:1002;
	overflow:hidden;
	border:1px solid #333;
}
</style>
<div id="popup_modal"></div>
<div id="popup_msj">
	<a href="#" onclick="ocultar()">Ocultar</a>
	<div id="popup_close"><a href="#" onClick="popup_close();">[CERRAR]</a></div>
	<div id="popup_title">{popup_titulo}</div>
	<iframe id="popup_page" src="{popup_page}" width=100% height=100% scroll="auto" frameborder="0"></iframe>
</div>
<a href="#" onclick="ver()">Ver</a>
<script>
function ocultar(){
	document.getElementById('popup_msj').style.display='none';
	document.getElementById('popup_modal').style.display='none';
}
function ver(){
	document.getElementById('popup_msj').style.display='block';
	document.getElementById('popup_modal').style.display='block';
}
</script>