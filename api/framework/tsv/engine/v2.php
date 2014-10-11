<?php
/*
 *
 *
 *
 */
require_once(PATH_FRAMEWORK. '/engine/components/v2/mvc/model.php');
require_once(PATH_FRAMEWORK. '/engine/components/v2/mvc/view.php');
require_once(PATH_FRAMEWORK. '/engine/components/v2/mvc/controller.php');

//
require_once(PATH_LIBRARIES. '/error_codes.php');






/*
 *
 *
 *
 *
 *
 *
 */
class RockEngineComponentRouterV2 {










    /*
     *
     */
    public $output_type     = null;
    public $root_element    = 'root';
    public $results         = null;
    public $errors          = array();
    public $qs_seg_placeholder     = array();
    public $api_resources          = null;
    public $version                = null;
    public $resource_controller    = null;
    private $matched_element       = array();
    public $debug_data             = null;
    /*
     *
     */
    private $views_path = "/views";
    private $models_path = "/models";
    private $default_results = array('success'=>false,'data'=>null,'report'=>null);












    /*
     *
     */
    function __construct($options){
        //
        $this->helper               = $options['helper'];
        $this->config               = $options['config'];
        $this->database             = $options['database'];
        $this->request_uri          = $options['server']['request_uri'];
        $this->request_method       = $options['server']['request_method'];
        $this->allowed_methods      = $options['server']['allowed_request_methods'];
        $this->post_data            = $options['server']['post_data'];
        $this->body_data            = $options['server']['body_data'];
    }









    /*
     *
     *
     */
    function formatURIPaths(){
        //
        $plus_directories = 0;
        if ( isset($this->config->no_of_directories) && !is_null($this->config->no_of_directories) && is_numeric($this->config->no_of_directories) ){
            $plus_directories = $this->config->no_of_directories;
        }
        //
        $no_of_parts = 2 + $plus_directories;
        $path_data_no = $no_of_parts - 1;

        $resources_path = explode( '/', $this->request_uri, $no_of_parts );

        if (isset($resources_path[$path_data_no])){
            return $resources_path[$path_data_no];
        }
        return false;
    }





    /*
     *
     */
    function routeMVCType() {

        // pick api info
        $this->debug_data .= "" .
            "Allowed Methods: ---------------------------------------<br />" .
            "<pre>";
        foreach($this->config->allowed_request_methods as $allowed_method){
            $this->debug_data .= $allowed_method . ' ';
        }
        $this->debug_data .= "" .
            "</pre><br /><br />" .
            "Default Output Type: -----------------------------------<br />" . $this->config->default_output_type . "<br /><br />" .
            "Request URI: -------------------------------------------<br />" . $this->request_uri . "<br /><br />";


        //
        if ( $uri_path = $this->formatURIPaths() ){

            //
            $uri_path_parts = (explode( '/', $uri_path));

            // check if main & clean resources have more than one segment (plus the 1 in the version)
            if ( count($uri_path_parts) > 1 ){

                // If we have only 2 parts in the request uri then just get version & resource controller, there are no other resources
                if ( count($uri_path_parts) === 2 ){
                    list($this->version, $this->resource_controller ) = explode( '/', $uri_path);
                }

                // If we have more then 2 parts get version, resource controller, and all the rest of api resources pass them to api_resources
                else {
                    list($this->version, $this->resource_controller, $this->api_resources ) = explode( '/', $uri_path, 3);
                }

                // Bring normalized controller & uri by ignoring query strings and bring only the content before the "?"
                $normalized_resource_controller = preg_replace('~\?[^/]++~', '', $this->resource_controller);
                $normalized_api_resources = preg_replace('~\?[^/]++~', '', $this->api_resources);

                // get current segments from api resources
                $segments1 = explode( '/', $this->resource_controller . '/' . $this->api_resources);
                $this->setSegmentQueryString($segments1);

                // pick api info
                $this->debug_data = "" .
                    "API Version: -----------------------------------------------<br />" . $this->version . "<br /><br />" .
                    "Resource Controller: ---------------------------------------<br />" . $this->resource_controller . "<br /><br />" .
                    "Path Resources: --------------------------------------------<br />" . $this->api_resources . "<br /><br />" .
                    "Normalized Resource Controller: ----------------------------<br />" . $normalized_resource_controller . "<br /><br />" .
                    "Normalized Path Resources: ---------------------------------<br />" . $normalized_api_resources . "<br /><br />" .
                    "Query String Segments: -------------------------------------<br />" .
                    "<pre>";
                foreach($this->qs_seg_placeholder as $seg){
                    $this->debug_data .= $seg . ' ';
                }
                $this->debug_data .= "</pre><br /><br />";

                // Model Resources for this engine
                $this->resourceModeler($this->version, $normalized_resource_controller, $normalized_api_resources );

            } else {
                array_push($this->errors, 'Error, please provide API resources');
            }
        } else {
            array_push($this->errors, 'Error, please provide API version & resources');
        }
    }




















