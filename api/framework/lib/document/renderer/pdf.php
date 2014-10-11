<?php
/*
 *
 *
 *
 *
 */
class PDFRenderer{










    /*
     *
     *
     */
    public function setHeaders(){
        header('Content-type: application/pdf');
    }











    /*
     *
     *
     */
    public function getParsedResults($results, $errors){

        //
        $results->Output();
        return $results;

        //
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
