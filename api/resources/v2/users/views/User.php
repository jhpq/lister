<?php
/*
 *
 *
 *
 */
class UserView extends View{


    /*
     *
     *
     *
     */
    function Users(){
        // get model & data
        $model = $this->getModel();
        $model_results = $model->Users();
        //
        if ($model_results){
            $this->results['success'] = true;
            $this->results['data'] = $model_results;
            $this->results['report'] = 'list users ok';
        } else {
            //
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }
        return $this->results;
    }



    /*
     *
     *
     *
     */
    function User(){
        // get model & data
        $model = $this->getModel();
        //
        $model_results = $model->User();

        //
        if ($model_results){
            $this->results['success'] = true;
            $this->results['data'] = $model_results;
            $this->results['report'] = 'list single user ok';
        } else {
            //
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }
        return $this->results;
    }




    /*
     *
     *
     *
     */
    function PostUser(){

        // solo pueden agregar superadmin y encargado de area
        // $this->authenticate(array(1, 5), 'nivel', 401);


        // get model & data
        $model = $this->getModel();
        $model_results = $model->postUser();
        //
        if ($model_results){
            $this->results['success'] = true;
            $this->results['data'] = $model_results;
            $this->results['report'] = 'user created ok';
        } else {
            //
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }
        return $this->results;
    }





    /*
     *
     *
     *
     */
    function PostRegisterUser(){

        // no auth
        //$this->authenticate(array(1, 5), 'nivel', 401);
        // get model & data
        $model = $this->getModel();
        $model_results = $model->PostRegisterUser();
        //
        if ($model_results){
            $this->results['success'] = true;
            $this->results['data'] = $model_results;
            $this->results['report'] = 'user registered ok';
        } else {
            //
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }
        return $this->results;
    }










    /*
     *
     *
     *
     */
    function PostBaja(){

        // no auth
        //$this->authenticate(array(1, 5), 'nivel', 401);
        // get model & data
        $model = $this->getModel();
        $model_results = $model->PostBaja();
        //
        if ($model_results){
            $this->results['success'] = true;
            $this->results['data'] = $model_results;
            $this->results['report'] = 'user baja ok';
        } else {
            //
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }
        return $this->results;
    }






    /*
     *
     *
     *
     */
    function PutUser(){
        // get model & data
        $model = $this->getModel();
        $model_results = $model->PutUser();
        //
        if ($model_results){
            $this->results['success'] = true;
            $this->results['data'] = $model_results;
            $this->results['report'] = 'user updated ok';
        } else {
            //
            $model_error = $model->getError();
            $error = $this->formatError($model_error);
            $this->results['report'] = $error;
        }
        return $this->results;
    }






    function DeleteUser(){
        try{
            // Who can delete users? only super administrators
            //$this->authenticate(array(1), 'nivel', 401);


            $db = $this->getDB();
            $request_body = $this->getRequestBody();
            $helper = $this->getHelper();
            $utilities = $helper->getTools('utilities');
            $body_data = $utilities->JSONToArray($request_body);

            //
            if ($id =  $this->getSegmentValue(1)){
                //
                $sql = sprintf("
                    DELETE FROM usuarios WHERE idusuario = %s
                    ",
                    $id
                );
                //echo $sql; exit();
                $sql = $db->query($sql);
                $rows_affected = sqlsrv_rows_affected($sql);

                if ($rows_affected) {
                    $this->results['success'] = true;
                    $this->results['report'] = 'registro eliminado correctamente';
                    return $this->results;
                }
                //
                else {
                    $this->results['report'] = 'no se pudo eliminar el registro';
                    return $this->results;
                }
            }

            //
            return $this->results;
        }
        catch(Exception $e){
            $this->results['data'] = $e;
            return $this->results;
        }
    }







}