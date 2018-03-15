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
class workflow_handlers_factoryhandler  {
    public static function getInstance($handlerName)
    {
        if (strlen($handlerName)==0)
        {
            return new workflow_handlers_nullhandler();
        }
        
        return new $handlerName();
    }
}
