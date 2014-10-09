<?php
/*
 *
 *
 *
 *
 */
class RdmsModel extends Model{



    /*
     *
     *
     *
     */
    function GetRequisiciones(){
        try{
            /* Get Db & Tools */
            $db         = $this->getDB();
            $utils      = $this->getHelper();
            $database_tools = $utils->getTools('database');

            // array results
            $results = array(); //Total count sql

            //
            if (isset($_SESSION['logged']) && $_SESSION['depto']){
                //
                //print_r($_SESSION);exit();
                $departamento_id = $_SESSION['depto'];
            }

            else {

                return;
            }

            $suc_id = $_SESSION['id_sucursal'];
            

            $scl_clause = "
                                   
                        SELECT rm.idrdm as id, rm.eco as unidad, est.name as prev_status, CONVERT(CHAR, rest.fecha, 21) as fecha_estatus , CONVERT(CHAR, rm.prioridad, 21) as datetime_prioridad_requisicion, con.tipo as concepto_tipo, CONVERT(CHAR,ins.fecha, 21) as fecha_insercion, rest.codigo, mec.nombre as mecanico
                        FROM rdm.dbo.rdm rm 
                        LEFT JOIN rdm.dbo.rdm_estatus rest ON rest.idrdm = rm.idrdm and rest.id_rdm_estatus = (select max(rest.id_rdm_estatus) from rdm.dbo.rdm_estatus rest where rest.idrdm = rm.idrdm)
                        LEFT JOIN rdm.dbo.estatus est ON  est.codigo = rest.codigo
                        LEFT JOIN rdm.dbo.conceptos_rdm  con ON con.concepto_id = rm.concepto_id 
                        JOIN (SELECT fecha, idrdm FROM rdm.dbo.rdm_estatus WHERE codigo =10) ins ON ins.idrdm = rm.idrdm
                        LEFT JOIN mecanicos mec ON mec.numeromecanico = rm.mecanico_id  
                        WHERE  rm.depto_id = $departamento_id AND rm.sucursalid = $suc_id
                        AND year(rest.fecha) = '2014' ORDER by rest.codigo ASC
            ";                    
            //echo $scl_clause; exit();
            $query = $db->query($scl_clause);
            $results['rdm'] = array();
            //
            while($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                array_push( $results['rdm'],  $row);
            }


            return $results;
        }
        catch (Exception $e){
            $this->setError($e);
        };
    }


    function GetRequisicionesHistorial(){

        try {
            $db         = $this->getDB();
            $utils      = $this->getHelper();
            $database_tools = $utils->getTools('database');

            $results = array();
            $rid = $this->getSegmentValue(1);
            //echo $rid; exit();
            //
            if (isset($_SESSION['logged']) && $_SESSION['depto']){
                //

                $departamento_id = $_SESSION['depto'];
            }

            else {

                return;
            }

            if(isset($rid) && $rid != ''){
                $sql="   
                        SELECT rm.idrdm as id, rm.eco as unidad, est.name as prev_status, CONVERT(CHAR, rest.fecha, 21) as fecha_estatus, CONVERT(CHAR, rm.prioridad, 21) as datetime_prioridad_requisicion, con.tipo as concepto_tipo, CONVERT(CHAR,ins.fecha, 21) as fecha_insercion, rest.codigo
                        FROM rdm.dbo.rdm rm 
                        LEFT JOIN rdm.dbo.rdm_estatus rest ON rest.idrdm = rm.idrdm 
                        LEFT JOIN rdm.dbo.estatus est ON  est.codigo = rest.codigo
                        LEFT JOIN rdm.dbo.conceptos_rdm  con ON con.concepto_id = rm.concepto_id 
                        JOIN (SELECT fecha, idrdm FROM rdm.dbo.rdm_estatus WHERE codigo =10) ins ON ins.idrdm = rm.idrdm 
                        WHERE  rm.depto_id = $departamento_id AND rm.idrdm = $rid
                        AND year(rest.fecha) = '2014' ORDER by rest.codigo ASC";
                         
                $query = $db->query($sql);
                $results['rdmhistory'] = array();
                while($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                    array_push( $results['rdmhistory'],  $row);
                }

                return $results;        

            }
            else{
                return;
            }    

        } catch (Exception $e) {
            $this->setError($e);
        }
    }




