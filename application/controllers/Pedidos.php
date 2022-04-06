<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos extends CI_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->library('session');
		$this->load->model('articulos_model');
        $this->load->model('productos_model');
        $this->load->model('categorias_model');
        $this->load->model('pedidos_model');
        $this->load->model('pedidosDetalles_model');
        $this->load->model('presentaciones_model');
        $this->load->model('preposiciones_model');
        $this->load->model('usuarios_model');
        $this->load->model('aperturaCaja_model');
        $this->load->model('aperturaCajaItems_model');
        $this->load->model('documentosTipos_model');
        $this->load->model('personas_model');
        $this->load->model('mesas_model');
        $this->load->model('cajas_model');
        $this->load->model('paises_model');
        $this->load->model('comprobantesDePago_model');
        $this->load->model('comprobantesDePagoTiposSeries_model');
        $this->load->model('comprobantesDePagoTipos_model');
        $this->load->model('documentosTiposComprobantesDePagoTipos_model');
    }

    public function comanda(){
        $user=$_SESSION["user"];
        $p_id=$this->input->get('p_id');
        $mesa_id=$this->input->get('m_id');
        $width=56;
		$height=300;
		$margin_lat=1;
		$margin_top=5;
		$salto_de_linea=5;
        $salto=5;
		$npage=1;
		$w=$width-2*$margin_lat;
        $fontDefault='Arial';
        $fontSizeDefaul=9;
        $pdf=PRINTER::load('P','mm',array($width,$height));
		$pdf->SetMargins($margin_lat,$margin_lat,$margin_lat);
        
		$pdf->AddPage();
		$pdf->AliasNbPages();

        //cabecera
        $pdf->SetFont($fontDefault,'B',$fontSizeDefaul+9);
		$pdf->setXY($margin_lat,$margin_top);
        $pdf->setTextColor(255,255,255);
		$pdf->MultiCell($w,3*$salto_de_linea,strtoupper('COMANDA'),0,'C',true);

        $pdf->setTextColor(0,0,0);
        
        $pdf->SetFont($fontDefault,'b',$fontSizeDefaul+3);
        $pdf->setXY($margin_lat,$pdf->getY());
		$pdf->MultiCell($w,$salto_de_linea+2,strtoupper($user["business_type_denomination_es"]),0,'C',false);

        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+3);
        $pdf->setXY($margin_lat,$pdf->getY());
		$pdf->MultiCell($w,$salto_de_linea,strtoupper($user["business_name"]),0,'C',false);
        //$pdf->MultiCell($w,$salto_de_linea,strtoupper($user["business_name"].'('.$user["business_ruc"].')'),0,'C',false);
        
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul-2);
        $pdf->setXY($margin_lat,$pdf->getY());
		$pdf->MultiCell($w,$salto_de_linea,'ID: '.md5($user["business_id"]),0,'C',false);
        
        //$pdf->SetFont($fontDefault,'',$fontSizeDefaul-1);
        //$pdf->setXY($margin_lat,$pdf->getY());
		//$pdf->MultiCell($w,$salto_de_linea,strtoupper( $user["business_address"]),0,'C',false);

        //REFERENCIA
         $pdf->SetFont($fontDefault,'',$fontSizeDefaul+1);
         $cabecera=array(
             array("denominacion"=>"REF","align"=>"L","ancho"=>30),
             array("denominacion"=>":","align"=>"L","ancho"=>5),
             array("denominacion"=>strtoupper($p_id),"align"=>"L","ancho"=>30)
         );
 
         $posX=$pdf->getX();
         $posY=$pdf->getY()+2;
         foreach($cabecera as $index=>$item){
             if($index==0){
                 $pdf->setXY($posX,$posY);
                 $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
             }else{
                 $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                 $pdf->setXY($posX,$posY);
                 $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,$item["denominacion"],0,$item["align"],false);
             }
         }

        //MESA 
        $objMesas=$this->mesas_model->get(array("id"=>$mesa_id));
        $mesa=$objMesas["data"][0];

        $pdf->SetFont($fontDefault,'',$fontSizeDefaul);
        $cabecera=array(
			array("denominacion"=>"MESA","align"=>"L","ancho"=>30),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($mesa["denominacion"]),"align"=>"L","ancho"=>65)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,$item["denominacion"],0,$item["align"],false);
            }
        }

        //FECHA DE EMISION
        $objPedido=$this->pedidos_model->get(array("id"=>$p_id));
        $pedido=$objPedido["data"][0];
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul);
        $fecha_emision=DATE::convertUTCToDateTimeZone($pedido["fecha_hora_emision"]);
        $cabecera=array(
			array("denominacion"=>"EMISION","align"=>"L","ancho"=>30),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($fecha_emision->format('Y-m-d h:i a')),"align"=>"L","ancho"=>65)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,$item["denominacion"],0,$item["align"],false);
            }
        }

        //MOZO
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul);
        $objPersonas=$this->personas_model->get(array("id"=>$pedido["mozo_id"]));
        $mozo=$objPersonas["data"][0];
        $nick_array= explode(" ", $mozo["nombres"]);
        $cabecera=array(
			array("denominacion"=>"MOZO","align"=>"L","ancho"=>30),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($nick_array[0]),"align"=>"L","ancho"=>65)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }
        }

        //DETALLE DEL PEDIDO 
        $objDetails=$this->pedidosDetalles_model->get(array("pedido_id"=>$p_id));
        $details=$objDetails["data"];

        $cabecera_details=array(
			array("denominacion"=>"CANT","ancho"=>15),
			array("denominacion"=>"DESCRIPCION","ancho"=>80)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY()+$salto;
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul);
        foreach($cabecera_details as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->Cell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,1,'C');
            }else{
                $posX=$posX+($w*$cabecera_details[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->Cell($w*($cabecera_details[$index]["ancho"]/100),$salto_de_linea,utf8_decode($item["denominacion"]),0,1,'C');
            }
        }

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+3);
        foreach($details as $index1 =>$d){
            
            $posX=$margin_lat;
            
            $pdf->setXY($posX,$posY);
            $pdf->MultiCell($w*$cabecera_details[0]["ancho"]/100,$salto_de_linea,utf8_decode($d["cantidad"]),0,'C',false);

            $posX=$posX+($w*$cabecera_details[0]["ancho"]/100);
            $pdf->setXY($posX,$posY);
            $sugerencias="";
            if($d["sugerencias"]!=""){
                $sugerencias=" (".$d["sugerencias"].")";
            }
            $pdf->MultiCell($w*$cabecera_details[1]["ancho"]/100,$salto_de_linea,ucfirst(utf8_decode($d["articulo_denominacion"].($sugerencias))),0,'L',false);
            
            $posY=$pdf->getY();     
        }
     
        $pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->Output();


    }
    public function print_ticket(){
        $user=$_SESSION["user"];
        $p_id=$this->input->get('p_id');
        
		$width=56;
		$height=300;
		$margin_lat=2;
		$margin_top=5;
		$salto_de_linea=5;
        $salto=5;
		$npage=1;
		$w=$width-2*$margin_lat;
        $fontDefault='Arial';
        $fontSizeDefaul=7;
		$pdf=PRINTER::load('P','mm',array($width,$height));
		$pdf->SetMargins($margin_lat,$margin_lat,$margin_lat);
        
		$pdf->AddPage();
		$pdf->AliasNbPages();
        
		//cabecera
        //$pdf->SetFont($fontDefault,'B',$fontSizeDefaul+9);
		//$pdf->setXY($margin_lat,$margin_top);
        //$pdf->setTextColor(255,255,255);
		//$pdf->MultiCell($w,3*$salto_de_linea,strtoupper('antojos app'),0,'C',true);
        
        $pdf->setTextColor(0,0,0);
        
      
        
        $pdf->SetFont($fontDefault,'b',$fontSizeDefaul+3);
        $pdf->setXY($margin_lat,$pdf->getY());
		$pdf->MultiCell($w,$salto_de_linea,strtoupper($user["business_type_denomination_es"]),0,'C',false);

        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+3);
        $pdf->setXY($margin_lat,$pdf->getY());
		$pdf->MultiCell($w,$salto_de_linea,strtoupper($user["business_name"]),0,'C',false);
        //$pdf->MultiCell($w,$salto_de_linea,strtoupper($user["business_name"].'('.$user["business_ruc"].')'),0,'C',false);
        
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul);
        $pdf->setXY($margin_lat,$pdf->getY());
		$pdf->MultiCell($w,$salto_de_linea,'ID: '.md5($user["business_id"]),0,'C',false);

        //$pdf->SetFont($fontDefault,'',$fontSizeDefaul);
        //$pdf->setXY($margin_lat,$pdf->getY());
		//$pdf->MultiCell($w,$salto_de_linea,strtoupper( $user["business_address"]),0,'C',false);

        $q="pedido_id=".$p_id."&";
        $objAperturaCajaItems=$this->aperturaCajaItems_model->get(array("referencia"=>$q));
        $modalidad_pago="";
        $lote="";
        $mesa_id="";
        $aperturaCajaItems=$objAperturaCajaItems["data"];
        
        foreach($aperturaCajaItems as $index => $a){
            
            $referencia_array = array();
            parse_str($a["referencia"], $referencia_array);
            
            foreach($referencia_array as $key=>$val){
                if($key=="pedido_id" && $val==$p_id){
                    $modalidad_pago=$referencia_array["modalidad_pago"];
                    //codigo de la apertura de caja
                    $lote=$a["apertura_caja_id"];
                    $mesa_id=$referencia_array["mesa_id"];
                    break;
                }
            }
        }

        //LOTE
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+2);
        $cabecera=array(
			array("denominacion"=>"LOTE","align"=>"L","ancho"=>30),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($lote),"align"=>"L","ancho"=>13),
            array("denominacion"=>"","align"=>"L","ancho"=>4),
            array("denominacion"=>"REF","align"=>"R","ancho"=>25),
            array("denominacion"=>":","align"=>"R","ancho"=>5),
			array("denominacion"=>strtoupper($p_id),"align"=>"L","ancho"=>15)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,$item["denominacion"],0,$item["align"],false);
            }
        }

        //MESA 
        $objMesas=$this->mesas_model->get(array("id"=>$mesa_id));
        $mesa=$objMesas["data"][0];

        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+2);
        $cabecera=array(
			array("denominacion"=>"MESA","align"=>"L","ancho"=>30),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($mesa["denominacion"]),"align"=>"L","ancho"=>65)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,$item["denominacion"],0,$item["align"],false);
            }
        }

        //CAJA
        $objAperturaCaja=$this->aperturaCaja_model->get(array("id"=>$lote));
        $aperturaCaja=$objAperturaCaja["data"][0];
        $caja_id=$aperturaCaja["caja_id"];

        $objCajas=$this->cajas_model->get(array("id"=>$caja_id));
        $caja=$objCajas["data"][0];

        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+1);
        $cabecera=array(
			array("denominacion"=>"CAJA","align"=>"L","ancho"=>30),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($caja["denominacion"]),"align"=>"L","ancho"=>65)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,$item["denominacion"],0,$item["align"],false);
            }
        }

        //FECHA DE EMISION
        $objPedido=$this->pedidos_model->get(array("id"=>$p_id));
        $pedido=$objPedido["data"][0];
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+1);
        $fecha_emision=DATE::convertUTCToDateTimeZone($pedido["fecha_hora_emision"]);
        $cabecera=array(
			array("denominacion"=>"EMISION","align"=>"L","ancho"=>30),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($fecha_emision->format('Y-m-d h:i a')),"align"=>"L","ancho"=>65)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,$item["denominacion"],0,$item["align"],false);
            }
        }

        //CLIENTE
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+2);
        if($pedido["cliente_id"]!=0){
            $objPersonas=$this->personas_model->get(array("id"=>$pedido["cliente_id"]));
            $persona=$objPersonas["data"][0];
            if($persona["documento_tipo_id"]==2){
                $cabecera=array(
                    array("denominacion"=>"CLIENTE","align"=>"L","ancho"=>30),
                    array("denominacion"=>":","align"=>"L","ancho"=>5),
                    array("denominacion"=>strtoupper($persona["razon_social"]),"align"=>"L","ancho"=>65)
                );
            }else{
                $cabecera=array(
                    array("denominacion"=>"CLIENTE","align"=>"L","ancho"=>30),
                    array("denominacion"=>":","align"=>"L","ancho"=>5),
                    array("denominacion"=>strtoupper($persona["apellidos"].' '.$persona["nombres"]),"align"=>"L","ancho"=>65)
                );
            }
            
        }else{
            $cabecera=array(
                array("denominacion"=>"CLIENTE","align"=>"L","ancho"=>30),
                array("denominacion"=>":","align"=>"L","ancho"=>5),
                array("denominacion"=>"","align"=>"L","ancho"=>65)
            );
        }
        
        

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }
        }

        //MODALIDAD DE PAGO
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+2);
        $cabecera=array(
			array("denominacion"=>"PAGO","align"=>"L","ancho"=>30),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($modalidad_pago),"align"=>"L","ancho"=>65)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }
        }

        //DETALLE DEL PEDIDO 
        $objDetails=$this->pedidosDetalles_model->get(array("pedido_id"=>$p_id));
        $details=$objDetails["data"];

        $cabecera_details=array(
			array("denominacion"=>"CANT","ancho"=>10),
			array("denominacion"=>"DESCRIPCION","ancho"=>67),
			array("denominacion"=>"IMPORTE","ancho"=>23)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY()+$salto;
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+1);
        foreach($cabecera_details as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->Cell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,1,'C');
            }else{
                $posX=$posX+($w*$cabecera_details[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->Cell($w*($cabecera_details[$index]["ancho"]/100),$salto_de_linea,utf8_decode($item["denominacion"]),0,1,'C');
            }
        }
        
        $posX=$pdf->getX();
        $posY=$pdf->getY();
        $total=0;

        foreach($details as $index1 =>$d){
            
            $posX=$margin_lat;
            
            $pdf->setXY($posX,$posY);
            $pdf->MultiCell($w*$cabecera_details[0]["ancho"]/100,$salto_de_linea,utf8_decode($d["cantidad"]),0,'C',false);

            $posX=$posX+($w*$cabecera_details[0]["ancho"]/100);
            $pdf->setXY($posX,$posY);
            $w_mm=$w*$cabecera_details[1]["ancho"]/100;
            $q=intval(($pdf->GetStringWidth($d["articulo_denominacion"])+10)/$w_mm);
            $pdf->MultiCell($w_mm,$salto_de_linea,utf8_decode($d["articulo_denominacion"]),0,'L',false);
            
            $posX=$posX+($w*$cabecera_details[1]["ancho"]/100);
            $pdf->setXY($posX,$posY);
            $importe=number_format($d["precio_unitario_pen"]*$d["cantidad"], 2, '.', '');
            $pdf->MultiCell($w*$cabecera_details[2]["ancho"]/100,$salto_de_linea,utf8_decode($importe),0,'C',false);
            
            $posX=$margin_lat;
            $posY=$pdf->getY()+$q*$salto;
            $pdf->setXY($posX,$posY);
        
            $total=$total+$importe;      
        }

        $pdf->SetFont($fontDefault,'B',$fontSizeDefaul+4);
        /*$SUB_TOTAL=number_format($total/1.18, 2, '.', '');
        $cabecera_total=array(
			array("denominacion"=>"SUB TOTAL: ","align"=>"R","ancho"=>60),
			array("denominacion"=>"S/. ".$SUB_TOTAL,"align"=>"R","ancho"=>40)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY()+$salto;
        foreach($cabecera_total as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->Cell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,1,$item["align"]);
            }else{
                $posX=$posX+($w*$cabecera_total[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->Cell($w*($cabecera_total[$index]["ancho"]/100),$salto_de_linea,utf8_decode($item["denominacion"]),0,1,$item["align"]);
            }
        }

        $IGV=number_format($total/1.18*0.18, 2, '.', '');
        $cabecera_total=array(
			array("denominacion"=>"I.G.V: ","align"=>"R","ancho"=>60),
			array("denominacion"=>"S/. ".$IGV,"align"=>"R","ancho"=>40)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera_total as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->Cell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,1,$item["align"]);
            }else{
                $posX=$posX+($w*$cabecera_total[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->Cell($w*($cabecera_total[$index]["ancho"]/100),$salto_de_linea,utf8_decode($item["denominacion"]),0,1,$item["align"]);
            }
        }*/

        $total=number_format($total, 2, '.', '');
        $cabecera_total=array(
			array("denominacion"=>"TOTAL: ","align"=>"R","ancho"=>60),
			array("denominacion"=>"S/. ".$total,"align"=>"R","ancho"=>40)
		);

        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera_total as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera_total[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }
        }

        $cabecera_total=array(
			array("denominacion"=>"Muchas gracias por su visita, vuelva pronto ","align"=>"C","ancho"=>100)
		);

        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+1);
        $posX=$pdf->getX();
        $posY=$pdf->getY()+$salto;
        foreach($cabecera_total as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera_total[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }
        }

        $pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->Output();
    }
    
    public function buscar_articulo(){
        $user=$_SESSION["user"];
        $busqueda_por=$this->input->post("busqueda_por");
        $filtro=$this->input->post("filtro");
        $establecimiento_id=$user["business_id"];
        $filtros=null;
        switch($busqueda_por){
            case 1:
                $filtros=array(
                        "articulo_denominacion"=>$filtro,
                        "establecimiento_id"=>$establecimiento_id,
                );
                break;
            case 2:

                break;
            case 3:
                break;
        }
        $objArticulos=$this->articulos_model->get($filtros);
        $articulos=$objArticulos["data"];
        echo json_encode($articulos);
    }
	public function abrir_agregar_nuevo(){
        $user=$_SESSION["user"];
        $articulos=array();
        $dc_templates_extranet_mostrador_buscar["busqueda_por"]=array(
            array("id"=>1,"denominacion"=>"Denominacion"),
        );
        $dc_templates_extranet_mostrador_buscar["articulos"]=$articulos;
        $template=$this->load->view('templates/extranet/mostrador/buscador',$dc_templates_extranet_mostrador_buscar,true);
        $title="Agregar nuevo producto o articulo";
        echo json_encode(array("title"=>$title,"template"=>$template));
    }

	public function abrir_nuevo()
	{
        $user=$_SESSION["user"];
        $ref_pedido=$this->input->post('ref_ped');
        $mesa_id=$this->input->post("mesa_id");
        $mesa_denominacion=$this->input->post('mesa_denominacion');
      
        $pedidoDetalles=array();
        $title="Nuevo pedido - ".ucfirst($mesa_denominacion);
        $readonly=false;
        $pedido=array();
        if($ref_pedido!=0){
            $objPedidos=$this->pedidos_model->get(array("id"=>$ref_pedido));
            $pedido=$objPedidos["data"][0];
            $objPedidosDetalles=$this->pedidosDetalles_model->get(array("pedido_id"=>$ref_pedido,"establecimiento_id"=>$user["business_id"]));
            $pedidoDetalles=$objPedidosDetalles["data"];
            $title="Pedido ID:".$ref_pedido .' | '.ucfirst($mesa_denominacion);
            $readonly=true;
            
        }
        $mozos=array();
        if($user["user_type"]=="admin"){
            array_push($mozos,array("id"=>$user["user_person_id"],"nombres"=>$user["user_firstname"],"apellidos"=>$user["user_lastname"]));
        }
        $objUsuarios=$this->usuarios_model->get(array("rol_id"=>2,"establecimiento_id"=>$user["business_id"]));
        foreach($objUsuarios["data"] as $index =>$u){
            array_push($mozos,array("id"=>$u["persona_id"],"nombres"=>$u["persona_nombres"],"apellidos"=>$u["persona_apellidos"]));
        }
        $dc_templates_extranet_mostrador_nuevo["pedido"] = $pedido;
        $dc_templates_extranet_mostrador_nuevo["mozos"]=$mozos;
        $dc_templates_extranet_mostrador_nuevo["pedido_id"] = $ref_pedido;
        $dc_templates_extranet_mostrador_nuevo["mesa_id"] = $mesa_id;
        $dc_templates_extranet_mostrador_nuevo["readonly"] = $readonly;
        $dc_templates_extranet_mostrador_nuevo["mesa_denominacion"] = $mesa_denominacion;
    
        $dc_templates_extranet_mostrador_nuevo["pedidos_detalles"]=$pedidoDetalles;
        $template=$this->load->view('templates/extranet/mostrador/nuevo',$dc_templates_extranet_mostrador_nuevo,true);
		echo json_encode(array("title"=>$title,"template"=>$template));
	}
    public function documentos_segun_comprobante_tipo(){
        $comprobante_tipo_serie_id=$this->input->post('comprobante_tipo_serie_id');
        
        $objComprobanteTipoSerie=$this->comprobantesDePagoTiposSeries_model->get(array('id'=>$comprobante_tipo_serie_id));
        $comprobante_de_pago_tipo_id=$objComprobanteTipoSerie["data"][0]["comprobante_de_pago_tipo_id"];
        $objDocumentosTipos=$this->documentosTiposComprobantesDePagoTipos_model->get(array("comprobante_de_pago_tipo_id"=>$comprobante_de_pago_tipo_id));
        $documentosTiposSegunComprobante=$objDocumentosTipos["data"];
        foreach($documentosTiposSegunComprobante as $index =>$d){
            $documentosTiposSegunComprobante[$index]["denominacion_corto"]=strtoupper($d["denominacion_corto"]);
            $documentosTiposSegunComprobante[$index]["denominacion_largo_es"]=ucfirst($d["denominacion_largo_es"]);
        }
        echo json_encode(
            array(
                "success"=>true,
                "from"=>$comprobante_de_pago_tipo_id,
                "data"=>$documentosTiposSegunComprobante
            )
        );

    }
    public function abrir_generar_comprobante_electronico(){
        $user=$_SESSION["user"];
        $pedido_id=$this->input->post('ref');
      
        
        $objPedidosDetalles=$this->pedidosDetalles_model->get(array("pedido_id"=>$pedido_id));
        $pedidosDetalles=$objPedidosDetalles["data"];
        
        $total=0;
        foreach($pedidosDetalles as $index => $pd){
            $cantidad=$pd["cantidad"];
            $precio_unit=$pd["precio_unitario_pen"];
            $total+=($cantidad*$precio_unit);
        }

        $title='Nuevo comprobante electronico:  Total a pagar: S/.'.number_format($total, 2, '.', '');

        $objPaises=$this->paises_model->get(array());
        $objComprobantesDePagosSeries=$this->comprobantesDePagoTiposSeries_model->get(array("comprobante_de_pago_tipo_activo"=>1));
        
        $objDocumentosTipos=$this->documentosTiposComprobantesDePagoTipos_model->get(array("comprobante_de_pago_tipo_id"=>$objComprobantesDePagosSeries["data"][0]["comprobante_de_pago_tipo_id"]));
        $documentosTiposSegunComprobante=$objDocumentosTipos["data"];

        $documentosElectronicos=$objComprobantesDePagosSeries["data"];
        $dc_templates_extranet_pedidos_generar_comprobante_electronico["moneda_id"]='PEN';
        $dc_templates_extranet_pedidos_generar_comprobante_electronico["monto"]=$total;
        $dc_templates_extranet_pedidos_generar_comprobante_electronico["country_postal"]='51';
        $dc_templates_extranet_pedidos_generar_comprobante_electronico["paises"]=$objPaises["data"];
        $dc_templates_extranet_pedidos_generar_comprobante_electronico["pedido_id"]=$pedido_id;
        $dc_templates_extranet_pedidos_generar_comprobante_electronico["documentosTipos"]=$documentosTiposSegunComprobante;
        $dc_templates_extranet_pedidos_generar_comprobante_electronico["documentosElectronicos"]=$documentosElectronicos;
        
        $template=$this->load->view('templates/extranet/pedidos/generar_comprobante_electronico',$dc_templates_extranet_pedidos_generar_comprobante_electronico,true);
        
        echo json_encode(array("title"=>$title,"template"=>$template));
    }
    public function abrir_cerrar_pedido(){

        $user=$_SESSION["user"];
        $pedido_mesa=$this->input->post('mesa_denominacion');
        $pedido_mesa_id=$this->input->post('mesa_id');
        $pedido_id=$this->input->post('pedido_id');
      
        $objDocumentosTipos=$this->documentosTipos_model->get(array());
        $documentosTipos=$objDocumentosTipos["data"];
        $objPedidosDetalles=$this->pedidosDetalles_model->get(array("pedido_id"=>$pedido_id));
        $pedidosDetalles=$objPedidosDetalles["data"];
        
        $total=0;
        foreach($pedidosDetalles as $index => $pd){
            $cantidad=$pd["cantidad"];
            $precio_unit=$pd["precio_unitario_pen"];
            $total+=($cantidad*$precio_unit);
        }
        $title="Cerrar pedido: ".strtoupper($pedido_mesa).' | Total a pagar: S/.'.number_format($total, 2, '.', '');
        $rol_id=1; //Cajero

        $objAperturaCaja=$this->aperturaCaja_model->get(array("establecimiento_id"=>$user["business_id"],"estado"=>"abierta"));
        
        $cajas=$objAperturaCaja["data"];

        $objPaises=$this->paises_model->get(array());

        $dc_templates_extranet_mostrador_cerrar_pedido["moneda_id"]='PEN';
        $dc_templates_extranet_mostrador_cerrar_pedido["monto"]=$total;
        $dc_templates_extranet_mostrador_cerrar_pedido["country_postal"]='51';
        $dc_templates_extranet_mostrador_cerrar_pedido["paises"]=$objPaises["data"];
        $dc_templates_extranet_mostrador_cerrar_pedido["mesa_id"]=$pedido_mesa_id;
        $dc_templates_extranet_mostrador_cerrar_pedido["modalidades"]=APIS::getModalidadesDePago();
        $dc_templates_extranet_mostrador_cerrar_pedido["pedido_id"]=$pedido_id;
        $dc_templates_extranet_mostrador_cerrar_pedido["documentosTipos"]=$documentosTipos;
        $dc_templates_extranet_mostrador_cerrar_pedido["cajas"]=$cajas;
       
        $template=$this->load->view('templates/extranet/mostrador/cerrar_pedido',$dc_templates_extranet_mostrador_cerrar_pedido,true);
        
        echo json_encode(array("title"=>$title,"template"=>$template));
    }
    public function abrir_cortar_cierre_pedido(){

        $user=$_SESSION["user"];
        $pedido_mesa=$this->input->post('mesa_denominacion');
        $pedido_mesa_id=$this->input->post('mesa_id');
        $pedido_id=$this->input->post('pedido_id');
      
        $objDocumentosTipos=$this->documentosTipos_model->get(array());
        $documentosTipos=$objDocumentosTipos["data"];
        $objPedidosDetalles=$this->pedidosDetalles_model->get(array("pedido_id"=>$pedido_id));
        $pedidosDetalles=$objPedidosDetalles["data"];
        
        $total=0;
        foreach($pedidosDetalles as $index => $pd){
            $cantidad=$pd["cantidad"];
            $precio_unit=$pd["precio_unitario_pen"];
            $total+=($cantidad*$precio_unit);
        }
        $title="Dividir pedido: ".strtoupper($pedido_mesa).' | Total pendiente: S/.'.number_format($total, 2, '.', '');
        $rol_id=1; //Cajero

        $objAperturaCaja=$this->aperturaCaja_model->get(array("establecimiento_id"=>$user["business_id"],"estado"=>"abierta"));
        
        $cajas=$objAperturaCaja["data"];

        $objPaises=$this->paises_model->get(array());
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["pedidos_detalles"]=$pedidosDetalles;
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["moneda_id"]='PEN';
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["monto"]=$total;
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["country_postal"]='51';
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["paises"]=$objPaises["data"];
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["mesa_id"]=$pedido_mesa_id;
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["modalidades"]=APIS::getModalidadesDePago();
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["pedido_id"]=$pedido_id;
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["documentosTipos"]=$documentosTipos;
        $dc_templates_extranet_mostrador_cortar_cierre_pedido["cajas"]=$cajas;
       
        $template=$this->load->view('templates/extranet/mostrador/cortar_cierre_pedido',$dc_templates_extranet_mostrador_cortar_cierre_pedido,true);
        
        echo json_encode(array("title"=>$title,"template"=>$template));

    }
    public function abrir_ver(){
        $user=$_SESSION["user"];
        $categoria_id=$this->input->post('id');
        
        $filtros=array("id"=>$categoria_id);
        $objCategoria=$this->categorias_model->get($filtros);
        
        if(!$objCategoria["success"]){
            echo json_encode(array("success"=>false,"msg"=>"error"));
            return;
        }
        
        $categoria=$objCategoria["data"][0];
       
        $title="Ver categoria - ".md5($categoria["id"]);
        $dc_templates_extranet_categorias_ver["user"]=$user;
        $dc_templates_extranet_categorias_ver["categoria"]=$categoria;
        $template=$this->load->view('templates/extranet/categorias/ver',$dc_templates_extranet_categorias_ver,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
    }
    public function abrir_editar(){
        $user=$_SESSION["user"];

        $articulo_id=$this->input->post('id');
        $filtros=array("id"=>$articulo_id);
        $objArticulo=$this->articulos_model->get($filtros);
        $objProductos=$this->productos_model->get(array("establecimiento_id"=>$user["business_id"]));
        $objCategorias=$this->categorias_model->get(array("establecimiento_id"=>$user["business_id"]));
        $objPresentaciones=$this->presentaciones_model->get(array("establecimiento_id"=>$user["business_id"]));
        $objPreposiciones=$this->preposiciones_model->get();
        $categorias=$objCategorias["data"];
        $productos=$objProductos["data"];
        $preposiciones=$objPreposiciones["data"];
        $presentaciones=$objPresentaciones["data"];

        if(!$objArticulo["success"]){
            echo json_encode(array("success"=>false,"msg"=>"error"));
            return;
        }
        
        $articulo=$objArticulo["data"][0];
        $denominacion="";
        switch($articulo["preposicion_id"]){
            case 1:
                $denominacion=ucfirst($articulo["categoria_denominacion_por_unidad"].' '.$articulo["producto_denominacion"].' - '.$articulo["presentacion_denominacion"]);
                break;
            case 2:
                $denominacion=ucfirst($articulo["producto_denominacion"].' '.$articulo["categoria_denominacion_por_unidad"].' - '.$articulo["presentacion_denominacion"]);
                break;    
            case 3:
                $denominacion=ucfirst($articulo["producto_denominacion"].' - '.$articulo["presentacion_denominacion"]);
                break;
            default:
                $denominacion=ucfirst($articulo["categoria_denominacion_por_unidad"].' '.$articulo["preposicion_denominacion"].' '.$articulo["producto_denominacion"].' - '.$articulo["presentacion_denominacion"]);
                break;
        }

        $title="Editar categoria - ".$denominacion;
        $dc_templates_extranet_articulos_editar["user"]=$user;
        $dc_templates_extranet_articulos_editar["productos"]=$productos;
        $dc_templates_extranet_articulos_editar["categorias"]=$categorias;
        $dc_templates_extranet_articulos_editar["presentaciones"]=$presentaciones;
        $dc_templates_extranet_articulos_editar["preposiciones"]=$preposiciones;
        $dc_templates_extranet_articulos_editar["denominacion"]=$denominacion;
        $dc_templates_extranet_articulos_editar["articulo"]=$articulo;
        $template=$this->load->view('templates/extranet/articulos/editar',$dc_templates_extranet_articulos_editar,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
        
    }
    public function abrir_cambiar_caja(){
        $user=$_SESSION["user"];
        $ref=$this->input->post('ref');
        $title="Cajas disponibles:";

        $q="pedido_id=".$ref."&";
        $objAperturaCajaItems=$this->aperturaCajaItems_model->get(array("referencia"=>$q));
        $aperturaCajaItems=$objAperturaCajaItems["data"];
        $apertura_caja_id_activa="";
        foreach($aperturaCajaItems as $index3 => $a){
            $referencia_array = array();
            parse_str($a["referencia"], $referencia_array);
            foreach($referencia_array as $key=>$val){
                if($key=="pedido_id" && $val==$ref){
                    $apertura_caja_id_activa=$a["apertura_caja_id"];
                    break;
                }
            }
        }
        
        $objAperturaCaja=$this->aperturaCaja_model->get(array("id"=>$apertura_caja_id_activa));
        $aperturaCaja_activa=$objAperturaCaja["data"][0];

        $objAperturasCajas=$this->aperturaCaja_model->get(array("establecimiento_id"=>$user["business_id"],"estado"=>"abierta"));
        $aperturas_cajas=$objAperturasCajas["data"];
        $dc_templates_extranet_pedidos_cambiar_caja["referencia"]=$ref;
        $dc_templates_extranet_pedidos_cambiar_caja["apertura_caja_actual"]=$aperturaCaja_activa;
        $dc_templates_extranet_pedidos_cambiar_caja["aperturas_cajas"]=$aperturas_cajas;
        $template=$this->load->view('templates/extranet/pedidos/cambiar_caja',$dc_templates_extranet_pedidos_cambiar_caja,true);

		echo json_encode(array("title"=>$title,"template"=>$template));

    }
    public function abrir_cambiar_metodo_de_pago(){
        $ref=$this->input->post('ref');
        $title="Modalidades de pagos disponibles:";


        $q="pedido_id=".$ref."&";
        $objAperturaCajaItems=$this->aperturaCajaItems_model->get(array("referencia"=>$q));
        $aperturaCajaItems=$objAperturaCajaItems["data"];
        $metodo_pago_seleccionado="";
        foreach($aperturaCajaItems as $index3 => $a){
            $referencia_array = array();
            parse_str($a["referencia"], $referencia_array);
            foreach($referencia_array as $key=>$val){
                if($key=="pedido_id" && $val==$ref){
                    $metodo_pago_seleccionado=$referencia_array["modalidad_pago"];
                    break;
                }
            }
        }

        $dc_templates_extranet_pedidos_cambiar_metodo_de_pago["referencia"]=$ref;
        $dc_templates_extranet_pedidos_cambiar_metodo_de_pago["metodo_de_pago_seleccionado"]=$metodo_pago_seleccionado;
        $dc_templates_extranet_pedidos_cambiar_metodo_de_pago["metodos_de_pago"]=APIS::getModalidadesDePago();
        $template=$this->load->view('templates/extranet/pedidos/cambiar_metodo_de_pago',$dc_templates_extranet_pedidos_cambiar_metodo_de_pago,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));

    }
    public function reabrir(){
        $user=$_SESSION["user"];
        $ref=$this->input->post('ref');
        $q="pedido_id=".$ref."&";
        $objAperturaCajaItems=$this->aperturaCajaItems_model->get(array("referencia"=>$q));
        
        $mesa_id="";
        foreach($objAperturaCajaItems["data"] as $index3 => $a){
            $referencia_array = array();
            parse_str($a["referencia"], $referencia_array);
            foreach($referencia_array as $key=>$val){
                if($key=="pedido_id" && $val==$ref){
                    $aperturaCajaItems_id=$a["id"];
                    $aperturaCaja_id=$a["apertura_caja_id"];
                    $mesa_id=$referencia_array["mesa_id"];
                }
            }
        }

        //verificamos si mesa esta ocupada
        $mesa_ocupada=false;
        $objPedidos=$this->pedidos_model->get(array("establecimiento_id"=>$user["business_id"],"cerrado"=>0));
        $pedidos=$objPedidos["data"];
        foreach($pedidos as $index_pedidos=>$pedido){
            if($pedido["referencia_a_bd"]!=""){
                $referencia_array = array();
                parse_str($pedido["referencia_a_bd"], $referencia_array);
                if($referencia_array["mesa_id"]==$mesa_id){
                    $mesa_ocupada=true;
                    break;
                }
            }
        }

        $objAperturaCaja=$this->aperturaCaja_model->get(array("id"=>$aperturaCaja_id));
        $aperturaCaja=$objAperturaCaja["data"][0];
        if($aperturaCaja["fecha_cierre"]=="0000-00-00 00:00:00"){
            $objPedidos=$this->pedidos_model->get(array("id"=>$ref));
            $pedido=$objPedidos["data"][0];
            
            $actualizado=$this->pedidos_model->actualizar($ref,$pedido["referencia_a_bd"],$pedido["cliente_id"],0);

            $eliminado=$this->aperturaCajaItems_model->delete($aperturaCajaItems_id);
        
            if($eliminado["success"]){
                echo json_encode(array("success"=>true,"msg"=>"Registro reabierto con exito"));
                return;
            }
        }else{
            echo json_encode(array("success"=>false,"msg_id"=>2,"msg"=>'Imposible <b>reabrir</b> pedido, La caja <b class="text-uppercase">'.$aperturaCaja["caja_denominacion"] .'</b> se encuentra cerrada.'));
            return;
        }

        if($mesa_ocupada){
            echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"Imposible <b>reabrir</b> pedido, la mesa se encuentra en uso."));
            return;
        }

        
        
    }
    public function guardar_generar_comprobante_electronico(){
        
        $user=$_SESSION["user"];
        $personaId=$user["business_apisunat_personaId"];
        $personaToken=$user["business_apisunat_personaToken"];
        $ruc=$user["business_ruc"];
        $comprobante_tipo_serie_id=$this->input->post("comprobante_tipo_serie");
        $pedido_id=$this->input->post('pedido_id');
        $objComprobantesDePagosTiposSeries=$this->comprobantesDePagoTiposSeries_model->get(array("id"=>$comprobante_tipo_serie_id));
        $comprobantesDePagosTiposSeries=$objComprobantesDePagosTiposSeries["data"][0];
        $comprobante_tipo=str_pad($comprobantesDePagosTiposSeries["comprobante_de_pago_tipo_id"], 2, "0", STR_PAD_LEFT);
        $serie=$comprobantesDePagosTiposSeries["numeracion"];
        $serie_id=$comprobantesDePagosTiposSeries["id"];
        $objComprobantesDePago=$this->comprobantesDePago_model->get(array("serie_id"=>$serie_id));
        if(count($objComprobantesDePago["data"])==0){
            $comprobante_numero=$comprobantesDePagosTiposSeries["comprobante_de_pago_numero_inicio"];
        }else{
            $last_index=count($objComprobantesDePago["data"])-1;
            
            $comprobante_numero=$objComprobantesDePago["data"][$last_index]["numero"]+1;
        }
        
        $comprobante_numero=str_pad($comprobante_numero, 8, "0", STR_PAD_LEFT);
        $now=DATE::convertUTCToDateTimeZone(DATE::getNowAccordingUTC());
        $documento_tipo_id=$this->input->post('documento_tipo');
        $documento_numero=$this->input->post('documento_numero');
        $moneda_id=$this->input->post("moneda_id");
        $objDocumentoTipoCliente=$this->documentosTipos_model->get(array("id"=>$documento_tipo_id));
        $documento_tipo_sunat_id=$objDocumentoTipoCliente["data"][0]["sunat_id"];
        $monto=$this->input->post("monto");
        $igv=$monto/1.18*0.18;
        
        $fileName=APIS::createFileName($ruc,$comprobante_tipo,$serie,$comprobante_numero);
        $customerEmail=$this->input->post("correo_electronico");
        $objDetails=$this->pedidosDetalles_model->get(array("pedido_id"=>$pedido_id));
        $details=array();

        foreach($objDetails["data"] as $index=>$d){
            $cantidad=$d["cantidad"];
            $precio=$d["precio_unitario_pen"];
            $denominacion=$d["articulo_denominacion"];
            $subTotal=$cantidad*$precio;
            $item=array(
                "cbc:ID"=>array("_text"=>($index+1)),
                "cbc:InvoicedQuantity"=>array("_attributes"=>array("unitCode"=>"NIU"),"_text"=>$cantidad),
                "cbc:LineExtensionAmount"=>array(
                    "_attributes"=>array("currencyID"=>$moneda_id),
                    "_text"=>sprintf('%.2f', $subTotal/1.18)
                ),
                "cac:PricingReference"=>array(
                    "cac:AlternativeConditionPrice"=>array(
                        "cbc:PriceAmount"=>array(
                            "_attributes"=>array("currencyID"=>$moneda_id),
                            "_text"=>sprintf('%.5f', $precio)
                        ),
                        "cbc:PriceTypeCode"=>array(
                            "_text"=>"01"
                        )
                    )
                ),
                "cac:TaxTotal"=>array(
                    "cbc:TaxAmount"=>array(
                        "_attributes"=>array("currencyID"=>$moneda_id),
                        "_text"=>sprintf('%.2f', $subTotal/1.18*0.18)
                    ),
                    "cac:TaxSubtotal"=>array(
                        array(
                            "cbc:TaxableAmount"=>array(
                                "_attributes"=>array("currencyID"=>$moneda_id),
                                "_text"=>sprintf('%.2f', $subTotal/1.18)
                            ),
                            "cbc:TaxAmount"=>array(
                                "_attributes"=>array("currencyID"=>$moneda_id),
                                "_text"=>sprintf('%.2f', $subTotal/1.18*0.18)
                            ),
                            "cac:TaxCategory"=>array(
                                "cbc:Percent"=>array("_text"=>18),
                                "cbc:TaxExemptionReasonCode"=>array("_text"=>10),
                                "cac:TaxScheme"=>array(
                                    "cbc:ID"=>array("_text"=>"1000"),
                                    "cbc:Name"=>array("_text"=>"IGV"),
                                    "cbc:TaxTypeCode"=>array("_text"=>"VAT")
                                )
                            )
                        )
                    )
                ),
                "cac:Item"=>array(
                    "cbc:Description"=>array("_text"=>$denominacion)
                ),
                "cac:Price"=>array(
                    "cbc:PriceAmount"=>array(
                        "_attributes"=>array("currencyID"=>$moneda_id),
                        "_text"=>sprintf('%.3f', $precio/1.18)
                    )
                )
            );
            array_push($details,$item);
        }
        if($comprobante_tipo=="01"){
            $documentBody=array(
                "cbc:UBLVersionID"=>array("_text"=>"2.1"),
                "cbc:CustomizationID"=>array("_text"=>"2.0"),
                "cbc:ID"=>array("_text"=>$serie.'-'.$comprobante_numero),
                "cbc:IssueDate"=>array("_text"=>$now->format('Y-m-d')),
                "cbc:IssueTime"=>array("_text"=>$now->format('H:i:s')),
                "cbc:DueDate"=>array(),
                "cbc:InvoiceTypeCode"=>array("_attributes"=>array("listID"=>"0101"),"_text"=>$comprobante_tipo),
                "cbc:Note"=>array(),
                "cbc:DocumentCurrencyCode"=>array("_text"=>$moneda_id),
                "cac:DespatchDocumentReference"=>array(),
                "cac:AdditionalDocumentReference"=>array(),
                "cac:AccountingSupplierParty"=>array(
                    "cac:Party"=>array(
                        "cac:PartyIdentification"=>array(
                            "cbc:ID"=>array(
                                "_attributes"=>array("schemeID"=>6),
                                "_text"=>$ruc
                            )
                        ),
                        "cac:PartyLegalEntity"=>array(
                            "cbc:RegistrationName"=>array(
                                    "_text"=>APIS::getRUC($ruc)["razonSocial"]
                                ),
                            "cac:RegistrationAddress"=>array(
                                    "cbc:AddressTypeCode"=>array(
                                        "_text"=>"0000"
                                    ),
                                    "cac:AddressLine"=>array(
                                        "cbc:Line"=>array(
                                            "_text"=>APIS::getRUC($ruc)["direccion"]
                                        )
                                    )
                                )
                        )
                    )
                ),
                "cac:AccountingCustomerParty"=>array(
                    "cac:Party"=>array(
                        "cac:PartyIdentification"=>array(
                            "cbc:ID"=>array(
                                "_attributes"=>array("schemeID"=>$documento_tipo_sunat_id),
                                "_text"=>$documento_numero
                            )
                        ),
                        "cac:PartyLegalEntity"=>array(
                            "cbc:RegistrationName"=>array(
                                    "_text"=>($documento_tipo_id == 2 ? $this->input->post('nombres') : $this->input->post('apellidos').' ' .$this->input->post('nombres'))
                                ),
                            "cac:RegistrationAddress"=>array(
                                    "cac:AddressLine"=>array(
                                        "cbc:Line"=>array(
                                            "_text"=>($documento_tipo_id == 2 ? $this->input->post('apellidos') : "")
                                        )
                                    )
                                )
                        )
                    )
                ),
                "cac:TaxTotal"=>array(
                    "cbc:TaxAmount"=>array(
                        "_attributes"=>array(
                            "currencyID"=>$moneda_id
                        ),
                        "_text"=>sprintf('%.2f', $igv)
                    ),
                    "cac:TaxSubtotal"=>array(
                        array(
                            "cbc:TaxableAmount"=>array(
                                "_attributes"=>array(
                                    "currencyID"=>$moneda_id
                                ),
                                "_text"=>sprintf('%.2f', $monto-$igv)
                            ),
                            "cbc:TaxAmount"=>array(
                                "_attributes"=>array(
                                    "currencyID"=>$moneda_id
                                ),
                                "_text"=>sprintf('%.2f', $igv)
                            ),
                            "cac:TaxCategory"=>array(
                                "cac:TaxScheme"=>array(
                                    "cbc:ID"=>array("_text"=>"1000"),
                                    "cbc:Name"=>array("_text"=>"IGV"),
                                    "cbc:TaxTypeCode"=>array("_text"=>"VAT")
                                )
                            )
                        )
                    )
                ),
                "cac:LegalMonetaryTotal"=>array(
                    "cbc:LineExtensionAmount"=>array(
                        "_attributes"=>array(
                            "currencyID"=>$moneda_id
                        ),
                        "_text"=>sprintf('%.2f', $monto-$igv)
                    ),
                    "cbc:TaxInclusiveAmount"=>array(
                        "_attributes"=>array(
                            "currencyID"=>$moneda_id
                        ),
                        "_text"=>sprintf('%.2f', $monto)
                    ),
                    "cbc:PayableAmount"=>array(
                        "_attributes"=>array(
                            "currencyID"=>$moneda_id
                        ),
                        "_text"=>sprintf('%.2f', $monto)
                    ),
                ),
                "cac:AllowanceCharge"=>array(),
                "cac:InvoiceLine"=>$details,
                "cac:PaymentTerms"=>array(
                    array(
                        "cbc:ID"=>array("_text"=>"FormaPago"),
                        "cbc:PaymentMeansID"=>array("_text"=>"Contado")
                    )
                ),
                "cac:Delivery"=>array(),
                "cac:OrderReference"=>array(),
                "cac:PaymentMeans"=>array()
            );
        }else{
            $documentBody=array(
                "cbc:UBLVersionID"=>array("_text"=>"2.1"),
                "cbc:CustomizationID"=>array("_text"=>"2.0"),
                "cbc:ID"=>array("_text"=>$serie.'-'.$comprobante_numero),
                "cbc:IssueDate"=>array("_text"=>$now->format('Y-m-d')),
                "cbc:IssueTime"=>array("_text"=>$now->format('H:i:s')),
                "cbc:InvoiceTypeCode"=>array("_attributes"=>array("listID"=>"0101"),"_text"=>$comprobante_tipo),
                "cbc:Note"=>array(),
                "cbc:DocumentCurrencyCode"=>array("_text"=>$moneda_id),
                "cac:DespatchDocumentReference"=>array(),
                "cac:AdditionalDocumentReference"=>array(),
                "cac:AccountingSupplierParty"=>array(
                    "cac:Party"=>array(
                        "cac:PartyIdentification"=>array(
                            "cbc:ID"=>array(
                                "_attributes"=>array("schemeID"=>6),
                                "_text"=>$ruc
                            )
                        ),
                        "cac:PartyLegalEntity"=>array(
                            "cbc:RegistrationName"=>array(
                                    "_text"=>APIS::getRUC($ruc)["razonSocial"]
                                ),
                            "cac:RegistrationAddress"=>array(
                                    "cbc:AddressTypeCode"=>array(
                                        "_text"=>"0000"
                                    ),
                                    "cac:AddressLine"=>array(
                                        "cbc:Line"=>array(
                                            "_text"=>APIS::getRUC($ruc)["direccion"]
                                        )
                                    )
                                )
                        )
                    )
                ),
                "cac:AccountingCustomerParty"=>array(
                    "cac:Party"=>array(
                        "cac:PartyIdentification"=>array(
                            "cbc:ID"=>array(
                                "_attributes"=>array("schemeID"=>$documento_tipo_sunat_id),
                                "_text"=>$documento_numero
                            )
                        ),
                        "cac:PartyLegalEntity"=>array(
                            "cbc:RegistrationName"=>array(
                                    "_text"=>($documento_tipo_id == 2 ? $this->input->post('nombres') : $this->input->post('apellidos').' ' .$this->input->post('nombres'))
                                ),
                            "cac:RegistrationAddress"=>array(
                                    "cbc:AddressTypeCode"=>array(
                                        "_text"=>"0000"
                                    ),
                                    "cac:AddressLine"=>array(
                                        "cbc:Line"=>array(
                                            "_text"=>($documento_tipo_id == 2 ? $this->input->post('apellidos') : "")
                                        )
                                    )
                                )
                        )
                    )
                ),
                "cac:TaxTotal"=>array(
                    "cbc:TaxAmount"=>array(
                        "_attributes"=>array(
                            "currencyID"=>$moneda_id
                        ),
                        "_text"=>sprintf('%.2f', $igv)
                    ),
                    "cac:TaxSubtotal"=>array(
                        array(
                            "cbc:TaxableAmount"=>array(
                                "_attributes"=>array(
                                    "currencyID"=>$moneda_id
                                ),
                                "_text"=>sprintf('%.2f', $monto-$igv)
                            ),
                            "cbc:TaxAmount"=>array(
                                "_attributes"=>array(
                                    "currencyID"=>$moneda_id
                                ),
                                "_text"=>sprintf('%.2f', $igv)
                            ),
                            "cac:TaxCategory"=>array(
                                "cac:TaxScheme"=>array(
                                    "cbc:ID"=>array("_text"=>"1000"),
                                    "cbc:Name"=>array("_text"=>"IGV"),
                                    "cbc:TaxTypeCode"=>array("_text"=>"VAT")
                                )
                            )
                        )
                    )
                ),
                "cac:LegalMonetaryTotal"=>array(
                    "cbc:LineExtensionAmount"=>array(
                        "_attributes"=>array(
                            "currencyID"=>$moneda_id
                        ),
                        "_text"=>sprintf('%.2f', $monto-$igv)
                    ),
                    "cbc:TaxInclusiveAmount"=>array(
                        "_attributes"=>array(
                            "currencyID"=>$moneda_id
                        ),
                        "_text"=>sprintf('%.2f', $monto)
                    ),
                    "cbc:PayableAmount"=>array(
                        "_attributes"=>array(
                            "currencyID"=>$moneda_id
                        ),
                        "_text"=>sprintf('%.2f', $monto)
                    ),
                ),
                "cac:InvoiceLine"=>$details,
                "cbc:DueDate"=>array(),
                "cac:Delivery"=>array(),
                "cac:OrderReference"=>array(),
                "cac:PaymentTerms"=>array(),
                "cac:PaymentMeans"=>array()
            );
        }
        
        $objComprobante=$this->comprobantesDePago_model->send($personaId,$personaToken,$fileName,$documentBody,$customerEmail);
        if($objComprobante["success"]){
            $documentId=$objComprobante["documentId"];
            $status=$objComprobante["status"];
            $insertado=$this->comprobantesDePago_model->insert($pedido_id,$documentId,$serie_id,$comprobante_numero);
            echo json_encode(array("success"=>true,"msg"=>"registro generado con exito","data"=>array(
                    "id"=>$insertado["id"],"pedido_id"=>$pedido_id,"documentId"=>$documentId,"fileName"=>$fileName,"status"=>$status
                )));
            return;
        }else{
            echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"Error al enviar el comprobante a la sunat"));
            return;
        }
    }
    public function guardar_cambiar_caja(){
        $ref=$this->input->post('referencia');
        $apertura_caja_id=$this->input->post('apertura_caja');
        $q="pedido_id=".$ref."&";
        $objAperturaCajaItems=$this->aperturaCajaItems_model->get(array("referencia"=>$q));
        $aperturaCajaItems=$objAperturaCajaItems["data"];
        $metodo_pago_seleccionado="";
        
        foreach($aperturaCajaItems as $index3 => $a){
            $referencia_array = array();
            parse_str($a["referencia"], $referencia_array);
            foreach($referencia_array as $key=>$val){
                if($key=="pedido_id" && $val==$ref){
                    $id=$a["id"];
                    $item_descripcion=$a["item_descripcion"];
                    $fecha_de_registro=$a["fecha_de_registro"];
                    $moneda_id=$a["moneda_id"];
                    $monto=$a["monto"];
                    $referencia=$a["referencia"];
                }
            }
        }

        $objAperturaCaja=$this->aperturaCaja_model->get(array("id"=>$apertura_caja_id));
        $aperturaCaja=$objAperturaCaja["data"][0];
        if($aperturaCaja["fecha_cierre"]=="0000-00-00 00:00:00"){
            $objAperturaCajaItems=$this->aperturaCajaItems_model->update($id,$apertura_caja_id,$item_descripcion,$fecha_de_registro,$moneda_id,$monto,$referencia);
            echo json_encode(array("success"=>true,"msg"=>$objAperturaCajaItems["msg"]));
            return;
        }
        echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>'La caja <b class="text-uppercase">'.$aperturaCaja["caja_denominacion"].'</b> ya se encuentra cerrada. Imposible realizar esta opeacion'));
        
    }

    public function guardar_cambiar_metodo_de_pago(){
        $ref=$this->input->post('referencia');
        $modalidad_de_pago=$this->input->post('modalidad_de_pago');
        $q="pedido_id=".$ref."&";
        $objAperturaCajaItems=$this->aperturaCajaItems_model->get(array("referencia"=>$q));
        $aperturaCajaItems=$objAperturaCajaItems["data"];
        $metodo_pago_seleccionado="";
        
        foreach($aperturaCajaItems as $index3 => $a){
            $referencia_array = array();
            parse_str($a["referencia"], $referencia_array);
            foreach($referencia_array as $key=>$val){
                if($key=="pedido_id" && $val==$ref){
                    $id=$a["id"];
                    $apertura_caja_id=$a["apertura_caja_id"];
                    $item_descripcion=$a["item_descripcion"];
                    $fecha_de_registro=$a["fecha_de_registro"];
                    $moneda_id=$a["moneda_id"];
                    $monto=$a["monto"];
                }
                if($key=="modalidad_pago"){
                    $referencia_array[$key]=strtolower(APIS::getModalidadDePago($modalidad_de_pago)["denominacion"]);
                }
            }
        }

        $referencia = http_build_query($referencia_array);
        $objAperturaCaja=$this->aperturaCaja_model->get(array("id"=>$apertura_caja_id));
        $aperturaCaja=$objAperturaCaja["data"][0];
        if($aperturaCaja["fecha_cierre"]=="0000-00-00 00:00:00"){
            $objAperturaCajaItems=$this->aperturaCajaItems_model->update($id,$apertura_caja_id,$item_descripcion,$fecha_de_registro,$moneda_id,$monto,$referencia);
            echo json_encode(array("success"=>true,"msg"=>$objAperturaCajaItems["msg"]));
            return;
        }
        echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>'La caja <b class="text-uppercase">'.$aperturaCaja["caja_denominacion"].'</b> ya se encuentra cerrada. Imposible realizar esta opeacion'));
        
    }
    public function guardar_editar(){
        $id=$this->input->post('id');
        $denominacion_por_unidad=$this->input->post('denominacion_por_unidad');
        $denominacion_por_grupo=$this->input->post('denominacion_por_grupo');
        $descripcion=$this->input->post('descripcion');
        $imagen_url_loaded=$this->input->post('imagen_url_loaded');
        $imagen_url_to_change=$this->input->post('imagen_url_to_change'); 
        $imagen_url_state=$this->input->post('imagen_url_state');
        $establecimiento_id=$this->input->post('establecimiento_id');
        $url=($imagen_url_state=="changed") ? $imagen_url_to_change : $imagen_url_loaded;
       
        $actualizado=$this->categorias_model->actualizar(
            $id,
            $denominacion_por_unidad,
            $denominacion_por_grupo,
            $descripcion,
            $url,
            $establecimiento_id
        );

        echo json_encode($actualizado);
    }
    public function guardar_dividir_cortar_pedido(){
        $pedido_id=$this->input->post("pedido_id");
        $mesa_id=$this->input->post("mesa_id");

        $objPedido=$this->pedidos_model->get(array("id"=>$pedido_id));
        $pedido=$objPedido["data"][0];
        $referencia_a_bd_array = array();
        parse_str($pedido["referencia_a_bd"], $referencia_a_bd_array);
        if(!isset($referencia_a_bd_array["pago_en_partes"])){
            $referencia_a_bd="venta=establecimiento&modo=presencial&pago_en_partes=padre&mesa_id=".$mesa_id;
            //actualizamos referecia de pedido
            $actualiza_pedido=$this->pedidos_model->actualizar_referencia($pedido_id,$referencia_a_bd);
        }
        //creamos un nuevo pedido
        $referencia_a_bd="venta=establecimiento&modo=presencial&pago_en_partes=hijo&ref_pedido=".$pedido_id."&mesa_id=".$mesa_id;
        $inserta_pedido_ref=$this->pedidos_model->insert($referencia_a_bd);

        $detalle=$this->input->post('detalle');
        //insertar el detalle en el nuevo pedido creado
        $monto_a_pagar=0;
        $cantidad_en_mesa_disponible=0;
        foreach($detalle as $index=>$d){
            $monto_a_pagar+=$d["cantidad_a_pagar"]*$d["precio"];
            $cantidad_en_mesa_disponible+=$d["cantidad_en_mesa"];
        }

        if($cantidad_en_mesa_disponible>0){

            $nuevo_pedido_id=$inserta_pedido_ref["id"];
            $antiguo_pedido_id=$pedido_id;
            foreach($detalle as $index=>$dt){
                $cantidad_a_pagar=$dt["cantidad_a_pagar"];
                $cantidad_en_mesa=$dt["cantidad_en_mesa"];
                $precio=$dt["precio"];
                $articulo_id=$dt["articulo_id"];
                $sugerencia="";
                if($cantidad_a_pagar!=0){
                    $pedido_detalle_guardado=$this->pedidosDetalles_model->insert($nuevo_pedido_id,$articulo_id,$precio,$cantidad_a_pagar,$sugerencia);
                }
               
                if($cantidad_en_mesa==0){
                    //eliminas detalle de pedido padre
                    $elimina=$this->pedidosDetalles_model->delete($pedido_id,$articulo_id);
                }else{
                    //actualiza detalle de pedido padre
                    $sugerencias="";
                    $actualizado=$this->pedidosDetalles_model->actualizar($pedido_id,$articulo_id,$precio,$cantidad_en_mesa,$sugerencias);
                }
            }

            $moneda_id=$this->input->post("moneda_id");
            $monto_global=$this->input->post("monto");
            $apertura_caja_id=$this->input->post("caja_aperturada");
            if(is_null($apertura_caja_id)){
                echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"Seleccione caja aperturada"));
                return;
            }

            $documento_tipo_id=$this->input->post("documento_tipo");
            $documento_numero=$this->input->post("documento_numero");
            $nombres=$this->input->post("nombres");
            $apellidos=$this->input->post("apellidos");
            $celular_postal=$this->input->post("postal");
            $celular_numero=$this->input->post("celular");
            $correo_electronico=$this->input->post("correo_electronico");
            $modalidad_pago_id=$this->input->post("modalidad_pago");

            $pedido_id=$nuevo_pedido_id;
            $referencia="tipo=entrada&pedido_id=".$pedido_id."&ref_pedido_id=".$antiguo_pedido_id."&mesa_id=".$mesa_id."&modalidad_pago=".APIS::getModalidadDePago($modalidad_pago_id)["denominacion"];
            $item_descripcion="V. directa ref:".$antiguo_pedido_id;
            $aperturaCajaItems=$this->aperturaCajaItems_model->insert($apertura_caja_id,$item_descripcion,$moneda_id,$monto_a_pagar,$referencia);
            
            if(!$aperturaCajaItems["success"]){
                echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"error al registrar en caja"));
                return;
            }

            $cliente_id=0;
            if($documento_numero!=""){
                $objPersonas=$this->personas_model->get(array("documento_tipo_id"=>$documento_tipo_id,"documento_numero"=>$documento_numero));
                if(count($objPersonas["data"])==0){
                //registramos a la persona en la base de datos
                $persona_registrada=$this->personas_model->insert($documento_tipo_id,$documento_numero,$nombres,$apellidos,$celular_numero,$celular_postal,$correo_electronico);
                if(!$persona_registrada["success"]){
                        echo json_encode(array("success"=>false,"msg_id"=>2,"msg"=>"error al registrar el cliente"));
                        return;
                }
                $cliente_id=$persona_registrada["id"];
                }else{
                    $cliente_id=$objPersonas["data"][0]["id"];
                }
            }
            

            $cerrado=1;
            $objPedidos=$this->pedidos_model->get(array("id"=>$pedido_id));
            if(!$objPedidos["success"]){
                echo json_encode(array("success"=>false,"msg_id"=>3,"msg"=>"error al listar el pedido"));
                return;
            }
            $pedido=$objPedidos["data"][0];
            $referencia_a_bd=$pedido["referencia_a_bd"];
            $pedido_actualizado=$this->pedidos_model->actualizar($pedido_id,$referencia_a_bd,$cliente_id,$cerrado);

            if(!$pedido_actualizado["success"]){
                echo json_encode(array("success"=>false,"msg_id"=>4,"msg"=>"error al actualizar el pedido"));
                return;
            }

            echo json_encode(array("success"=>true,"msg"=>"registro guardado correctamente","p_id"=>$pedido_id));
        }else{
            $moneda_id=$this->input->post("moneda_id");
            $monto_global=$this->input->post("monto");
            $apertura_caja_id=$this->input->post("caja_aperturada");
            if(is_null($apertura_caja_id)){
                echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"Seleccione caja aperturada"));
                return;
            }

            $documento_tipo_id=$this->input->post("documento_tipo");
            $documento_numero=$this->input->post("documento_numero");
            $nombres=$this->input->post("nombres");
            $apellidos=$this->input->post("apellidos");
            $celular_postal=$this->input->post("postal");
            $celular_numero=$this->input->post("celular");
            $correo_electronico=$this->input->post("correo_electronico");
            $modalidad_pago_id=$this->input->post("modalidad_pago");

            $referencia="tipo=entrada&pedido_id=".$pedido_id."&mesa_id=".$mesa_id."&modalidad_pago=".APIS::getModalidadDePago($modalidad_pago_id)["denominacion"];
            $item_descripcion="V. directa";
            $aperturaCajaItems=$this->aperturaCajaItems_model->insert($apertura_caja_id,$item_descripcion,$moneda_id,$monto_a_pagar,$referencia);
            
            if(!$aperturaCajaItems["success"]){
                echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"error al registrar en caja"));
                return;
            }

            $cliente_id=0;
            if($documento_numero!=""){
                $objPersonas=$this->personas_model->get(array("documento_tipo_id"=>$documento_tipo_id,"documento_numero"=>$documento_numero));
                if(count($objPersonas["data"])==0){
                //registramos a la persona en la base de datos
                $persona_registrada=$this->personas_model->insert($documento_tipo_id,$documento_numero,$nombres,$apellidos,$celular_numero,$celular_postal,$correo_electronico);
                if(!$persona_registrada["success"]){
                        echo json_encode(array("success"=>false,"msg_id"=>2,"msg"=>"error al registrar el cliente"));
                        return;
                }
                $cliente_id=$persona_registrada["id"];
                }else{
                    $cliente_id=$objPersonas["data"][0]["id"];
                    $actualizado=$this->personas_model->actualizar($cliente_id,$documento_tipo_id,$documento_numero,$nombres,$apellidos,$celular_numero,$celular_postal,$correo_electronico);
                    
                }
            }

            $cerrado=1;
            $objPedidos=$this->pedidos_model->get(array("id"=>$pedido_id));
            if(!$objPedidos["success"]){
                echo json_encode(array("success"=>false,"msg_id"=>3,"msg"=>"error al listar el pedido"));
                return;
            }
            $pedido=$objPedidos["data"][0];
            $referencia_a_bd=$pedido["referencia_a_bd"];
            $pedido_actualizado=$this->pedidos_model->actualizar($pedido_id,$referencia_a_bd,$cliente_id,$cerrado);

            if(!$pedido_actualizado["success"]){
                echo json_encode(array("success"=>false,"msg_id"=>4,"msg"=>"error al actualizar el pedido"));
                return;
            }
            echo json_encode(array("success"=>true,"msg"=>"registro guardado correctamente","p_id"=>$pedido_id));
        }
        
        
    }
    public function guardar_cerrar_pedido(){
        
        $pedido_id=$this->input->post("pedido_id");
        $mesa_id=$this->input->post("mesa_id");
        $moneda_id=$this->input->post("moneda_id");
        $monto=$this->input->post("monto");
        $apertura_caja_id=$this->input->post("caja_aperturada");
        $documento_tipo_id=$this->input->post("documento_tipo");
        $documento_numero=$this->input->post("documento_numero");
        $nombres=$this->input->post("nombres");
        $apellidos=$this->input->post("apellidos");
        $celular_postal=$this->input->post("postal");
        $celular_numero=$this->input->post("celular");
        $correo_electronico=$this->input->post("correo_electronico");
        $modalidad_pago_id=$this->input->post("modalidad_pago");

        if(is_null($apertura_caja_id)){
            echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"Seleccione caja aperturada"));
            return;
        }

        $referencia="tipo=entrada&pedido_id=".$pedido_id."&mesa_id=".$mesa_id."&modalidad_pago=".APIS::getModalidadDePago($modalidad_pago_id)["denominacion"];
        $item_descripcion="V. directa";
        $aperturaCajaItems=$this->aperturaCajaItems_model->insert($apertura_caja_id,$item_descripcion,$moneda_id,$monto,$referencia);
        
        if(!$aperturaCajaItems["success"]){
            echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"error al registrar en caja"));
            return;
        }

        $cliente_id=0;
        if($documento_numero!=""){
            $objPersonas=$this->personas_model->get(array("documento_tipo_id"=>$documento_tipo_id,"documento_numero"=>$documento_numero));
            if(count($objPersonas["data"])==0){
            //registramos a la persona en la base de datos
            $persona_registrada=$this->personas_model->insert($documento_tipo_id,$documento_numero,$nombres,$apellidos,$celular_numero,$celular_postal,$correo_electronico);
            if(!$persona_registrada["success"]){
                    echo json_encode(array("success"=>false,"msg_id"=>2,"msg"=>"error al registrar el cliente"));
                    return;
            }
            $cliente_id=$persona_registrada["id"];
            }else{
                $cliente_id=$objPersonas["data"][0]["id"];
                $actualizado=$this->personas_model->actualizar($cliente_id,$documento_tipo_id,$documento_numero,$nombres,$apellidos,$celular_numero,$celular_postal,$correo_electronico);
                
            }
        }
        

        $cerrado=1;
        $objPedidos=$this->pedidos_model->get(array("id"=>$pedido_id));
        if(!$objPedidos["success"]){
            echo json_encode(array("success"=>false,"msg_id"=>3,"msg"=>"error al listar el pedido"));
            return;
        }
        $pedido=$objPedidos["data"][0];
        $referencia_a_bd=$pedido["referencia_a_bd"];
        $pedido_actualizado=$this->pedidos_model->actualizar($pedido_id,$referencia_a_bd,$cliente_id,$cerrado);

        if(!$pedido_actualizado["success"]){
            echo json_encode(array("success"=>false,"msg_id"=>4,"msg"=>"error al actualizar el pedido"));
            return;
        }

        echo json_encode(array("success"=>true,"msg"=>"registro guardado correctamente"));
    }
    public function guardar_nuevo()
    {
        $mozo_id=$this->input->post('mozo');
        $pedido_id=$this->input->post('pedido_id');
        $detalles=$this->input->post('detalle');
        $mesa_denominacion=$this->input->post('mesa_denominacion');
        $mesa_id=$this->input->post('mesa_id');
        
        if($pedido_id==0){
            $cliente_id=0;
            $referencia_a_bd="venta=establecimiento&modo=presencial&mesa_id=".$mesa_id;
            $pedido_guardado=$this->pedidos_model->insert($referencia_a_bd,$mozo_id,$cliente_id);
            if(!$pedido_guardado["success"]){

            }
            $pedido_id=$pedido_guardado["id"];
            foreach($detalles as $index=>$dt){
                $cantidad=$dt["cantidad"];
                $precio=$dt["precio"];
                $articulo_id=$dt["articulo_id"];
                $sugerencia=$dt["descripcion"];
                $pedido_detalle_guardado=$this->pedidosDetalles_model->insert($pedido_id,$articulo_id,$precio,$cantidad,$sugerencia);
            }

            echo json_encode(array("success"=>true,"operacion"=>"inserted","msg"=>"registro guardado con exito","pedido_id"=>$pedido_id,"mesa_denominacion"=>$mesa_denominacion));

        }else{
            foreach($detalles as $index=>$dt){
                $cantidad=$dt["cantidad"];
                $precio=$dt["precio"];
                $articulo_id=$dt["articulo_id"];
                $sugerencia=$dt["descripcion"];
                $objPedidosDetalles=$this->pedidosDetalles_model->get(array("pedido_id"=>$pedido_id,"articulo_id"=>$articulo_id));
            
                if(count($objPedidosDetalles["data"])>0){
                    $pedido_detalle=$objPedidosDetalles["data"][0];
                    //if($pedido_detalle["cantidad"]!=$cantidad){
                        $actualizado=$this->pedidosDetalles_model->actualizar($pedido_id,$articulo_id,$precio,$cantidad,$sugerencia);
                    //}
                }else{
                    $pedido_detalle_guardado=$this->pedidosDetalles_model->insert($pedido_id,$articulo_id,$precio,$cantidad,$sugerencia);
                }
            }
            echo json_encode(array("success"=>true,"operacion"=>"updated","msg"=>"registro actualizado con exito","pedido_id"=>$pedido_id,"mesa_denominacion"=>$mesa_denominacion));
        }
        
    }
    public function eliminar_detalle(){
        $ref=$this->input->post('ref');
        $a_id=$this->input->post('a_id');
        $index=$this->input->post('index');
        $items=$this->input->post('items');
        if($ref!=0){
            if($items==1){
                $eliminado=$this->pedidos_model->delete($ref);
                echo json_encode(array("success"=>true,"action"=>"refresh"));
                return;
            }else{
                $eliminado=$this->pedidosDetalles_model->delete($ref,$a_id);
            }
            
        }
        echo json_encode(array("success"=>true,"index"=>$index));
    }
}
