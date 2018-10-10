<?php

	namespace library\Error;

	use \Library\Response\Response;
	use \Library\Request\Request;
    use \Log;

	class SmartError{

		public function registerErrorHandler(){

			set_exception_handler([$this, 'handle']);
            //注册普通级别的错误处理方法
            register_shutdown_function([$this, 'otherErrorHandle']);
		}

		public function handle($error){

            $params     = [
                'code'    => $error->getCode(),
                'line'    => $error->getLine(),
                'file'    => str_replace(APP_ROOT, '/', $error->getFile()),
                'message' => $error->getMessage(),
                'trace'   => $this->getTraceInfo($error->getTrace()),
                'type'    => 'Error',
            ];

            Log::debug($params);

            if ($error -> getCode() && !(error_reporting() & $error->getCode())) {

                return;
            }

            $handle = include APP_ROOT . 'config/handle.php';

            if($handle && isset($handle['error'])){

                ob_clean();

                return call_user_func_array($handle['error'], [$params]);
            }

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
                'line'    => str_replace(APP_ROOT, '/', $error['line']),
                'file'    => $error['file'],
                'message' => $error['message'],
                'trace'   => '',
                'type'    => 'Error',
            ];

            Log::debug($params);

            $handle = include APP_ROOT . 'config/handle.php';

            if($handle && isset($handle['error'])){

                ob_clean();

                return call_user_func_array($handle['error'], [$params]);
            }

            return $response->withVars($params)->withView('common/exception.html')->display();
        }

        private function getTraceInfo($trace){

            $errorTrace = [];

            foreach ($trace as $k => $v) {

                if(isset($v['function'])) break;

                $errorTrace[] = $v;
            }

            return $errorTrace;
        }
	}
