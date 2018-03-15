<?php
namespace WorkflowApi;

use Zend\View\Model\JsonModel;

class Response {

	static function success ($data) {
		return Response::emit(true, $data, null);
	}

	static function error ($error) {
		return Response::emit(false, null, $error);
	}

	static function emit($result, $data, $error) {
		return new JsonModel(
			$data
		);
	}
        
}