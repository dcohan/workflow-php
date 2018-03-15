<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author David
 */
class workflow_handlers_thirdmail {
    public function execute($contexto)
    {
        $mailManager =new core_framework_mail ();
        $mensaje = $mailManager->enviarEmail2($contexto['cliente']['email'],'Cotización Seguro, ultima opotunidad! ',"Hola ".$contexto['cliente']['nombre']." esta es su ultima oportunidad de contratar su seguro de autos a un precio preferencial!");
        echo nl2br("Handler enviar mail ultima chance ejecutado satisfactoriamente.\n");
        return true;
    }
}
