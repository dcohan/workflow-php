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
class workflow_handlers_atv_firstmail {
    public function execute($contexto)
    {
        $mailManager =new core_framework_mail ();
        $mailManager->enviarEmailCotizacionViajero($contexto['cotizacion']);
        echo nl2br("Handler ATV enviar primer ultima chance ejecutado satisfactoriamente.\n");
        return true;
    }
}
