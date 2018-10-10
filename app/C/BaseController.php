<?php

	namespace App\C;

	use \Request;
	use \Response;
	use \Config;

	class BaseController extends Controller{

		protected $_retAjax = true;

        public function __construct(Request $req, Response $resp){

			$this->request  = $req;
			
			$this->response = $resp;
			//强制输出Json
			$this->response->dataType(true);
        }
	}
