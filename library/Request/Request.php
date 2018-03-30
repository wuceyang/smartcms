<?php
    namespace Library\Request;

    use \Config;

    class Request{

        protected $_post        = [];
        protected $_get         = [];
        protected $_cookie      = [];
        protected $_session     = [];
        protected $_server      = [];
        protected $_group       = '';
        protected $_controller  = '';
        protected $_action      = '';
        protected $_paramStr    = '';
        protected $_seperator   = '';
        protected static $_instance = null;

        protected function __construct(){

            $this->_post    = $_POST;

            $this->_get     = $_GET;

            $this->_cookie  = $_COOKIE;

            $this->_server  = $_SERVER;

            unset($_POST, $_GET, /*$_COOKIE,*/ $_SERVER, $_SESSION, $_REQUEST);
        }

        public static function getInstance(){

            if(self::$_instance === null){

                self::$_instance = new self();

            }

            return self::$_instance;

        }

        public function setGroup($group){
            $this->_group = $group;
        }

        public function setController($controller){
            $this->_controller = $controller;
        }

        public function setAction($action){
            $this->_action = $action;
        }

        public function setParamStr($paramStr, $seperator = ''){
            $this->_paramStr  = $paramStr;
            $this->_seperator = $seperator;
        }

        public function getGroup(){
            return $this->_group;
        }

        public function getController(){
            return $this->_controller;
        }

        public function getAction(){
            return $this->_action;
        }

        public function getParamStr(){
            return $this->_paramStr;
        }

        protected function decodeGetParams(){
            if($this->_paramStr && $this->_seperator){
                $params = explode($this->_seperator, $this->_paramStr);
                $pairs  = [];
                for ($i = 0; $i < count($params); ++$i) {
                    $pairs[$params[$i++]] = $params[$i] ? $params[$i] : '';
                }
                $this->_get = array_merge($this->_get, $pairs);
            }
        }

        protected function startSession(){
            $autoStartSession = Config::get('global.session.auto_start');
            if($autoStartSession){
                $this->session()->start();
            }
        }

        public function parse(){
            $this->decodeGetParams();
            $this->startSession();
        }

        public function isPost(){
            return $this->server('REQUEST_METHOD') == 'POST';
        }

        public function isGet(){
            return $this->server('REQUEST_METHOD') == 'GET';
        }

        public function isAjax($crossDomain = false){
            if(!$crossDomain){
                return $this->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest';
            }
            return strpos($this->server('HTTP_ACCEPT'), 'javascript') !== false;
        }

        public function method(){
            return $this->server('REQUEST_METHOD');
        }

        public function get($paramName = '', $defaultValue = ''){
            if(!$paramName) return $this->_get;
            return isset($this->_get[$paramName]) ? $this->_get[$paramName] : $defaultValue;
        }

        public function post($paramName = '', $defaultValue = ''){
            if(!$paramName) return $this->_post;
            return isset($this->_post[$paramName]) ? $this->_post[$paramName] : $defaultValue;
        }

        public function server($paramName = '', $defaultValue = ''){
            if(!$paramName) return $this->_server;
            $key = strtoupper($paramName);
            return isset($this->_server[$key]) ? $this->_server[$key] : $defaultValue;
        }

        public function session(){
            $config         = Config::get('global.session');
            $sessionClass   = "\\Library\\Session\\" . ucfirst($config['driver']) . 'Session';
            if(!class_exists($sessionClass)){
                throw new \Exception("找不带指定的驱动:" . $sessionClass, 101);
            }
            return $sessionClass::getInstance($config['configure']);
        }

        public function cookie($key = '', $default = null, $decrypt = false){
            if(!$key){
                return $this->_cookie;
            }
            return isset($this->_cookie[$key]) ? ($decrypt ? unserialize(\Library\Common\Cryptography::decode($this->_cookie[$key], Config::get('global.cookie.encryptKey'))) : $this->_cookie[$key]) : $default;

        }

        public function file($key = '', $idx = null){

            static $_files = [];

            if(!$_files && $_FILES){

                $keys = array_keys($_FILES);

                foreach ($keys as $key => $value) {

                    //多文件上传
                    if(is_array($_FILES[$value]['name'])){

                        foreach ($_FILES[$value]['name'] as $k => $v) {

                            if(!$v) continue;

                            $_files[$value][] = [
                                            'name'     => $v,
                                            'type'     => $_FILES[$value]['type'][$k],
                                            'tmp_name' => $_FILES[$value]['tmp_name'][$k],
                                            'error'    => $_FILES[$value]['error'][$k],
                                            'size'     => $_FILES[$value]['size'][$k],
                                            ];
                        }
                        continue;
                    }
                    //单文件上传
                    $formKey = key($_FILES);

                    if($_FILES[$formKey]['name']){

                        $_files[$formKey] = $_FILES[$formKey];
                    }
                }
            }
            if($key){

                if(!isset($_files[$key])){

                    return [];
                }

                $files = $_files[$key];

                return $files[$idx] ? $files[$idx] : [];
            }

            return $_files;
        }
    }
