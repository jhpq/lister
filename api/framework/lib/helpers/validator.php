<?php
/*
 *
 *
 *
 *
 */
class HelperValidator{





    public function ValidatePost($post, $required_fields){
        //
        $this->validfields = $post;
        $this->requiredfields = array();
        $this->valid = true;

        //
        foreach($required_fields as $k=>$v){
            if ( !array_key_exists($v, $post) ){
                $this->valid = false;
                array_push($this->requiredfields, $v);
            }
        }
        return $this;
    }

    public function Valid(){
        return $this->valid;
    }

    public function getValidFields(){
        return $this->validfields;
    }

    public function getRequiredFields(){
        return implode(", ", $this->requiredfields);
    }






}