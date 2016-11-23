<?php

	namespace library\Error;

	use \Library\Response\Response;
	use \Library\Request\Request;

	class SmartError{

		public function registerErrorHandler(){

			set_exception_handler([$this, 'handle']);
            //注册普通级别的错误处理方法
            register_shutdown_function([$this, 'otherErrorHandle']);

		}

		public function handle($error){

			if ($error -> getCode() && !(error_reporting() & $error->getCode())) {

		        return;
		    }

		    $response   = Response::getInstance(Request::getInstance());

            $params     = [
                'code'    => $error->getCode(),
                'line'    => $error->getLine(),
                'file'    => $error->getFile(),
                'message' => $error->getMessage(),
                'trace'   => $error->getTrace(),
                'type'    => 'Error',
            ];

            \Log::debug('Exception:' . var_export($params, true));

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
                'trace'   => '',
                'type'    => 'Error',
            ];

            \Log::debug('Error:' . var_export($params, true));

            return $response->withVars($params)->withView('common/exception.html')->display();
        }
	}
