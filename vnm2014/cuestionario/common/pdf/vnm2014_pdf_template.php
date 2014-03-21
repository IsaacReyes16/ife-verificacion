<?php
require_once('common/php/pdf/fpdf.php');
require_once('common/php/o3m_functions.php');
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

    function WriteTag($x, $w, $h, $txt, $border=0, $align="J", $fill=false, $padding=0)
    {
        $this->wLine=$w;
        $this->hLine=$h;
        $this->Text=trim($txt);
        $this->Text=preg_replace("/\n|\r|\t/","",$this->Text);
        $this->border=$border;
        $this->align=$align;
        $this->fill=$fill;
        $this->Padding=$padding;

        // $this->Xini=$this->GetX();
        $this->Xini=$x;
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
    
    ##INICIA template de PDF
    function Header() {
        
    
    }

    function Footer()   {
      $this->SetY(-15);
      $this->SetFont('Arial','I',8);
      $this->SetTextColor(128);
       #$this->Cell(0,10,'Hoja '.$this->PageNo(),0,0,'C');
    }

    function Hoja1($in){
        //Variables con datos
        $folio = utf8_decode($in['folio']);
        $consecutivo = utf8_decode($in['consecutivo']);
        $entidad = $in['entidad'];
        $distrito = utf8_decode(ceros($in['distrito'],2));
        $seccion = utf8_decode(ceros($in['seccion'],4));
        $manzana = utf8_decode(ceros($in['manzana'],4));
        $municipio = ($in['municipio']);
        $localidad = ($in['localidad']);
        $calle = ($in['calle']);
        $num_ext = utf8_decode($in['num_ext']);
        $num_int = utf8_decode($in['num_int']);
        $colonia = ($in['colonia']);
        $reemplazo = utf8_decode($in['reemplazo']);  
        $hojas = '';   

        //Variables layout        
        $w=208;     //ancho de tabla
        $h=4;       //alto de fila
        $i=42;      // Inicio superior de tabla
        $y=10;      //separacion de linea inicial
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
        $ft2=9;     //tamaño de fuente
        $ft3=8;     //tamaño de fuente
        $ft4=6;     //tamaño de fuente
        $ft5=4;     //tamaño de fuente
        $salto=4;
        $saltolinea=.5;
        $colo1="0,0,0"; // color de relleno de celda
        $color2="255,255,255";
        $color3="180,180,180";
        $si='B';        //relleno de casilla F - B
        $fondo1 = "B";
        $this->SetLeftMargin(12);
        $this->SetRightMargin(5);
        $this->SetStyle("b","arial","B",0,$color1);
        $this->SetStyle("cur","arial","I",0,$color1);
        
        //Header
        $this->Image('common/img/logo.jpg',10,7,40);
        $this->SetFont('Arial','B',$ft0);
        $this->SetTextColor($color1);
        $this->Text(72,$y,utf8_decode("VERIFICACIÓN NACIONAL MUESTRAL, 2014"));
        $y = $y + $salto +.5; 
        $this->SetFont('Arial','B',$ft1);
        $this->Text(63,$y,utf8_decode("CUESTIONARIO DE RESIDENTES POR VIVIENDA (PARTE A)"));
        $y = $y + $salto +.5; 
        $this->Text(100,$y,utf8_decode("ZONA URBANA"));
        $y = $y + $salto +.5; 
        $this->SetTextColor($color3);
        $this->Text(90,$y,$reemplazo);
        
        //salto de linea 
        $y = $y + $salto + 3;           
        $this->SetFont($fuente,'',$ft3);
        $this->SetTextColor($color1);
        $this->Text($x[4],$y,utf8_decode('FOLIO DE CAPTURA:'));
        $this->Text($x[37],$y,utf8_decode('CONSECUTIVO DE VIVIENDA:'));
        $this->Text($x[80],$y,utf8_decode('TOTAL DE HOJAS:'));
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[19],$y,$folio);
        $this->Text($x[58],$y,$consecutivo);
        $this->Text($x[93],$y,$hojas);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->Line($x[93],$y+.5,$x[100],$y+.5);
        
        ##1. IDENTIFICACIÓN GEOELECTORAL
        //salto de linea y marco
        $y = $y + $salto -3;  
        $this->SetFillColor($color3);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.4);
        $this->Rect($x[4],$y,$x[95],5,'F');
        $this->Rect($x[4],$y,$x[95],20,'B');
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[37],$y,utf8_decode('1. IDENTIFICACIÓN GEOELECTORAL'));
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('Entidad'));
        $this->Text($x[40],$y,utf8_decode('Distrito'));
        $this->Text($x[60],$y,utf8_decode('Sección'));
        $this->Text($x[85],$y,utf8_decode('Manzana'));
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[5],$y,$entidad);
        $this->Text($x[41]+1,$y,$distrito);
        $this->Text($x[60]+2,$y,$seccion);
        $this->Text($x[86]+1,$y,$manzana);
        //salto de linea 
        $y = $y + $salto+1;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('Municipio'));
        $this->Text($x[50],$y,utf8_decode('Localidad'));
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[13],$y,$municipio);
        $this->Text($x[58],$y,$localidad);

        ##2. DOMICILIO 
        //salto de linea y marco
        $y = $y + $salto;  
        $this->SetFillColor($color3);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->Rect($x[4],$y,$x[95],5,'F');
        $this->Rect($x[4],$y,$x[95],16,'B');
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[48],$y,utf8_decode('2. DOMICILIO'));
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('Calle:'));
        $this->Text($x[40],$y,utf8_decode('Número Exterior:'));
        $this->Text($x[75],$y,utf8_decode('Número Interior:'));
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[9],$y,$calle);
        $this->Text($x[51],$y,$num_ext);
        $this->Text($x[86],$y,$num_int);
        //salto de linea 
        $y = $y + $salto+1;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('Colonia o Localidad:'));
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[18],$y,$colonia);
        //salto de linea 
        $y = $y + $salto-2;           
        $this->SetFont($fuente,'B',$ft4);
        $this->Ln(54);
        $texto = '<cur><b>'.utf8_decode('Pasa al apartado 3').'</b></cur>';
        $this->WriteTag($x[48],20,5,$texto,0,"",0,0);

        ##3. CARACTERÍSTICAS DE LA VIVIENDA
        //salto de linea y marco
        $y = $y + $salto-2;  
        $this->SetFillColor($color3);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->Rect($x[4],$y,$x[95],5,'F');
        $this->Rect($x[4],$y,$x[95],26,'B');
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[36],$y,utf8_decode('3. CARACTERÍSTICAS DE LA VIVIENDA'));
        //salto de linea 
        $y = $y + $salto;
        $this->Rect($x[4],$y-3,$x[46]+1,21,'B');
        $this->Rect($x[52],$y-3,$x[47],21,'B');
        //Cuadro izquierdo
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[10],$y+1,utf8_decode('3.1 Anota las características de la vivienda'));
        $this->Rect($x[45],$y-2,$x[4],5,'B');
        //salto de linea 
        $y = $y + $salto+2;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('1. Vivienda habitada'));
        $this->SetFont($fuente,'B',$ft4);
        $texto = '<cur><b>'.utf8_decode('(pasa a 3.2)').'</b></cur>';
        $this->Ln(11);
        $this->WriteTag($x[18],20,5,$texto,0,"",0,0);
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('2. Vivienda deshabitada'));
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('3. Otro uso'));
        $this->SetLineWidth(0.1);
        $this->SetDash(1,1); 
        $this->Line($x[14],$y+1,$x[35],$y+1);
        $this->SetFont($fuente,'B',$ft4);
        $texto = '<cur><b>'.utf8_decode('Específica').'</b></cur>';
        $this->Ln(6.2);
        $this->WriteTag($x[22],20,5,$texto,0,"",0,0);
        $this->SetFont($fuente,'',$ft4);
        $this->Image('common/img/llave.png',$x[36],$y-10,3);
        $this->Text($x[38]-1,$y-5,utf8_decode('Fin de llenado utiliza vivienda'));
        $this->Text($x[42]-1,$y-2,utf8_decode('de reemplazo'));
        //Cuadro derecha
        //salto de linea 
        $y = $y + $salto-18;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[53],$y+1,utf8_decode('3.2 Anota si detectaste viviendas omitidas en el domicilio'));
        $this->SetDash();
        $this->Rect($x[93],$y-2,$x[4],5,'B');
        //salto de linea 
        $y = $y + $salto+2;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[53],$y,utf8_decode('1. Sí, ¿cuántas?'));
        $this->Line($x[64],$y+1,$x[75],$y+1);
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[53],$y,utf8_decode('2. No'));
        //salto de linea 
        $y = $y + $salto; 
        $this->SetFont($fuente,'B',$ft4);
        $texto = '<cur><b>'.utf8_decode('Pasa a 4').'</b></cur>';
        $this->Ln(-5);
        $this->WriteTag($x[75],20,5,$texto,0,"",0,0);

        ##4. ENTREVISTA
        ##Cuadro Izquierdo
        //salto de linea y marco
        $y = $y + $salto+1;  
        $this->SetFillColor($color3);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->Rect($x[4],$y,$x[46],5,'F');
        $this->Rect($x[4],$y,$x[46],40,'B');
        
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[22],$y,utf8_decode('4. ENTREVISTA'));
        //salto de linea 
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[7],$y+1,utf8_decode('4.1 Anota si realizaste la entrevista, el día y la hora de la visita'));
        //salto de linea 
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[9],$y,utf8_decode('1ª Visita'));
        $this->Text($x[23],$y,utf8_decode('2ª Visita'));
        $this->Text($x[38],$y,utf8_decode('3ª visita'));
        //salto de linea 
        $y = $y + $salto-1;
        $this->SetFont($fuente,'',$ft4);
        $this->SetDash(1,1);
        $this->Text($x[12],$y,utf8_decode('Día'));        
        $this->Line($x[15],$y+1,$x[18],$y+1);
        $this->Text($x[26],$y,utf8_decode('Día'));
        $this->Line($x[29],$y+1,$x[32],$y+1);
        $this->Text($x[40],$y,utf8_decode('Día'));
        $this->Line($x[43],$y+1,$x[46],$y+1);
         //salto de linea 
        $y = $y + $salto+1;
        $this->SetFont($fuente,'',$ft4);
        $this->SetDash(1,1);
        $this->Text($x[7],$y,utf8_decode('Código')); 
        $this->Rect($x[7],$y-6,$x[2],4,'B');
        $this->SetFillColor($color2);
        $this->SetDash();
        $this->Rect($x[7]-1,$y-7,$x[2]+2,2,'F');
        $this->SetDash(1,1);
        $this->Text($x[21],$y,utf8_decode('Código'));
        $this->Rect($x[21],$y-6,$x[2],4,'B');
        $this->SetFillColor($color2);
        $this->SetDash();
        $this->Rect($x[21]-1,$y-7,$x[2]+2,2,'F');
        $this->SetDash(1,1);
        $this->Text($x[35],$y,utf8_decode('Código'));
        $this->Rect($x[35],$y-6,$x[2],4,'B');
        $this->SetFillColor($color2);
        $this->SetDash();
        $this->Rect($x[35]-1,$y-7,$x[2]+2,2,'F');
        $this->SetDash(1,1);
        $this->Text($x[12],$y,utf8_decode('Hora'));        
        $this->Line($x[15],$y+1,$x[18],$y+1);
        $this->Text($x[26],$y,utf8_decode('Hora'));
        $this->Line($x[29],$y+1,$x[32],$y+1);
        $this->Text($x[40],$y,utf8_decode('Hora'));
        $this->Line($x[43],$y+1,$x[46],$y+1);
        $this->SetDash();
        //salto de linea 
        $y = $y + $salto+2;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('1. Sí, se realizó'));
        $this->SetFont($fuente,'B',$ft4);
        $texto = '<cur><b>'.utf8_decode('(pasa a 5)').'</b></cur>';
        $this->Ln(22.2);
        $this->WriteTag($x[15],20,5,$texto,0,"",0,0);
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('2. No, por ausencia'));
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('3. No, por informante inadecuado'));
        //salto de linea 
        $y = $y + $salto;           
        $this->SetFont($fuente,'',$ft3);
        $this->Text($x[5],$y,utf8_decode('4. No, por rechazo'));
        //llave
        $this->SetFont($fuente,'B',$ft3);
        $this->Image('common/img/llave.png',$x[26],$y-13,3);
        $texto = '<cur><b>'.utf8_decode('Programa segunda y tercera visitas').'</b></cur>';
        $this->Ln(1.5);
        $this->WriteTag($x[28]-.5,50,5,$texto,0,"",0,0);

        #Cuadro Derecho
        //salto de linea
        $y = $y + $salto-42;
        $this->SetFillColor($color3);
        $this->Rect($x[52],$y,$x[47],5,'F');
        $this->Rect($x[52],$y,$x[47],40,'B');
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[66],$y,utf8_decode('5. OCUPANTES DE LA VIVIENDA'));
        //salto de linea
        $y = $y + $salto+1;        
        $this->SetFont($fuente,'B',$ft3);
        $this->Ln(-27);
        $texto = '<cur><b>'.utf8_decode('5.1 ¿Cuántas personas que tienen 18 años, viven aquí?').'</b></cur>';
        $this->WriteTag($x[53],32,3,$texto,0,"C",0,0);
        $this->Line($x[70],$y-4,$x[70],$y+31);
        $this->Ln(-9);
        $texto = '<cur><b>'.utf8_decode('5.2 ¿Cuántas personas mayores de 18 años, viven aquí?').'</b></cur>';        
        $this->WriteTag($x[71],35,3,$texto,0,"C",0,0);
        $this->Line($x[89],$y-4,$x[89],$y+31);
        $this->Ln(-9);
        $texto = '<cur><b>'.utf8_decode('5.3 Total de personas').'</b></cur>';
        $this->WriteTag($x[91],16,3,$texto,0,"C",0,0);
        //salto de linea
        $y = $y + $salto+15;
        $this->SetFont($fuente,'',$ft4);
        $this->SetDash(1,1);
        $this->Text($x[57],$y+1,utf8_decode('ANOTA NÚMERO')); 
        $this->Rect($x[54],$y-6,$x[12],4,'B');
        $this->SetFillColor($color2);
        $this->SetDash();
        $this->Rect($x[54]-1,$y-7,$x[12]+2,2,'F');
        $this->SetDash(1,1);
        $this->Text($x[76],$y+1,utf8_decode('ANOTA NÚMERO')); 
        $this->Rect($x[73],$y-6,$x[12],4,'B');
        $this->SetFillColor($color2);
        $this->SetDash();
        $this->Rect($x[73]-1,$y-7,$x[12]+2,2,'F');
        $this->SetDash(1,1);
        $this->Text($x[90]+1,$y+1,utf8_decode('ANOTA NÚMERO')); 
        $this->Rect($x[90],$y-6,$x[8],4,'B');
        $this->SetFillColor($color2);
        $this->SetDash();
        $this->Rect($x[90]-1,$y-7,$x[8]+2,2,'F');
        //salto de linea
        $this->SetFont($fuente,'B',$ft4);
        $this->Ln(20);
        $texto = '<cur><b>'.utf8_decode('Pasa a 5.2').'</b></cur>';
        $this->WriteTag($x[56]+1,18,3,$texto,0,"C",0,0);
        $this->Ln(-3);
        $texto = '<cur><b>'.utf8_decode('Pasa a 5.3').'</b></cur>';
        $this->WriteTag($x[75]+1,18,3,$texto,0,"C",0,0);
        $this->Ln(-3);
        $texto = '<cur><b>'.utf8_decode('Pasa a 6').'</b></cur>';
        $this->WriteTag($x[90]+1,18,3,$texto,0,"C",0,0);

        ##6. LISTA DE RESIDENTES HABITUALES
        //salto de linea y marco
        $y = $y + $salto+9;  
        $this->SetFillColor($color3);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->Rect($x[4],$y,$x[95],5,'FD');
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[37],$y,utf8_decode('6. LISTA DE RESIDENTES HABITUALES'));
        //salto de linea 
        $y = $y + $salto;
        //marcos
        $this->Rect($x[4],$y-3,$x[37],21,'B');
        $this->Rect($x[42]+1,$y-3,$x[16],21,'B');
        $this->Rect($x[60],$y-3,$x[12],21,'B');
        $this->Rect($x[73]+1,$y-3,$x[12],21,'B');
        $this->Rect($x[87],$y-3,$x[12],21,'B');
        //titulos
        $this->SetFont($fuente,'B',$ft3);
        $this->Ln(17);
        $texto = '<b>'.utf8_decode('6.1 Solicita los nombres de los residentes, inicia con la persona que te atiende.').'</b>';
        $this->WriteTag($x[5],75,3,$texto,0,"C",0,0);
        $this->Ln(-6);
        $texto = '<b>'.utf8_decode('6.2 Solicita la fecha de nacimiento').'</b>';
        $this->WriteTag($x[44],30,3,$texto,0,"C",0,0);
        $this->Ln(-7);
        $texto = '<b>'.utf8_decode("6.3 Indica el sexo 1. Hombre                           2. Mujer").'</b>';
        $this->WriteTag($x[60]+1,25,3,$texto,0,"C",0,0);
        $this->Ln(-10);
        $texto = '<b>'.utf8_decode("6.4 Búscalo en el padrón e indica:                1.- Sí está              2.- No está").'</b>';
        $this->WriteTag($x[74],25,3,$texto,0,"C",0,0);
        $this->Ln(-11);
        $texto = '<b>'.utf8_decode('6.5 Anota Consecutivo del Padrón').'</b>';
        $this->WriteTag($x[88],22,3,$texto,0,"C",0,0);
        //Opciones
        for($i=1; $i<=3; $i++){
            //Opciones - salto de linea 
            if($i==1){$y = $y + $salto+17;}else{$y = $y + $salto+4;}
            
            //marcos
            $this->Rect($x[4],$y-3,$x[37],30,'B');
            $this->Rect($x[42]+1,$y-3,$x[16],30,'B');
            $this->Rect($x[60],$y-3,$x[12],30,'B');
            $this->Rect($x[73]+1,$y-3,$x[12],30,'B');
            $this->Rect($x[87],$y-3,$x[12],30,'B');
            //salto de linea 
            $y = $y + $salto-3;
            $this->SetFont($fuente,'B',$ft3);
            $this->Text($x[5],$y+1,utf8_decode($i.'.'));
            //salto de linea 
            $y = $y + $salto;        
            $this->SetDash(1,1);
            $this->Line($x[4]+1,$y,$x[42],$y);        
            //salto de linea 
            $y = $y + $salto;
            $this->Text($x[20],$y,utf8_decode('NOMBRE(S)'));
            //salto de linea 
            $y = $y + $salto+5;
            $this->Line($x[4]+1,$y,$x[42],$y);        
            //salto de linea 
            $y = $y + $salto;
            $this->Text($x[7],$y,utf8_decode('APELLIDO PATERNO'));
            $this->Text($x[26],$y,utf8_decode('APELLIDO MATERNO'));
            //cuadro 2 - dia
            $y = $y + $salto-20;
            $this->Text($x[44],$y,utf8_decode('DÍA'));
            $this->SetDash();
            $this->Rect($x[47],$y-5,$x[1],6,'B');
            $this->Rect($x[50]-1,$y-5,$x[1],6,'B');
            $this->SetFillColor($color2);
            $this->Rect($x[47]-1,$y-6,$x[5],2,'F');
            //cuadro 2 - dia
            $y = $y + $salto+4;
            $this->Text($x[44]-1,$y,utf8_decode('MES'));
            $this->SetDash();
            $this->Rect($x[47],$y-5,$x[1],6,'B');
            $this->Rect($x[50]-1,$y-5,$x[1],6,'B');
            $this->SetFillColor($color2);
            $this->Rect($x[47]-1,$y-6,$x[5],2,'F');
            //cuadro 2 - dia
            $y = $y + $salto+4;
            $this->Text($x[44]-1,$y,utf8_decode('AÑO'));
            $this->SetDash();
            $this->Rect($x[47],$y-5,$x[1],6,'B');
            $this->Rect($x[50]-1,$y-5,$x[1],6,'B');
            $this->Rect($x[52],$y-5,$x[1],6,'B');
            $this->Rect($x[54]+1,$y-5,$x[1],6,'B');
            $this->SetFillColor($color2);
            $this->Rect($x[47]-1,$y-6,$x[10],2,'F');
        }             
    }

    function Hoja2(){        
        //Variables layout        
        $w=208;     //ancho de tabla
        $h=4;       //alto de fila
        $i=42;      // Inicio superior de tabla
        $y=5;      //separacion de linea inicial
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
        $ft2=9;     //tamaño de fuente
        $ft3=8;     //tamaño de fuente
        $ft4=6;     //tamaño de fuente
        $ft5=4;     //tamaño de fuente
        $salto=4;
        $saltolinea=.5;
        $colo1="0,0,0"; // color de relleno de celda
        $color2="255,255,255";
        $color3="180,180,180";
        $si='B';        //relleno de casilla F - B
        $fondo1 = "B";
        $this->SetLeftMargin(12);
        $this->SetRightMargin(5);
        $this->SetStyle("b","arial","B",0,$color1);
        $this->SetStyle("cur","arial","I",0,$color1);
        
        ##6. LISTA DE RESIDENTES HABITUALES
        //salto de linea y marco
        $y = $y + $salto;  
        $this->SetFillColor($color3);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->Rect($x[4],$y,$x[95],5,'FD');
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[37],$y,utf8_decode('6. LISTA DE RESIDENTES HABITUALES'));
        //salto de linea 
        $y = $y + $salto;
        //marcos
        $this->Rect($x[4],$y-3,$x[37],13,'B');
        $this->Rect($x[42]+1,$y-3,$x[16],13,'B');
        $this->Rect($x[60],$y-3,$x[12],13,'B');
        $this->Rect($x[73]+1,$y-3,$x[12],13,'B');
        $this->Rect($x[87],$y-3,$x[12],13,'B');
        //titulos
        $this->SetFont($fuente,'B',$ft3);
        $this->Ln(6);
        $texto = '<b>'.utf8_decode('6.1 Solicita los nombres de los residentes, inicia con la persona que te atiende.').'</b>';
        $this->WriteTag($x[5],75,3,$texto,0,"C",0,0);
        $this->Ln(-6);
        $texto = '<b>'.utf8_decode('6.2 Solicita la fecha de nacimiento').'</b>';
        $this->WriteTag($x[44],30,3,$texto,0,"C",0,0);
        $this->Ln(-7);
        $texto = '<b>'.utf8_decode("6.3 Indica el sexo 1. Hombre                           2. Mujer").'</b>';
        $this->WriteTag($x[60]+1,25,3,$texto,0,"C",0,0);
        $this->Ln(-10);
        $texto = '<b>'.utf8_decode("6.4 Búscalo en el padrón e indica:                1.- Sí está              2.- No está").'</b>';
        $this->WriteTag($x[74],25,3,$texto,0,"C",0,0);
        $this->Ln(-11);
        $texto = '<b>'.utf8_decode('6.5 Anota Consecutivo del Padrón').'</b>';
        $this->WriteTag($x[88],22,3,$texto,0,"C",0,0);
        
        for($i=4; $i<=10; $i++){
            //Opciones - salto de linea 
            if($i==4){$y = $y + $salto+9;}else{$y = $y + $salto+2;}
            
            //marcos
            $this->Rect($x[4],$y-3,$x[37],23,'B');
            $this->Rect($x[42]+1,$y-3,$x[16],23,'B');
            $this->Rect($x[60],$y-3,$x[12],23,'B');
            $this->Rect($x[73]+1,$y-3,$x[12],23,'B');
            $this->Rect($x[87],$y-3,$x[12],23,'B');
            //salto de linea 
            $y = $y + $salto-4;
            $this->SetFont($fuente,'B',$ft3);
            $this->Text($x[5],$y+1,utf8_decode($i.'.'));
            //salto de linea 
            $y = $y + $salto-1;        
            $this->SetDash(1,1);
            $this->Line($x[4]+1,$y,$x[42],$y);        
            //salto de linea 
            $y = $y + $salto;
            $this->Text($x[20],$y,utf8_decode('NOMBRE(S)'));
            //salto de linea 
            $y = $y + $salto+1;
            $this->Line($x[4]+1,$y,$x[42],$y);        
            //salto de linea 
            $y = $y + $salto;
            $this->Text($x[7],$y,utf8_decode('APELLIDO PATERNO'));
            $this->Text($x[26],$y,utf8_decode('APELLIDO MATERNO'));
            //cuadro 2 - dia
            $y = $y + $salto-17;
            $this->Text($x[44],$y,utf8_decode('DÍA'));
            $this->SetDash();
            $this->Rect($x[47],$y-5,$x[1],6,'B');
            $this->Rect($x[50]-1,$y-5,$x[1],6,'B');
            $this->SetFillColor($color2);
            $this->Rect($x[47]-1,$y-5.2,$x[5],2,'F');
            //cuadro 2 - mes
            $y = $y + $salto+3;
            $this->Text($x[44]-1,$y,utf8_decode('MES'));
            $this->SetDash();
            $this->Rect($x[47],$y-5,$x[1],6,'B');
            $this->Rect($x[50]-1,$y-5,$x[1],6,'B');
            $this->SetFillColor($color2);
            $this->Rect($x[47]-1,$y-5.5,$x[5],2,'F');
            //cuadro 2 - año
            $y = $y + $salto+3;
            $this->Text($x[44]-1,$y,utf8_decode('AÑO'));
            $this->SetDash();
            $this->Rect($x[47],$y-5,$x[1],6,'B');
            $this->Rect($x[50]-1,$y-5,$x[1],6,'B');
            $this->Rect($x[52],$y-5,$x[1],6,'B');
            $this->Rect($x[54]+1,$y-5,$x[1],6,'B');
            $this->SetFillColor($color2);
            $this->Rect($x[47]-1,$y-5.5,$x[10]+2,2,'F');
        }        

        ##FIRMAS 1
        ##Cuadro Izquierdo
        //salto de linea y marco
        $y = $y + $salto;  
        $this->SetFillColor($color3);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->Rect($x[4],$y,$x[46],5,'F');
        $this->Rect($x[4],$y,$x[46],25,'B');                
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[24],$y,utf8_decode('VISITADOR'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[5],$y,$x[50],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[25],$y+1,utf8_decode('NOMBRE'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[5],$y,$x[50],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[25]+1,$y+1,utf8_decode('FIRMA'));
        #Cuadro Derecho
        //salto de linea
        $y = $y + $salto-26;
        $this->SetFillColor($color3);
        $this->Rect($x[52],$y,$x[47],5,'F');
        $this->Rect($x[52],$y,$x[47],25,'B');
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[64],$y,utf8_decode('VALIDADOR DEL PARTIDO POLÍTICO'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[54],$y,$x[99],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[75],$y+1,utf8_decode('NOMBRE'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[54],$y,$x[99],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[68],$y+1,utf8_decode('FIRMA'));
        $this->Text($x[89],$y+1,utf8_decode('SIGLAS P.P.'));

        ##FIRMAS 2
        ##Cuadro Izquierdo
        //salto de linea y marco
        $y = $y + $salto;  
        $this->SetFillColor($color3);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->Rect($x[4],$y,$x[46],5,'F');
        $this->Rect($x[4],$y,$x[46],25,'B');                
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[21],$y,utf8_decode('VALIDADOR DEL RFE'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[5],$y,$x[50],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[25],$y+1,utf8_decode('NOMBRE'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[5],$y,$x[50],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[25]+1,$y+1,utf8_decode('FIRMA'));
        #Cuadro Derecho
        //salto de linea
        $y = $y + $salto-26;
        $this->SetFillColor($color3);
        $this->Rect($x[52],$y,$x[47],5,'F');
        $this->Rect($x[52],$y,$x[47],25,'B');
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[64],$y,utf8_decode('SUPERVISOR DEL PARTIDO POLÍTICO'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[54],$y,$x[99],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[75],$y+1,utf8_decode('NOMBRE'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[54],$y,$x[99],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[68],$y+1,utf8_decode('FIRMA'));
        $this->Text($x[89],$y+1,utf8_decode('SIGLAS P.P.'));

        ##FIRMAS 3
        ##Cuadro Izquierdo
        //salto de linea y marco
        $y = $y + $salto;  
        $this->SetFillColor($color3);
        $this->SetDrawColor($color1);
        $this->SetLineWidth(0.1);
        $this->Rect($x[4],$y,$x[46],5,'F');
        $this->Rect($x[4],$y,$x[46],25,'B');                
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[21],$y,utf8_decode('SUPERVISOR DEL RFE'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[5],$y,$x[50],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[25],$y+1,utf8_decode('NOMBRE'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[5],$y,$x[50],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[25]+1,$y+1,utf8_decode('FIRMA'));
        #Cuadro Derecho
        //salto de linea
        $y = $y + $salto-26;
        $this->SetFillColor($color3);
        $this->Rect($x[52],$y,$x[47],5,'F');
        $this->Rect($x[52],$y,$x[47],25,'B');
        //salto de linea y titulo de marco
        $y = $y + $salto;
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[72],$y,utf8_decode('OBSERVACIONES'));
        //salto de linea 
        $y = $y + $salto+3;
        $this->Line($x[54],$y,$x[99],$y);
        //salto de linea 
        $y = $y + $salto-2;
        $this->SetFont($fuente,'B',$ft3);
        //salto de linea 
        $y = $y + $salto-1;
        $this->Line($x[54],$y,$x[99],$y);
        //salto de linea 
        $y = $y + $salto+1;
        $this->Line($x[54],$y,$x[99],$y);    
    }

    function PrintDatos($Valores){
        $this->AddPage();
        $this->Hoja1($Valores);
        // $this->AddPage();
        // $this->Hoja2();
    }
    ##FIN template PDF
}
/*O3M*/
?>