    /*
     *
     *
     */
    private function resourceModeler($version, $resource_controller, $api_resource_uri ){

        // build context root with resource information
        $context_root = PATH_API."/{$version}/{$resource_controller}";

        // Include content with file structure like version and api resource name  [api_path]/resources/[version]/[resource]/api.[resource].php
        $resource_controler_file = $context_root."/{$resource_controller}controller.php";

        // include the "resource controller file" and at the same time compare its provided routes with the one from the request uri, also compare the current request method.
        if( file_exists($resource_controler_file) ) {
            //
            require_once $resource_controler_file;

            //
            $resource_classname = ucfirst($resource_controller).'Controller';

            //
            if((int)class_exists($resource_classname)){

                // controller classname exist then get controller class
                $controller_class = new $resource_classname();
                // declare urls array
                $controller_class->urls = array();


                // initialize and set reoutes and handlers
                if ( (int)method_exists($controller_class, 'initialize')){

                    // initialize controller
                    $controller_class->initialize();

                    //count & get urls if actually set
                    if ( ($this->controller_resources = $controller_class->urls) && count($controller_class->urls)>0){

                        // pick api info
                        $this->debug_data .= "Controller Urls: -------------------------------------<br />";
                        foreach($this->controller_resources as $resource_ctrl){
                            foreach($resource_ctrl as $prop){
                                $this->debug_data .= $prop . ' ';
                            }
                        }
                        $this->debug_data .= "<br /><br />";

                        // Callable property will be holding data from route being called (the one that matches)
                        $this->callable = array();

                        // if Api resources URI has a value just append a '/' diagonal at the beggining of such, otherwise leave it without so that the resource_controller itself can act as a single resource
                        if ( !empty($api_resource_uri ) ){
                            $api_resource_uri  = '/' . $api_resource_uri;
                        }

                        // must match a single callable method due to the request method can be called once, this must match the number of resource handler properties: route, method, handler and params from the function being called
                        if ( $this->matchRoutes($version, $resource_controller, '/'.$resource_controller. $api_resource_uri) ){
                            // pick api info
                            /*
                            $this->debug_data .= "Match to call : ----------------------------------------<br />";
                            foreach($this->matched_element as $element_property){
                                if (is_array($element_property)){
                                    foreach($element_property as $prop){
                                        $this->debug_data .= $prop . ' xx ';
                                    }
                                } else {
                                    $this->debug_data .= $element_property . ' ** ';
                                }
                            }
                            $this->debug_data .= "<br /><br />";
                            */
                            // get view and model file
                            $resource_view_file = $this->matched_element['view_file_path'];
                            // load optional model
                            $resource_model_file = $this->matched_element['model_file_path'];

                            // LAOD OPTIONAL MODEL
                            $resource_model_instance = null;
                            if( file_exists($resource_model_file) ) {

                                require_once $resource_model_file;
                                // test class
                                $model_class = $this->matched_element['model_classname'];
                                //
                                if((int)class_exists($model_class)){

                                    //
                                    $model_klass = new $model_class();

                                    // Halt if there is no inheritance
                                    (int)method_exists($model_klass, 'setConfig') or die("Error, " . $model_class . " must inherit from \"Model\" Class ");

                                    // First set Utils & Config then set the rest
                                    $model_klass->setConfig($this->config);
                                    $model_klass->setHelper($this->helper);

                                    // db
                                    $model_klass->setDB($this->database);

                                    // Set content, params & args
                                    $model_klass->setPostData($this->post_data);
                                    $model_klass->setRequestBody($this->body_data);
                                    //
                                    $model_klass->setQueryStrings($this->qs_seg_placeholder);
                                    $model_klass->setParamValues($this->matched_element['func_args']);

                                    //
                                    $resource_model_instance = $model_klass;
                                }
                            }

                            // LOAD VIEW
                            if( file_exists($resource_view_file) ) {
                                //
                                require_once $resource_view_file;

                                // test class
                                $handler_class = $this->matched_element['view_classname'];

                                //
                                if((int)class_exists($handler_class)){

                                    // default results
                                    $app_results = $this->default_results;
                                    if ( isset($this->config->default_api_results) ){
                                        $app_results = $this->config->default_api_results;
                                    }

                                    //
                                    $klass = new $handler_class($app_results);

                                    // pass resource api uri
                                    $klass->api_resources = explode('/', $api_resource_uri);

                                    // Halt if there is no inheritance
                                    (int)method_exists($klass, 'setDB') or die("Error, " . $handler_class . " must inherit from \"View\" Class ");

                                    // First set Utils & Config then set the rest
                                    $klass->setConfig($this->config);

                                    // Set db and utils
                                    $klass->setDB($this->database);
                                    $klass->setHelper($this->helper);

                                    // Set content, params & args
                                    $klass->setPostData($this->post_data);
                                    $klass->setRequestBody($this->body_data);
                                    $klass->setQueryStrings($this->qs_seg_placeholder);
                                    $klass->setParamValues($this->matched_element['func_args']);

                                    //
                                    $handler_method = $this->matched_element['view_method_name'];
                                    $klass->setParamValues($this->matched_element['func_args']);

                                    //
                                    if ( (int)method_exists($klass, $handler_method) ){

                                        // if we have a model just pass that as arguments without native ones
                                        if (!is_null($resource_model_instance)){
                                            // pass model instance to parent so we can use it in child views
                                            $klass->setModel($resource_model_instance);
                                            // call handler with with same controller class, and with the post handler provided by the controller, params by the resource routing and loader
                                            $this->results = call_user_func_array(array($klass, $handler_method), array());


                                        }
                                        //
                                        else {
                                            // call handler with with same controller class, and with the post handler provided by the controller, params by the resource routing and loader
                                            $this->results = call_user_func_array(array($klass, $handler_method), $this->matched_element['func_args']);
                                        }

                                        // Set XML root element & Content Type. set output type and root element if xml. All after calling class
                                        $this->root_element     = $klass->getRootElement();
                                        $this->output_type      = $klass->getType();

                                        // stop exec on true
                                        return true;
                                    } else {
                                        array_push($this->errors, "Error, class method " . $handler_method. " not in " . $handler_class );
                                    }
                                } else {
                                    array_push($this->errors, "Error, class " . $handler_class . " not in view file");
                                }
                            } else {
                                array_push($this->errors, "Error, unable to load view " . $resource_view_file);
                            }
                        }// ** no match found for requested uri
                    }// ** please provide at least one url pattern
                } else {
                    array_push($this->errors, "Error, resource controller needs to be initialized");
                }
            } else {
                array_push($this->errors, "Error, resource class not found must set prefix to '[Resource]Controller' ");
            }
        } else {
            array_push($this->errors, 'Error, resource file not found');
        }
    }















