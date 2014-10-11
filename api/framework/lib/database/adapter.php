<?php
/*
 *
 *
 *
 *
 */
class Database{






    /*
     *
     *
     */
    function __construct($config){
        // load configuration
        $this->config       = $config;
    }








    /*
     *
     *
     */
    public function getInstance(){
        $driver = strtolower($this->config->driver);
        $driver_path = PATH_LIBRARIES.'/database/drivers/'.$driver.'.php';
        if (is_file($driver_path)){
            require_once $driver_path;
            $klass_name = ucfirst($driver).'Database';
            if (class_exists($klass_name)){
                //
                $debug_queries = false;
                if (isset($this->config->debug_queries)){
                    $debug_queries = $this->config->debug_queries;
                }
                //
                return new $klass_name($this->config, $debug_queries);
            }
        }
        return;
    }









}










/*
 *
 *
 */
interface AdapterInterface{



    /*
     *
     * */
    public function doQuery($query, $type=null);




    /*
     *
     * */
    public function closeConnection();


}

