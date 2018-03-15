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
class workflow_handlers_agentmail {
    public function execute($contexto)
    {
        $usuario = cachedDatabase::load('usuario', 2);
        $mailManager =new core_framework_mail ();
        $mensaje = $mailManager->enviarEmail2($usuario->email,'Cotización seguro a agente',"El cliente ".$contexto['cliente']['nombre']." no se ha comunicado con nosotros, por favor llamelo al numero: ".$contexto['cliente']['telefono']);
        echo nl2br("Handler enviar mail a agente ejecutado satisfactoriamente.\n");
        return true;
    }
}
