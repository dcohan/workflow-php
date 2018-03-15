<?php


//registramos una variable global para deshabilitar el manejo de errors
$GLOBALS['isMVC']=true;


Zend\Mvc\Application::init(require 'config/application.config.php')->run();
