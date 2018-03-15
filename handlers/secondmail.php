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
class workflow_handlers_secondmail {
    public function execute($contexto)
    {
        $mailManager =new core_framework_mail ();   
        $mensaje = $mailManager->enviarEmail2($contexto['cliente']['email'],'Cotización Seguro, no te pierdas esta promo!',"Hola ".$contexto['cliente']['nombre']." no se pierda la oportunidad de poder contar con un 10% de descuento si nos contrata en el dia de hoy!.");
        echo nl2br("Handler enviar mail secundario ejecutado satisfactoriamente.\n");
        return true;
    }
}
