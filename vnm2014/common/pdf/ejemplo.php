<?php
require_once('common/php/pdf/fpdf.php');
##FPDF
class PDF extends FPDF
{
    ##### INICIO Formateo de Texto ####
    
    var $wLine; // Maximum width of the line
    var $hLine; // Height of the line
    var $Text; // Text to display
    var $border;
    var $align; // Justification of the text
    var $fill;
    var $Padding;
    var $lPadding;
    var $tPadding;
    var $bPadding;
    var $rPadding;
    var $TagStyle; // Style for each tag
    var $Indent;
    var $Space; // Minimum space between words
    var $PileStyle; 
    var $Line2Print; // Line to display
    var $NextLineBegin; // Buffer between lines 
    var $TagName;
    var $Delta; // Maximum width minus width
    var $StringLength; 
    var $LineLength;
    var $wTextLine; // Width minus paddings
    var $nbSpace; // Number of spaces in the line
    var $Xini; // Initial position
    var $href; // Current URL
    var $TagHref; // URL for a cell

    // Public Functions

    function WriteTag($w, $h, $txt, $border=0, $align="J", $fill=false, $padding=0)
    {
        $this->wLine=$w;
        $this->hLine=$h;
        $this->Text=trim($txt);
        $this->Text=preg_replace("/\n|\r|\t/","",$this->Text);
        $this->border=$border;
        $this->align=$align;
        $this->fill=$fill;
        $this->Padding=$padding;

        $this->Xini=$this->GetX();
        $this->href="";
        $this->PileStyle=array();        
        $this->TagHref=array();
        $this->LastLine=false;

        $this->SetSpace();
        $this->Padding();
        $this->LineLength();
        $this->BorderTop();

        while($this->Text!="")
        {
            $this->MakeLine();
            $this->PrintLine();
        }

        $this->BorderBottom();
    }

    function SetStyle($tag, $family, $style, $size, $color, $indent=-1)
    {
         $tag=trim($tag);
         $this->TagStyle[$tag]['family']=trim($family);
         $this->TagStyle[$tag]['style']=trim($style);
         $this->TagStyle[$tag]['size']=trim($size);
         $this->TagStyle[$tag]['color']=trim($color);
         $this->TagStyle[$tag]['indent']=$indent;
    }
    
    // Private Functions

    function SetSpace() // Minimal space between words
    {
        $tag=$this->Parser($this->Text);
        $this->FindStyle($tag[2],0);
        $this->DoStyle(0);
        $this->Space=$this->GetStringWidth(" ");
    }

    function Padding()
    {
        if(preg_match("/^.+,/",$this->Padding)) {
            $tab=explode(",",$this->Padding);
            $this->lPadding=$tab[0];
            $this->tPadding=$tab[1];
            if(isset($tab[2]))
                $this->bPadding=$tab[2];
            else
                $this->bPadding=$this->tPadding;
            if(isset($tab[3]))
                $this->rPadding=$tab[3];
            else
                $this->rPadding=$this->lPadding;
        }
        else
        {
            $this->lPadding=$this->Padding;
            $this->tPadding=$this->Padding;
            $this->bPadding=$this->Padding;
            $this->rPadding=$this->Padding;
        }
        if($this->tPadding<$this->LineWidth)
            $this->tPadding=$this->LineWidth;
    }

    function LineLength()
    {
        if($this->wLine==0)
            $this->wLine=$this->w - $this->Xini - $this->rMargin;

        $this->wTextLine = $this->wLine - $this->lPadding - $this->rPadding;
    }

    function BorderTop()
    {
        $border=0;
        if($this->border==1)
            $border="TLR";
        $this->Cell($this->wLine,$this->tPadding,"",$border,0,'C',$this->fill);
        $y=$this->GetY()+$this->tPadding;
        $this->SetXY($this->Xini,$y);
    }

    function BorderBottom()
    {
        $border=0;
        if($this->border==1)
            $border="BLR";
        $this->Cell($this->wLine,$this->bPadding,"",$border,0,'C',$this->fill);
    }

    function DoStyle($tag) // Applies a style
    {
        $tag=trim($tag);
        $this->SetFont($this->TagStyle[$tag]['family'],
            $this->TagStyle[$tag]['style'],
            $this->TagStyle[$tag]['size']);

        $tab=explode(",",$this->TagStyle[$tag]['color']);
        if(count($tab)==1)
            $this->SetTextColor($tab[0]);
        else
            $this->SetTextColor($tab[0],$tab[1],$tab[2]);
    }

