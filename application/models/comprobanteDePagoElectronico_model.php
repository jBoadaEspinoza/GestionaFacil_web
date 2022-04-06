<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComprobanteDePagoElectronico_model extends CI_Model
{
    private $path="https://back.apisunat.com/";
    public function __construct()
    {
        parent::__construct();
    }

    public function get($filtros=null)
    {
        
       
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