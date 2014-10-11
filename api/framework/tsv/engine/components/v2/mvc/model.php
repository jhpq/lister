<?php
/*
 *
 *
 *
 *
 */
abstract class Model{


    /*
     *
     *
     * Properties Defaults
     *
     *
     */
    private $args            = null;
    private $db              = null;
    private $helper          = null;
    private $request_body    = null;
    private $post_data       = null;
    private $error           = null;
    private $query_strings   = null;
    private $config          = null;









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
     * Get/Set Errors
     *
     */
    public function setError($msg){
        $this->error = $msg;
    }
    public function getError(){
        return $this->error;
    }





    /*
     *
     * Get/Set Database Object
     *
     */
    public function getDB(){
        return $this->db;
    }
    public function setDB($value){
        $this->db = $value;
    }






    //
    public function authenticate($allowed_ids, $in_session, $err_code){

        // Validate arguments
        if ( is_array($allowed_ids) && count($allowed_ids) > 0 && is_string($in_session) && is_numeric($err_code) ){

            //init flag value to exit
            $flag_valid_user = false;

            // if there is no value exit
            if (isset($_SESSION['logged']) && isset($_SESSION[$in_session])){

                //
                foreach($allowed_ids as $v){

                    // if any of the roles matches current level session
                    if ($v===$_SESSION[$in_session]){
                        // User allowed Ok
                        $flag_valid_user = true;
                        // Break loop and continue
                        break;
                    }
                }
            }
            // do we have a valid user?
            if (!$flag_valid_user){
                http_response_code($err_code) and exit;
            }
            // def return true
            return true;
        }
        http_response_code($err_code) and exit;
    }





    /*
     *
     * Get/Set
     * Request Body for PUT/POST Requests
     *
     */
    public function setRequestBody($value){
        $this->request_body = $value;
    }
    public function getRequestBody(){
        return $this->request_body;
    }








    /*
     *
     * Get/Set
     * Parameter Values from Arguments in Restful API URI.
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
     * Utilities from helpers
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
     * Post Data from $_POST vars
     *
     */
    public function setPostData($value){
        $this->post_data = $value;
    }
    public function getPostData(){
        return $this->post_data;
    }




    /*
     *
     * Get/Set
     * Query Strings
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
     * Model Helpers
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
    public function is_method($method){
        if ( strtolower($_SERVER['REQUEST_METHOD']==strtolower($method)) ){
            return true;
        }
        return false;
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

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($type==='POST' && !is_null($data)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //$postdata = array(...);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

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



}