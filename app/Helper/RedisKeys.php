<?php
    namespace App\Helper;

    //定义key时，无需设置前缀。所有在config/database.php中的redis key的前缀，是在执行redis命令时自动添加
    class RedisKeys{

        //用户信息,后缀:用户ID,存:hmset, 取:hgetall
        const USER_PROFILE                  = 'user_profile_';
    }
