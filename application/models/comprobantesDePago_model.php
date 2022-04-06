<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComprobantesDePago_model extends CI_Model
{
    private $path="https://back.apisunat.com/";
    public function __construct()
    {
        parent::__construct();
    }

    public function get($filtros=null)
    {
        if(!is_null($filtros)){
            $this->db->distinct();
            $this->db->select('comprobantes_de_pago.id as id,comprobantes_de_pago.pedido_id as pedido_id,comprobantes_de_pago.documentId as documentId,comprobantes_de_pago_series.id as serie_id,comprobantes_de_pago_series.numeracion as serie_numeracion,comprobantes_de_pago_series.comprobante_de_pago_numero_inicio as comprobante_numero_inicio,comprobantes_de_pago_tipos.id as tipo_id,comprobantes_de_pago_tipos.denominacion as tipo_denominacion,comprobantes_de_pago.numero as numero,comprobantes_de_pago_tipos.activo as tipo_activo,comprobantes_de_pago.fecha_emision as fecha_emision');
            $this->db->from('comprobantes_de_pago');
            $this->db->join('comprobantes_de_pago_series', 'comprobantes_de_pago_series.id = comprobantes_de_pago.serie_id');
            $this->db->join('comprobantes_de_pago_tipos', 'comprobantes_de_pago_tipos.id = comprobantes_de_pago_series.comprobante_de_pago_tipo_id');
            if(isset($filtros["id"])){
                $this->db->where('comprobantes_de_pago.id=',$filtros["id"]);   
            }
            if(isset($filtros["pedido_id"])){
                $this->db->where('comprobantes_de_pago.pedido_id=',$filtros["pedido_id"]);   
            }
            if(isset($filtros["serie_id"])){
                $this->db->where('comprobantes_de_pago_series.id=',$filtros["serie_id"]);   
            }
            if(isset($filtros["num_filas"]) && isset($filtros["pagina"]) && isset($filtros["fila_inicial"])){
                $this->db->limit($filtros["num_filas"],$filtros["fila_inicial"]-1);
            }
            $query = $this->db->get(); 

            if($query->num_rows()>=0){
                $result=$query->result_array();
                foreach($result as $index=>$d){
                    $comprobante=self::getById($d["documentId"]);
                    $result[$index]["estado"]=$comprobante["status"];
                    $result[$index]["fileName"]=$comprobante["fileName"];
                }
                return array(
                    "success"=>true,
                    "data"=>$result
                );
            } 
            return array(
                "success"=>false,
                "msg"=>"registros no encontrados",
            ); 
           
        }
       
    }
    public function insert($pedido_id,$documentId,$serie_id,$numero){
        
        $this->db->set('pedido_id', $pedido_id);
        $this->db->set('documentId', $documentId);
        $this->db->set('serie_id', $serie_id);
        $this->db->set('numero', $numero);
      
        $this->db->set('fecha_emision',DATE::getNowAccordingUTC());
        $this->db->insert('comprobantes_de_pago');
        
        $item_id=$this->db->insert_id();

        if($this->db->trans_status()){
            $this->db->trans_commit();
            return array("success"=>true,"msg"=>"Registro guardado con exito","id"=>$item_id);
         
        }
        $this->db->trans_rollback();
        return array("success"=>false,"msg"=>"Error al guardar el registro");

    }
    public function getById($documentId){
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->path.'documents/'.$documentId.'/getById'); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        $data = curl_exec($ch); 
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
       
        if($info==200){
            $data=json_decode($data,true); 
            $data["success"]=true;
            return $data;
        }

        return array("success"=>false,"msg"=>"ingrese un numero de documento valido");
    }
    public function send($personaId,$personaToken,$fileName,$documentBody,$customerEmail=""){
        
            //API URL
            $url = $this->path.'personas/v1/sendBill';

            //create a new cURL resource
            $ch = curl_init($url);

            //setup request to send json via POST
            $data = array(
                'personaId' => $personaId,
                'personaToken' => $personaToken,
                'fileName' =>$fileName,
                'documentBody'=>$documentBody,
                'customerEmail'=>$customerEmail
            );
            $payload = json_encode($data);

            //attach encoded JSON string to the POST fields
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            //set the content type to application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute the POST request
            $result = curl_exec($ch);

            $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        
            if($info==200){
                $result=json_decode($result,true); 
                $result["success"]=true;
                return $result;
            }
            return array("success"=>false,"error"=>$result);

    }

    
}