<?php
/*
 *
 *
 * REST API - Controlador para getsion de usuarios, operaciones de usuarios, niveles de accesos y sus almacenes relacionados.
 * Algunos archivo llevan sus metodos directamente en la vista,
 * otros requieren de un modelo debido a que son extensos y/o comparten
 * datos comunes para la extraccion de registros de la BD.
 *
 *
 */
class UsersController extends Controller {



    /* Inicializamos la funcion principal */
    function initialize(){


        /* Agregamos las Rutas/Metodos al arreglo $this->urls */
        array_push($this->urls,


            /*
            *
            * Metodos para Usuarios
            * Archivo: Users.php
            * Se incluye en: Vista y Modelo.
            *
            * */
            array( '/users',       'user.Users' ),
            array( '/users/:id',   'user.User' ),
            array( '/users',       'user.PostUser' ),
            array( '/users/:id',   'user.PutUser' ),
            array( '/users/:id',   'user.DeleteUser' ),


            // Register User
            array( '/users/register',       'user.PostRegisterUser' ),

            // Bajas
            array( '/users/bajas',          'user.PostBaja' ),


            /*
            *
            * Metodos para Verificar session de usuario
            * Archivo: Session.php
            * Se incluye en: Vista
            *
            * */
            array( '/users/session/active', 'session.IsSessionActive' ),


            /*
            *
            * Metodos para Operaciones con usuarios
            * Archivo: Operations.php
            * Se incluye en: Vista
            *
            * */
            array( '/users/request/login',   'operations.PostRequestLogin' ),
            array( '/users/request/login2',  'operations.PostRequestLogin2' ),
            array( '/users/request/logout',  'operations.RequestLogout' ),


            // Roles de usuarios
            array( '/users/roles',   'populator.Roles' ),



            /*
            *
            * Metodos para gestion de sucursales con usuarios
            * Archivo: Sucursales.php
            * Se incluye en: Vista y Modelo.
            *
            * */
            array( '/users/sucursal',                               'populator.CurrentUserSucursal' ),
            array( '/users/sucursal/almacenes',                     'populator.CurrentUserSucursalAlmacenes' ),
            array( '/users/sucursal/almacenes/search/:almacen',     'populator.SearchCurrentUserSucursalAlmacen' ),
            array( '/users/sucursal/almacenes/search',              'populator.PostSearchCurrentUserSucursalAlmacen' )
        );
    }



}