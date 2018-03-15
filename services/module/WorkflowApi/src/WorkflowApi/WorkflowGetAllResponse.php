<?php
namespace WorkflowApi;

use Zend\View\Model\JsonModel;

class WorkflowGetAllResponse  {

	static function success ($nodos, $transiciones) {
                $formattedData = array(
                   "nodos" => $nodos,
                   "transiciones" => $transiciones
                );
		return WorkflowGetAllResponse::emit(true, $formattedData, null);
	}

	static function error ($error) {
		return WorkflowGetAllResponse::emit(false, null, $error);
	}

	static function emit($result, $model, $error) {
		
            return new JsonModel($model);
	}
        
}