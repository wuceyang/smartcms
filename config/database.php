<?php
    return [
        'driver'    => 'mysql',
        //是否使用长连接
        'persistent'=> true,
        //设置数据取出的格式，可以使用PDO的取出方式，如:PDO::FETCH_ASSOC等
        'fetchMode' => \PDO::FETCH_ASSOC,
        //表前缀
        'tablePrefix' => '',
        'mysql' => [
            //只读连接设置，设置此项时，default设置无效
            //默认连接设置，不需要读写分离时设置此项代替。否则此项设置无效
            'default' => [
                'host' 		=> '127.0.0.1',
                'port' 		=> '3306',
                'username' 	=> 'root',
                'password' 	=> '123456',
                'dbname' 	=> 'smartcms',
                'charset' 	=> 'utf8'
            ],
        ],
        'redis' => [
            //默认Redis连接
            'default' => [
                'host' 		=> '127.0.0.1',
                'port'      => 6379,
                'database'  => 0
            ],
        ],
    ];
