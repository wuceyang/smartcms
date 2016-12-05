<?php
    namespace Library\Response;

    use Library\Template\SmartTpl;
    use \Config;

    class Response{

        protected $vars     = [];
        protected $retstr   = '';
        protected $debug    = false;
        protected static $_instance = null;

        protected function __construct(\Request &$req){

                $this->vars['request'] = &$req;

        }

        public static function getInstance(\Request $req){

            if(self::$_instance === null){

                self::$_instance = new self($req);
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

    }
