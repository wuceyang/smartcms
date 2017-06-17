<?php
    //加载常量定义文件
    include __DIR__ . '/../config/defined.php';
    //加载自动加载文件
	$loader = include APP_ROOT . 'vendor/autoload.php';

    //注册类别名映射
    $alias = \Library\Common\Config::get('global.alias');
    foreach ($alias as $k => $v) {
    	class_alias($v, $k, true);
    }

    (new \Library\Router\Router())->dispatch();

?>
