<?php
    return [
        //路由格式定义
        'route'     => [
                        //除去query string部分之外的路由字符串格式，以下格式可以使用如下url:/mv/test/go/b/200/c/134.html?a=123（group=mv,controller=test,action=go,params=b/200/c/134）
                        'pattern'       => '/{group}/{controller}/{action}',
                        //不同组成部分可以接受的字符串
                        'group'         => '[\w\-]+',
                        'controller'    => '[\w\-]+',
                        'action'        => '[\w\-]+',
                        'rules'         => '',
                        'params'        => '.+',
                        //非正常query string模式下，params参数分隔符，比如:id-10-order-100,此时，分隔符为-;可以在此参数后面接正常的?和query string。如果参数只放在query string中，seperator请置为空字符串
                        'seperator'     => '/',
                        ],
        //子域名与模块(group)的对应关系
        'subdomain'  => [
                        /*'m.wangxingcm.com' => 'H5'*/
                        ],
        //是否记录日志
        'debug'     => true,
        //是否调试sql语句，在需要调试的模板底部使用<include file="sql.html"/>,如果未true会在该位置输出所有执行过的sql,可用参数:true/false
        'debugSql'  => true,
        //运行环境,可用参数: development:开发环境/production:生产环境
        'env'       => 'development',
        //命名空间映射
        'alias'       => [
            'Log'     => \Library\Log\Log::class,
            'Config'  => \Library\Common\Config::class,
            'Request' => \Library\Request\Request::class,
            'Response'=> \Library\Response\Response::class,
        ],
        //默认方法和默认控制器
        'defaultConfig' => [
                            'group'      => 'index',
                            'controller' => 'index',
                            'action'     => 'index'
        ],
        //cookie设置
        'cookie' => [
            'domain'        => '',
            'path'          => '/',
            'sslOnly'       => false,
            'httpOnly'      => false,
            //cookie有效时间，单位秒
            'maxLifetime'   => 3600 * 30,
            //加密密钥
            'encryptKey' => '5%a-s#$',
        ],
        //session设置
        'session' => [
            //是否自动启动session
            'auto_start' => false,
            //session driver类型
            'driver' => 'redis',
            //连接设置
            'configure' => [
                //redis默认连接，只针对redis驱动有效
                'redis' => 'default',
                //mysql默认连接，只针对mysql驱动有效
                'mysql' => 'default',
                //文件存储路径，只针对file的驱动有效
                'saveDir' => APP_ROOT . 'cache/session/',
                //session最大生存时间,单位:秒
                'maxLifetime' => 3600 * 30,
                //session前缀
                'prefix' => 'zc_',
                //加密密钥
                'encryptKey' => '&1as#r$',
                //session回收百分比,可用值1-100,不建议使用较大的值
                'gc_probability' => 5,
                //session id在cookie中的名称
                'cookieName' => 'SmartSid',
            ]
        ]
    ];