    function FindStyle($tag, $ind) // Inheritance from parent elements
    {
        $tag=trim($tag);

        // Family
        if($this->TagStyle[$tag]['family']!="")
            $family=$this->TagStyle[$tag]['family'];
        else
        {
            reset($this->PileStyle);
            while(list($k,$val)=each($this->PileStyle))
            {
                $val=trim($val);
                if($this->TagStyle[$val]['family']!="") {
                    $family=$this->TagStyle[$val]['family'];
                    break;
                }
            }
        }

        // Style
        $style="";
        $style1=strtoupper($this->TagStyle[$tag]['style']);
        if($style1!="N")
        {
            $bold=false;
            $italic=false;
            $underline=false;
            reset($this->PileStyle);
            while(list($k,$val)=each($this->PileStyle))
            {
                $val=trim($val);
                $style1=strtoupper($this->TagStyle[$val]['style']);
                if($style1=="N")
                    break;
                else
                {
                    if(strpos($style1,"B")!==false)
                        $bold=true;
                    if(strpos($style1,"I")!==false)
                        $italic=true;
                    if(strpos($style1,"U")!==false)
                        $underline=true;
                } 
            }
            if($bold)
                $style.="B";
            if($italic)
                $style.="I";
            if($underline)
                $style.="U";
        }

        // Size
        if($this->TagStyle[$tag]['size']!=0)
            $size=$this->TagStyle[$tag]['size'];
        else
        {
            reset($this->PileStyle);
            while(list($k,$val)=each($this->PileStyle))
            {
                $val=trim($val);
                if($this->TagStyle[$val]['size']!=0) {
                    $size=$this->TagStyle[$val]['size'];
                    break;
                }
            }
        }

        // Color
        if($this->TagStyle[$tag]['color']!="")
            $color=$this->TagStyle[$tag]['color'];
        else
        {
            reset($this->PileStyle);
            while(list($k,$val)=each($this->PileStyle))
            {
                $val=trim($val);
                if($this->TagStyle[$val]['color']!="") {
                    $color=$this->TagStyle[$val]['color'];
                    break;
                }
            }
        }
         
        // Result
        $this->TagStyle[$ind]['family']=$family;
        $this->TagStyle[$ind]['style']=$style;
        $this->TagStyle[$ind]['size']=$size;
        $this->TagStyle[$ind]['color']=$color;
        $this->TagStyle[$ind]['indent']=$this->TagStyle[$tag]['indent'];
    }

    function Parser($text)
    {
        $tab=array();
        // Closing tag
        if(preg_match("|^(</([^>]+)>)|",$text,$regs)) {
            $tab[1]="c";
            $tab[2]=trim($regs[2]);
        }
        // Opening tag
        else if(preg_match("|^(<([^>]+)>)|",$text,$regs)) {
            $regs[2]=preg_replace("/^a/","a ",$regs[2]);
            $tab[1]="o";
            $tab[2]=trim($regs[2]);

            // Presence of attributes
            if(preg_match("/(.+) (.+)='(.+)'/",$regs[2])) {
                $tab1=preg_split("/ +/",$regs[2]);
                $tab[2]=trim($tab1[0]);
                while(list($i,$couple)=each($tab1))
                {
                    if($i>0) {
                        $tab2=explode("=",$couple);
                        $tab2[0]=trim($tab2[0]);
                        $tab2[1]=trim($tab2[1]);
                        $end=strlen($tab2[1])-2;
                        $tab[$tab2[0]]=substr($tab2[1],1,$end);
                    }
                }
            }
        }
         // Space
         else if(preg_match("/^( )/",$text,$regs)) {
            $tab[1]="s";
            $tab[2]=' ';
        }
        // Text
        else if(preg_match("/^([^< ]+)/",$text,$regs)) {
            $tab[1]="t";
            $tab[2]=trim($regs[1]);
        }

        $begin=strlen($regs[1]);
         $end=strlen($text);
         $text=substr($text, $begin, $end);
        $tab[0]=$text;

        return $tab;
    }

