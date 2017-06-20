<?php
	return [
			'error' => function($errorInfo){

				$req  = \Library\Request\Request::getInstance();

				$resp = \Library\Response\Response::getInstance();

				$resp->response($errorInfo);
			}
		   ];