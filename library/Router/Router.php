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
        protected $_customRoute = '';
        protected $_request     = null;

        public function __construct(){

            $this->_pattern = $this->loadRoutePattern();

            (new \Library\Exception\SmartException())->registerExceptionHandler();

            (new \Library\Error\SmartError())->registerErrorHandler();

            $this->_request = Request::getInstance();

        }

        //加载配置文件的路由规则
        protected function loadRoutePattern(){

            $route              = Config::get('global.route');

            $this->_routeStr    = $route['pattern'];

            $this->_seperator   = $route['seperator'];

            $this->_customRoute = $route['routefile'];

            $this->escapePattern();

            unset($route['pattern'], $route['seperator'], $route['routefile']);

            $this->_routeParams = $route;
        }

        //路由规则解析
        protected function resolveRoute(){

            $pathinfo           = parse_url($this->_request->server('REQUEST_URI'));

            $this->_paramStr    = $pathinfo['path'];

            if(($this->_paramStr) > 1 && substr($this->_paramStr, -1) == '/'){

                $this->_paramStr = substr($this->_paramStr, 0, -1);
            }

            $customRoute = false;

            if($this->_customRoute){

                $customRoute = $this->matchCustomRoute();
            }

            if(!$customRoute){

                $this->_routeStr    = preg_replace('/\{([^}]+?)\}/is', '(?<$1>.+?)', $this->_routeStr);

                $defaultConfig      = Config::get('global.defaultConfig');

                preg_match_all("/^{$this->_routeStr}$/i", $this->_paramStr, $matches);

                foreach ($this->_routeParams as $k => $v) {
                    
                    $key = '_' . $k;

                    $this->$key = '';

                    if(isset($matches[$k])){

                        $this->$key = $defaultConfig[$k];

                        if(count($matches[$k]) > 0){

                            $this->$key = $this->toCamel($matches[$k][0], $k == 'action');
                        }
                    }
                }
            }

            if(!$this->_controller && !$this->_action){

                throw new Exception("路由规则不正确，请参考config/global.php中的路由规则", 1);
            }
        }

        /**
         * 匹配自定义路由
         * @return bool
         */
        protected function matchCustomRoute(){

            if(!$this->_customRoute) return false;

            $routes = include $this->_customRoute;

            if(!is_array($routes)) return false;

            if(isset($routes[$this->_paramStr])){

                foreach ($routes[$this->_paramStr] as $k => $v) {
                    
                    $key = '_' . $k;

                    $this->$key = $v;
                }

                return true;
            }

            return false;
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
            
            $subdomain = Config::get('global.subdomain');
            //如果配置了子域名，则强制指定子域名对应的分组
            if(isset($subdomain) && $subdomain){

                $host = $this->_request->server('HTTP_HOST');

                if(isset($subdomain[$host])){

                    $this->_group = $this->toCamel($subdomain[$host]);
                }
            }

            $controllerPath = "\\App\\C\\" . ($this->_group ? $this->_group . '\\' : '') . $this->_controller;

            if(!class_exists($controllerPath)){

                $prefixGroup = $this->_group . ($this->_group?'/':'');

                throw new Exception("找不到指定的控制器:" . $prefixGroup . $this->_controller . '[' . $this->_request->server('REQUEST_URI') . ']', 101);
            }

            if(!method_exists($controllerPath, $this->_action)){

                $prefixGroup = $this->_group . ($this->_group?'/':'');

                throw new Exception("找不到指定的处理方法:" . $prefixGroup . $this->_controller . '/' . $this->_action, 102);
            }

            $this->_request->setController($this->_controller);

            $this->_request->setAction($this->_action);

            $this->_request->setGroup($this->_group);

            $this->_request->setParamStr($this->_params, $this->_seperator);

            $this->_request->parse();

            $response = Response::getInstance($this->_request);

            $response->setSqlDebugMode(Config::get('global.debugSql'));

            $controller = new $controllerPath($this->_request, $response);

            call_user_func([$controller, $this->_action], $this->_request, $response);
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
