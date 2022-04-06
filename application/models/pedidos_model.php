<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos_model extends CI_Model
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('personas_model');
        $this->load->model('pedidosDetalles_model');
        $this->load->model('aperturaCajaItems_model');
        $this->load->model('aperturaCaja_model');
        $this->load->model('mesas_model');
        $this->load->model('comprobantesDePago_model');
    }

    
    public function get($filtros=null)
    {
        if(!is_null($filtros)){

            $this->db->distinct();
            $this->db->select('pedidos.id,pedidos.fecha_hora_emision,pedidos.fecha_hora_cierre as pedido_fecha_hora_cierre,pedidos.mozo_id,pedidos.cliente_id,pedidos.entrega_lng,pedidos.entrega_lat,pedidos.entrega_referencia,pedidos.hora_preparacion_inicio,pedidos.forma_de_pago_id,pedidos.forma_de_pago_monto_a_entregar,pedidos.importe_productos_pen,pedidos.importe_delivery_pen,pedidos.cerrado,pedidos.referencia_a_bd');
            $this->db->from('pedidos');
            $this->db->join('pedidos_detalles','pedidos_detalles.pedido_id=pedidos.id');
            $this->db->join('articulos','pedidos_detalles.articulo_id=articulos.id');
            $this->db->join('productos', 'productos.id = articulos.producto_id');
            $this->db->join('presentaciones', 'presentaciones.id = articulos.presentacion_id');
            $this->db->join('preposiciones', 'preposiciones.id = articulos.preposicion_id');
            $this->db->join('categorias','categorias.id = articulos.categoria_id');
            

            if(isset($filtros["id"])){
                $this->db->where('pedidos.id=',$filtros["id"]);   
            }
            if(isset($filtros["cerrado"])){
                $this->db->where('pedidos.cerrado=',$filtros["cerrado"]);
            }
            if(isset($filtros["establecimiento_id"])){
                $this->db->where('categorias.establecimiento_id=',$filtros["establecimiento_id"]);
                $this->db->where('productos.establecimiento_id=',$filtros["establecimiento_id"]);
            }
            if(!isset($filtros["modalidades"])){
                if(isset($filtros["num_filas"]) && isset($filtros["pagina"]) && isset($filtros["fila_inicial"])){
                    $this->db->limit($filtros["num_filas"],$filtros["fila_inicial"]-1);
                }
            }
           
            if(isset($filtros["ordenado_por"]) && isset($filtros["en_orden"])){
                $this->db->order_by($filtros["ordenado_por"], $filtros["en_orden"]);
            }   
            if(isset($filtros["desde"]) && isset($filtros["hasta"])){
                $this->db->where('DATE_FORMAT(pedidos.fecha_hora_cierre,"%Y-%m-%d") BETWEEN "'.$filtros["desde"] .'" AND "'.$filtros["hasta"].'"');
            }         
            $query = $this->db->get(); 

            if($query->num_rows()>=0){
                $result=$query->result_array();
                $new_result=[];
                foreach($result as $index=>$r){
                    $es_aceptado=false;
                    if($r["cliente_id"]==0){
                        $result[$index]["cliente_full_name"]="-";
                    }else{
                        $objPersona=$this->personas_model->get(array("id"=>$r["cliente_id"]));
                        $persona=$objPersona["data"][0];
                        if($persona["documento_tipo_id"]==2){
                            $result[$index]["cliente_full_name"]=$persona["razon_social"];
                        }else{
                            $result[$index]["cliente_full_name"]=$persona["apellidos"]." ".$persona["nombres"];
                        }
                        
                    }

                    $objPedidosDetalles=$this->pedidosDetalles_model->get(array("pedido_id"=>$r["id"]));
                    $total=0;
                    foreach($objPedidosDetalles["data"] as $index2=>$pd){
                        $total+=$pd["cantidad"]*$pd["precio_unitario_pen"];
                    }
                    $result[$index]["total"]=$total;

                    $q="pedido_id=".$r["id"]."&";
                    $modalidad_pago="";
                    $lote_id=0;
                    
                    $result[$index]["lote"]="";
                    
                    if($r["cerrado"]==1){
                        $objAperturaCajaItems=$this->aperturaCajaItems_model->get(array("referencia"=>$q));
                        if(count($objAperturaCajaItems["data"])>0){
                            $aperturaCajaItems=$objAperturaCajaItems["data"];
                            foreach($aperturaCajaItems as $index3 => $a){
                                $referencia_array = array();
                                parse_str($a["referencia"], $referencia_array);
                                foreach($referencia_array as $key=>$val){
                                    if($key=="pedido_id" && $val==$r["id"]){
                                        $lote_id=$a["apertura_caja_id"];
                                        
                                        $mp=$referencia_array["modalidad_pago"];
                                        break;
                                    }
                                }
                            }
                            $objAperturaCaja=$this->aperturaCaja_model->get(array("id"=>$lote_id));
                            $result[$index]["lote"]=$objAperturaCaja["data"][0];             
                            $modalidad_pago='<span class="badge badge-light" style="color:white;background-color:'.APIS::getModalidadDePagoPorDenominacion($mp)["color"].'">'.strtoupper($mp).'</span>'; 
                            if(isset($filtros["modalidades"])){
                                if($filtros["modalidades"]!=""){
                                    $array_mod=explode("_",$filtros["modalidades"]);
                                    for($i=0;$i<count($array_mod);$i++){
                                        if($array_mod[$i]==APIS::getModalidadDePagoPorDenominacion($mp)["id"]){
                                            $es_aceptado=true;
                                        }
                                    }
                                }else{
                                    $es_aceptado=true;
                                }
                            }else{
                                $es_aceptado=true;
                            }
                        }
                    }
                    $mesa_id="";
                    $referencia_array = array();
                    parse_str($result[$index]["referencia_a_bd"], $referencia_array);
                    foreach($referencia_array as $key=>$val){
                        if($key=="mesa_id"){
                            $mesa_id=$val;
                            break;
                        }
                    }
                    $objMesa=$this->mesas_model->get(array("id"=>$mesa_id));
                    $result[$index]["mesa"]=$objMesa["data"][0];
                    $objComprobanteElectronico=$this->comprobantesDePago_model->get(array("pedido_id"=>$r["id"]));
                    $comprobanteElectronico=$objComprobanteElectronico["data"];
                    $result[$index]["comprobante_electronico"]=$comprobanteElectronico;
                    $result[$index]["modalidad_pago"]=$modalidad_pago;
                    if(isset($filtros["modalidades"]) && $es_aceptado){
                        array_push($new_result,$result[$index]);
                    }
                }
                if(isset($filtros["modalidades"])){
                    $new_result2=[];
                    if(isset($filtros["num_filas"]) && isset($filtros["pagina"]) && isset($filtros["fila_inicial"])){
                        for($i=0;$i<$filtros["num_filas"];$i++){
                            if(isset($new_result[$i+($filtros["num_filas"]*($filtros["pagina"]-1))])){
                                array_push($new_result2,$new_result[$i+($filtros["num_filas"]*($filtros["pagina"]-1))]);
                            }else{
                                break;
                            }
                            
                        }
                        $new_result=$new_result2;
                    }
                }
                if(isset($filtros["modalidades"])){
                    return array(
                        "success"=>true,
                        "data"=>$new_result
                    );
                }else{
                    return array(
                        "success"=>true,
                        "data"=>$result
                    );
                }
                
            } 
            return array(
                "success"=>false,
                "msg"=>"articulos no encontradas",
            ); 
           
        }
        
        return array(
            "success"=>false,
            "msg"=>"Usuario no encontrado",
        ); 

    }
    public function insert($referencia_a_bd,$mozo_id=0,$cliente_id=0){
        $this->db->set('mozo_id',$mozo_id);
        $this->db->set('fecha_hora_emision', DATE::getNowAccordingUTC());
        $this->db->set('fecha_hora_cierre', DATE::getNowAccordingUTC());
        $this->db->set('cliente_id', $cliente_id);
        $this->db->set('referencia_a_bd', $referencia_a_bd);

        $this->db->insert('pedidos');
        
        $item_id=$this->db->insert_id();

        if($this->db->trans_status()){
            $this->db->trans_commit();
            return array("success"=>true,"msg"=>"Registro guardado con exito","id"=>$item_id);
         
        }
        $this->db->trans_rollback();
        return array("success"=>false,"msg"=>"Error al guardar el registro");

    }   
    public function actualizar($id,$referencia_a_bd,$cliente_id=0,$cerrado=0){
        
        $this->db->set('cliente_id', $cliente_id);
        $this->db->set('referencia_a_bd', $referencia_a_bd);
        $this->db->set('fecha_hora_cierre', DATE::getNowAccordingUTC());
        $this->db->set('cerrado', $cerrado);
        $this->db->where('id=',$id);
        $this->db->update('pedidos');

        return array("success"=>true,"msg"=>"registro actualizado con exito");       
    }
    public function actualizar_referencia($id,$referencia_a_bd){
    
        $this->db->set('referencia_a_bd', $referencia_a_bd);
        $this->db->where('id=',$id);
        $this->db->update('pedidos');
        
        return array("success"=>true,"msg"=>"registro actualizado con exito");      
    }
    public function delete($pedido_id){
        
        $this->db->select('*');
        $this->db->from('pedidos');
        $this->db->where('id=',$pedido_id);
    

        $query = $this->db->get(); 
        if($query->num_rows()>0){
            
            $this->db->where('id=',$pedido_id);
            $this->db->delete('pedidos');

            return array(
                "success"=>false,
                "msg"=>"registro eliminado con exito"
            );
        } 
        return array(
            "success"=>false,
            "msg"=>"registro no encontrado",
        ); 
       
    }
}