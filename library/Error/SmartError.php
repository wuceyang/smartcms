<?php

	namespace library\Error;

	use \Library\Response\Response;
	use \Library\Request\Request;

	class SmartError{

		public function registerErrorHandler(){

			set_error_handler([$this, 'handle'], E_ALL);
            //注册普通级别的错误处理方法
            register_shutdown_function([$this, 'otherErrorHandle']);

		}

		public function handle($errno, $errstr, $errfile, $errline){

		    $response   = Response::getInstance(Request::getInstance());

            $params     = [
                'code'    => $errno,
                'line'    => $errline,
                'file'    => $errfile,
                'message' => $errstr,
                'trace'	  => [],
                'type'    => 'Error',
            ];

            return $response->withVars($params)->withView('common/exception.html')->display();
		}

        //处理更高级别错误的方法
        public function otherErrorHandle(){

            $error      = error_get_last();

            if(!$error) return;

            ob_clean();

            $response   = Response::getInstance(Request::getInstance());

            $params     = [
                'code'    => $error['type'],
                'line'    => $error['line'],
                'file'    => $error['file'],
                'message' => $error['message'],
                'trace'   => [],
                'type'    => 'Error',
            ];

            return $response->withVars($params)->withView('common/exception.html')->display();
        }
	}
