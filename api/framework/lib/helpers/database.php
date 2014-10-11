<?php
/*
 *
 *
 *
 *
 */
class HelperDatabase{









    /*
     *
     *
     *
     */
    function getSegmentFields($segment){
        if (strpos($segment, ',')) {
            $seg_fields = explode(',', $segment);
            if (count($seg_fields)>0){
                $i = 0;
                $str_seg_fields = null;
                foreach($seg_fields as $field){
                    if ($i==0){
                        $str_seg_fields .= $field;
                    } else {
                        $str_seg_fields .= ", " . $field;
                    }
                    $i++;
                }
                return $str_seg_fields;
            }
        } else {
            return $segment;
        }
        return false;
    }







    /*
     *
     *
     *
     */
    function boolQuery($db_inst, $table, $where_field, $where_value, $where_field_type = null){
        try{
            // Default where type = string
            $sql_clause = sprintf(" SELECT * FROM %s WHERE %s = %s ",
                $table,
                $where_field,
                $where_value
            );
            // Override sql clause for int type
            if (strtolower($where_field_type)==='int'){
                $sql_clause = sprintf(" SELECT * FROM %s WHERE %s = %s ",
                    $table,
                    $where_field,
                    $where_value
                );
            }
            //
            $res = $db_inst->doQuery($sql_clause);
            //
            if (sqlsrv_has_rows($res)){
                return true;
            } else {
                return false;
            }
        }
        catch (Exception $e){
            return false;
        }
    }










}