    function MakeLine()
    {
        $this->Text.=" ";
        $this->LineLength=array();
        $this->TagHref=array();
        $Length=0;
        $this->nbSpace=0;

        $i=$this->BeginLine();
        $this->TagName=array();

        if($i==0) {
            $Length=$this->StringLength[0];
            $this->TagName[0]=1;
            $this->TagHref[0]=$this->href;
        }

        while($Length<$this->wTextLine)
        {
            $tab=$this->Parser($this->Text);
            $this->Text=$tab[0];
            if($this->Text=="") {
                $this->LastLine=true;
                break;
            }

            if($tab[1]=="o") {
                array_unshift($this->PileStyle,$tab[2]);
                $this->FindStyle($this->PileStyle[0],$i+1);

                $this->DoStyle($i+1);
                $this->TagName[$i+1]=1;
                if($this->TagStyle[$tab[2]]['indent']!=-1) {
                    $Length+=$this->TagStyle[$tab[2]]['indent'];
                    $this->Indent=$this->TagStyle[$tab[2]]['indent'];
                }
                if($tab[2]=="a")
                    $this->href=$tab['href'];
            }

            if($tab[1]=="c") {
                array_shift($this->PileStyle);
                if(isset($this->PileStyle[0]))
                {
                    $this->FindStyle($this->PileStyle[0],$i+1);
                    $this->DoStyle($i+1);
                }
                $this->TagName[$i+1]=1;
                if($this->TagStyle[$tab[2]]['indent']!=-1) {
                    $this->LastLine=true;
                    $this->Text=trim($this->Text);
                    break;
                }
                if($tab[2]=="a")
                    $this->href="";
            }

            if($tab[1]=="s") {
                $i++;
                $Length+=$this->Space;
                $this->Line2Print[$i]="";
                if($this->href!="")
                    $this->TagHref[$i]=$this->href;
            }

            if($tab[1]=="t") {
                $i++;
                $this->StringLength[$i]=$this->GetStringWidth($tab[2]);
                $Length+=$this->StringLength[$i];
                $this->LineLength[$i]=$Length;
                $this->Line2Print[$i]=$tab[2];
                if($this->href!="")
                    $this->TagHref[$i]=$this->href;
             }

        }

        trim($this->Text);
        if($Length>$this->wTextLine || $this->LastLine==true)
            $this->EndLine();
    }

    function BeginLine()
    {
        $this->Line2Print=array();
        $this->StringLength=array();

        if(isset($this->PileStyle[0]))
        {
            $this->FindStyle($this->PileStyle[0],0);
            $this->DoStyle(0);
        }

        if(count($this->NextLineBegin)>0) {
            $this->Line2Print[0]=$this->NextLineBegin['text'];
            $this->StringLength[0]=$this->NextLineBegin['length'];
            $this->NextLineBegin=array();
            $i=0;
        }
        else {
            preg_match("/^(( *(<([^>]+)>)* *)*)(.*)/",$this->Text,$regs);
            $regs[1]=str_replace(" ", "", $regs[1]);
            $this->Text=$regs[1].$regs[5];
            $i=-1;
        }

        return $i;
    }

    function EndLine()
    {
        if(end($this->Line2Print)!="" && $this->LastLine==false) {
            $this->NextLineBegin['text']=array_pop($this->Line2Print);
            $this->NextLineBegin['length']=end($this->StringLength);
            array_pop($this->LineLength);
        }

        while(end($this->Line2Print)==="")
            array_pop($this->Line2Print);

        $this->Delta=$this->wTextLine-end($this->LineLength);

        $this->nbSpace=0;
        for($i=0; $i<count($this->Line2Print); $i++) {
            if($this->Line2Print[$i]=="")
                $this->nbSpace++;
        }
    }

    function PrintLine()
    {
        $border=0;
        if($this->border==1)
            $border="LR";
        $this->Cell($this->wLine,$this->hLine,"",$border,0,'C',$this->fill);
        $y=$this->GetY();
        $this->SetXY($this->Xini+$this->lPadding,$y);

        if($this->Indent!=-1) {
            if($this->Indent!=0)
                $this->Cell($this->Indent,$this->hLine);
            $this->Indent=-1;
        }

        $space=$this->LineAlign();
        $this->DoStyle(0);
        for($i=0; $i<count($this->Line2Print); $i++)
        {
            if(isset($this->TagName[$i]))
                $this->DoStyle($i);
            if(isset($this->TagHref[$i]))
                $href=$this->TagHref[$i];
            else
                $href='';
            if($this->Line2Print[$i]=="")
                $this->Cell($space,$this->hLine,"         ",0,0,'C',false,$href);
            else
                $this->Cell($this->StringLength[$i],$this->hLine,$this->Line2Print[$i],0,0,'C',false,$href);
        }

        $this->LineBreak();
        if($this->LastLine && $this->Text!="")
            $this->EndParagraph();
        $this->LastLine=false;
    }

