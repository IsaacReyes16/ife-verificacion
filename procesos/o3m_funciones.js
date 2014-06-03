/*O3M*/
function fajax(EtiquetaDiv,Pagina,Tipo,MultiValores,Texto){
/*
*	EtiquetaDiv = 	Nombre de Div/Input en el que regresará el valor
*	Pagina 		=	Pagina PHP que se ejecutará para devolver la respuesta Ejem.: pagina.php
*	Tipo		=	Tipo de respuesta; 1 = Para Div / 2 = Para Input
*	MultiValores=	Cadena de parametros (valores) separados por pipe (|) 
*					los cuales se enviaran a la Pagina.php que devolverá la respuesta.
*					Ejem.: valor1|valor2|...
*	Texto		=	Texto que aparecera mientras se genera la respuesta.
*/
	if(Texto==''){Texto='Generando...';}
	Texto=Texto+'<img src=http://localhost/i/ajax-loader.gif></img>';
	var xmlhttp;
	try{xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");}
	catch(e){try{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	catch(E){if(!xmlhttp && typeof XMLHttpRequest!='undefined') 
	xmlhttp=new XMLHttpRequest();}}
	if (MultiValores.length==0){
		if(Tipo==1){document.getElementById(EtiquetaDiv).innerHTML="";}
		if(Tipo==2){document.getElementById(EtiquetaDiv).value="";}
		return;
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(Tipo==1){document.getElementById(EtiquetaDiv).innerHTML=xmlhttp.responseText;}
			if(Tipo==2){document.getElementById(EtiquetaDiv).value=xmlhttp.responseText;}			
		}else{
			if(Tipo==1){document.getElementById(EtiquetaDiv).innerHTML = Texto;}
			if(Tipo==2){document.getElementById(EtiquetaDiv).value = Texto;}
			//setTimeout(fajax(EtiquetaDiv,Pagina,Tipo,MultiValores,Texto),10000);
		}

	}
	xmlhttp.open("POST",Pagina+"?v="+MultiValores,true);
	xmlhttp.send(null);
}

/*O3M*/