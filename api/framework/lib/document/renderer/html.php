<?php
/*
 *
 *
 *
 *
 */
class HTMLRenderer{










    /*
     *
     *
     */
    public function setHeaders(){
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header("Content-type: text/html;");
    }











    /*
     *
     *
     */
    public function getParsedResults($results, $errors){
        if (count($errors)>0){
            $error = null;
            foreach($errors as $err){
                $error .= $err . ' ';
            }
            return $error;
        }
        return $results;
    }











}
