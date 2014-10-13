<?php
/**
* Database connection driver
*
*/

function getConnection(){
	$database = 'lister';
	$user = 'root';
	$pass = 'fragmentacion';
	$server = 'localhost'; 
	
	$connection = new mysqli($server, $user, $pass, $database);

	return $connection;
}


?>