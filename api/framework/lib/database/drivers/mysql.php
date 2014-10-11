<?php
/*
 *
 *
 *
 *
 */
class MysqlDatabase implements AdapterInterface{





    /*
     *
     *
     */
    public $err = null;






    /*
     *
     *
     *
     *
     */
    function __construct($config, $debug = false){
        if (function_exists('mysql_connect')){
            $this->config       = $config;
            $this->host         = $this->config->host;
            $this->user         = $this->config->user;
            $this->password     = $this->config->password;
            $this->db           = $this->config->db;
            $this->conn = mysql_connect($this->host, $this->user, $this->password)
                or die(mysql_error());
        } else {
            throw new Exception('MySql module is not installed');
        }
        //http://www.forosdelweb.com/f18/problema-con-tildes-n-php-mysql-505576/
        @mysql_query("SET NAMES 'utf8'");
        if (function_exists('mysql_select_db')){
            mysql_select_db($this->db, $this->conn) or die(mysql_error());
        }
        $this->debug = $debug;
    }










    /*
     *
     *
     *
     */
    public function doQuery($query, $type=null){
        $this->total_queries++;

        if ($this->config->debug_queries){
            echo "<br />$query<br />";
        }
        $result = mysql_query($query, $this->conn);
        if(!$result){
            throw new Exception('MySQL Error: ' . mysql_error());
        }
        return $result;
    }










    /*
     *
     *
     *
     */
    public function closeConnection(){
        mysql_close($this->conn);
    }












}