    /*
     *
     *
     */
    private function matchRoutes($version, $resource_name, $api_resource_uri){

        // load engine version
        if ($version==='v2'){

            // pick api info
            $this->debug_data .= "Pattern/Uri: ------------------------------------------<br />";

            $i = 0;
            //
            foreach($this->controller_resources as $controller_resource){

                // Resource controller element must have 2 values: 1 for route 2 for namespace, file, class & method.
                (count($controller_resource)===2) or die('Error, wrong params number for url element with index "' . $i . '" ');

                // Get route
                $handler_route  = $controller_resource[0];
                // build path
                $handler_path   = $controller_resource[1];

                // Handler View Defaults
                $handler_method             = null; // default handler method for the view
                $handler_namespace          = null; // default empty namespace

                // default class & file name
                $handler_filename_and_classname  = $resource_name;;

                // Prepare handler parts to determine namespace, file, class & method
                $handler_parts = explode('.', $handler_path);


                // get handler
                if (count($handler_parts)===1){
                    $handler_method    = $handler_parts[0];
                }

                // get handler filename , classname and method
                elseif (count($handler_parts)===2){

                    // get filename and classname
                    $handler_filename_and_classname = $handler_parts[0];

                    // get view callable method name from the second part
                    $handler_method    = $handler_parts[1];
                }

                // handler part specifies file, class & method
                elseif (count($handler_parts)>2){

                    // get view callable method name
                    $handler_method     = array_pop($handler_parts);

                    // get file and class name
                    $handler_filename_and_classname = array_pop($handler_parts);

                    //
                    $handler_namespace = '/'.implode('/', $handler_parts); // implode remaining namespace DSV (dot separated values)
                }

                // find handler request method, if none provided default will be get
                $handler_request_method     = 'get';
                //
                if ( strtolower(substr( $handler_method, 0, 3 )) === 'get'){
                    $handler_request_method = 'get';
                }
                elseif ( strtolower(substr( $handler_method, 0, 4 )) === 'post'){
                    $handler_request_method = 'post';
                }
                elseif ( strtolower(substr( $handler_method, 0, 3 )) === 'put'){
                    $handler_request_method = 'put';
                }
                elseif ( strtolower(substr( $handler_method, 0, 5 )) === 'patch'){
                    $handler_request_method = 'patch';
                }
                elseif ( strtolower(substr( $handler_method, 0, 6 )) === 'delete'){
                    $handler_request_method = 'delete';
                }
                elseif ( strtolower(substr( $handler_method, 0, 4 )) === 'head'){
                    $handler_request_method = 'head';
                }
                elseif ( strtolower(substr( $handler_method, 0, 7 )) === 'options'){
                    $handler_request_method = 'options';
                }

                // Set the pattern to retrieve only params with ':' (without prefixed)
                $allowed_params_pattern = '/\/\:([\w\-]+)/';

                // http://www.aivosto.com/vbtips/regex.html
                // Set param var type allowed \d for numeric, \D non digit and \w for alphanumeric
                //[\\w-]+ [\w&_.?-%=#]+
                $replacement = "/([\w-.]+)$3";
                $replaced_pattern = preg_replace($allowed_params_pattern, $replacement, $handler_route);

                // Get the url pattern ready to test
                $url_pattern = '/^'.str_replace('/','\/', $replaced_pattern).'$/';

                // match handler method with request method (convert to lower case)
                if ( $this->request_method == $handler_request_method ){

                    // pick api info
                    $this->debug_data .= " {$url_pattern} {$api_resource_uri} <br />";
                    $this->debug_data .= "<br />";

                    // if route pattern matches with api url then get param values
                    if(preg_match($url_pattern, $api_resource_uri, $values)) {
                        // remove unwanted match values
                        unset($values[0]);
                        // Build matched element properties
                        $this->matched_element['func_args']             = $values;
                        $this->matched_element['view_method_name']      = $handler_method;
                        $this->matched_element['view_classname']        = ucfirst($handler_filename_and_classname).'View';
                        $this->matched_element['model_classname']       = ucfirst($handler_filename_and_classname).'Model';
                        $this->matched_element['view_file_path']        = PATH_API."/{$version}/{$resource_name}$this->views_path{$handler_namespace}/{$handler_filename_and_classname}.php";
                        $this->matched_element['model_file_path']       = PATH_API."/{$version}/{$resource_name}$this->models_path{$handler_namespace}/{$handler_filename_and_classname}.php";
                    }
                }
                $i++;
            }//-- end iteration
        }
        return $this->matched_element;
    }














    /*
     *
     *
     *
     */
    public function setSegmentQueryString($str_segments){
        // do operations
        for ( $i = 0; $i < count($str_segments); $i++ ){
            preg_match("/\?(.+)/", $str_segments[$i], $matches);
            if ($matches && $matches[1]) {

                // Explode "&", rename key and iterate
                $expl2 = explode("&", $matches[1]);
                $this->qs_seg_placeholder[/*'segment'.*/$i] = array();
                for ( $j = 0; $j < count($expl2); $j++ ){

                    // Explode "=", rename key and iterate
                    $expl3 = explode("=", $expl2[$j]);

                    // retrieve key value pairs data
                    $key = null; $value = null;
                    for ( $k = 0; $k < count($expl3); $k++ ){
                        //
                        if ($k===0){
                            $key = $expl3[$k];
                        } elseif ($k===1){
                            $value = $expl3[$k];
                        }
                    }
                    $this->qs_seg_placeholder[/*'segment'.*/$i][$key] = $value;
                }
            }
        }
    }










}