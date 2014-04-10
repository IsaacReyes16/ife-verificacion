<?php
##Function
function Plantilla_RTF1($Plantilla,$Ruta,$NuevoDoc,$Variables,$CharAbre,$CharCierra,$Valores){
	$NuevoDoc=$Ruta.$NuevoDoc;
	$txtplantilla=file_get_contents($Plantilla);
	$matriz=explode("sectd",$txtplantilla);
	$cabecera=$matriz[0]."sectd";
	$inicio=strlen($cabecera);
	$final=strrpos($txtplantilla,"}");
	$largo=$final-$inicio;
	$cuerpo=substr($txtplantilla,$inicio,$largo);
	$punt=fopen($NuevoDoc,"wb");
	fputs($punt,$cabecera);
	$Registros = count($Valores);
	for($i=1; $i<=$Registros; $i++){			
		$row=$Valores[$i];
		$despues=$cuerpo;
		for($x=0; $x<count($Variables); $x++){$nvariables[$x][0]=$CharAbre.$Variables[$x].$CharCierra;}
		$n=0;
		foreach($nvariables as $dato){
			$datosql=utf8_decode(str_replace('\\','Ñ',utf8_encode(utf8_decode($row[$n]))));
			$datortf=$dato[0];
			$despues=str_replace($datortf,$datosql,$despues);
			$despues=str_replace(strtoupper($datortf),$datosql,$despues);
			$despues=str_replace(strtolower($datortf),$datosql,$despues);
			$n++;
		}
		fputs($punt,$despues);
	}	
	fputs($punt,"}");
	fclose($punt);
	return $NuevoDoc;
}
function limpiar_tmp($dir, $extension, $segundos){
    $t=time();
    $h=opendir($dir);
    while($file=readdir($h)){
        if(substr($file,-4)=='.'.$extension){
            $path=$dir.$file;
            if($t-filemtime($path)>$segundos)
                @unlink($path);
        }
    }
    closedir($h);
}
function zipFile($File='', $Path='', $Delete=false){
##Compress in Zip file in the same directory
	chdir($Path);
	$f = explode('.',$File);
	for($i=0; $i<count($f)-1; $i++){
		$zipFile .= $f[$i];
	}
	$zipFile .= '.zip';
	$Zip = new ZipArchive;
	$Zip->open($zipFile, ZipArchive::CREATE);	
	$Zip->addFile($File);
	$Zip->close();
	if($Delete){
		unlink($File);
	}
	return $zipFile;
}
function ceros($num=0, $digitos=1){
	if(strlen($num)<$digitos){
	    for($i=1; $i<=$digitos-strlen($num); $i++){
	        $cero .= '0';
	    }
	    $num = $cero.$num;
	}
    return $num;
}
/*O3M*/
?>