<?php
/*
 *
 *
 *
 *
 */
class PopulatorView extends View{







    /*
     *
     *
     *
     */
    function CurrentUserSucursal(){
        try{
            //
            $db = $this->getDB();

            //
            $tbl_name       = "sucursal";
            $where_field    = 'id';

            // Build final sql clause
            $sql_clause = sprintf(" SELECT * FROM %s WHERE %s = %s ",
                $tbl_name,
                $where_field,
                $_SESSION['sucursalid']
            );
            $res = $db->query($sql_clause);

            $results = array();
            // get results
            while($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)){
                array_push($results, $row);
            }
            // if nothing found just return the whole list
            if (count($results)===0){
            }

            return $results;

        }
        catch (Exception $e){
            return false;
        }
    }







    /*
     *
     *
     *
     */
    function CurrentUserSucursalAlmacenes(){
        try{
            //
            $db = $this->getDB();

            // Build final sql clause
                $sql_clause = sprintf(" " .
                    " SELECT ta.shortname, ta.descripcion, tst.sucursalid, tst.tipoalmllantaid FROM sucursal ts " .
                    " LEFT JOIN sucytipo tst on tst.sucursalid = ts.id " .
                    " LEFT JOIN almacen ta on ta.id = tst.tipoalmllantaid " .
                    " WHERE sucursalid = %s",
                $_SESSION['sucursalid']
            );
            $res = $db->query($sql_clause);

            $results = array();
            // get results
            while($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)){
                array_push($results, $row);
            }
            // if nothing found just return the whole list
            if (count($results)===0){
            }

            return $results;

        }
        catch (Exception $e){
            return false;
        }
    }






    /*
     *
     *
     *
     */
    function Roles(){
        try{

            /*
            $allowed_ids = array(2, 3);
            $in_session = 'nivel';
            $err_code = 404;
            $this->authenticate($allowed_ids, $in_session, $err_code);
            */

            //
            //$this->authenticate(array(1, 5), 'nivel', 401);




            // we must be logged
            $user_nivel = $_SESSION['nivel'];

            $sql = "";
            // super admin muestra todo
            if ($user_nivel===1) {
                //$sql = "SELECT idnivel id,descripcion role FROM nivelespuestos WHERE idnivel NOT IN (3, 4)";
                $sql = "SELECT idnivel id,descripcion role FROM nivelespuestos";
            }

            // si es nivel 3(administrador) no muestra super usuario
            elseif ($user_nivel===5){
                $sql = "SELECT idnivel id,descripcion role FROM nivelespuestos WHERE idnivel NOT IN(1)";
            }

            //
            $db = $this->getDB();

            // Build final sql clause
            $sql_clause = sprintf($sql);
            $res = $db->query($sql_clause);

            $results = array();
            // get results
            while($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)){
                array_push($results, $row);
            }
            // if nothing found just return the whole list
            if (count($results)===0){
            }

            return $results;

        }
        catch (Exception $e){
            return false;
        }
    }







    /*
     *
     *
     *
     */
    function SearchCurrentUserSucursalAlmacen(){
        try{
            //
            $db = $this->getDB();

            if ( $where_value = $this->getSegmentValue(1) ) {

                // Build final sql clause
                $sql_clause = sprintf(" " .
                        " SELECT ta.shortname, ta.descripcion, tst.sucursalid, tst.tipoalmllantaid FROM sucursal ts " .
                        " LEFT JOIN sucytipo tst on tst.sucursalid = ts.id " .
                        " LEFT JOIN almacen ta on ta.id = tst.tipoalmllantaid " .
                        " WHERE sucursalid = %s" .
                        " AND ta.shortname LIKE '%%%s%%' "
                    ,
                    $_SESSION['sucursalid'],
                    $where_value
                );
                $res = $db->query($sql_clause);

                $results = array();
                // get results
                while($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)){
                    array_push($results, trim($row['shortname']));
                }
                // if nothing found just return the whole list
                if (count($results)===0){
                }
                return $results;
            }

            return false;
        }
        catch (Exception $e){
            return false;
        }
    }








    /*
     *
     *
     *
     */
    function PostSearchCurrentUserSucursalAlmacen(){
        try{
            //
            $db = $this->getDB();

            // get post variables
            $post       = $this->getPostData();

            // if POST it means it is a validation so just return true or false
            if ( isset($post['almacen']) ){
                //
                $where_value     = filter_var($post['almacen'], FILTER_SANITIZE_STRING);

                // Build final sql clause
                $sql_clause = sprintf(" " .
                        " SELECT ta.shortname, ta.descripcion, tst.sucursalid, tst.tipoalmllantaid FROM sucursal ts " .
                        " LEFT JOIN sucytipo tst on tst.sucursalid = ts.id " .
                        " LEFT JOIN almacen ta on ta.id = tst.tipoalmllantaid " .
                        " WHERE sucursalid = %s" .
                        " AND ta.shortname = '%s' "
                    ,
                    $_SESSION['sucursalid'],
                    $where_value
                );
                $res = $db->query($sql_clause);
                //
                if (sqlsrv_has_rows($res)){
                    return true;
                } else {
                    return false;
                }

            }
            return false;
        }
        catch (Exception $e){
            return false;
        }
    }


}