<?php
/**
*
*
*
*
*/

Class Login{
	/**
	*
	*
	*/
	function login($params){
		session_start();
		//session_destroy(); 
		if (isset($params->data->mail) && isset($params->data->password)){
			$mail = $params->data->mail;
			$password = $params->data->password;
			$password = sha1($password);
			$sql = "SELECT * FROM Users WHERE password = '$password' AND Mail = '$mail'";
			
			$dbConn = dbconnection();

	      	if ($dbConn){
	      		$sth = $dbConn->prepare($sql));

		         if ($sth->execute()){		     
		         	$count = $sth->rowCount();

		         	if ($count==0){
		         		$resdata = '{"success":false, "message":"data OK", "report": "No username-password", "respnum": 6}';

		         	}
		         	else{			         				         	
			         	$result = array();
			         	$response = array();
			         	$count = 0;

		         		while ($row = $sth->fetch(PDO::FETCH_OBJ)){	         			
		         			$id = $_SESSION['Id'] = $row->Id;
	                    	$name = $_SESSION['Name'] = $row->Name;
	         				$connected = $_SESSION['Connected'] = $row->Connected;
					    	}
					    	if ($count > 0){
						    	$response[] = array(
			                                'id'=>$id,
			                                'name'=>$name
			                );
						    	$resdata = '{"success":true, "message":"data OK", "data":'.json_encode($response).'}';
					    	}
					    	else{
					    		$resdata = '{"success":false, "message":"no user-password", "data":'.json_encode($response).'}';	
					    	}						   
						}
		         }
		         else{
		         	//echo $qry."<BR>";
		         	$arrHerr = $sth->errorInfo();
		         	$Herrinfo = $arrHerr[0].'-'.$arrHerr[1].': '.$arrHerr[2];
		            $resdata = '{"success":false, "count":-1, "message":"Failed to execute query", "data":"'.$Herrinfo.'"}';
		         }

	      	}
	      	else{
	      		$resdata = '{"success":false, "count":-1, "message":"Failed to Connect to DB", "report":"Failed to Connect to DB"}';         
	      	}

			//$resdata = '{"success":true, "count":-1, "message":"'.$username.'", "report":"'.$qry.'"}';
		}
		else{
			$resdata = '{"success":false, "count":-1, "message":"incomplete data", "report":"incomplete data"}';         
		}
		return $resdata;
	}



	/***
	*
	*
	*
	*
	*/
	function createUser(){

		if (isset($params->data->username) && isset($params->data->password)){
			$mail = $params->data->mail;
			$password = $params->data->password;
			$password = sha1($password);
			$sql = "SELECT * FROM Users WHERE Mail = '$mail'";
			
			$dbConn = dbconnection();

	      	if ($dbConn){
	      		$sth = $dbConn->prepare($sql));

		         if ($sth->execute()){		     
		         	$count = $sth->rowCount();

		         	if ($count==0){
		         		

		         	}
		         	else{			         				         	
			         	$result = array();
			         	$response = array();
			         	$count = 0;

		         		while ($row = $sth->fetch(PDO::FETCH_OBJ)){	         			
		         			$id = $_SESSION['Id'] = $row->Id;
	                    	$name = $_SESSION['Name'] = $row->Name;
	         				$connected = $_SESSION['Connected'] = $row->Connected;
					    	}
					    	if ($count > 0){
						    	$response[] = array(
			                                'id'=>$id,
			                                'name'=>$name
			                );
						    	$resdata = '{"success":true, "message":"data OK", "data":'.json_encode($response).'}';
					    	}
					    	else{
					    		$resdata = '{"success":false, "message":"no user-password", "data":'.json_encode($response).'}';	
					    	}						   
						}
		         }
		         else{
		         	//echo $qry."<BR>";
		         	$arrHerr = $sth->errorInfo();
		         	$Herrinfo = $arrHerr[0].'-'.$arrHerr[1].': '.$arrHerr[2];
		            $resdata = '{"success":false, "count":-1, "message":"Failed to execute query", "data":"'.$Herrinfo.'"}';
		         }

	      	}
	      	else{
	      		$resdata = '{"success":false, "count":-1, "message":"Failed to Connect to DB", "report":"Failed to Connect to DB"}';         
	      	}

			//$resdata = '{"success":true, "count":-1, "message":"'.$username.'", "report":"'.$qry.'"}';
		}
		else{
			$resdata = '{"success":false, "count":-1, "message":"incomplete data", "report":"incomplete data"}';         
		}
		return $resdata;
	}

}


?>