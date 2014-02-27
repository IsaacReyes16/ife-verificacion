function mayusc(campo){
	campo.value=campo.value.toUpperCase();
}

function mostrardiv(nombre) {
	div = document.getElementById(nombre);
	div.style.display = "block";
}

function ocultardiv(nombre) {
	div = document.getElementById(nombre);
	div.style.display="none";
}

function divShow(div){
  ver = document.getElementById(div).style.display;
  if(ver=='block'){
    document.getElementById(div).style.display='none';
  }else{
    document.getElementById(div).style.display='block';
  }
}

function solo_txt(e) { 
	tecla = (document.all) ? e.keyCode : e.which; 
	if (tecla==8 || tecla==13 || tecla==164 || tecla==165) return true;  
	patron =/[A-Za-zÑñ\\\s]/;
	te = String.fromCharCode(tecla);
	return patron.test(te); 
} 

function solo_num(e) { 
	tecla = (document.all) ? e.keyCode : e.which; 
	if (tecla==8 || tecla==13) return true; 
	patron = /\d/;
	te = String.fromCharCode(tecla);
	return patron.test(te); 
} 

function EmailVerify(Data){
	var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
	if(filter.test(Data)){return true;}else{return false;}
}

function trim(data){
	return data.replace(/^\s+|\s+$/g, '');
}

function ltrim(data){
	return data.replace(/^\s+/,'');
}

function ltrim(data){
	return data.replace(/\s+$/,'');
}

function fulltrim(data){
	return data.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ');
}

function ButtonON(IdButton){
	$("#"+IdButton).removeAttr('disabled');
}
function ButtonOFF(IdButton){
	$("#"+IdButton).attr('disabled', 'disabled');
}

function DivContenido(Div, Contenido){
	document.getElementById(Div).innerHTML=Contenido;
}

// Funciones hardcode
function ocultar(){
  window.parent.document.getElementById('popup_msj').style.display='none';
  window.parent.document.getElementById('popup_modal').style.display='none';
}
function ver(){
  document.getElementById('popup_msj').style.display='block';
  document.getElementById('popup_modal').style.display='block';
}