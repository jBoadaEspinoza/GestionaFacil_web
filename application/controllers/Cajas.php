<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cajas extends CI_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->library('session');
		$this->load->model('cajas_model');
        $this->load->model('usuarios_model');
        $this->load->model('aperturaCaja_model');
        $this->load->model('aperturaCajaItems_model');
    }
    public function cierre_ticket(){
        $user=$_SESSION["user"];
        $lote=$this->input->get('lote');
        
		$width=60;
		$height=300;
		$margin_lat=2;
		$margin_top=5;
		$salto_de_linea=5;
        $salto=5;
		$npage=1;
		$w=$width-2*$margin_lat;
        $fontDefault='Arial';
        $fontSizeDefaul=8;
		$pdf=PRINTER::load('P','mm',array($width,$height));
		$pdf->SetMargins($margin_lat,$margin_lat,$margin_lat);
        
		$pdf->AddPage();
		$pdf->AliasNbPages();
        
		//cabecera
        $pdf->SetFont($fontDefault,'B',$fontSizeDefaul+9);
		$pdf->setXY($margin_lat,$margin_top);
        $pdf->setTextColor(255,255,255);
		$pdf->MultiCell($w,3*$salto_de_linea,strtoupper('cierre de lote'),0,'C',true);
        $pdf->setTextColor(0,0,0);
        
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul);
        $pdf->setXY($margin_lat,$pdf->getY());
		$pdf->MultiCell($w,$salto_de_linea,'ID: '.md5($user["business_id"]),0,'C',false);
        

        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+1);
        $pdf->setXY($margin_lat,$pdf->getY());
		$pdf->MultiCell($w,$salto_de_linea,strtoupper($user["business_name"].'('.$user["business_ruc"].')'),0,'C',false);
        
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul);
        $pdf->setXY($margin_lat,$pdf->getY());
		$pdf->MultiCell($w,$salto_de_linea,strtoupper( $user["business_address"]),0,'C',false);

        $objAperturaCaja=$this->aperturaCaja_model->get(array("id"=>$lote));
        $aperturaCaja=$objAperturaCaja["data"][0];
        
        //LOTE
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+1);
        $cabecera=array(
			array("denominacion"=>"LOTE","align"=>"L","ancho"=>25),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($lote),"align"=>"L","ancho"=>70)
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

        //FECHA DE CIERRE
        $fecha_cierre=DATE::convertUTCToDateTimeZone($aperturaCaja["fecha_cierre"]);
        $pdf->SetFont($fontDefault,'',$fontSizeDefaul+1);
        $cabecera=array(
			array("denominacion"=>"FECHA","align"=>"L","ancho"=>25),
            array("denominacion"=>":","align"=>"L","ancho"=>5),
			array("denominacion"=>strtoupper($fecha_cierre->format('Y-m-d H:i a')),"align"=>"L","ancho"=>70)
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
        //RESUME ENTRADA CABECERA
        $posX=$pdf->getX();
        $posY=$pdf->getY()+$salto;
        $pdf->setXY($posX,$posY);
        $pdf->SetFont($fontDefault,'B',$fontSizeDefaul+2);
        $pdf->setTextColor(255,255,255);
		$pdf->MultiCell($w,2*$salto_de_linea,strtoupper('RESUMEN - ENTRADAS'),0,'C',true);
        $pdf->setTextColor(0,0,0);
        
        //CABECERA DETALLE ITEMS
        $pdf->SetFont($fontDefault,'b',$fontSizeDefaul-1);
        $cabecera_details=array(
			array("denominacion"=>"REF","align"=>"C","fontWeight"=>"b","ancho"=>20),
			array("denominacion"=>"DETALLE","align"=>"C","fontWeight"=>"","ancho"=>26),
			array("denominacion"=>"TIPO","align"=>"C","fontWeight"=>"b","ancho"=>27),
            array("denominacion"=>"MONTO S/.","align"=>"R","fontWeight"=>"","ancho"=>27)
		);
        
        $posX=$pdf->getX();
        $posY=$pdf->getY();
        foreach($cabecera_details as $index=>$item){
            if($index==0){
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*$item["ancho"]/100,$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }else{
                $posX=$posX+($w*$cabecera_details[$index-1]["ancho"]/100);
                $pdf->setXY($posX,$posY);
                $pdf->MultiCell($w*($cabecera_details[$index]["ancho"]/100),$salto_de_linea,utf8_decode($item["denominacion"]),0,$item["align"],false);
            }
        }

        $objAperturaCajaItems=$this->aperturaCajaItems_model->get(array("apertura_caja_id"=>$lote,"ordenado_por"=>"fecha_de_registro","en_orden"=>"ASC"));
        $items=$objAperturaCajaItems["data"];
        
        $posX=$pdf->getX();
        $posY=$pdf->getY();
        $total_entrada=0;

        $modalidadesDePagoArray= APIS::getModalidadesDePago();
        //asigna un nuevo atributo total a todos
        foreach($modalidadesDePagoArray as $index => $item){
            $modalidadesDePagoArray[$index]["total"]=0;
        }

        foreach($items as $index =>$item){
            $ref="#";
            $modalidad_pago="Efectivo";
            if($item["item_descripcion"]=='M. inicial'){
                foreach($modalidadesDePagoArray as $indexM =>$item_mod){
                    if("efectivo"==$modalidadesDePagoArray[$indexM]["denominacion"]){
                       if(isset($modalidadesDePagoArray[$indexM]["total"])){
                           $modalidadesDePagoArray[$indexM]["total"]+=$item["monto"];
                       }else{
                           $modalidadesDePagoArray[$indexM]["total"]=$item["monto"];
                       }
                       
                    }
                }
            }

            $referencia_array = array();
            parse_str($item["referencia"], $referencia_array);
            foreach($referencia_array as $key=>$val){
                 if($key=="pedido_id"){
                     $modalidad_pago=$referencia_array["modalidad_pago"];
                     foreach($modalidadesDePagoArray as $indexM =>$item_mod){
                         if($modalidad_pago==$modalidadesDePagoArray[$indexM]["denominacion"]){
                            if(isset($modalidadesDePagoArray[$indexM]["total"])){
                                $modalidadesDePagoArray[$indexM]["total"]+=$item["monto"];
                            }else{
                                $modalidadesDePagoArray[$indexM]["total"]=$item["monto"];
                            }
                            
                         }
                     }
                     $ref=$referencia_array["pedido_id"];
                     break;
                 }
                 if($referencia_array["tipo"]=="entrada"){
                     $total_entrada+=$item["monto"];
                 }
             }
            
            $posX=$margin_lat;
            
            $pdf->setXY($posX,$posY);
            $pdf->SetFont($fontDefault,$cabecera_details[0]["fontWeight"],$fontSizeDefaul);
            $pdf->MultiCell($w*$cabecera_details[0]["ancho"]/100,$salto_de_linea,utf8_decode($ref),0,'C',false);
            $posX=$posX+($w*$cabecera_details[0]["ancho"]/100);
            $pdf->setXY($posX,$posY);

            $w_mm=$w*$cabecera_details[1]["ancho"]/100;
            $q=intval(($pdf->GetStringWidth($item["item_descripcion"]))/$w_mm);
            $pdf->SetFont($fontDefault,$cabecera_details[1]["fontWeight"],$fontSizeDefaul);
            $pdf->MultiCell($w_mm,$salto_de_linea,utf8_decode($item["item_descripcion"]),0,'L',false);
            
            $posX=$posX+($w*$cabecera_details[1]["ancho"]/100);
            $pdf->setXY($posX,$posY);
            $pdf->SetFont($fontDefault,$cabecera_details[2]["fontWeight"],$fontSizeDefaul);
            $pdf->MultiCell($w*$cabecera_details[2]["ancho"]/100,$salto_de_linea,ucfirst(utf8_decode($modalidad_pago)),0,'C',false);
            
            $posX=$posX+($w*$cabecera_details[2]["ancho"]/100);
            $pdf->setXY($posX,$posY);
            $monto=number_format($item["monto"], 2, '.', '');
            $pdf->SetFont($fontDefault,$cabecera_details[3]["fontWeight"],$fontSizeDefaul);
            $pdf->MultiCell($w*$cabecera_details[3]["ancho"]/100,$salto_de_linea,ucfirst(utf8_decode($monto)),0,'R',false);

            $posX=$margin_lat;
            $posY=$pdf->getY()+$q*$salto;
            $pdf->setXY($posX,$posY);
        }
        
        //TOTAL ENTRADAS
        $pdf->SetFont($fontDefault,'B',$fontSizeDefaul+2);
        $total_entrada=number_format($total_entrada, 2, '.', '');
        $cabecera_total=array(
			array("denominacion"=>"TOTAL: ","align"=>"R","ancho"=>60),
			array("denominacion"=>"S/. ".$total_entrada,"align"=>"R","ancho"=>40)
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

        //RESUMEN TOTALES
        $posX=$pdf->getX();
        $posY=$pdf->getY()+$salto;
        $pdf->setXY($posX,$posY);
        $pdf->SetFont($fontDefault,'B',$fontSizeDefaul+2);
        $pdf->setTextColor(255,255,255);
		$pdf->MultiCell($w,2*$salto_de_linea,strtoupper('RESUMEN - TOTALES'),0,'C',true);
        $pdf->setTextColor(0,0,0);
        
        foreach($modalidadesDePagoArray as $index => $item){
            //TOTAL ENTRADAS
             if($item["total"]!=0){
                $pdf->SetFont($fontDefault,'B',$fontSizeDefaul);
                $total_entrada=number_format($item["total"], 2, '.', '');
                $cabecera_total=array(
                    array("denominacion"=>"TOTAL ".strtoupper($item["denominacion"]).": ","align"=>"R","ancho"=>60),
                    array("denominacion"=>"S/. ".$total_entrada,"align"=>"R","ancho"=>40)
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
                }
            }
        }

        $pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->Output();
    }
    public function cierre_caja(){
        $caja_id=$this->input->post("caja_id");
        $objAperturaCaja=$this->aperturaCaja_model->get(array("caja_id"=>$caja_id,"estado"=>"abierta"));
        $aperturaCaja=$objAperturaCaja["data"][0];
        if($aperturaCaja["fecha_cierre"]=="0000-00-00 00:00:00"){
            $cierre=$this->aperturaCaja_model->cierre($aperturaCaja["id"]);
            echo json_encode(array("success"=>$cierre["success"],"msg"=>$cierre["msg"],"lote"=>$aperturaCaja["id"]));
            return;
        }
        echo json_encode(array("success"=>false,"msg"=>"la caja ya fue cerrada"));
    }
	public function apertura_caja(){
        $caja_id=$this->input->post('caja_id');
        $user=$_SESSION["user"];
        $title="Apertura de caja";
        $rol_id=1; //Cajero
        $objUsuarios=$this->usuarios_model->get(array("establecimiento_id"=>$user["business_id"],"rol_id"=>$rol_id));
        $cajeros=$objUsuarios["data"];
        $dc_templates_extranet_cajas_apertura["caja"]=array("id"=>$caja_id);
        $dc_templates_extranet_cajas_apertura["user"]=$user;
        $dc_templates_extranet_cajas_apertura["cajeros"]=$cajeros;
        $template=$this->load->view('templates/extranet/cajas/apertura',$dc_templates_extranet_cajas_apertura,true);;
        echo json_encode(array("title"=>$title,"template"=>$template));
    }
	public function abrir_nuevo()
	{
        $user=$_SESSION["user"];
        $title="Nueva caja";
        $dc_templates_extranet_cajas_nuevo["user"]=$user;
        $template=$this->load->view('templates/extranet/cajas/nuevo',$dc_templates_extranet_cajas_nuevo,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
	}
    public function abrir_ver(){
        $user=$_SESSION["user"];
        $caja_id=$this->input->post('id');
        
        $filtros=array("id"=>$caja_id);
        $objCaja=$this->cajas_model->get($filtros);
        
        if(!$objCaja["success"]){
            echo json_encode(array("success"=>false,"msg"=>"error"));
            return;
        }
        
        $caja=$objCaja["data"][0];
       
        $title="Ver categoria - ".md5($caja["id"]);
        $dc_templates_extranet_cajas_ver["user"]=$user;
        $dc_templates_extranet_cajas_ver["caja"]=$caja;
        $template=$this->load->view('templates/extranet/cajas/ver',$dc_templates_extranet_cajas_ver,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
    }
    
    public function guardar_nuevo()
    {
        $denominacion=$this->input->post('denominacion');
        $establecimiento_id=$this->input->post('establecimiento_id');

        $objCajas=$this->cajas_model->insert(
            $denominacion,
            $establecimiento_id
        );

         echo json_encode($objCajas);
    }
    public function guardar_apertura_caja(){

        $user=$_SESSION["user"];
        $caja_id=$this->input->post('caja_id');
        $cajero_id=$this->input->post('cajero');
        $monto_inicial=$this->input->post('monto_inicial');

        if(!is_numeric($monto_inicial)){
            echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"Ingrese un monto válido"));
            return;
        }
        
        $objAperturaCaja=$this->aperturaCaja_model->insert($caja_id,$cajero_id);
        if(!$objAperturaCaja["success"]){
            echo json_encode(array("success"=>false,"msg_id"=>2,"msg"=>"Error al aperturar la caja"));
            return;
        }
        
        $apertura_caja_id=$objAperturaCaja["id"];
        $item_descripcion='M. inicial';
        $moneda_id='PEN';
        $monto=$monto_inicial;
        $referencia='tipo=entrada';
        $objAperturaCajaItems=$this->aperturaCajaItems_model->insert($apertura_caja_id,$item_descripcion,$moneda_id,$monto,$referencia);

        if(!$objAperturaCajaItems["success"]){
            echo json_encode(array("success"=>false,"msg_id"=>2,"msg"=>"Error al aperturar la caja"));
            return;
        }

        echo json_encode(array("success"=>true,"msg"=>"caja aperturada con exito"));
    }
    
}
