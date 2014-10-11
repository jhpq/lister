<?php
/*
 *
 *
 *
 *
 */
class HelperUtilities{












    /*
     *
     *
     */
    public function session_is_active()
    {
        $setting = 'session.use_trans_sid';
        $current = ini_get($setting);
        if (FALSE === $current)
        {
            throw new UnexpectedValueException(sprintf('Setting %s does not exists.', $setting));
        }
        $result = @ini_set($setting, $current);
        return $result !== $current;
    }









    /*
     *
     *
     */
    public function reinitialize_session(){
        if ( self::session_is_active() ){
            session_destroy();
            session_start();
        } else {
            session_start();
        }
    }









    /*
     *
     *
     */
    function mul_isset(){
        $numargs = func_num_args();
        $arg_list = func_get_args();
        for ($i = 0; $i < $numargs; $i++) {
            $arg = $arg_list[$i];
            if (!isset($arg)){
                return false;
            }
        }
        return true;
    }






    /*
     *
     *
     *
     */
    function in_multiarray($elem, $array)
    {
        $top = sizeof($array) - 1;
        $bottom = 0;
        while($bottom <= $top)
        {
            if($array[$bottom] == $elem)
                return true;
            else
                if(is_array($array[$bottom]))
                    if(in_multiarray($elem, ($array[$bottom])))
                        return true;

            $bottom++;
        }
        return false;
    }










    /*
     *
     *
     */
    public static function print_array($msg){
        echo "<pre>";
        print_r($msg);
        echo "</pre>";
    }










    /*
     *
     *
     */
    public static function validParams($m, $params = null){
        $passed = false;
        if ($_SERVER['REQUEST_METHOD']===$m){
            // passed 1st validation
            $passed = true;
            // test params
            if (is_array($params)){
                foreach($params as $param){
                    if (!isset($_REQUEST[$param])){
                        $passed = false;
                    }
                }
            }
        }
        return $passed;
    }









    /*
     *
     *
     *
     */
    public static function validBodyParams($body, $params = null){
        $passed = false;
        if (strtolower($_SERVER['REQUEST_METHOD'])==='post' || strtolower($_SERVER['REQUEST_METHOD'])==='put'){
            // passed 1st validation
            $passed = true;
            // test params
            if (is_array($body) && is_array($params)){
                foreach($params as $param){
                    if (!isset($body[$param])){
                        $passed = false;
                    }
                }
            }
        }
        return $passed;
    }











    /*
     *
     *
     *
     */
    public static function JSONToArray($json_str){
        return json_decode($json_str, true);
    }













    /*
     *
     *
     *
     */
    public static function debug_print($msg){
        die($msg);
    }













}