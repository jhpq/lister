<?php
/*
 *
 * Configuration with specific variables
 *
 */
class RConfig{


    /*
     * Configuration Options
     * (Optional) Access control Allow Origin: set allowed domains to use the API, default is allowed only local host.
     * (Optional) Allowed request methods: Restrict request method, default methods are 'get', 'post', 'put', 'patch', 'delete', 'head', 'options'
     * (Optional) Set default results if required. When a method calls $this->results will use "$default_app_results", you can override this in your calls.
     * (Optional) The number of directories where the Api is installed, i.e.: you have your api installed inside two directories like '/path1/api' it will have 2 directories,
     *  for '/api' it will be only 1, if no directory is provided and api is installed on the root you can exclude this option or set to 0.
     */

    public $access_control_allow_origin     = array( '*' );
    public $allowed_request_methods         = array('get', 'post', 'put', 'patch', 'delete', 'head', 'options');
    public $default_output_type             = 'json';
    public $default_api_results             = array('success'=>false,'data'=>null,'report'=>null);

    //
    public $no_of_directories   = 2; // 2 dirs = "/llantas/api"
    // Set api URL for internal calls

    public $api_url             = 'http://localhost/API/api/v2';
    //public $api_url           = 'http://localhost/API/compras/api/v2';



    /*
     * Debug Options
     * (Optional) Debug API Resources: will display API information like version, handlers, paths and so on.
     * (Optional) Debug API Results: will display results in an array
     * (Optional) Debug queries: will display SQL queries in the window.
     */
    public $debug_api_resources         = false;
    public $debug_api_results           = false;
    public $debug_queries               = false;


    /*
     *
     * Database Configuration
     * $driver = 'Sql to use', $host, $user, $password, $db
     *
     */
    

    // SQL Server 2008 RC2 - PRODUCTION
    public $driver    = 'MySql';    public $host       = 'localhost'; public $user       = 'root';    public $password   = 'proyecto';    public $db         = 'lister';


} 