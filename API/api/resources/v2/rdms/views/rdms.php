<?php












/*
 *
 *
 *
 */
class RdmsView extends View{








    /*
     *
     *
     */
    function GetRequisiciones(){

        //
        $model = $this->getModel();
        $model_results = $model->GetRequisiciones();

        //
        if ($model_results){
            $this->results = $model_results;
        }

        //
        else {
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }

        // default type is json
        return $this->results;
    }

    /*
    *
    *
    *
    */
    function GetRequisicionesHistorial(){
         //
        $model = $this->getModel();
        $model_results = $model->GetRequisicionesHistorial();

        //
        if ($model_results){
            $this->results = $model_results;
        }

        //
        else {
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }

        // default type is json
        return $this->results;
    }









    /*
     *
     *
     */
    function GetUnasignedRequisiciones(){

        //
        $model = $this->getModel();
        $model_results = $model->GetUnasignedRequisiciones();

        //
        if ($model_results){
            $this->results = $model_results;
        }

        //
        else {
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }

        // default type is json
        return $this->results;
    }







    /*
    *
    *
    */
    function GetArticulo(){

        //
        $model = $this->getModel();
        $model_results = $model->GetArticulo();

        //
        if ($model_results){
            $this->results = $model_results;
        }

        //
        else {
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }

        // default type is json
        return $this->results;
    }











    /*
     *
     *
     */
    function PostRequisicion(){

        //
        $model = $this->getModel();
        $model_results = $model->PostRequisicion();

        //
        if ($model_results){
            $this->results['success'] = true;
            $this->results['data'] = $model_results;
        }

        //
        else {
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }

        // default type is json
        return $this->results;
    }







    /*
     *
     *
     */
    function PostCancelaRequisicion(){

        //
        $model = $this->getModel();
        $model_results = $model->PostCancelaRequisicion();

        //
        if ($model_results){
            $this->results['success'] = true;
            $this->results['data'] = $model_results;
        }

        //
        else {
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }

        // default type is json
        return $this->results;
    }















    /*
     *
     *
     */
    function GetRequisicionDetalles(){

        //
        $model = $this->getModel();
        $model_results = $model->GetRequisicionDetalles();

        //
        if ($model_results){
            $this->results = $model_results;
        }

        //
        else {
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }

        // default type is json
        return $this->results;
    }






      /*
     *
     *
     */
    function GetDetallesHistorial(){

        //
        $model = $this->getModel();
        $model_results = $model->GetDetallesHistorial();

        //
        if ($model_results){
            $this->results = $model_results;
        }

        //
        else {
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }

        // default type is json
        return $this->results;
    }





}