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
class workflow_handlers_nullhandler {
    public function execute($contexto)
    {
        echo nl2br("Handler NULL ejecutado satisfactoriamente.\n");
        return true;
    }
}