    function LineAlign()
    {
        $space=$this->Space;
        if($this->align=="J") {
            if($this->nbSpace!=0)
                $space=$this->Space + ($this->Delta/$this->nbSpace);
            if($this->LastLine)
                $space=$this->Space;
        }

        if($this->align=="R")
            $this->Cell($this->Delta,$this->hLine);

        if($this->align=="C")
            $this->Cell($this->Delta/2,$this->hLine);

        return $space;
    }

    function LineBreak()
    {
        $x=$this->Xini;
        $y=$this->GetY()+$this->hLine;
        $this->SetXY($x,$y);
    }

    function EndParagraph()
    {
        $border=0;
        if($this->border==1)
            $border="LR";
        $this->Cell($this->wLine,$this->hLine/2,"",$border,0,'C',$this->fill);
        $x=$this->Xini;
        $y=$this->GetY()+$this->hLine/2;
        $this->SetXY($x,$y);
    }   
    ##### FIN Formateo de Texto
    
    

    function Header() {
        global $cons;
        global $digito;
        $this->Image('common/img/logo.jpg',10,7,40);
        $this->SetFont('Arial','B',9);
        $this->SetTextColor($color1);
        $num_fol = strlen($cons);
        $this->Text(63.5,14,"TERCER AVISO CIUDADANO PARA RECOGER");
        $this->Text(60,19,"LA CREDENCIAL PARA VOTAR CON FOTOGRAFÍA");
        $this->Ln(2);
    
    }

    function Footer()   {
      $this->SetY(-15);
      $this->SetFont('Arial','I',8);
      $this->SetTextColor(128);
       #$this->Cell(0,10,'Hoja '.$this->PageNo(),0,0,'C');
    }

    function Hoja1($Valores){        
        //Variables layout        
        $w=208;     //ancho de tabla
        $h=4;       //alto de fila
        $i=42;      // Inicio superior de tabla
        $y=30;      //separacion de linea inicial
        $x[1]=5;        //posicion x - margen izquierdo
        $largo = 203;   //Espacio de trabajo - Largo
        $celdas =100;   //Número de celdas a crear
        $celda = $largo/$celdas;    //Largo de cada celda
        for($a=2; $a<=$celdas; $a++){
            $x[$a]=$x[$a-1]+$celda; //Celdas $x[n]
        }
        $fuente='Arial';    //Fuente
        $ft0=12;        //tamaño de fuente
        $ft1=10;        //tamaño de fuente
        $ft2=7;     //tamaño de fuente
        $ft3=6;     //tamaño de fuente
        $ft4=4;     //tamaño de fuente
        $salto=4;
        $saltolinea=.5;
        $colo1="0,0,0"; // color de relleno de celda
        $color2="255,255,255";
        $color3="102,102,102";
        $si='B';        //relleno de casilla F - B
        $fondo1 = "B";
        $this->SetLeftMargin(12);
        $this->SetRightMargin(5);
        
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'B',$ft1);
        $this->SetTextColor($color1);
        $this->Text($x[67],$y,'México, D.F., 16 de enero de 2012.');
        //salto de linea 
        $y = $y + $salto;       
        $this->Text($x[5],$y,ucwords(strtolower(utf8_decode($row['nombre_comp']))));
        //salto de linea 
        $y = $y + $salto;
        $this->SetFont($fuente,'',$ft1);
        $this->SetTextColor($color3);
        $this->Text($x[5],$y,ucwords(strtolower(utf8_decode($row['a1_calle']." ".$row['a1_exterior']." ".$row['a1_interior']))));
        //salto de linea 
        $y = $y + $salto;
        $this->Text($x[5],$y,ucwords(strtolower(utf8_decode($row['a1_colonia']))).", C.P. ".$row['a1_cp']);
        //salto de linea 
        $y = $y + $salto;
        $this->Text($x[5],$y,ucwords(strtolower(utf8_decode($row['mpio_ent']))));
        
        //salto de linea 
        $y = $y + $salto+3;
        $this->SetTextColor($color1);
        $this->SetFont($fuente,'B',$ft1);
        $this->Text($x[5],$y,'Presente');
        
