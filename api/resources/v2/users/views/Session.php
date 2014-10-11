<?php
/*
 *
 *
 *
 */
class SessionView extends View{





    /*
     *
     *
     *
     */
    function IsSessionActive (){
        //
        if (isset($_SESSION['logged']) && isset($_SESSION['id'])){

            // Valida si el usuario esta conectado de lo contrario destruye la session
            $db = $this->getDB();
            $id_usuario = $_SESSION['id'];
            $query = $db->query("SELECT * FROM usuarios WHERE idusuario = $id_usuario");
            if ($query){
                if ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                    //
                    if (isset($row['conectado']) && $row['conectado']===0){
                        session_destroy();
                        //
                        $this->results['success'] = false;
                        $this->results['report'] = 'Sesion is Not Active';
                        return $this->results;
                    }
                }
            }

            //
            $this->results['success'] = true;
            $this->results['report'] = 'Sesion is Active';
            $this->results['data'] = array(
                'logged'    =>$_SESSION['logged'],
                'descripcion_nivel'    =>$_SESSION['descripcion_nivel'],
                'id'        =>$_SESSION['id'],
                'nombre'    =>$_SESSION['nombre'],
                'puesto'    =>$_SESSION['puesto'],
                'sucursal'  =>$_SESSION['sucursal'],
                'idsucursal'     => $_SESSION['idsucursal'],
                'nivel'     =>$_SESSION['nivel'],
                'iddepartamento' =>$_SESSION['depto']
            );
            return $this->results;
        }
        //
        $this->results['report'] = 'no session active';
        return $this->results;
    }





}