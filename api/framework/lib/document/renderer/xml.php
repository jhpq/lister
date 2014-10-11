<?php
/*
 *
 *
 *
 *
 */
class XMLRenderer{









    /*
    *
    *
    */
    public function setHeaders(){
        header('Content-type: application/xml');
    }









    /*
     *
     *
     */
    public function getParsedResults($results, $errors, $xml_root_element){
        if (count($errors)>0){
            return $this->xml_encode($errors, $xml_root_element );
        }
        return $this->xml_encode($results, $xml_root_element );
    }








    /*
     *
     *
     */
    private function xml_encode($arr, $root_element_name)
    {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><{$root_element_name}></{$root_element_name}>");
        $f = create_function('$f,$c,$a','
            foreach($a as $k=>$v) {
                if(is_array($v)) {
                    $ch=$c->addChild($k);
                    $f($f,$ch,$v);
                } else {
                    $c->addChild($k,$v);
                }
            }');
        $f($f,$xml,$arr);
        return $xml->asXML();
    }













}