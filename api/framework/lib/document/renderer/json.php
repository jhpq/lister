<?php
/*
 *
 *
 *
 *
 */
class JSONRenderer{










    /*
     *
     *
     */
    public function setHeaders(){
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header("Content-type: application/json; charset=windows-1251");
    }











    /*
     *
     *
     */
    public function getParsedResults($results, $errors){
        if (count($errors)>0){
            //$this->results = json_encode( array('event_id' => '603'), 'JSON_NUMERIC_CHECK');
            return json_encode($errors);
        }
        return json_encode($results);
    }









}