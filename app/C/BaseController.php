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

			$this->_retAjax = $req->isAjax();
			//强制输出Json
			$this->dataType(true);
        }

        //指定返回的数据类型
		protected function dataType($isAjax = true){

			$this->_retAjax = $isAjax;
		}

        //返回错误信息
        protected function error($err_code = 101, $message = '', $retdata = []){

			$data = ['code' => $err_code, 'message' => $message];

			if($retdata){

				$data['data'] = $retdata;
			}

			return $this->response($data);
		}

        //返回成功信息
		protected function success($retdata = '', $message = ''){

			$data = ['code' => 200, 'message' => $message, 'data' => $retdata];

			return $this->response($data);
		}

		//信息返回
		protected function response($retdata){

			$this->_retAjax ? $this->retAjax($retdata) : $this->retHtml($retdata);

			exit;
		}

		//返回json
		private function retAjax($data){

			exit(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		//返回HTML信息提示页面
		private function retHtml($data){

			$req  = Request::getInstance();

			$resp = Response::getInstance($req);

			return $resp->withVars($data)->withView('admin/info.html')->display();
		}
	}
