<?php

	namespace App\C;

	use \Request;
	use \Response;
	use \Config;
	use \App\Helper\RetCode;

	class BaseController extends Controller{

		protected $_sesskey = 'userinfo';

		protected $_retAjax = true;

		protected $userinfo = [];

		protected $defaultDateTime = '1971-01-01 00:00:00';

        public function __construct(Request $req, Response $resp){

			$this->request  = $req;
			
			$this->response = $resp;
			//强制输出Json
			$this->response->dataType(true);
        }
	}
