<?php
/* */
session_name('compras');
session_start();
/* */
require_once(PATH_FRAMEWORK.'/factory.php');

/*
 *
 * Rock Application Main Framework Class
 *
 *
 * */
class RApp extends RestServer{



    /*
     * Results placeholder
     */
    private $results    = null;


    /*
     * error-prone array variable
     */
    private $errors     = array();


    /*
     * Default allowed request methods
     */
    private $default_allowed_request_methods = array('get', 'post', 'put', 'patch', 'delete', 'head', 'options');


    /*
     * Default allowed output type
     */
    private $default_output_type = 'json';


    /*
     * Allowed domains for XHR calls. Default is allow from all domains
     */
    public $default_access_control_allow_origin = array('*');


    /*
     * Get debug data for Api information
     */
    public $debug_data  = null;




    /*
     * Initialize Factory
     */
    function __construct(){
        //
        $this->resource = null;
        //
        $factory                = RFactory::getInstance();
        $this->database         = $factory->getDatabase();
        $this->config           = $factory->getConfig();
        $this->helper           = $factory->getHelper( $this->database ); // send a db instance to helper for further use

        // Set default allowed request methods
        $this->allowed_request_methods = ( isset($this->config->allowed_request_methods) && count($this->config->allowed_request_methods)>0 ) ? $this->config->allowed_request_methods : $this->default_allowed_request_methods;
        $this->allowed_request_methods = array_map('strtolower', $this->allowed_request_methods);

        // Set default output type
        if (isset($this->config->default_output_type)){
            $this->default_output_type = $this->config->default_output_type;
        }

        // pass to options array
        $this->options = array(
            'helper' => $this->helper,
            'config' => $this->config,
            'database' => $this->database,
            'server' => array(
                'allowed_request_methods' => $this->allowed_request_methods,
                'request_uri' => $this->getRequestURI(),
                'request_method' => $this->getRequestMethod(),
                'post_data' => $this->getPostData(),
                'body_data' => $this->getRequestBody()
            )
        );
    }









    /*
     *
     * Route Main application
     * Engine: V2.0, type MVC.
     *
     */
    public function route(){

        // Route according to Engine Component Router Version
        require_once(PATH_FRAMEWORK.'/engine/v2.php');
        $ecrv1 = new RockEngineComponentRouterV2($this->options);
        // route engine component
        $ecrv1->routeMVCType();
        // get elements
        $this->root_element     = $ecrv1->root_element;
        $this->results          = $ecrv1->results;
        $this->errors           = $ecrv1->errors;
        $this->debug_data       = $ecrv1->debug_data;

        // get output type
        if ( !is_null($ecrv1->output_type) ){
            $this->default_output_type = $ecrv1->output_type;
        }
        return $this;
    }












    /*
     *
     *
     *
     */
    public function display($debug = null){
        //
        $config = $this->config;
        $utils  = $this->helper->getTools('utilities');


        // Print Api information
        if ( isset($this->config->debug_api_resources) && $this->config->debug_api_resources === true ){
            echo $this->debug_data;
        }

        // Verify if request method is allowed
        if ( !in_array( $this->getRequestMethod(), $this->allowed_request_methods ) ){
            array_push($this->errors, 'Error, request method "' . strtoupper($this->getRequestMethod()) . '" not allowed');
        }

        // Debug resources/results
        if ( (isset($config->debug_api_results) && $config->debug_api_results===true) || (isset($config->debug_api_resources) && $config->debug_api_resources)|| $debug){

            // print errors if any
            if (count($this->errors)>0){
                $utils->print_array($this->errors);
            }

            // print normal results in debug mode
            else {
                $utils->print_array($this->results);
            }
        }

        //
        else {

            // load document type from library and render accordingly
            require_once(PATH_LIBRARIES. '/document/renderer.php');
            //
            $renderer = new Renderer(strtolower($this->default_output_type));

            // get allowed origins
            if ( (isset($config->access_control_allow_origin) && is_array($config->access_control_allow_origin) && count($config->access_control_allow_origin)>0) ) {
                $this->default_access_control_allow_origin = $config->access_control_allow_origin;
            }

            // the parser will handle results & errors independly. in case xml is set we need the root element provided by the handler
            $renderer->parseResults($this->results, $this->errors, $this->default_access_control_allow_origin, $this->root_element);
            $renderer->render(/*'data'*/);

        }

    }




}






















/*
 *
 *
 * Grab Request data,
 * URI information,
 * Post Data & Request Body for Payloads
 *
 *
 *
 *  */
abstract class RestServer{







    /*
     * Request Method
     */
    public function getRequestMethod(){
        return strtolower($_SERVER['REQUEST_METHOD']);
    }



    /*
     * Request URI
     */
    public function getRequestURI(){
        return $_SERVER['REQUEST_URI'];
    }



    /*
     * Get post data only for 'post' request method, otherwise return false
     */
    public function getPostData(){
        //
        if ( $this->getRequestMethod() == 'post'){
            return $_POST;
        }
        return false;
    }



    /*
     *
     * Main call to get Request Payloads, can be both for post & put methods otherwise return false
     * file_get_contents('php://input') can be called once as per idempotent method
     *
     */
    public function getRequestBody(){
        //
        if ( $this->getRequestMethod() == 'post' || $this->getRequestMethod() == 'put' ){
            return file_get_contents('php://input');
        }
        return false;
    }







}