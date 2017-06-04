<?php

    namespace Library\Router;

    use \Config;
    use \Request;
    use \Response;
    use \Exception;
    use \Library\Exception\SmartException;

    class router{

        protected $_uri         = '';
        protected $_routeStr    = '';
        protected $_routeParams = [];
        protected $_group       = '';
        protected $_controller  = '';
        protected $_action      = '';
        protected $_params      = '';

        public function __construct(){

            $this->_pattern = $this->loadRoutePattern();

            (new \Library\Exception\SmartException())->registerExceptionHandler();

            (new \Library\Error\SmartError())->registerErrorHandler();

        }

        //加载配置文件的路由规则
        protected function loadRoutePattern(){

            $route              = Config::get('global.route');

            $this->_routeStr    = $route['pattern'];

            $this->_seperator   = $route['seperator'];

            $this->escapePattern();

            unset($route['pattern']);

            $this->_routeParams = $route;
        }

        //路由规则解析
        protected function resolveRoute(){

            $source             = [];

            $symbol             = '';

            if(strpos($this->_routeStr, '{params}') !== false){

                $symbol = preg_replace('/^.*?{action}(.+?){params}.*$/', '$1', $this->_routeStr);

            }

            foreach ($this->_routeParams as $k => $v) {

                $source[]       = '\/{' . $k . '}';

                $destination[]  = "(\/(?<$k>$v?))?";

            }

            if($symbol){

                $routeInfo = explode($symbol, $this->_routeStr);

                if($routeInfo && isset($routeInfo[1])){

                    $this->_routeStr = $routeInfo[0] . $symbol . '(\?' . $routeInfo[1] . ')?';
                }
            }

            $this->_routeStr    = str_replace($source, $destination, $this->_routeStr);

            $pathinfo           = parse_url($_SERVER['REQUEST_URI']);

            $this->_paramStr    = $pathinfo['path'];

            if(substr($this->_paramStr, -1) == '/'){

                $this->_paramStr = substr($this->_paramStr, 0, strlen($this->_paramStr)-1);
            }

            $defaultConfig      = Config::get('global.defaultConfig');

            preg_match_all("/^{$this->_routeStr}$/i", $this->_paramStr, $matches);

            $this->_group       = $this->toCamel((!isset($matches['group']) || count($matches['group']) == 0 || !$matches['group'][0]) ? $defaultConfig['group'] : $matches['group'][0]);

            $this->_controller  = $this->toCamel((!isset($matches['controller']) || count($matches['controller']) == 0 || !$matches['controller'][0]) ?  $defaultConfig['controller'] : $matches['controller'][0]);

            $this->_action      = $this->toCamel((!isset($matches['action']) || count($matches['action']) == 0 || !$matches['action'][0]) ?  $defaultConfig['action'] : $matches['action'][0], true);

            $this->_params      = (!isset($matches['params']) || count($matches['params']) == 0) ? '' : $matches['params'][0];
        }

        //正则特殊字符转义
        protected function escapePattern(){

            $fromChars = ['/', '?', '.', '^', '$', '[', ']', '(', ')', '+', '-'];

            $toChars   = ['\/', '\?', '\.', '\^', '\$', '\[', '\]', '\(', '\)', '\+', '\-'];

            $this->_routeStr = str_replace($fromChars, $toChars, $this->_routeStr);
        }

        //路由分发
        public function dispatch(){

            $this->resolveRoute();

            $request   = Request::getInstance();
            
            $subdomain = Config::get('global.subdomain');
            //如果配置了子域名，则强制指定子域名对应的分组
            if(isset($subdomain) && $subdomain){

                $host = $request->server('HTTP_HOST');

                if(isset($subdomain[$host])){

                    $this->_group = $this->toCamel($subdomain[$host]);
                }
            }

            $controllerPath = "\\App\\C\\" . ($this->_group ? $this->_group . '\\' : '') . $this->_controller;

            if(!class_exists($controllerPath)){

                $prefixGroup = $this->_group . ($this->_group ? '/' : '');

                throw new Exception("找不到指定的控制器:" . $prefixGroup . $this->_controller . '[' . $request->server('REQUEST_URI') . ']', 101);
            }

            if(!method_exists($controllerPath, $this->_action)){

                $prefixGroup = $this->_group . ($this->_group ? '/' : '');

                throw new Exception("找不到指定的处理方法:" . $prefixGroup . $this->_controller . '/' . $this->_action, 102);
            }

            $request->setController($this->_controller);

            $request->setAction($this->_action);

            $request->setGroup($this->_group);

            $request->setParamStr($this->_params, $this->_seperator);

            $request->parse();

            $response = Response::getInstance($request);

            $response->setSqlDebugMode(Config::get('global.debugSql'));

            $controller = new $controllerPath($request, $response);

            call_user_func([$controller, $this->_action], $request, $response);
        }

        //驼峰转换
        protected function toCamel($str, $isAction = false){

            $str    = str_replace(['-', '_', ' '], '.', $str);

            $str    = preg_replace('/\.+/', '.', $str);

            $parts  = explode('.', $str);

            $retstr = '';

            foreach ($parts as $k => $v) {

                $retstr .= ($k == 0 && $isAction) ? strtolower($v) : ucfirst(strtolower($v));
            }

            return $retstr;
        }
    }
