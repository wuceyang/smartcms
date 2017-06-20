<?php
    namespace Library\Response;

    use Library\Template\SmartTpl;
    use \Config;
    use \Request;

    class Response{

        protected $vars     = [];
        protected $retstr   = '';
        protected $debug    = false;
        protected $_retAjax = false;
        protected static $_instance = null;

        protected function __construct(){

                $this->vars['request'] = Request::getInstance();
        }

        public static function getInstance(){

            if(self::$_instance === null){

                self::$_instance = new self();
            }

            return self::$_instance;
        }

        public function setSqlDebugMode($debugMode = false){

            $this->debug = $debugMode;
        }

        //设置cookie
        public function cookie($key, $val, $expireAt = null, $path = '/', $domain = '', $sslOnly = false, $httpOnly = false, $encrypt = true){

            $cookieConfig = Config::get('global.cookie');

            $expireAt     = $expireAt ? $expireAt : $cookieConfig['maxLifetime'];

            $domain       = $domain ?: $cookieConfig['domain'];

            $sslOnly      = $sslOnly ? $sslOnly : $cookieConfig['sslOnly'];

            $httpOnly     = $httpOnly ? $httpOnly : $cookieConfig['httpOnly'];

            $path         = $path ? $path : $cookieConfig['path'];

            if($encrypt){

                $val      = serialize($val);

                $val      = \Library\Common\Cryptography::encode($val, $cookieConfig['encryptKey']);

            }

            setcookie($key, $val, TIME + $expireAt, $path, $domain, $sslOnly, $httpOnly);

            return $this;
        }

        //设置变量
        public function withVars($vals){
            $this->vars = array_merge($this->vars, $vals);
            return $this;
        }

        //设置模板，并运行回调函数
        public function withView($template, $callback = null){
            $this->template = $template;
            $this->callback = $callback;
            if($this->debug){
                $dbDriver = Config::get('database.driver');
                $dbDriverClass = "\\Library\\Database\\" . ucfirst($dbDriver);
                $this->withVars(['sql' => $dbDriverClass::getInstance()->getSqls()]);
            }
            return $this;
        }

        //输出模板运行后的html
        public function display(){
            echo $this->runCode();
        }

        //获取模板运行后的html
        public function toString(){
            return $this->runCode();
        }

        //解析模板，并运行，返回最终输出的html
        protected function runCode(){
            $viewConfig = Config::get('view');
            $viewParams = [
                'start_tag'    => $viewConfig['start_tag'],
                'end_tag'      => $viewConfig['end_tag'],
                'template_dir' => $viewConfig['root_dir'],
                'compiled_dir' => $viewConfig['cache_dir']
            ];
            $tpl      = new SmartTpl($viewParams);
            $retstr   = $tpl->fetch($this->template, $this->vars);
            if($this->callback && is_callable($this->callback)){
                $retstr = call_user_func($this->callback, $retstr);
            }
            return $retstr;
        }

        //指定返回的数据类型
        public function dataType($isAjax = true){

            $this->_retAjax = $isAjax;
        }

        //返回错误信息
        public function error($err_code = 101, $message = '', $retdata = []){

            $data = ['code' => $err_code, 'message' => $message];

            if($retdata){

                $data['data'] = $retdata;
            }

            return $this->response($data);
        }

        //返回成功信息
        public function success($retdata = '', $message = ''){

            $data = ['code' => 200, 'message' => $message, 'data' => $retdata];

            return $this->response($data);
        }

        //信息返回
        public function response($retdata){

            ob_clean();

            $this->_retAjax ? $this->retAjax($retdata) : $this->retHtml($retdata);

            exit;
        }

        //返回json
        private function retAjax($data){

            exit(json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        //返回HTML信息提示页面
        private function retHtml($data){

            return $this->withVars($data)->withView('common/info.html')->display();
        }
    }
