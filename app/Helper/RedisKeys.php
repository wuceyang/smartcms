<?php
    namespace App\Helper;

    //缓存KEY，最后带有下划线"_"的表示需要接用户或者内容ID,多个后缀的，以下划线"_"连接(注意，说明文字中是用"+"表示多个后缀)
    class RedisKeys{

        //用户信息,后缀:用户ID,存:hmset, 取:hgetall
        const USER_PROFILE                  = 'user_profile_';
        //banner图片列表，后缀:平台类型（见data.Platform）_广告位置（见data.Position），存:zadd
        const POSITION_BANNER               = 'position_banner_';
        //banner详情，后缀:banner ID，存:hmset, 取:hgetall
        const BANNER_INFO                   = 'banner_info_';
        //app版本信息记录ID，后缀:OS类型+APP类型，见common.DeviceOs && data.AppType, 存:set,取:get
        const APP_RELEASE                   = 'app_release_';
        //app版本信息，后缀:版本记录ID，存:hmset,取:hgetall
        const APP_RELEASE_INFO              = 'app_release_info_';
        //分享内容详情,后缀:分享点，存:hmset,取:hgetall
        const SHARE_INFO                    = 'share_info_';
        //机构信息详情
        const ORGANIZTION_INFO              = 'exam_organization_info_';
        //考试项目信息详情
        const CATEGORY_INFO                 = 'exam_category_info_';
        //考试等级信息详情
        const GRADE_INFO                    = 'exam_grade_info_';
        //考试报名详情
        const EXAM_INFO                     = 'exam_info_';
        //考生报名资料详情
        const EXAM_EXTEND_INFO              = 'exam_extend_info_';
        //学生资料详情
        const STUDENT_INFO                  = 'student_info_';
        //地区信息
        const AREA_INFO                     = 'area_info_';
    }
