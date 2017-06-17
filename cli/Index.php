<?php
    define('ENV', 'CLI');
    //加载常量定义文件
    include __DIR__ . '/../config/defined.php';
    //加载自动加载文件
    $loader = include APP_ROOT . '/vendor/autoload.php';
    
    //注册类别名映射
    $alias = \Library\Common\Config::get('global.alias');
    foreach ($alias as $k => $v) {

        class_alias($v, $k, true);
    }

    if(count($argv[1]) == 0){

        echo "参数名称错误，请填写要运行的服务";
        exit;
    }

    $service   = $argv[1];
    
    $service   = substr($service, -4) == '.php' ? substr($service, 0, -4) : $service;
    
    $className = '\\Cli\\' . $service;

    if(!class_exists($className)){

        echo $className . "不存在，请检查代码";

        exit;
    }

    $args     = array_slice($argv, 2);
    
    $request  = \Library\Request\Request::getInstance();
    
    $response = \Library\Response\Response::getInstance($request);
    
    $instance = new $className($request, $response);

    call_user_func_array([$instance, 'start'], $args);