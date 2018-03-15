<?php


require ($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');

$swagger = Swagger\scan($_SERVER['DOCUMENT_ROOT'].'/workflow/services/module');

echo $swagger;
