<?php
/*
 *
 *
 *
 */
class ListsView extends View{






    //
    function GetRequisicionesStatus(){
        try{

            //
            $db = $this->getDB();

            //
            $results = array();

            //
            $sql_clause = sprintf("
                SELECT estado FROM requisicion_materiales GROUP BY estado ORDER BY estado ASC
            "
            );

            //
            $res = $db->query($sql_clause);


            // get results
            while($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)){
                array_push($results, $row);
            }

            return $results;
        }
        catch (Exception $e){
            //
            $error = $this->formatError($e);
            $this->results['report'] = $error;
            return $this->results;
        }
    }





}