    /*
     *
     *
     *
     */
    function GetUnasignedRequisiciones(){
        try{

            /* Get Db & Tools */
            $db         = $this->getDB();
            $utils      = $this->getHelper();
            $database_tools = $utils->getTools('database');

            // array results
            $results = array(); //Total count sql


            /* Get page number and rows to display */
            $page_no = ($this->getSegmentParamValue( 0, 'page' )) ? $this->getSegmentParamValue( 0, 'page' ) : 1;
            $page_size = ($this->getSegmentParamValue( 0, 'rows' )) ? $this->getSegmentParamValue( 0, 'rows' ) : 25;


            /* Get records Order*/
            $order_by_clause = 'ORDER BY id DESC'; // default if none provided
            //


            //
            if (isset($_SESSION['logged']) && $_SESSION['depto']){
                //
                $departamento_id = $_SESSION['depto'];
            }

            else {
                return;
            }



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
            $search_date = null;

            //
            $filter_status = 'NINGUNO';

            // Default filter initial values
            $filter_clause = null;


            // Filtros
            if ( $this->getSegmentParamValue( 0, '_search' ) && ($this->getSegmentParamValue( 0, '_search' )!='false') ){

                //
                /*
                if ( $departamento_id = $this->getSegmentParamValue( 0, 'departamento_id' ) ) {
                    $search_clause .= " AND departamento_id = $departamento_id ";
                }
                */
                // con Folio de requisicion = id
                if ( $no_unidad = $this->getSegmentParamValue( 0, 'no_unidad' ) ) {
                    $filter_clause .= " AND rm.eco = '$no_unidad' ";
                }

                //
                if ( ($this->getSegmentParamValue( 0, 'ds' )) && ($this->getSegmentParamValue( 0, 'de' ))) {

                    //
                    $ds =  urldecode($this->getSegmentParamValue( 0, 'ds' ));
                    $de =  urldecode($this->getSegmentParamValue( 0, 'de' ));

                    //
                    $date_start_arr = explode('/', $ds);
                    $ds_d = $date_start_arr[0];
                    $ds_m = $date_start_arr[1];
                    $ds_y = $date_start_arr[2];

                    //
                    $date_end_arr = explode('/', $de);
                    $de_d = $date_end_arr[0];
                    $de_m = $date_end_arr[1];
                    $de_y = $date_end_arr[2];

                    //
                    $date_start = $ds_y.$ds_m.$ds_d;
                    $date_end   = $de_y.$de_m.$de_d;

                    //
                    $filter_clause .= " AND CAST(rm.fecha as DATE) >= '$date_start' AND CAST(rm.fecha as DATE) <= '$date_end' ";
                }
            }

            $suc_id = $_SESSION['id_sucursal'];
            $where_sucursal_clause = " WHERE ts.id = $suc_id ";
            if (isset($_SESSION['nivel']) && $_SESSION['nivel']===1){
                $where_sucursal_clause = '';
            }

            /* Do Total Query*/
            $sql_total = "
                    SELECT
                        COUNT(*) total
                        FROM
                            (SELECT
                                 ROW_NUMBER() OVER ( ORDER BY id desc) as row
                                ,*
                                FROM
                                    (SELECT

                                        -- Numero de Requisicion (folio)
                                         distinct rm.id

                                        -- Unidad para la que se solicita la requisicion
                                        ,rm.unidad

                                        -- Fecha de requisicion, de prioridad y visto
                                        ,CONVERT(CHAR,rm.prioridad,21) As datetime_prioridad_requisicion
                                        ,CONVERT(CHAR,rm.fecha_visto,21) As fecha_visto
                                        ,CONVERT(CHAR,rm.fecha,21) As fecha_requisicion

                                        -- descripcion de la requisicion
                                        ,rm.descripcion

                                        ,rm.depto_id depto_id
                                        ,td.nombre depto_nombre

                                        ,rm.usuario_id usuario_id
                                        ,tu.nombre usuario_nombre

                                        -- Concepto para que se requiere generar la requisicion
                                        ,tc.id concepto_id
                                        ,tc.tipo concepto_tipo

                                        -- Incluye muestras para la requisicion?
                                        ,rm.incluye_muestra

                                        -- Observacion de la requisicion
                                        ,rm.observacion

                                        -- Estatus de la requisicion
                                        ,rm.estado prev_status

                                        FROM requisicion_materiales rm
                                            --
                                            LEFT JOIN requisicion_materiales_articulos rma ON rm.id = rma.rdm_id
                                            LEFT JOIN departamentos td ON td.iddepartamento = rm.depto_id
                                            LEFT JOIN usuarios tu ON tu.idusuario = rm.usuario_id

                                            --
                                            LEFT JOIN conceptos_rdm tc  ON tc.id = rm.concepto_id
                                            WHERE rm.estado <> 'CANCELADO'
                                            ) t
                                        )t
                                ";
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
            $where_row_clause = " AND row BETWEEN $start AND $limit ";
            /*
            $where_row_clause = null;
            if (is_null($search_clause)){
                $where_row_clause = " AND row BETWEEN $start AND $limit ";
            }
            */

            /* Get Table Fields */
            $table_fields = "id, unidad, depto_id, depto_nombre,no_oc, no_factura, datetime_prioridad_requisicion, fecha_visto, descripcion, concepto_id, concepto_tipo, incluye_muestra, observacion, prev_status, fecha_requisicion, usuario_id, usuario_nombre, sucursal_id, sucursal_shortname, pc, usuario_modifica_id, usuario_modifica_nombre, sucursal_modifica_id, sucursal_modifica_shortname, mecanico_id, mecanico_nombre";
            if ( $fields = $this->getSegmentParamValue( 0, 'fields' ) ){
                //
                if ( $database_tools->getSegmentFields( $fields ) ) {
                    $table_fields = $database_tools->getSegmentFields( $fields );
                }
            }

            /* Do Api Query */
            $scl_clause = "
                    SELECT
                        id, unidad, depto_id, usuario_id, usuario_nombre, depto_nombre, datetime_prioridad_requisicion, fecha_visto, descripcion, concepto_id, concepto_tipo, incluye_muestra, observacion, prev_status, fecha_requisicion
                        FROM
                            (SELECT
                                 ROW_NUMBER() OVER ( ORDER BY id desc) as row
                                ,*
                                FROM
                                    (SELECT

                                        -- Numero de Requisicion (folio)
                                         distinct rm.id

                                        -- Unidad para la que se solicita la requisicion
                                        ,rm.unidad

                                        -- Fecha de requisicion, de prioridad y visto
                                        ,CONVERT(CHAR,rm.prioridad,21) As datetime_prioridad_requisicion
                                        ,CONVERT(CHAR,rm.fecha_visto,21) As fecha_visto
                                        ,CONVERT(CHAR,rm.fecha,21) As fecha_requisicion

                                        -- descripcion de la requisicion
                                        ,rm.descripcion

                                        --
                                        ,rm.depto_id depto_id
                                        ,td.nombre depto_nombre

                                        ,rm.usuario_id usuario_id
                                        ,tu.nombre usuario_nombre

                                        -- Concepto para que se requiere generar la requisicion
                                        ,tc.id concepto_id
                                        ,tc.tipo concepto_tipo

                                        -- Incluye muestras para la requisicion?
                                        ,rm.incluye_muestra

                                        -- Observacion de la requisicion
                                        ,rm.observacion

                                        -- Estatus de la requisicion
                                        ,rm.estado prev_status

                                        FROM requisicion_materiales rm
                                            --
                                            LEFT JOIN requisicion_materiales_articulos rma ON rm.id = rma.rdm_id
                                            LEFT JOIN departamentos td ON td.iddepartamento = rm.depto_id
                                            LEFT JOIN usuarios tu ON tu.idusuario = rm.usuario_id

                                            --
                                            LEFT JOIN conceptos_rdm tc  ON tc.id = rm.concepto_id
                                            WHERE rm.estado <> 'CANCELADO'
                                            ) t
                                        )t
                                        WHERE 1=1
                                        $where_row_clause
                                ";
            //echo $scl_clause; exit();
            $query = $db->query($scl_clause);


            /* Set Results */
            $results['totalrecords']       = $total; //Total count sql
            $results['totalpages']     = $total_pages; //Total Pages
            $results['currpage']        = $page_no; //Current Page Number
            $results['page_size']        = $page_size; //Current Page Number
            //
            $results['filtro_status'] = $filter_status; // Status del filtro

            $results['rows'] = array();
            //
            while($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){

                //
                if ($row['id']){
                    $rid = $row['id'];

                    $row['partes'] = array();

                    $qry = "
                    SELECT
                             t.*
                            ,part_comply = CASE
                                WHEN t.cantidad_anexada = t.cantidad_salida
                            THEN 1
                                ELSE 0
                            END
                                FROM
                                    (SELECT
                                         rma.idrdm
                                        ,rma.idarticulo
                                        ,a.numeroparte no_parte
                                        ,a.nombrepieza descripcion
                                        ,ISNULL(rma.cantidad, 0 ) cantidad_anexada
                                        ,ISNULL(ds.cantidad, 0 ) cantidad_salida
                                        ,ds.foliosalida folio_salida
                                        FROM
                                            rdm.dbo.rdm_articulos rma
                                            LEFT JOIN almacen.dbo.articulos a ON a.idarticulo = rma.idarticulo
                                            LEFT JOIN almacen.dbo.detallessalidas ds ON ds.rdm = rma.idrdm AND ds.idarticulo = rma.idarticulo
                                            WHERE rdm_id = $rid
                                            ) t
                                        ";
                    //
                    $qry_results = $db->query($qry);
                    //
                    while($row2 = sqlsrv_fetch_array($qry_results, SQLSRV_FETCH_ASSOC)){
                        array_push( $row['partes'],  $row2);
                    }

                    //
                    $row['rdm_comply'] = false;
                    foreach($row['partes'] as $k=>$v){
                        //
                        if ($v['part_comply']){
                            $row['rdm_comply'] = true;
                        }
                    }
                }//--

                //
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
     */
    function GetRequisicionDetalles(){
        try{

            /* Get Db & Tools */
            $db         = $this->getDB();
            $utils      = $this->getHelper();
            $database_tools = $utils->getTools('database');


            if ($rid = $this->getSegmentValue(1)){


                // Depto id
                if (isset($_SESSION['logged']) && $_SESSION['depto']){
                    //
                    $departamento_id = $_SESSION['depto'];
                }
                else {
                    return;
                }


                /* Get Table Fields */
                $table_fields = "id, unidad, no_oc, no_factura, datetime_prioridad_requisicion,  descripcion, concepto_id, concepto_tipo, incluye_muestra, observacion, prev_status,  fecha_requisicion, usuario_id, usuario_nombre, sucursal_id, sucursal_shortname, pc, usuario_modifica_id, usuario_modifica_nombre, sucursal_modifica_id, sucursal_modifica_shortname, mecanico_id, mecanico_nombre";
                if ( $fields = $this->getSegmentParamValue( 0, 'fields' ) ){
                    //
                    if ( $database_tools->getSegmentFields( $fields ) ) {
                        $table_fields = $database_tools->getSegmentFields( $fields );
                    }
                }

                /* Do Api Query */
                /*$sql_clause = "
                        SELECT
                            $table_fields
                               FROM
                                (SELECT
                                     DISTINCT t.id did
                                    ,t.*
                                    FROM
                                        (SELECT

                                            -- Numero de Requisicion (folio)
                                             rm.idrdm id

                                            -- Unidad para la que se solicita la requisicion
                                            ,rm.eco as unidad

                                            -- Requisiciones de Ordenes de Compra
                                            ,no_oc = CASE
                                                WHEN roc.ordencompra IS NULL
                                                THEN NULL
                                                ELSE roc.ordencompra
                                                END

                                            -- Tiene factura la requisicion
                                            ,no_factura = CASE
                                            WHEN oc.factura IS NULL
                                            THEN NULL
                                            ELSE oc.factura
                                            END

                                            -- Fecha de requisicion, de prioridad y visto
                                            ,CONVERT(CHAR,rm.prioridad,21) As datetime_prioridad_requisicion
                                            

                                            -- descripcion de la requisicion
                                            ,rdet.descripcion

                                            -- Concepto para que se requiere generar la requisicion
                                            ,tc.concepto_id concepto_id
                                            ,tc.tipo concepto_tipo

                                            -- Incluye muestras para la requisicion?
                                            ,rdet.incluye_muestra

                                            -- Observacion de la requisicion
                                            ,rdet.observacion

                                            -- Estatus de la requisicion
                                            ,est.name prev_status

                                            -- Requisicion tiene Numeros de parte asignados?
                                            /*,con_partes = CASE
                                                WHEN rma.idarticulo IS NULL
                                                THEN 'n'
                                                  ELSE 'y'
                                                END*/

                                            //-- Requisicion tiene salidas?
                                            /*,con_salidas = CASE WHEN (SELECT COUNT(*) FROM almacen.dbo.detallessalidas sd
                                                WHERE sd.rdm = rma.idrdm
                                                ) = 0
                                                THEN 'n'
                                                    ELSE 'y'
                                                END

                                            -- genero usuario info
                                            ,CONVERT(CHAR,res.fecha,21) As fecha_requisicion
                                            ,tu.idusuario usuario_id
                                            ,tu.nombre usuario_nombre
                                            ,ts.id sucursal_id
                                            ,ts.shortname sucursal_shortname
                                            ,rm.pc

                                            -- modifico usuario info
                                            ,tu2.idusuario usuario_modifica_id
                                            ,tu2.nombre usuario_modifica_nombre
                                            ,ts2.id sucursal_modifica_id
                                            ,ts2.shortname sucursal_modifica_shortname

                                            -- genero departamento info

                                            -- solicito mecanico info
                                            ,tm.numeromecanico mecanico_id
                                            ,tm.nombre mecanico_nombre

                                            FROM rdm.dbo.rdm rm
                                                --
                                                LEFT JOIN rdm.dbo.rdm_detalles rdet ON rdet.idrdm = rm.idrdm
                                                --
                                                LEFT JOIN rdm.dbo.rdm_estatus res ON res.idrdm = rm.idrdm
                                                LEFT JOIN rdm.dbo.estatus est ON est.codigo = res.codigo 
                                                --
                                                LEFT JOIN compras.dbo.requisicionesdeordenescompra roc ON roc.foliorequisicion = rm.idrdm
                                                LEFT JOIN compras.dbo.ordenescompra oc ON oc.ordencompra = roc.ordencompra
                                                --
                                                LEFT JOIN rdm.dbo.rdm_articulos rma ON rm.idrdm = rma.idrdm
                                                LEFT JOIN almacen.dbo.articulos     a   ON a.idarticulo = rma.idarticulo
                                                --
                                                LEFT JOIN mecanicos     tm  ON tm.numeromecanico = rm.mecanico_id
                                                --
                                                LEFT JOIN usuarios      tu  ON tu.idusuario = rm.usuario_id
                                                LEFT JOIN sucursal      ts  ON ts.id = tu.sucursal
                                                --
                                                LEFT JOIN usuarios      tu2 ON tu2.idusuario = rm.usuario_id
                                                LEFT JOIN sucursal      ts2 ON ts2.id = tu2.sucursal
                                                --
                                                LEFT JOIN rdm.dbo.conceptos_rdm tc  ON tc.concepto_id = rm.concepto_id
                                                WHERE rm.depto_id =  $departamento_id) t
                                                    ) m
                                                -- filtro
                                                WHERE 1=1
                                                AND id = $rid;
                                                ";*/
                //echo $sql_clause; exit();

                $sql_clause="
                    SELECT  rdet.idrdm as id ,rdet.idrdm_detalles, rdet.numeroparte, rdet.cantidad, rdet.descripcion, est.name as estatus, CONVERT(CHAR,rdest.fecha,21) as fecha_estatus, est.codigo  FROM rdm.dbo.rdm_detalles rdet
                    LEFT JOIN rdm.dbo.rdm_detalles_estatus rdest ON rdest.idrdm_detalles = rdet.idrdm_detalles and rdest.id_rdm_estatus_detalles = (select max(rdest.id_rdm_estatus_detalles) from rdm.dbo.rdm_detalles_estatus rdest where rdest.idrdm_detalles = rdet.idrdm_detalles)
                    LEFT JOIN rdm.dbo.estatus est ON est.codigo = rdest.codigo  
                    WHERE idrdm = $rid AND rdet.activo = 1
                "; 
                //echo $sql_clause; exit();                               
                $query = $db->query($sql_clause);

                //
                $results = array();
                while($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                    array_push( $results,  $row);
                }

               /* $partes = array();
                //
                /*if ( count($results) > 0 ){

                    // Verificamos si la requisicion ya tiene partes anexas
                    if ($results[0]['id'] ){
                        
                        /*$qry = "
                                SELECT
                                 t.*
                                ,comply = CASE
                                    WHEN t.cantidad_anexada = t.cantidad_salida
                                THEN 1
                                    ELSE 0
                                END
                                    FROM
                                        (SELECT
                                             --rma.rdm_id
                                             rma.idarticulo
                                            ,a.numeroparte no_parte
                                            ,a.nombrepieza descripcion
                                            ,ISNULL(rma.cantidad, 0 ) cantidad_anexada
                                            ,ISNULL(ds.salidas, 0 ) cantidad_salida
                                            FROM
                                                rdm.dbo.rdm_articulos rma
                                                LEFT JOIN almacen.dbo.articulos a ON a.idarticulo = rma.idarticulo
                                                LEFT JOIN (SELECT idarticulo, rdm, SUM(cantidad) salidas FROM almacen.dbo.detallessalidas group by idarticulo, rdm) ds ON ds.rdm = rma.idrdm AND ds.idarticulo = rma.idarticulo
                                                WHERE idrdm = $rid
                                                ) t
                                        ";*/

                        /* $qry= "
                              
                                SELECT art.numeroparte, rdet.descripcion, est.name as estado, rdet.observacion FROM rdm.dbo.rdm_detalles rdet
                                --
                                LEFT JOIN rdm.dbo.rdm_detalles_Estatus rdest ON rdest.idrdm_detalles = rdet.idrdm_detalles
                                LEFT JOIN rdm.dbo.rdm_estatus rest ON rest.codigo = rdest.codigo
                                LEFT JOIN rdm.dbo.estatus est ON est.codigo = rest.codigo
                                --
                                LEFT JOIN rdm.dbo.rdm_articulos rart ON rart.idrdm = rdet.idrdm
                                LEFT JOIN almacen.dbo.articulos art ON art.idarticulo = rart.idarticulo
                                WHERE rdet.idrdm = $rid
                         ";              
                         //echo $qry; exit();               
                        //
                        $qry_results = $db->query($qry);

                        while($row = sqlsrv_fetch_array($qry_results, SQLSRV_FETCH_ASSOC)){
                            array_push( $partes,  $row);
                        }

                        $results[0]['partes'] = $partes;
                    }
                }*/
                //
                return $results;
            }

            else {
                $this->setError('Error, proporcione id de la requisicion');
            }



        }
        catch (Exception $e){
            $this->setError($e);
        };
    }






    /*
     *
     *
     *
     */
    function GetArticulo(){
        try{

            /* Get Db & Tools */
            $db         = $this->getDB();
            $utils      = $this->getHelper();
            $database_tools = $utils->getTools('database');


            if ($rid = $this->getSegmentValue(1)){


                /* Get Table Fields */
                $table_fields = "ta.idarticulo, ta.numeroparte, ta.nombrepieza, ta.localizaciones, ta.clasificacionporlote";
                if ( $fields = $this->getSegmentParamValue( 0, 'fields' ) ){
                    //
                    if ( $database_tools->getSegmentFields( $fields ) ) {
                        $table_fields = $database_tools->getSegmentFields( $fields );
                    }
                }

                /* Do Api Query */
                $sql_clause = "
                    SELECT
                         rma.idrdm rdm_id
                        ,rma.idarticulo articulo_id
                        ,a.numeroparte no_parte
                        ,a.nombrepieza nombre_pieza
                        ,ISNULL(rma.cantidad, 0 ) cantidad_solicitada
                        ,ISNULL(ds.salidas, 0 ) cantidad_salida
                        ,a.clasificacionporlote por_lote
                        ,loc.localizacion familia
                        FROM
                            rdm.dbo.rdm_articulos rma
                            LEFT JOIN almacen.dbo.articulos a ON a.idarticulo = rma.idarticulo
                            LEFT JOIN almacen.dbo.localizaciones loc ON loc.idlocalizacion = a.idlocalizacion
                            -- hacemos el join con el id del articulo y de la requisicion. se debe de agrupar inicialmente para obtener la suma de cada una de las salidas que se han realizado
                            LEFT JOIN (SELECT idarticulo, rdm, SUM(cantidad) salidas FROM almacen.dbo.detallessalidas group by idarticulo, rdm) ds ON ds.rdm = rma.idrdm AND ds.idarticulo = rma.idarticulo
                            WHERE rdm_id = $rid";

                //echo $sql_clause; exit();
                $query = $db->query($sql_clause);

                //
                $results = array();
                while($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                    array_push( $results,  $row);
                }

                //
                return $results;
            }

            else {
                $this->setError('Error, proporcione id de la requisicion');
            }



        }
        catch (Exception $e){
            $this->setError($e);
        };
    }






    function PostRequisicion(){
        try{
            // Get Helpers & Tools
            $db         = $this->getDB();
            $utils      = $this->getHelper();
            $database_tools = $utils->getTools('database');
            $post_data = $this->getPostData();
           // print_r($post_data);exit();
            // Validacion concepto id
            if ( !(isset($post_data['concepto_id']) && is_numeric($post_data['concepto_id'])) ){$this->setError('Error, proporcione el id del concepto'); return; }
            if ( !(isset($post_data['mecanico_id']) && is_numeric($post_data['mecanico_id'])) ){$this->setError('Error, proporcione el id del mecanico'); return; }
            if ( !(isset($post_data['no_unidad'])) ){$this->setError('Error, proporcione el numero de la unidad'); return; }
            if ( !(isset($post_data['tipo_equipo'])) ){$this->setError('Error, proporcione el tipo de equipo'); return; }
            if ( !(isset($post_data['descripcion'])) ){$this->setError('Error, proporcione la descripcion de la requisicion'); return; }
            if ( !(isset($post_data['fecha_prioridad'])) ){$this->setError('Error, proporcione la fecha y hora de la prioridad'); return; }

            // Obtenemos las variables listas para insercion
            $descripcion = $post_data['descripcion'];
            $concepto_id = $post_data['concepto_id'];
            $tipo_equipo = $post_data['tipo_equipo'];
            $fecha_prioridad = $post_data['fecha_prioridad'];
            $mecanico_id = $post_data['mecanico_id'];

            $no_unidad = strtoupper($post_data['no_unidad']);

            // Incluye muestra?
            $incluye_muestra = 0;
            if ( isset($post_data['incluye_muestra']) && $post_data['incluye_muestra']==1 ){
                $incluye_muestra = 1;
            }

            // Datos del usuario que genero el registro
            $usuario_id = $_SESSION['uid'];
            $depto_id = $_SESSION['depto'];
            $sucursal_id = $_SESSION['idsucursal'];

            // http://stackoverflow.com/questions/4262081/serverremote-addr-gives-server-ip-rather-than-visitor-ip
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
                $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
            }

            //

            /*
            $query = sprintf("
                INSERT INTO rdm.dbo.rdm_materiales
                (fecha, concepto_id, prioridad, usuario_id, mecanico_id, unidad, descripcion, fecha_modificacion, incluye_muestra, depto_id, pc)
                  VALUES
                (GETDATE(), %s, '%s', %s, %s, '%s', '%s', GETDATE(), %s, %s, '%s'); SELECT SCOPE_IDENTITY()",
                $concepto_id,
                $fecha_prioridad,
                $usuario_id,
                $mecanico_id,
                $no_unidad,
                $descripcion,
                $incluye_muestra,
                $depto_id,
                $ipAddress
            );*/
            $fr = date('Y-m-d H:i:s');
            $fecha_reg = $fr;

            $query = sprintf("
                    INSERT INTO rdm.dbo.rdm(concepto_id,eco,prioridad,usuario_id,mecanico_id,pc,depto_id,sucursalid,activo)
                    VALUES(%s, '%s', '%s', %s, %s, '%s', %s, %s, 1)",
                    $concepto_id,
                    $no_unidad,
                    $fecha_prioridad,
                    $usuario_id,
                    $mecanico_id,
                    $ipAddress,
                    $depto_id,
                    $sucursal_id
                    );
            //echo $query;exit();
            try {
                $db->query($query);

            } catch (Exception $e) {
                return $e;
            }
            
            //obtener id de la requisicion
            $idrdm = " (SELECT TOP 1 idrdm FROM rdm.dbo.rdm order by idrdm desc)";


            $sql= "SELECT TOP 1 idrdm FROM rdm.dbo.rdm order by idrdm desc";
            $query = $db->query($sql);

            while($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                    $idrdm2 = $row['idrdm'];
            }


            /*$query = "
            INSERT INTO rdm.dbo.rdm_estatus(idrdm,codigo,fecha,comentario,usuario_id,activo)
            VALUES($idrdm, 10,'$fecha_reg','',$usuario_id,1)";

            try {
                $db->query($query);

            } catch (Exception $e) {
                return $e;
            }

            $idrdm_detalles= " (SELECT TOP 1 idrdm_detalles FROM rdm.dbo.rdm_detalles order by idrdm_detalles desc
            )";*/


           /* $query = "
            INSERT INTO rdm.dbo.rdm_detalles_estatus(idrdm_detalles,codigo,fecha,comentario,usuario_id,activo)
            VALUES($idrdm_detalles,10,'$fecha_reg','',$usuario_id,1)
            ";*/
            //echo $query; exit();
          
            //$query= "Exec rdm.dbo.proc_insertar_estatus_req $idrdm2, $usuario_id ";


            $query = "
                  
    
   
                INSERT INTO rdm.dbo.rdm_estatus
                (
                idrdm,
                codigo,
                fecha,
                comentario,
                usuario_id,
                activo
                )
                VALUES
                (
                $idrdm2, 
                10,
                GETDATE(),
                '',
                $usuario_id,
                1
                )
    
            ";
           // echo $query; exit();
            try {
                
                $db->query($query);

            } catch (Exception $e) {
                
                return $e;
            }

            foreach ($descripcion as $key => $value) {

                $descripcion = $value['descripcion'];
                $cantidad = $value['cantidad'];
                $query = "
                    INSERT INTO rdm.dbo.rdm_detalles(idrdm,observacion,incluye_muestra,numeroparte,cantidad,descripcion,activo)
                    VALUES($idrdm,'',$incluye_muestra,'NA' ,$cantidad,'$descripcion',1)";
                
                $query.="

                     
                     DECLARE @idrdm_detalles INT SET @idrdm_detalles  = (SELECT TOP 1 idrdm_detalles FROM rdm.dbo.rdm_detalles order by idrdm_detalles desc)
      
                      INSERT INTO rdm.dbo.rdm_detalles_estatus
                      (
                      idrdm_detalles,
                      codigo,
                      fecha,
                      comentario,
                      usuario_id,
                      activo
                      )
                    VALUES
                    (
                    @idrdm_detalles,
                    10,
                    GETDATE(),
                    '',
                    $usuario_id,
                    1
                    )"; 
                   //echo $query; exit();   
                try {
                
                    $db->query($query);
                    
                } catch (Exception $e) {
                    
                    return $e;
                }    

            }
          

/*
            // Si es Tractor
            if($tipo_equipo == 't'){

                // For tractores
                $query = sprintf("
                      INSERT INTO rdm.dbo.requisicion_materiales_relaciones
                      (tractor_id, req_mat_id)
                        VALUES
                      ((SELECT eco FROM tractores WHERE eco = '%s'), %s)",
                    $no_unidad,
                    $idrdm
                );
                //echo $query; exit();
                $db->query($query);

            }

            // Si es Trailer/Caja
            else if($tipo_equipo == 'c'){

                // For trailers/cajas
                $query = sprintf("
                      INSERT INTO rdm.dbo.requisicion_materiales_relaciones
                      (caja_id, req_mat_id)
                        VALUES
                      ('%s', %s)",
                    $no_unidad,
                    $idrdm
                );
                //echo $query; exit();
                $db->query($query);

            }

            // Si es Otro
            else{

                // For Others
                $query = sprintf("
                      INSERT INTO rdm.dbo.requisicion_materiales_relaciones
                      (otro, req_mat_id)
                        VALUES
                      ('%s', %s)",
                    $no_unidad,
                    $idrdm
                );
                //echo $query; exit();
                $db->query($query);
            }*/

            // Devuelve el lid de la requisicion
            return array('rdm_lid' => $idrdm2 );

        } catch (Exception $e){
            //
            $this->setError($e);
            return;
        }
    }





    function PostCancelaRequisicion(){
        try{
            // Get Helpers & Tools
            $db         = $this->getDB();
            $helper     = $this->getHelper();
            $utils      = $helper->getTools('utilities');
            $body_data       = $utils->JSONToArray($this->getRequestBody());

            //
            $usuario_id = $_SESSION['uid'];
            $depto_id = $_SESSION['depto'];


            // Validacion concepto id
            if ( !(isset($body_data['rid']) && is_numeric($body_data['rid'])) ){$this->setError('Error, proporcione el id de la requisicion'); return; }
            if ( !(isset($body_data['just'])) ){$this->setError('Error, proporcione la justificacion de la requisicion'); return; }

            // Obtenemos las variables listas para insercion
            $rid = $body_data['rid'];
            $just = $body_data['just'];


            //
            $query = " SELECT id, estado FROM requisicion_materiales rm WHERE rm.id = $rid AND rm.estado = 'CANCELADO' ";
            $res = $db->query($query);
            if (sqlsrv_has_rows($res)){
                $this->setError('Error, requisicion ya ha sido cancelada previamente'); return;
            }

            //
            $query = "SELECT *
                        FROM requisicion_materiales rm
                        LEFT JOIN requisicion_materiales_articulos rma ON rma.rdm_id = rm.id
                        WHERE rm.id = $rid";
            //
            $res = $db->query($query);

            if (sqlsrv_has_rows($res)){
                $this->setError('Error, no se pueden cancelar requisiciones que ya cuenten con salida de articulos'); return;
            }

            return;

            $query = "UPDATE requisicion_materiales SET estado = 'CANCELADO' WHERE id = ".$rid;
            $db->query($query);

            // Devuelve el lid de la requisicion
            return array('rdm_id_cancelada' => $rid);

        } catch (Exception $e){
            //
            $this->setError($e);
            return;
        }
    }



    /*
    *
    *
    *
    */
    function GetDetallesHistorial(){
        try {
            /* Get Db & Tools */
            $db         = $this->getDB();
            $utils      = $this->getHelper();
            $database_tools = $utils->getTools('database');


            if ($rid = $this->getSegmentValue(1)){

                //detalle de las requisiciones

                /* Get Table Fields */
                $table_fields = "adetsal.cantidad, rdet.idrdm_detalles, sal.quiensolicita, sal.eco, sal.numeromecanico, adetsal.rdm, CONVERT(CHAR, sal.fecha, 21) as fecha_estatus, sal.idfoliosalida as salida ";

                /* Do Api Query */
                $sql_clause = "
                    SELECT
                    $table_fields
                    FROM
                    rdm.dbo.rdm_articulos rart
                    LEFT JOIN rdm.dbo.rdm_detalles rdet ON rart.idrdm_detalles = rdet.idrdm_detalles 
                    JOIN almacen.dbo.detallessalidas adetsal ON adetsal.idarticulo = rart.idarticulo AND rdet.idrdm = adetsal.rdm
                    LEFT JOIN almacen.dbo.salidas sal ON sal.idfoliosalida = adetsal.idfoliosalida 
                    WHERE rdet.idrdm_detalles = $rid";

                //echo $sql_clause; exit();
                $query = $db->query($sql_clause);

                //
                $results = array();
                $results['salidas'] = array();
                while($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                    array_push( $results['salidas'],  $row);
                };
                //print_r($results);exit();
                //detalles salidas

                /* Get Table Fields */
                $table_fields = "CONVERT(CHAR, rdest.fecha, 21) as fecha_estatus, rest.name as estatus, rdest.codigo, rdest.idrdm_detalles";

                /* Do Api Query */
                $sql_clause = "
                    SELECT
                    $table_fields
                    FROM
                    rdm.dbo.rdm_detalles_estatus rdest
                    LEFT JOIN rdm.dbo.estatus rest ON rest.codigo = rdest.codigo
                    WHERE rdest.idrdm_detalles = $rid";

                //echo $sql_clause; exit();
                $query = $db->query($sql_clause);

                //
                $results['estatus'] = array();
                while($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                    array_push($results['estatus'],$row);
                };
                //
                return $results;
            }

            else {
                $this->setError('Error, proporcione id de la requisicion');
            }   
        } catch (Exception $e) {
            $this->setError($e);
        }
    }    
}