<?php
/**
* Database Connection
*
*
*
*/

 //
 function dbconnection(){
      $host = 'localhost';
      $dbname = 'commondata';
      $user = 'user.tcts';
      $pass='ninguno';
      $connstring = 'mysql:host='.$host.';dbname='.$dbname.';charset=utf8';

      try {
         $DBH = new PDO($connstring, $user, $pass);
         return $DBH; 

      } catch (PDOException $e) {
            return false;            
      }

 }
?>