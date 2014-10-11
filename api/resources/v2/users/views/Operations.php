<?php
/*
 *
 *
 *
 */
class OperationsView extends View{









    /*
     *
     *
     *
     *
     */
    function PostRequestLogin(){
        try{
            // Get Tools & data
            $db         = $this->getDB();
            $helper     = $this->getHelper();
            $utils      = $helper->getTools('utilities');
            $body       = $utils->JSONToArray($this->getRequestBody());



            // validate mandatory data
            if (
                isset($body['username']) &&
                isset($body['password']) &&
                is_numeric($body['username'])
            ){

                $username       = $body['username'];
                $password       = filter_var($body['password'], FILTER_SANITIZE_STRING);
                $password = base64_encode('tsv_pass_hash'.$password);

                // init query
                $sql = sprintf("
                       SELECT
                         t.idusuario idusuario
                        ,t.nombre nombre
                        ,t.puesto puesto
                        ,ts.id sucursal
                        ,t.idnivel idnivel
                        ,tn.descripcion descripcion_nivel
                        ,iddepartamento = CASE
                            WHEN iddepartamento IS NULL
                              THEN 0
                            ELSE
                              iddepartamento
                            END

                        FROM usuarios t
                        LEFT JOIN sucursal ts ON ts.id = t.sucursal
                        LEFT JOIN nivelespuestos tn ON tn.idnivel = t.idnivel
                        WHERE (iddepartamento = 7 OR iddepartamento = 9)
                        AND t.idnivel != 0
                        AND t.idusuario = %s AND t.password = '%s'
                        ",
                    $username,
                    $password
                );

                // laod query results
                $stmt = $db->query($sql);

                // get results
                if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){


                    // store user data
                    $_SESSION['logged'] = true;

                    // Different names for apps
                    $uid = $clave  = $_SESSION['uid'] = $id = $_SESSION['clave'] = $_SESSION['id'] = $_SESSION['id_usuario'] = $row['idusuario'];
                    $depto  = $_SESSION['depto'] = $row['iddepartamento'];
                    $nombre     = $_SESSION['nombre']       = $row['nombre'];
                    $puesto     = $_SESSION['puesto']       = $row['puesto'];
                    $nivel      = $_SESSION['nivel']        = $row['idnivel'];
                    $descripcion_nivel    = $_SESSION['descripcion_nivel'] = $row['descripcion_nivel'];

                    // save sucursalid
                    $sucursalid    = $_SESSION['sucursalid'] = $_SESSION['idsucursal'] = $_SESSION['id_sucursal'] = $row['sucursal'];

                    // get sucursal shortname
                    $sql = sprintf("" .
                            " SELECT * FROM sucursal " .
                            " WHERE id = %s ",
                        $sucursalid
                    );
                    // laod query results
                    $query = $db->query($sql);
                    // get results
                    if($row2 = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                        $sucursal    = $_SESSION['sucursal']     = $row2['shortname'];
                    }


                    // Status de conectado
                    $sql = sprintf("UPDATE usuarios SET
                        conectado = 1
                        WHERE idusuario = %s",
                        $username
                    );
                    $db->query($sql);



                    // success = true
                    $this->results['success']=true;
                    // store data
                    $this->results['data']=array(
                        'id'=>$id,
                        'nombre'=>$nombre,
                        'puesto'=>$puesto,
                        'sucursal'=>$sucursal,
                        'sucursalid'=>$sucursalid,
                        'nivel'=>$nivel,
                        'descripcion_nivel'=>$descripcion_nivel,
                        // Different names for apps
                        'u'=>$uid,
                        'c'=>trim($clave),
                        'd'=>trim($depto),
                        'n'=>trim($depto)

                    );
                    $this->results['report']="login Ok! at " . date(DATE_RFC822);

                    // wrong username/password
                } else {
                    $this->results['report']="Usuario o contraseña erroneo";
                }
                // no data provided
            } else {
                $this->results['report']="Se require el nombre de usuario y contraseña";
            }

            //
        } catch (Exception $e){
            //
            $error = $this->formatError($e);
            $this->results['report'] = $error;
        };

        return $this->results;
    }











    /*
     *
     *
     *
     *
     */
    function RequestLogout(){
        //if ($utils->session_is_active()){
        if (isset($_SESSION['logged']) && isset($_SESSION['id'])){

            // desconecta usuario
            $sql = sprintf("UPDATE usuarios SET
                        conectado = 0
                        WHERE idusuario = %s",
                $_SESSION['id']
            );
            $this->getDB()->query($sql);


            session_destroy();
            $this->results['success'] = true;
            $this->results['report'] = 'Cierre de sesion satisfactorio';
        } else {
            $this->results['report'] = 'No existe usuario con sesion';
        }
        return $this->results;
    }




}



/*


function validaLDAP($user, $password){
    $bres = FALSE;
    include ("adLDAP.php");
    try {
        $adldap = new adLDAP();
    }
    catch (adLDAPException $e) {
        echo $e;
        exit();
    }
    //authenticate the user
    if ($adldap -> authenticate($user,$password)){
        $_SESSION["login_user"]=$user;
        $userinfo = $adldap->user_info($user, array("mail","displayname"));
        $_SESSION["display_name"]= $userinfo[0]["displayname"][0];
        $_SESSION["last_access"] = time();
        $bres = TRUE;
    }
    return $bres;
}


*/