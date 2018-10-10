<?php
	
	namespace Library\Exception;
    
    use \Response;
    use \Log;

	class SmartException extends \Exception{
        
        //注册异常处理方法
		public function registerExceptionHandler(){
            
            set_exception_handler([$this, 'handle']);
        }
        
        //异常处理业务逻辑
        public function handle($e){

            $response = Response::getInstance();

            $params = [
                'code'    => $e->getCode(),
                'line'    => $e->getLine(),
                'file'    => str_replace(APP_ROOT, '/', $e->getFile()),
                'message' => $e->getMessage(),
                'trace'   => $e->getTrace(),
                'type'    => 'Exception',
            ];

            Log::debug($params);

           $handle = include APP_ROOT . 'config/handle.php';

            if($handle && isset($handle['error'])){

                ob_clean();

                return call_user_func_array($handle['error'], [$params]);
            }

            return $response->withVars($params)
                   ->withView('common/exception.html')
                   ->display();
        }
	}