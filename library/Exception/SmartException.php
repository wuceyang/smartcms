<?php
	
	namespace Library\Exception;
    
    use \Response;

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
                'file'    => $e->getFile(),
                'message' => $e->getMessage(),
                'trace'   => $e->getTrace(),
                'type'    => 'Exception',
            ];

            \Log::debug('Error:' . var_export($params, true));

            var_export($params);
            exit;

            $response->withVars($params)->withView('common/exception.html')->display();
        }
	}