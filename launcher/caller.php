<?php
$isCommandLine = false;
$adapterName = null; //toma el default del config.xml
//Command line compatibility
if (is_null($_SERVER['DOCUMENT_ROOT']) || strlen($_SERVER['DOCUMENT_ROOT'])==0)
{
    echo nl2br("Running on command line\n");
    $_SERVER['DOCUMENT_ROOT'] =str_replace("caller.php", "", __FILE__)."../..";
	
	$isCommandLine=(bool)(strtolower($argv[1])=='true');
        $adapterName=strtolower($argv[2]);
}
else
{
    if (array_key_exists('adapterName', $_GET))
    {
        $adapterName = $_GET['adapterName'];
    }
}
//emulate prepend
require_once($_SERVER['DOCUMENT_ROOT'].'/core/config/config.php');

echo nl2br("Comenzando ejecucion de workflow\n");
if(!$isCommandLine)
{
	\workflow_core_factoryengine::getInstance($adapterName)->execute();
} 
else
{
	while(true)
	{
		sleep(10);
		\workflow_core_factoryengine::getInstance()->execute($adapterName);
	}
}
echo nl2br("Finalizando ejecucion de workflow\n");