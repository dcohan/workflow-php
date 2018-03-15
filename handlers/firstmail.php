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
class workflow_handlers_firstmail {
    public function execute($contexto)
    {
        core_biz_contacto::procesarContacto($contexto['cliente'], false, false, $contexto['cotizacion'], null, $contexto['cotizacionExtendida']);
        echo nl2br("Handler enviar mail inicial ejecutado satisfactoriamente.\n");
        return true;
    }
}
