<?php
/*
 *
 *
 *
 *
 */
abstract class View{




    /*
     *
     *
     * Properties Defaults
     *
     *
     */
    private $xml_root_element   = 'root';
    private $model           = null;
    private $output_type     = null;
    private $args            = null;
    private $db              = null;
    private $helper          = null;
    private $request_body    = null;
    private $post_data       = null;
    public $allowed_ids      = array();
    public $in_session       = null;
    public $err_code         = null;

    /*
     *
     *
     */
    public function __construct($config_results){
        $this->results = $config_results; //array('success'=>false,'data'=>null,'report'=>null);
    }




    /*
     *
     *
     * Get/Set Model
     *
     *
     */
    public function setModel($model_instance){
        $this->model = $model_instance;
    }
    public function getModel(){
        return $this->model;
    }





    /*
     *
     * Get/Set
     * Post Data from $_POST vars
     *
     */

    public function setConfig($config){
        $this->config = $config;
    }
    public function getConfig(){
        return $this->config;
    }




    /*
     *
     * Internal Call to API
     *
     */
    public function Call($api_path, $type='GET', $data=null){

        // get config data
        $config = $this->getConfig();

        // Build Api Url
        $api_root = '/api';
        $api_version = 'v2';
        $api_host = 'http://localhost';
        $url = $api_host.$api_root.'/'.$api_version.$api_path;

        // Get api from config, in further versiones generate it automatically
        if (isset($config->api_url)){
            $url = $config->api_url.$api_path;
        }

        // Debug api
        //echo $url; exit();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($type==='POST' && !is_null($data)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //$postdata = array(...);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

        //
        if(!curl_errno($ch))
        {
            $info = curl_getinfo($ch);
            if ($info['http_code']=='200'){
                //
                $r = json_decode($output, true);
                //
                if (!is_null($r)){
                    curl_close($ch);
                    return $r;
                }
            }
        }

        return false;
    }






    //
    public function authenticate($allowed_ids, $in_session, $err_code){
        if ( is_array($allowed_ids) && count($allowed_ids) > 0 && is_string($in_session) && is_numeric($err_code) ){
            $this->allowed_ids = $allowed_ids;
            $this->in_session = $in_session;
            $this->err_code = $err_code;
            return true;
        }
        return false;
    }





    public function usePDFEngine($engine, $param1, $param2, $param3){

        //
        if ( $engine==='code128' ){

            //
            require_once(PATH_LIBRARIES.'/pdf/code128.php');
            //$pdf = new PDF_Code128('P','mm','letter');
            $pdf = new PDF_Code128($param1, $param2, $param3);
            return $pdf;
        }


        //
        elseif ( $engine==='anaderguans' ){
        }


        return false;
    }





    /*
     *
     * Get/Set
     * OutputType: html, xml & default which is json
     *
     */
    public function setType($type){
        $this->output_type = $type;
    }
    public function getType(){
        return $this->output_type;
    }



    /*
     *
     * Get/Set
     * if XML set root element to proper one
     *
     */
    public function setXMLRootElement($xml_root_element){
        $this->xml_root_element = $xml_root_element;
    }
    public function getRootElement(){
        return $this->xml_root_element;
    }








    /*
     *
     * Get/Set
     * Query String values
     *
     */
    public function setQueryStrings($value){
        $this->query_strings = $value;
    }
    private function getQueryStrings(){
        return $this->query_strings;
    }








    /*
     *
     * Get/Set
     * Param Values
     *
     */
    public function setParamValues($value){
        $this->args = $value;
    }
    private function getParamValues(){
        return $this->args;
    }








    /*
     *
     * Get/Set
     * Database Object
     *
     */
    public function setDB($value){
        $this->db = $value;
    }
    public function getDB(){
        return $this->db;
    }






    /*
     *
     * Get/Set
     * Utilities
     *
     */
    public function setHelper($value){
        $this->helper = $value;
    }
    public function getHelper(){
        return $this->helper;
    }








    /*
     *
     * Get/Set
     * The Request body
     *
     */
    public function setRequestBody($value){
        $this->request_body = $value;
    }
    function getRequestBody(){
        return $this->request_body;
    }









    /*
     *
     * Get/Set
     * The post Data $_POST
     *
     */
    public function setPostData($value){
        $this->post_data = $value;
    }
    function getPostData(){
        return $this->post_data;
    }










    /*
    *
    * View Helpers
    * get segment values and get params from segment values
    *
    */
    public function getSegmentValue($segment){
        //
        if (is_numeric($segment)){
            $params     = $this->getParamValues();
            //
            if ( isset($params[$segment]) ){
                return $params[$segment];
            }
        }
        return false;
    }
    public function getSegmentParamValue($segment, $paramName){
        $queries    = $this->getQueryStrings();
        if ( isset($queries[$segment]) && isset($queries[$segment][$paramName]) ){
            return $queries[$segment][$paramName];
        }
        return false;
    }
    public function formatError($err_instance){
        if (is_object($err_instance)){
            if (method_exists($err_instance, '__toString') ){
                return $err_instance->__toString();
            }
        }
        return $err_instance;
    }
    public function is_method($method){
        if ( strtolower($_SERVER['REQUEST_METHOD']==strtolower($method)) ){
            return true;
        }
        return false;
    }











}