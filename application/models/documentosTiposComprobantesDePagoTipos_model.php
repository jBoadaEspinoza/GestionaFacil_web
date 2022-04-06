<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentosTiposComprobantesDePagoTipos_model extends CI_Model
{
    
    public function __construct()
    {
        parent::__construct();
    }

    public function get($filtros=null)
    {
        if(!is_null($filtros)){
            $this->db->distinct();
            $this->db->select('documentos_tipos.id as id,documentos_tipos.denominacion_corto as denominacion_corto,documentos_tipos.denominacion_largo_es as denominacion_largo_es,documentos_tipos.sunat_id as sunat_id');
            $this->db->from('documentos_tipos_comprobantes_de_pago_tipos');
            $this->db->join('documentos_tipos', 'documentos_tipos.id = documentos_tipos_comprobantes_de_pago_tipos.documento_tipo_id');
            $this->db->join('comprobantes_de_pago_tipos', 'comprobantes_de_pago_tipos.id = documentos_tipos_comprobantes_de_pago_tipos.comprobante_de_pago_tipo');
            if(isset($filtros["comprobante_de_pago_tipo_id"])){
                $this->db->where('comprobantes_de_pago_tipos.id=',$filtros["comprobante_de_pago_tipo_id"]);   
            }
            if(isset($filtros["num_filas"]) && isset($filtros["pagina"]) && isset($filtros["fila_inicial"])){
                $this->db->limit($filtros["num_filas"],$filtros["fila_inicial"]-1);
            }
            $query = $this->db->get(); 

            if($query->num_rows()>=0){
                $result=$query->result_array();
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
    
    

    
}