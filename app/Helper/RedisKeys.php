<?php
    namespace App\Helper;

    use \Config;

    class RedisKeys{

        //分类详情，后面接分类的id
        const CATEGORY_INFO = CACHE_PREFIX . 'category_detail_';
        //按父分类的列表,zadd加入，按照id排序
        const CATEGORY_LIST = CACHE_PREFIX . 'category_list_';
        //视频剧集列表
        const VIDEO_INFO    = CACHE_PREFIX . 'video_info_';
    }
