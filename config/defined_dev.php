<?php
    error_reporting(E_ALL);
	//默认时区
	define('TIME_ZONE', 'Asia/Shanghai');
    
	date_default_timezone_set(TIME_ZONE);
    //当前时间戳
    define('TIME', time());
    //IP地址
    if(isset($_SERVER['X-Real-IP'])){

        $ip = $_SERVER['X-Real-IP'];
    }else if(isset($_SERVER['HTTP_X_FORWARED_FOR'])){

        $ip = $_SERVER['HTTP_X_FORWARED_FOR'];
    }elseif (isset($_SERVER["REMOTE_ADDR"])) {

        $ip = $_SERVER["REMOTE_ADDR"];
    }elseif(isset($_SERVER["HTTP_CLIENT_IP"])){

        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }else{

        $ip = '';
    }
    define('IP', $ip);
    //网站根目录
    define('APP_ROOT', dirname(__DIR__) . '/');
    //错误级别
    define('ERROR_LEVEL', E_ALL);
    //缓存前缀，保证缓存时，即使在同一个库，也不会与其他应用冲突
    define('CACHE_PREFIX', 'story_dev_');