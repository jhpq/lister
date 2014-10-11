<?php
/*
 *
 *
 *
 *
 */
class Helper
{








    /*
     *
     *
     */
    private static $instance;







    /*
     * priv const on singleton
     */
    private function __construct(){}








    /*
     *
     *
     */
    public static function getInstance()
    {
        if (  !self::$instance instanceof self)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }










    /*
     *
     *
     */
    public function setDb($database){
        $this->database = $database;
    }










    /*
     *
     *
     */
    public function import($base = null, $path){
        $success = false;
        $parts = explode('.', $path);
        $base = (!empty($base)) ? $base : __DIR__;
        $path = str_replace('.', DIRECTORY_SEPARATOR, $path);
        // If the file exists attempt to include it.
        if (is_file($base . '/' . $path . '.php'))
        {
            $success = (bool) include_once $base . '/' . $path . '.php';
        }
        return $success;
    }











    /*
     *
     *
     */
    function getTools($klass_name){
        //
        if ( $this->import(PATH_LIBRARIES, 'helpers.'.$klass_name) ){
            $klass_name = 'Helper'.ucfirst($klass_name);
            if (class_exists($klass_name)){
                return new $klass_name($this->database);
            }
        }
        return;
    }













}