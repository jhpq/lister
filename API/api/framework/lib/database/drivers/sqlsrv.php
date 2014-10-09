<?php
/*
 *
 *
 *
 */
class SqlsrvDatabase implements AdapterInterface{





    /*
     *
     */
    public $conn     = false;











    /*
     *
     *
     */
    function __construct($config, $debug = false){
        // Debug according
        $this->debug    = $debug;

        if (function_exists('sqlsrv_connect')){
            $this->config       = $config;
            $server             = $this->config->host;
            $uid                = $this->config->user;
            $pwd                = $this->config->password;
            $db                 = $this->config->db;
            // Connection arr
            $arr_connection_info = array("UID" => $uid, "PWD" => $pwd, "Database"=>$db);
            // try to conn  ect
            $this->conn = sqlsrv_connect($server, $arr_connection_info);

            if( $this->conn === false ) {
                if( ($errors = sqlsrv_errors() ) != null) {
                    foreach( $errors as $error ) {
                        throw new Exception("SQLSTATE: ".$error[ 'SQLSTATE'] . ", code: ".$error[ 'code'] . ", message: ".$error[ 'message']);
                    }
                }
            }
        } else {
            throw new Exception('SQL Server module is not installed');
        }
    }







    /*
     *
     *
     *
     */

    public function query($query, $lid=false){
        $stmt = sqlsrv_query($this->conn, $query);
        if ($lid){
            if( $stmt ) {
                sqlsrv_next_result($stmt);
                sqlsrv_fetch($stmt);
                return sqlsrv_get_field($stmt, 0);
            }
        }
        return $stmt;
    }





    public function rows_affected($resource){
        if ($resource){
            $rows_affected = sqlsrv_rows_affected($resource);
            if( $rows_affected === false) {
                $err = '';
                if( ($errors = sqlsrv_errors() ) != null) {
                    foreach( $errors as $error ) {
                        $err .= "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
                        $err .= "code: ".$error[ 'code']."<br />";
                        $err .= "message: ".$error[ 'message']."<br />";
                    }
                }
                return $err;
            } elseif( $rows_affected == -1) {
                return "No information available.";
            } else {
                return $rows_affected." rows were updated";
            }
        }
        return "Not a valid resource";
    }







    public function doQuery($query, $type=null){
        // Debug data
        if ($this->config->debug_queries){
            echo "<br />$query<br />";
        }
        // sql statement
        $stmt = sqlsrv_query($this->conn, $query);

        // Sql Server
        if( $stmt === false ) {
            if( ($errors = sqlsrv_errors() ) != null) {
                foreach( $errors as $error ) {
                    throw new Exception("SQLSTATE: ".$error[ 'SQLSTATE'] . ", code: ".$error[ 'code'] . ", message: ".$error[ 'message']);
                }
            }
        } else {

            if ($type==='get_users_lid'){

                sqlsrv_next_result($stmt);
                sqlsrv_fetch($stmt);
                return sqlsrv_get_field($stmt, 0);

            } elseif ($type==='get_last_insert_id'){

                sqlsrv_next_result($stmt);
                sqlsrv_fetch($stmt);
                return sqlsrv_get_field($stmt, 0);
                //
            } else {
                return sqlsrv_query($this->conn, $query);
            }
        }

    }








    /*
     *
     *
     */
    public function closeConnection(){
        sqlsrv_close($this->conn);
    }





}