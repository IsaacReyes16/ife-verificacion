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
            // $this->Cell($this->Delta/2,$this->hLine);
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
         ##Recuperación de arrays con datos        
        $Totales = $this->totales;
        $Valores = $this->valores;
        $RegsHoja = $this->regsPorHoja;

        //Variables layout        
        $w=208;     //ancho de tabla
        $h=4;       //alto de fila
        $y=10;      //Espacio Superior de linea inicial
        $x[1]=5;        //posicion x - margen izquierdo
        $largo = 260;   //Espacio de trabajo - Largo
        $celdas =100;   //Número de celdas a crear
        $celda = $largo/$celdas;    //Largo de cada celda
        for($a=2; $a<=$celdas; $a++){
            $x[$a]=$x[$a-1]+$celda; //Celdas $x[n]
        }
        $fuente='Arial';    //Fuente
        $ft0=16;        //tamaño de fuente
        $ft1=12;        //tamaño de fuente
        $ft2=10;     //tamaño de fuente
        $ft3=7;     //tamaño de fuente
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

        //Vars        
        foreach($Valores as $in){
            //Variables con datos
            $ent = utf8_decode($in['ent']);
            $entidad = utf8_decode($in['entidad']);        
            $dto = utf8_decode($in['dto']);
            $mpio = utf8_decode($in['mpio']);
            $municipio = utf8_decode($in['municipio']); 
            $seccion = utf8_decode($in['seccion']); 
            $tipo = utf8_decode($in['seccion_tipo']);
        }
        $TotHojas = ceil($Totales[0]/$RegsHoja);
        $Hoja = $this->hoja + 1;

        //Header
        $this->Image('common/img/logo.jpg',15,7,60);
        $this->SetFont('Arial','B',$ft0);
        $this->SetTextColor($color1);
        $this->Text($x[40],$y,utf8_decode("VERIFICACIÓN NACIONAL MUESTRAL, 2014"));
        $y = $y + $salto + $salto; 
        $this->SetFont('Arial','B',$ft1);
        $this->Text($x[40]+1,$y,utf8_decode("LISTADO DE VIVIENDAS SELECCIONADAS POR SECCIÓN"));
        $y = $y + $salto + $salto; 
        $this->Text($x[56],$y,utf8_decode("ZONA URBANA"));

        //Salto        
        $y = $y + $salto + 8;
        $this->SetFont($fuente,'',$ft2);
        $this->SetTextColor($color1);
        $this->SetDrawColor($color1);
        $this->SetFillColor($color2);
        $this->SetLineWidth(0.1);
        $this->Text($x[5],$y,utf8_decode('ENTIDAD:'));
        $this->Line($x[12],$y+.5,$x[30],$y+.5);
        $this->Rect($x[28],$y-4.5,$x[2],5,'B');
        $this->Rect($x[28]-1,$y-6,$x[3],2,'F');
        $this->Text($x[32],$y,utf8_decode('DISTRITO:'));
        $this->Rect($x[39],$y-4.5,$x[2],5,'B');
        $this->Rect($x[39]-1,$y-6,$x[3],2,'F');
        $this->Text($x[43],$y,utf8_decode('MUNICIPIO:'));
        $this->Line($x[51],$y+.5,$x[74],$y+.5);
        $this->Rect($x[74],$y-4.5,$x[3],5,'B');
        $this->Rect($x[74]-1,$y-6,$x[4],2,'F');
        $this->Text($x[79],$y,utf8_decode('SECCIÓN:'));
        $this->Rect($x[86],$y-4.5,$x[5],5,'B');
        $this->Rect($x[86]-1,$y-6,$x[6],2,'F');
        $this->Text($x[93],$y,utf8_decode('TIPO:'));
        $this->Rect($x[97],$y-4.5,$x[3],5,'B');
        $this->Rect($x[97]-1,$y-6,$x[4],2,'F');
        //Datos        
        $this->SetFont($fuente,'B',$ft2);
        $this->Text($x[12],$y,$entidad);
        $this->Text($x[28]+2,$y,ceros($ent,2));
        $this->Text($x[40]-1,$y,ceros($dto,2));
        $this->Text($x[51]+1,$y,$municipio);
        $this->Text($x[75],$y,ceros($mpio,3));
        $this->Text($x[88]-1,$y,ceros($seccion,4));
        $this->Text($x[98]+1,$y,$tipo);

        ##Titulos
        //salto de linea 
        $y = $y + $salto + 1;           
        $this->SetFont($fuente,'',$ft3);
        $this->SetTextColor($color1);
        $this->SetDrawColor($color1);
        $this->SetFillColor($color2);
        $this->SetLineWidth(0.1);
        //Cuadro base
        $this->SetFillColor($color3);
        $this->SetLineWidth(0.1);
        $this->Rect($x[5],$y,$x[95],20,'FD');        
        //Linea 1
        $y = $y;
        
        $this->SetFillColor($color2);   
        $this->Rect($x[5],$y,$x[6],10,'B');    
        $this->Rect($x[12]-.2,$y,$x[5],10,'B');
        $this->Rect($x[18]-.4,$y,$x[13],10,'B');
        $this->Rect($x[32]-.6,$y,$x[20],10,'B');
        $this->Rect($x[53]-.8,$y,$x[34]-.4,10,'B');
        $this->Rect($x[88]-1.4,$y,$x[6],20,'B');
        $this->Rect($x[95]-1.6,$y,$x[6]-1,20,'B');
        $y = $y + $salto + 2;
        $this->SetFont($fuente,'B',$ft2); 
        $this->Ln(33);   
        $this->WriteTag($x[5]+1,15,5,'<b>'.utf8_decode("LOCALI                 DAD").'</b>',0,"C",0,0);
        $this->Ln(-10);
        $this->WriteTag($x[12],15,5,'<b>'.utf8_decode("MANZA                 NA").'</b>',0,"C",0,0);
        $this->Ln(-10);
        $this->WriteTag($x[18],35,5,'<b>'.utf8_decode("VIVIENDAS                 SELECCIONADAS").'</b>',0,"C",0,0);
        $this->Text($x[37],$y,utf8_decode('ENTREGADO A:'));
        $this->Text($x[60],$y,utf8_decode('RESULTADO DE LA VALIDACIÓN'));
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[89]-1,$y+$salto,utf8_decode('CAPTURA'));
        $this->Ln(-6);
        $this->WriteTag($x[95]-2,20,4,'<b>'.utf8_decode("ENVÍO A                 VOCALÍA                 LOCAL").'</b>',0,"C",0,0);
        
        //Linea 2
        $y = $y + 4;
        $this->Rect($x[5],$y,$x[6],10,'B');
        $this->Rect($x[12]-.2,$y,$x[5],10,'B');
        $this->Rect($x[18]-.4,$y,$x[5],10,'B');
        $this->Rect($x[24]-.6,$y,$x[7]+.2,10,'B');
        $this->Rect($x[32]-.6,$y,$x[20],10,'B');
        $this->Rect($x[53]-.8,$y,$x[6],10,'B');
        $this->Rect($x[60]-1,$y,$x[20],10,'B');
        $this->Rect($x[81]-1.2,$y,$x[6],10,'B');
        // $this->Rect($x[88]-1.4,$y,$x[6],10,'B'); //
        // $this->Rect($x[95]-1.6,$y,$x[6]-1,10,'B'); //
        $y = $y + $salto + 2;
        $this->SetFont($fuente,'B',$ft3);
        $this->Text($x[7],$y,utf8_decode('CLAVE'));
        $this->Text($x[13]+1,$y,utf8_decode('CLAVE'));
        $this->Ln(-5);   
        $this->WriteTag($x[18],15,3,'<b>'.utf8_decode("FOLIO                 CAPTURA").'</b>',0,"C",0,0);
        $this->Ln(-6);  
        $this->WriteTag($x[24],20,3,'<b>'.utf8_decode("CONSECUTIVO DE VIVIENDA").'</b>',0,"C",0,0);
        $this->Text($x[36],$y,utf8_decode('NOMBRE DEL VISITADOR'));
        $this->Ln(-6);  
        $this->WriteTag($x[53],15,3,'<b>'.utf8_decode("RECON-                 SULTA").'</b>',0,"C",0,0);
        $this->Text($x[64],$y,utf8_decode('MOTIVO DE LA RECONSULTA'));
        $this->Ln(-6);  
        $this->WriteTag($x[81],15,3,'<b>'.utf8_decode("CORREC-                 TA").'</b>',0,"C",0,0);
        
        //Indices
        $y = $y + $salto;  
        $alto = 4;
        $this->Rect($x[5],$y,$x[6],$alto,'B');
        $this->Rect($x[12]-.2,$y,$x[5],$alto,'B');
        $this->Rect($x[18]-.4,$y,$x[5],$alto,'B');
        $this->Rect($x[24]-.6,$y,$x[7]+.2,$alto,'B');
        $this->Rect($x[32]-.6,$y,$x[20],$alto,'B');
        $this->Rect($x[53]-.8,$y,$x[6],$alto,'B');
        $this->Rect($x[60]-1,$y,$x[20],$alto,'B');
        $this->Rect($x[81]-1.2,$y,$x[6],$alto,'B');
        $this->Rect($x[88]-1.4,$y,$x[6],$alto,'B'); //
        $this->Rect($x[95]-1.6,$y,$x[6]-1,$alto,'B'); //
        $y = $y + $salto-1; 
        $this->SetFont($fuente,'',$ft4);
        $this->Text($x[7],$y,utf8_decode('(1)'));
        $this->Text($x[14]+2,$y,utf8_decode('(2)'));
        $this->Text($x[20],$y,utf8_decode('(3)'));
        $this->Text($x[27],$y,utf8_decode('(4)'));
        $this->Text($x[42],$y,utf8_decode('(5)'));
        $this->Text($x[56],$y,utf8_decode('(6)'));
        $this->Text($x[70],$y,utf8_decode('(7)'));
        $this->Text($x[84]-1,$y,utf8_decode('(8)'));
        $this->Text($x[91],$y,utf8_decode('(9)'));
        $this->Text($x[97],$y,utf8_decode('(10)'));
        $y-=3;             
    }

    function Footer()   {
      $this->SetY(-15);
      $this->SetFont('Arial','I',8);
      $this->SetTextColor(128);
      $this->Cell(0,10,$this->PageNo().' de '.'TotalPages',0,0,'R');
    }

    function Hoja1(){     
        ##Recuperación de arrays con datos        
        $Totales = $this->totales;
        $Valores = $this->valores;
        $RegsHoja = $this->regsPorHoja;

        //Variables layout        
        $w=208;     //ancho de tabla
        $h=4;       //alto de fila
        $i=42;      // Inicio superior de tabla
        $y=10;      //separacion de linea inicial
        $x[1]=5;        //posicion x - margen izquierdo
        $largo = 260;   //Espacio de trabajo - Largo
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

        ##Inicio Nominativo     
        $y = $y + $salto + 49;   
        
        ##DATOS
        $Hoja = 1;
        $ContRegs=0;
        $this->SetFont($fuente,'B',$ft3);  
        foreach($Valores as $in){
            $Reg = $i+1;               
            $ContRegs++;
            $this->hoja = $Hoja;         
            if($ContRegs==$RegsHoja+1){ 
                ##TOTALES                
                $ContRegs=1;
                $Hoja++;
                $y = $y + $salto + 4;
                // $this->SetFont($fuente,'B',$ft2);
                // $this->Rect($x[12]-.2,$y,$x[5],$alto,'B');
                // $this->Rect($x[18]-.4,$y,$x[5],$alto,'B');                
                // $this->Text($x[6],$y+$salto,utf8_decode('TOTAL'));
                // $this->Text($x[14],$y+$salto,utf8_decode($Totales[0]));
                // $this->Text($x[20],$y+$salto,utf8_decode($Totales[1]));                
                $this->AddPage();
                $y = 63;
                $f=true;
            }else{$f=false;} 
            //Variables con datos
            $loc = ceros($in['loc'],4);  
            $manzana = ceros($in['manzana'],2);  
            $folio = ceros($in['folio'],4);  
            $consecutivo = ceros($in['consecutivo'],4); 

            $y = $y + $salto;     
            $alto = 6;            
            $this->Rect($x[5],$y,$x[6],$alto,'B');
            $this->Rect($x[12]-.2,$y,$x[5],$alto,'B');
            $this->Rect($x[18]-.4,$y,$x[5],$alto,'B');
            $this->Rect($x[24]-.6,$y,$x[7]+.2,$alto,'B');
            $this->Rect($x[32]-.6,$y,$x[20],$alto,'B');
            $this->Rect($x[53]-.8,$y,$x[6],$alto,'B');
            $this->Rect($x[60]-1,$y,$x[20],$alto,'B');
            $this->Rect($x[81]-1.2,$y,$x[6],$alto,'B');
            $this->Rect($x[88]-1.4,$y,$x[6],$alto,'B'); //
            $this->Rect($x[95]-1.6,$y,$x[6]-1,$alto,'B'); //
            $y = $y + $salto; 
            $this->SetFont($fuente,'B',$ft3);
            // $this->Text($x[2]+1,$y,utf8_decode($ContRegs)); //
            $this->Text($x[6],$y,$loc);
            $this->Text($x[14],$y,$manzana);
            $this->Text($x[19]+1,$y,$folio);
            $this->Text($x[26]+1,$y,$consecutivo);           
            $y-=2;  
            $SubTotal = $ContRegs;
        }
        if(!$f){
            ##TOTALES
            $y = $y + $salto + 4;
            $this->SetFont($fuente,'B',$ft2);
            $this->Rect($x[12]-.2,$y,$x[5],$alto,'B');
            $this->Rect($x[18]-.4,$y,$x[5],$alto,'B');                
            $this->Text($x[6],$y+$salto,utf8_decode('TOTAL'));
            $this->Text($x[14],$y+$salto,utf8_decode($Totales[0]));
            $this->Text($x[20],$y+$salto,utf8_decode($Totales[1])); 
        }          
    }

    function PrintDatos($Valores, $Totales){ 
        $this->regsPorHoja = 20; //Registros por Hoja
        $this->setValores($Valores,$Totales);       
        $this->AddPage();
        $this->Hoja1();        
    }

    function setValores($Valores, $Totales){
        reset($Valores);
        $this->valores = $Valores;
        reset($Totales);
        $this->totales = $Totales;
    }
    ##FIN template PDF
}
/*O3M*/
?>