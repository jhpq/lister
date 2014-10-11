<?php
/*
 *
 *
 *
 *
 */
class UserModel extends Model{









    /*
     *
     *
     *
     */
    function Users(){
        try{

            /* Get Db & Tools */
            $db         = $this->getDB();
            $utils      = $this->getHelper();
            $database_tools = $utils->getTools('database');


            /* Get page number and rows to display */
            $page_no = ($this->getSegmentParamValue( 0, 'page' )) ? $this->getSegmentParamValue( 0, 'page' ) : 1;
            $page_size = ($this->getSegmentParamValue( 0, 'rows' )) ? $this->getSegmentParamValue( 0, 'rows' ) : 25;


            /* Get records Order*/
            $order_by_clause = 'ORDER BY id_usuario DESC'; // default if none provided
            //

            if ( ($sidx = $this->getSegmentParamValue( 0, 'sidx' )) && ($sord = $this->getSegmentParamValue( 0, 'sord' )) ){
                //
                $sid = urldecode($sidx); //sort index decoded
                // default sort
                $order_by_clause = ' ORDER BY ' . $sid . ' ' . $sord;
                // sid array
                $sidx_arr = explode(',', $sid);

                // GROUPING: if we have a comma it is a groupping store request and we must have a query for grouping, otherwise it will throw error on multiple ordering (A column has been specified more than once in the order by list. Columns in the order by list must be unique.)
                if ( count($sidx_arr) > 1 ){
                    $order_by_clause = ' ORDER BY ' . $sid . ' ' . $sord;
                }
            }

            /* Get search params/values if any */
            $search_clause = null;
            if ( $this->getSegmentParamValue( 0, '_search' ) && ($this->getSegmentParamValue( 0, '_search' )!='false') ){
                //
                if ( $no_tsv = $this->getSegmentParamValue( 0, 'no_tsv' ) ) {
                    $search_clause .= " AND no_tsv LIKE '%$no_tsv%' ";
                }
                if ( $no_orden_documento = $this->getSegmentParamValue( 1, 'no_orden_documento' ) ) {
                    $search_clause .= " AND no_orden_documento LIKE '%$no_orden_documento%' ";
                }
            }

            // Si es super administrador


            $suc_id = $_SESSION['id_sucursal'];
            $where_sucursal_clause = " AND sucursal = $suc_id ";
            if (isset($_SESSION['nivel']) && $_SESSION['nivel']===1){
                $where_sucursal_clause = '';
            }


            $limit_to_departamento_clause = " WHERE t.iddepartamento = 9 ";
            if ( isset($_SESSION['logged']) && ($_SESSION['id_usuario']===27 || $_SESSION['id_usuario']===7) ){
                //
                $limit_to_departamento_clause = "";
            }

            /* Do Total Query*/
            $sql_total = "
                 SELECT
                    COUNT(*) total
                    from usuarios t
                    LEFT JOIN departamentos td ON td.iddepartamento = t.iddepartamento
                    LEFT JOIN nivelespuestos tn ON tn.idnivel = t.idnivel
                    $limit_to_departamento_clause
                    $where_sucursal_clause $search_clause";
            //echo $sql_total; exit();
            $query_total = $db->query($sql_total);


            /* Get Total from Query and/or Search */
            $total = 0;
            if($row = sqlsrv_fetch_array($query_total, SQLSRV_FETCH_ASSOC)){
                $total = $row['total'];
            }

            /* Get Pagination Values */
            $total_pages     = ceil($total/$page_size);
            $start = ($page_size * $page_no - $page_size);
            $limit = $start+$page_size;
            $start++;

            /* Get Where Row Clause */
            $where_row_clause = null;
            if (is_null($search_clause)){
                $where_row_clause = " AND row BETWEEN $start AND $limit ";
            }

            /* Get Table Fields */
            $table_fields   = 'id_usuario, email, conectado, activo, sucursal, sucursal_shortname, nombre, puesto, id_departamento, departamento, id_nivel, nivel';
            if ( $fields = $this->getSegmentParamValue( 0, 'fields' ) ){
                //
                if ( $database_tools->getSegmentFields( $fields ) ) {
                    $table_fields = $database_tools->getSegmentFields( $fields );
                }
            }

            /* Do Api Query */
            $sql_clause = "
                SELECT
                    $table_fields
                    FROM
                        (SELECT
                        *
                        ,ROW_NUMBER() OVER($order_by_clause) as row
                        FROM
                        (SELECT
                             t.idusuario id_usuario
                            ,t.usuariocorreoe email
                            ,t.conectado conectado
                            ,t.conectado activo
                            ,ts.id sucursal
                            ,ts.shortname  sucursal_shortname
                            ,t.nombre nombre
                            ,t.puesto puesto
                            ,t.iddepartamento id_departamento
                            ,td.nombre departamento
                            ,t.idnivel id_nivel
                            ,tn.descripcion nivel
                            from usuarios t
                            LEFT JOIN departamentos td ON td.iddepartamento = t.iddepartamento
                            LEFT JOIN nivelespuestos tn ON tn.idnivel = t.idnivel
                            LEFT JOIN sucursal ts ON ts.id = t.sucursal
                            $limit_to_departamento_clause
                            ) t) t
                            WHERE 1=1
                            $where_row_clause
                            $where_sucursal_clause $search_clause
                                ";
            //echo $sql_clause; exit();
            $query = $db->query($sql_clause);


            /* Set Results */
            $results['totalrecords']       = $total; //Total count sql
            $results['totalpages']     = $total_pages; //Total Pages
            $results['currpage']        = $page_no; //Current Page Number
            $results['rows'] = array();
            //
            while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                /*
                $arr = array();
                $arr['invid'] = $row['id'];
                $arr['invrow'] = array();
                array_push($arr['invrow'],
                    $row['id'], $row['no_tsv'], $row['no_orden_documento'], $row['DOT'], $row['marca'], $row['medida'], $row['tipo'], $row['nombre'], $row['fecha_alta'], $row['ubicacion_actual'], $row['almacen'], $row['notas'], $row['activa']
                );
                */
                array_push( $results['rows'],  $row);
            }
            return $results;
        }
        catch (Exception $e){
            $this->setError($e);
        };
    }















    /*
     *
     *
     *
     *
     */
    function User(){
        try{
            // Do we have an id, then proceed to query
            if (
                ($id = $this->getSegmentValue(1)) && (is_numeric($this->getSegmentValue(1)))
            ){
                // Get Tools & Utilities
                $db         = $this->getDB();
                $helper     = $this->getHelper();
                $database_tools = $helper->getTools('database');

                // Get table values
                $tbl_name       = 'usuarios';
                $where_field    = 'idusuario';
                $table_fields   = 'idusuario, nombre, puesto, iddepartamento, idnivel, conectado, usuariocorreoe, contraseniacorreoe, pin, sucursal';

                // Get Table Fields (no limit offset for single id results)
                if ( $fields = $this->getSegmentParamValue( 0, 'fields' ) ){
                    //
                    if ( $database_tools->getSegmentFields( $fields ) ) {
                        $table_fields = $database_tools->getSegmentFields( $fields );
                    }
                }
                // Build final sql clause
                $sql_clause = sprintf(" SELECT %s FROM %s WHERE %s = %s",
                    $table_fields,
                    $tbl_name,
                    $where_field,
                    $id
                );
                // Get & Load Results
                $query = $db->query($sql_clause);
                //
                $results = array();
                // get results
                if($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                    array_push($results, $row);
                }
                return $results;

            } else {
                //throw new Exception('Error de validacion de datos');
                $this->setError('Error de validacion de datos');
            }
        }
        //
        catch (Exception $e){
            //
            $this->setError($e);
        };
    }





    /*
     *
     *
     *
     *
     */
    function postUser(){
        try{
            // Get Tools & Utilities
            $db         = $this->getDB();
            $helper     = $this->getHelper();
            $utils      = $helper->getTools('utilities');
            $body       = $utils->JSONToArray($this->getRequestBody());

            //
            if (
                isset($body['nombre']) &&
                isset($body['email']) &&
                isset($body['puesto']) &&
                isset($body['role']) && is_numeric($body['role']) &&
                isset($body['user_id']) && is_numeric($body['user_id']) &&
                isset($body['password'])
            ){

                // Mandatory fields
                // auto generated field with max value. $idusuario = filter_var($post['idusuario'], FILTER_VALIDATE_INT);
                $nombre         = filter_var($body['nombre'], FILTER_SANITIZE_STRING);
                $email          = filter_var($body['email'], FILTER_SANITIZE_STRING);
                $puesto         = filter_var($body['puesto'], FILTER_SANITIZE_STRING);
                $idnivel        = $body['role'];
                $idusuario      = $body['user_id'];
                $password       = $body['password'];
                $iddepartamento = 9;


                // Valida si el usuario ya existe
                $res = $db->query("SELECT * FROM usuarios WHERE idusuario = $idusuario");
                if (sqlsrv_has_rows($res)){
                    $this->setError('Error, usuario con id ' . $idusuario . ' ya existe en el catalogo');
                    return;
                }

                // Valida contrasena
                if (strlen($password)<6){
                    $this->setError('Error, password debe ser mayor o igual a 6 digitos');
                    return;
                }


                // obtiene la sucursal
                $sucursal_id = $_SESSION['id_sucursal'];
                // si tenemos super administrador obtenemos la sucursal del sucursal_id
                if (isset($_SESSION['nivel']) && $_SESSION['nivel']===1){
                    if (isset($body['sucursal'])){
                        $sucursal_id = $body['sucursal'];
                    }
                    else {
                        $this->setError('Error, usuario administrador proporcione el id de la sucursal');
                        return;
                    }
                }
                // Valida si la sucursal existe (no esta relacionada la tabla)
                $res2 = $db->query("SELECT * FROM sucursal WHERE id = $sucursal_id");
                if (!sqlsrv_has_rows($res2)){
                    $this->setError('Error, sucursal con id ' . $sucursal_id . ' no existe en catalogo sucursales');
                    return;
                }


                // Sacamos la sucursal
                $sql = sprintf("" .
                        " INSERT INTO usuarios " .
                        " ( idusuario, nombre,  puesto, password, idnivel, usuariocorreoe, iddepartamento, sucursal) " .
                        " VALUES " .
                        " ( %s, '%s', '%s', '%s', %s, '%s', %s, %s); SELECT SCOPE_IDENTITY() ",
                    $idusuario,
                    $nombre,
                    $puesto,
                    base64_encode('tsv_pass_hash'.$password),
                    $idnivel,
                    $email,
                    $iddepartamento,
                    $sucursal_id
                );
                //echo $sql; exit();
                $lid = $db->query($sql, true);
                return  array('lid'=>$lid);

            } else {
                //throw new Exception('Error de validacion de datos');
                $this->setError('Error de validacion de datos');
            }
        } catch (Exception $e){
            //
            $this->setError($e);
        };
    }









    /*
     *
     *
     *
     *
     */
    function PostRegisterUser(){
        try{
            // Get Tools & Utilities
            $db         = $this->getDB();
            $helper     = $this->getHelper();
            $utils      = $helper->getTools('utilities');
            $body       = $utils->JSONToArray($this->getRequestBody());

            //
            if (
                isset($body['nombre']) &&
                isset($body['email']) &&
                isset($body['user_id']) && is_numeric($body['user_id']) &&
                isset($body['password'])
            ){

                // Mandatory fields
                // auto generated field with max value. $idusuario = filter_var($post['idusuario'], FILTER_VALIDATE_INT);
                $nombre         = filter_var($body['nombre'], FILTER_SANITIZE_STRING);
                $email          = filter_var($body['email'], FILTER_SANITIZE_STRING);
                $idusuario      = $body['user_id'];
                $password       = $body['password'];


                // Valida si el usuario ya existe
                $res = $db->query("SELECT * FROM usuarios WHERE idusuario = $idusuario");
                if (sqlsrv_has_rows($res)){
                    $this->setError('Error, usuario con id ' . $idusuario . ' ya existe en el catalogo');
                    return;
                }

                // Valida contrasena
                if (strlen($password)<6){
                    $this->setError('Error, password debe ser mayor o igual a 6 digitos');
                    return;
                }

                // Sacamos la sucursal
                //
                $sql = sprintf("" .
                        " INSERT INTO usuarios " .
                        " ( idusuario, nombre,  puesto, password, idnivel, usuariocorreoe, iddepartamento, sucursal) " .
                        " VALUES " .
                        " ( %s, '%s', 's/a', '%s', 2, '%s', 9, 1); SELECT SCOPE_IDENTITY()",
                    $idusuario,
                    $nombre,
                    base64_encode($password),
                    $email
                );
                //echo $sql; exit();
                $lid = $db->query($sql, true);
                return  array('lid'=>$lid);

            } else {
                //throw new Exception('Error de validacion de datos');
                $this->setError('Error de validacion de datos');
            }
        } catch (Exception $e){
            //
            $this->setError($e);
        };
    }










    /*
     *
     *
     *
     *
     */
    function PostBaja(){
        try{
            // Get Tools & Utilities
            $db         = $this->getDB();
            $helper     = $this->getHelper();
            $utils      = $helper->getTools('utilities');
            $body       = $utils->JSONToArray($this->getRequestBody());

            //
            if (
                isset($body['baja_id']) && is_numeric($body['baja_id'])
            ){

                // Mandatory fields
                // auto generated field with max value. $idusuario = filter_var($post['idusuario'], FILTER_VALIDATE_INT);
                $baja_id         = $body['baja_id'];


                // Valida si el usuario ya existe
                $res = $db->query("SELECT * FROM usuarios WHERE idusuario = $baja_id");
                if (!sqlsrv_has_rows($res)){
                    $this->setError('Error, usuario con id ' . $baja_id . ' no existe para baja');
                    return;
                }


                // halt
                $this->setError('Error, no se puede generar baja');
                return;


                // damos de baja
                $sql = sprintf("UPDATE usuarios SET
                    activo = 0
                    WHERE idusuario = %s",
                    $baja_id
                );
                //echo $sql; exit();
                $lid = $db->query($sql, true);

                return  array('lid'=>$baja_id);

            } else {
                //throw new Exception('Error de validacion de datos');
                $this->setError('Error de validacion de datos');
            }
        } catch (Exception $e){
            //
            $this->setError($e);
        };
    }










    /*
     *
     *
     *
     *
     */
    function PutUser(){
        try{
            // Get Tools & Utilities
            $db         = $this->getDB();
            $helper     = $this->getHelper();
            $utils      = $helper->getTools('utilities');
            $body       = $utils->JSONToArray($this->getRequestBody());

            //
            if ( ($idusuario = $this->getSegmentValue(1)) && is_numeric($this->getSegmentValue(1)) ){

                //
                if (
                    isset($body['nombre']) &&
                    isset($body['email']) &&
                    isset($body['puesto']) &&
                    isset($body['role']) && is_numeric($body['role']) &&
                    isset($body['password'])
                ){
                    // Mandatory fields
                    // auto generated field with max value. $idusuario = filter_var($post['idusuario'], FILTER_VALIDATE_INT);
                    $nombre         = filter_var($body['nombre'], FILTER_SANITIZE_STRING);
                    $email          = filter_var($body['email'], FILTER_SANITIZE_STRING);
                    $puesto         = filter_var($body['puesto'], FILTER_SANITIZE_STRING);
                    $idnivel        = $body['role'];
                    $iddepartamento = 9;

                    // define password
                    $password = false;
                    if (!empty($body['password'])){
                        if (strlen($body['password'])<6){
                            $this->setError('Error, password debe ser mayor o igual a 6 digitos');
                            return;
                        }
                        else {
                            $password = $body['password'];
                        }
                    }

                    // Valida si el usuario ya existe
                    $res = $db->query("SELECT * FROM usuarios WHERE idusuario = $idusuario");
                    if (!sqlsrv_has_rows($res)){
                        $this->setError('Error, no existe usuario con id ' . $idusuario);
                        return;
                    }


                    // obtiene la sucursal
                    $sucursal_id = $_SESSION['id_sucursal'];
                    // si tenemos super administrador obtenemos la sucursal del sucursal_id
                    if (isset($_SESSION['nivel']) && $_SESSION['nivel']===1){
                        if (isset($body['sucursal'])){
                            $sucursal_id = $body['sucursal'];
                        }
                        else {
                            $this->setError('Error, usuario administrador proporcione el id de la sucursal');
                            return;
                        }
                    }
                    // Valida si la sucursal existe (no esta relacionada la tabla)
                    $res2 = $db->query("SELECT * FROM sucursal WHERE id = $sucursal_id");
                    if (!sqlsrv_has_rows($res2)){
                        $this->setError('Error, sucursal con id ' . $sucursal_id . ' no existe en catalogo sucursales');
                        return;
                    }

                    // Actualiza Password?
                    if ($password){
                        // Sacamos la sucursal
                        $sql = sprintf("
                        UPDATE usuarios SET
                        nombre = '%s',
                        puesto = '%s',
                        usuariocorreoe = '%s',
                        idnivel = %s,
                        iddepartamento = %s,
                        sucursal = %s,
                        password = '%s'
                        WHERE idusuario = %s
                    ",
                            $nombre,
                            $puesto,
                            $email,
                            $idnivel,
                            $iddepartamento,
                            $sucursal_id,
                            base64_encode('tsv_pass_hash'.$password),
                            $idusuario
                        );
                    }

                    // No Actualiza Password
                    else {
                        // Sacamos la sucursal
                        $sql = sprintf("
                        UPDATE usuarios SET
                        nombre = '%s',
                        puesto = '%s',
                        usuariocorreoe = '%s',
                        idnivel = %s,
                        iddepartamento = %s,
                        sucursal = %s
                        WHERE idusuario = %s
                    ",
                            $nombre,
                            $puesto,
                            $email,
                            $idnivel,
                            $iddepartamento,
                            $sucursal_id,
                            $idusuario
                        );
                    }
                    //echo $sql; exit();
                    $qry = $db->query($sql);
                    /*
                    $rows_affected = sqlsrv_rows_affected($qry);
                    if( $rows_affected === false) {
                        die( print_r( sqlsrv_errors(), true));
                    } elseif( $rows_affected == -1) {
                        echo "No information available.<br />";
                    } else {
                        echo $rows_affected." rows were updated.<br />";
                    }
                    */
                    return  array('upd'=>true);

                }
                else {
                    $this->setError('Error, proporcione parametros para edicion');
                    return;
                }
            }
            $this->setError('Error, proporcione id del usuario para editar');
            return;
        } catch (Exception $e){
            $this->setError($e);
        };
    }













    /*
    function deleteUser(){
        try{
            //
            if ( $id = $this->getSegmentValue(1) ){
                // Get Tools & Utilities
                $db         = $this->getDB();
                $helper     = $this->getHelper();
                $utils      = $helper->getTools('utilities');
                $body       = $utils->JSONToArray($this->getRequestBody());

                //
                $user_found = false;
                //
                $find_deleted = "SELECT * FROM usuarios WHERE idusuario = {$id}";
                $res = $db->query($find_deleted);
                //
                if (sqlsrv_has_rows($res)){
                    //
                    $sql = sprintf("" .
                        " DELETE FROM usuarios " .
                        " WHERE idusuario = %s ",
                        $id
                    );
                    //
                    $db->query($sql);
                    return array('deleted_id'=>$id);

                } else {
                    //throw new Exception('Error de validacion de datos');
                    $this->setError('Registro a eliminar no encontrado');
                }

            } else {
                //throw new Exception('Error de validacion de datos');
                $this->setError('Error de validacion de datos');
            }
        } catch (Exception $e){
            //
            $this->setError($e);
        }
    }
    */












}