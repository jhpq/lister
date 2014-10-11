<?php
/*
 *
 *
 *
 *
 */
class Renderer{






    /*
     *
     *
     */
    public $results = null;






    /*
     *
     *
     */
    function __construct($type){
        //
        $this->type = $type;
    }







    /*
     *
     *
     */
    public function parseResults($results, $errors, $access_control_allow_origin, $xml_root_element){

        $path_view_type = PATH_LIBRARIES.'/document/renderer/' . $this->type . '.php';
        if (is_file($path_view_type)){
            require_once $path_view_type;
            // get classname
            $document_renderer_classname = strtoupper($this->type).'Renderer';
                //
                if((int)class_exists($document_renderer_classname)){
                    //
                    $doc_klass = new $document_renderer_classname();
                    if ( (int)method_exists($doc_klass, 'setHeaders') && (int)method_exists($doc_klass, 'getParsedResults') ){

                        // allow origins and set headers
                        header("Access-Control-Allow-Origin: *");
                        $doc_klass->setHeaders();

                        //
                        if ($this->type==='xml'){
                            $this->results = $doc_klass->getParsedResults($results, $errors, $xml_root_element);
                        } else {
                            $this->results = $doc_klass->getParsedResults($results, $errors);
                        }
                    } else {
                        $this->results = ' setHeaders & getParsedResults must be overriden in order to load renderer type "' . $this->type . '" ';
                    }
                } else {
                    $this->results = ' no class handler for view type "' . $this->type . '" ';
                }
        } else {
            $this->results = ' document view type "' . $this->type . '" not implemented ';
        }
    }










    /*
     *
     *
     */
    public function render($results = null){
        //
        echo ($results) ? $results : $this->results;
    }













}