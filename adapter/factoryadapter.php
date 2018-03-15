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
class workflow_adapter_factoryadapter  {
    public static function getInstance($adapterName=null    )
    {
        if (is_null($adapterName))
        {
            //here we should have a parametrized configuration to one between many adapters
            //hack until we delete locale
            $adapterName = core_framework_config::getConfig()->workflow->adapter;
            if (is_null($adapterName))
            {
                    $adapterName = core_framework_config::getConfig()->workflow->adapter;
            }
        }
        
        return new $adapterName();
    }
}
