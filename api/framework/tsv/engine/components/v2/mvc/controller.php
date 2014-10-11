<?php
/*
 *
 *
 *
 *
 */
abstract class Controller{



    /*
     *
     *
     */
    public $resources = array();





    /*
     *
     *
     */
    private function setResource($type, $route, $handler){
        array_push($this->resources, array(
            'route' => $route,
            'method' => $type,
            'handler' => $handler,
        ));
    }





    /*
     *
     *
     */
    public function get($route, $handler){
        $this->setResource('get', $route, $handler);
    }






    /*
     *
     *
     */
    public function post($route, $handler){
        $this->setResource('post', $route, $handler);
    }





    /*
     *
     *
     */
    public function put($route, $handler){
        $this->setResource('put', $route, $handler);
    }






    /*
     *
     *
     */
    public function delete($route, $handler){
        $this->setResource('delete', $route, $handler);
    }








}