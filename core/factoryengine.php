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
class workflow_core_factoryengine  {
    public static function getInstance($adapterName=null)
    {
        //here we should have a parametrized configuration to one between many adapters
		//hack until we delete locale
        $engineName = core_framework_config::getConfig()->workflow->engine;
		if (is_null($engineName))
		{
			$engineName = core_framework_config::getConfig()->workflow->engine;
		}
		
		echo "Se utilizara el siguiente engine: ". $engineName;
        return new $engineName($adapterName);
    }
}