        //salto de linea 
        #$y = $y + $salto;
        $this->Ln($y-$salto-5);
        $this->SetFont($fuente,'',$ft1);
        $this->SetStyle("n","arial","B",0,$color1);
        $this->SetStyle("cur","arial","I",0,$color1);
        if($row['a1_modulo_dir']==NULL){
            $donde="se sugiere llamar a IFETEL (01-800-433-2000) para confirmar la dirección del módulo donde se encuentra disponible su Credencial para Votar";
            $ifetel=true;
        }else{
            $donde="ubicado en <cur>".ucwords(strtolower(utf8_decode($row['mod_direccion'])))."</cur>";
            $ifetel=false;
        }
        $parrafo1="Estimado ciudadano, el día $fec_tram[2] de $mes de $fec_tram[0], usted realizó en el módulo de atención ciudadana, un trámite para obtener su Credencial para Votar con Fotografía. Debido a que a la fecha no la ha recogido, el IFE le extiende una cordial invitación para que pase por ella.";
        $this->WriteTag(195,4,$parrafo1,0,"J",0,1);     
        //salto de linea 
        $this->Ln(2);
        $parrafo2="Su credencial está disponible en el módulo ".$row['a1_modulo_cred'].", ".$donde.", acuda cuanto antes, sólo revise sus datos, firme y ponga su huella en su credencial.";
        $this->WriteTag(195,4,$parrafo2,0,"J",0,1);     
        if(!$ifetel){
        //salto de linea 
        $this->Ln(2);
        $parrafo3="Sólo preséntese con una identificación con fotografía, revise sus datos, firme y poga su huella en su credencial.";
        $this->WriteTag(195,4,$parrafo3,0,"J",0,1);     
        }
        //salto de linea 
        $this->Ln(2);
        $parrafo4="De conformidad con lo dispuesto en el artículo 180, párrafo 5 del Código Federal de Instituciones y Procedimientos Electorales, le informamos que debe acudir al Módulo de Atención Ciudadana por su Credencial para Votar a más tardar el día 31 de marzo de 2012. En cumplimiento del artículo 199 del mismo Código, <n>su Credencial para Votar será destruida</n>, su solicitud será cancelada y su registro será dado de baja del Padrón Electoral. Y no podrá votar en las próximas elecciones.";      
        $this->WriteTag(195,4,$parrafo4,0,"J",0,1);
        //salto de linea 
        $this->Ln(2);
        $parrafo5="Sí ya recogió su Credencial para Votar, haga caso omiso a este aviso.";
        $this->WriteTag(195,4,$parrafo5,0,"J",0,1);     
        //salto de linea 
        $this->Ln(2);
        $parrafo6="Sin otro particular, aprovecho la ocasión para enviarle un cordial saludo.";
        $this->WriteTag(195,4,$parrafo6,0,"J",0,1);        

