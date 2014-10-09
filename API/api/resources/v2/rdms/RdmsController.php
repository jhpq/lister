<?php


//
class RdmsController extends Controller{


    //
    function initialize(){


        //
        array_push($this->urls,


            //
            array( '/rdms',                     'rdms.GetRequisiciones' ),
            array( '/rdms/unasigned',           'rdms.GetUnasignedRequisiciones' ),

            //
            array( '/rdms/articulos/:rid',      'rdms.GetArticulo' ),

            //
            array( '/rdms',                     'rdms.PostRequisicion' ),
            array( '/rdms/:rid',                'rdms.GetRequisicionesHistorial' ),
            array( '/rdms/detalles/:rid',       'rdms.GetRequisicionDetalles' ),
            array( '/rdms/detalleshis/:rid',    'rdms.GetDetallesHistorial' ),
            array( '/rdms/cancela',             'rdms.PostCancelaRequisicion' ),

            // Listas
            array( '/rdms/lists/requisiciones_status',       'lists.GetRequisicionesStatus' )
        );
    }
}