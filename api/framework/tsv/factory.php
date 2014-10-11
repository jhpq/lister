<?php



// Singleton Rock application Facotry
class RFactory
{





    // Priv member for singleton instance
    private static $instance;






    private function __construct(){/**/}
    //
    public static function getInstance()
    {
        if (  !self::$instance instanceof self)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }








    //
    public static function getConfig() {
        //
        $config_file = PATH_ROOT.'/config.php';
        //
        if (is_file($config_file)){
            require_once $config_file;
            return new RConfig();
        }

    }






    //
    public static function getHelper( $db ) {
        //
        $helper_file = PATH_LIBRARIES.'/helpers/helper.php';
        if (is_file($helper_file)){
            require_once $helper_file;

            $inst = Helper::getInstance();
            $inst->setDb($db);
            return $inst;
        }
    }










    //
    public static function getDatabase()
    {
        //
        $database_path = PATH_LIBRARIES.'/database/adapter.php';
        if (is_file($database_path)){
            require_once $database_path;
            $database = new Database(self::getConfig());
            $db = $database->getInstance();
            return $db;
        }
    }










}