        //salto de linea 
        $y = $y + $salto+2;
        $this->SetTextColor($color1);
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[8],$y,'FUAR No.');
        $this->Text($x[77],$y,'Clave de Elector');  
        $this->SetTextColor($color1);
        $this->Text($x[14],$y,$row['a1_fuar']);     
        $this->Text($x[86],$y,$row['a1_cve_elector']);      
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFillColor($color1);   
        $this->SetTextColor($color2);
        $this->SetFont($fuente,'B',$ft2-.8);        
        $this->Rect($x[5],$y,192,5,$style='FD');
        $this->Text($x[6],$y+3.5,'Para mayores informes LLAME GRATIS desde cualquier parte del país a IFETEL: 01-800-433-2000 o consulte la página electrónica www.ife.org.mx y síguenos en twitter.com/IFETEL');
        //salto de linea 
        $y = $y + $salto+4;
        $this->SetTextColor($color1);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->SetDash(1,1); 
        $this->Line($x[6]+3,$y,$x[99],$y);
        
        //salto de linea 
        $y = $y + $salto-2;
        $this->Image('common/img/logo.jpg',$x[6],$y,30);
        $this->SetFont('Arial','B',9);
        $num_fol = strlen($folio);
        $this->Text(70,$y+5,"REGISTRO FEDERAL DE ELECTORES");
        $this->Text(63.5,$y+10,"TERCER AVISO CIUDADANO PARA RECOGER");
        $this->Text(60,$y+15,"LA CREDENCIAL PARA VOTAR CON FOTOGRAFÍA");
        
        //salto de linea 
        $y = $y + $salto+16;
        $fuar=$row['a1_fuar'];
        $cve=$row['a1_cve_elector'];
        $e=$row['a1_ent'];
        $d=$row['a1_dto'];
        $m=$row['a1_mpio'];
        $s=$row['a1_secc'];
        $l=$row['a1_loc'];
        $mz=$row['a1_mza'];
        $this->SetFont($fuente,'',$ft2);
        for($i=0; $i<strlen($fuar); $i++){$letra[$i]=substr($fuar,$i,1); $fuar1.=$letra[$i]." ";}
        $this->Text($x[8],$y, $fuar1);
        for($i=0; $i<strlen($cve); $i++){$letra[$i]=substr($cve,$i,1); $cve1.=$letra[$i]." ";}
        $this->Text($x[25],$y, $cve1);
        for($i=0; $i<strlen($e); $i++){$letra[$i]=substr($e,$i,1); $e1.=$letra[$i]." ";}
        $this->Text($x[50],$y,$e1);
        for($i=0; $i<strlen($d); $i++){$letra[$i]=substr($d,$i,1); $d1.=$letra[$i]." ";}
        $this->Text($x[60],$y,$d1);
        for($i=0; $i<strlen($m); $i++){$letra[$i]=substr($m,$i,1); $m1.=$letra[$i]." ";}
        $this->Text($x[70],$y,$m1);
        for($i=0; $i<strlen($s); $i++){$letra[$i]=substr($s,$i,1); $s1.=$letra[$i]." ";}
        $this->Text($x[80],$y,$s1);
        for($i=0; $i<strlen($l); $i++){$letra[$i]=substr($l,$i,1); $l1.=$letra[$i]." ";}
        $this->Text($x[88],$y,$l1);
        for($i=0; $i<strlen($mz); $i++){$letra[$i]=substr($mz,$i,1); $mz1.=$letra[$i]." ";}
        $this->Text($x[95],$y,$mz1);
        //salto de linea 
        $y = $y + $salto-1;
        $this->SetTextColor($color3);
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[13],$y, 'FUAR');
        $this->Text($x[30],$y, 'CLAVE DE ELECTOR');
        $this->Text($x[50]-.5,$y,'ENT');
        $this->Text($x[60],$y,'DTTO');
        $this->Text($x[70],$y,'MPIO');
        $this->Text($x[80]+.5,$y,'SECC');
        $this->Text($x[89],$y,'LOC');
        $this->Text($x[96],$y,'MZA');
        
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetDash(1,0);
        $this->Line($x[5],$y,$x[99],$y);
        
        //salto de linea 
        $y = $y + $salto+2;
        $this->SetTextColor($color1);
        $this->SetFont($fuente,'B',$ft1);
        $this->Text($x[5],$y,ucwords(strtolower(utf8_decode($row['nombre_comp']))));
        //salto de linea 
        $y = $y + $salto;
        $this->SetTextColor($color3);
        $this->SetFont($fuente,'',$ft2);
        $this->Text($x[5],$y,ucwords(strtolower(utf8_decode($row['a1_calle']." ".$row['a1_exterior']." ".$row['a1_interior']))));
        //salto de linea 
        $y = $y + $salto;
        $this->Text($x[5],$y,ucwords(strtolower(utf8_decode($row['a1_colonia']))).", C.P. ".$row['a1_cp']);
        //salto de linea 
        $y = $y + $salto;
        $this->Text($x[5],$y,ucwords(strtolower(utf8_decode($row['mpio_ent']))));
        
        //salto de linea 
        $y = $y + $salto-18;
        $this->SetTextColor($color1);
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[68],$y+.5,'b) Recibí Aviso');
        $this->Line($x[45],$y+10,$x[85],$y+10);
        $this->Line($x[87],$y+10,$x[99],$y+10);
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[60],$y+12,'Nombre completo');
        $this->Text($x[93]-2,$y+12,'Firma');
        //salto de linea 
        $y = $y + $salto+5;
        $this->Line($x[45],$y+13,$x[85],$y+13);
        $this->Line($x[87],$y+13,$x[99],$y+13);
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[59],$y+15,'Identificación');
        $this->Text($x[93]-2,$y+15,'Número');
        
        //salto de linea 
        $y = $y + $salto+11;
        $this->SetFont($fuente,'B',$ft2);   
        $this->Text($x[14],$y-5,'a) Fecha de la Visita');
        $this->SetDrawColor($color1);   
        $this->Rect($x[5],$y,15,6,$style='B');
        $this->Line($x[9]-.5,$y+3,$x[9]-.5,$y+6);
        $this->Rect($x[14],$y,15,6,$style='B');
        $this->Line($x[18]-.5,$y+3,$x[18]-.5,$y+6);
        $this->Rect($x[23],$y,15,6,$style='B');
        $this->Line($x[27]-.5,$y+3,$x[27]-.5,$y+6);
        $this->Rect($x[32],$y,10,6,$style='B');
        $this->SetDrawColor(255,255,255);   
        $this->Line($x[5],$y,$x[31],$y);
        //salto de linea 
        $y = $y + $salto+4;
        $this->SetTextColor($color3);
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[8],$y,'Día');
        $this->Text($x[17],$y,'Mes');
        $this->Text($x[26],$y,'Año');
        $this->Text($x[33],$y,'Código');
        //salto de linea 
        $y = $y + $salto;
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,'1. Domicilio no localizado');
        $this->Text($x[5],$y+4,'2. Lote baldío');
        $this->Text($x[5],$y+8,'3. Cambio de uso de suelo');
        $this->Text($x[5],$y+12,'4. Vivienda deshabitada');
        $this->Text($x[5],$y+16,'5. Cambio de domicilio');
        $this->Text($x[5],$y+20,'6. Ciudadano ausente');
        $this->Text($x[5],$y+24,'7. No vive, no lo conocen');
        
        $this->Text($x[22],$y,'8. Fallecimiento');
        $this->Text($x[22],$y+4,'9. Receptor inadecuado');
        $this->Text($x[22],$y+8,'10. Rechazo');
        $this->Text($x[22],$y+12,'11. Ausencia de ocupantes');
        $this->Text($x[22],$y+16,'12. Entregado al ciudadano en cuestión');
        $this->Text($x[22],$y+20,'13. Entregado a un receptor adecuado');
        
        //salto de linea 
        $y = $y + $salto-8;
        $this->SetTextColor($color1);
        $this->SetFont($fuente,'B',$ft2);   
        $this->Text($x[45],$y,'c) Parentesco');
        $this->SetDrawColor($color1);   
        $this->Rect($x[54],$y-5,10,6,$style='B');
        $this->SetTextColor($color3);
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[55]-.5,$y+3,'Código');
        $this->Text($x[45],$y+8,'1. Ciudadano en cuestión');
        $this->Text($x[45],$y+12,'2. Familiar');
        $this->Text($x[45],$y+16,'3. No familiar');        
        
        $this->SetTextColor($color1);       
        $this->SetFont($fuente,'B',$ft2);   
        $this->Text($x[65]-.5,$y,'d) Causa por la que no ha recogido su CPVF');
        $this->SetDrawColor($color1);   
        $this->Rect($x[94],$y-5,10,6,$style='B');
        $this->SetTextColor($color3);
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[95],$y+3,'Código');
        $y = $y -3;
        $this->Text($x[67],$y+8,'1. Se le había olvidado');
        $this->Text($x[67],$y+11,'2. No ha tenido tiempo');
        $this->Text($x[67],$y+14,'3. Motivos de salud');
        $this->Text($x[67],$y+17,'4. Trabaja/estudia en otra Entidad/País');
        $this->Text($x[67],$y+20,'5. No sabe (receptor adecuado)');
        $this->Text($x[67],$y+23,'6. Otra');
        $this->SetDrawColor($color3);   
        $this->Line($x[72],$y+24,$x[99],$y+24);
        
        //salto de linea 
        $y = $y + $salto+24+2;
        $this->SetDrawColor($color1);
        $this->SetTextColor($color1);
        $this->SetFont($fuente,'B',$ft2);   
        $this->Text($x[45],$y-1,'e) Técnico de Campo/Visitador Domiciliario');
        $this->Line($x[45],$y+5,$x[80],$y+5);
        $this->Line($x[82],$y+5,$x[99],$y+5);
        $this->SetTextColor($color3);
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[61],$y+7,'Nombre');
        $this->Text($x[90],$y+7,'Firma');
    }

    function PrintDatos($Valores){
        $this->AddPage();
        $this->Hoja1($Valores);
    }
}
?>