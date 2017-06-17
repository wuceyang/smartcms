<?php
namespace App\Helper;

class KeyMap
{

    //用户资料
    const UserProfile = [
        'id'     => 'uid',
        'name'   => 'nickname',
        'avatar' => 'avatar',
    ];
    //用户当前正在学习的课程信息
    const StudyInfo = [
        'pkgid'     => 'pkgid',
        'name'      => 'pkgname',
        'cover'     => 'cover',
        'starttime' => 'starttime',
        'endtime'   => 'endtime',
        'cname'     => 'course_name',
        'cid'       => 'cid'
    ];
    //开班信息列表
    const PackageList = [
        'pkgid'            => 'pkgid',
        'name'             => 'pkgname',
        'cover'            => 'cover',
        'status'           => 'status',
        'pkgstatus'        => 'pkgstatus',
        'starttime'        => 'starttime',
        'endtime'          => 'endtime',
        'price'            => 'price',
        'fullprice'        => 'fullprice',
        'discount'         => 'discount',
        'sold'             => 'sold',
        'max_discount_num' => 'max_discount_num',
        'saved'            => 'saved',
        'invite_discount'  => 'invite_discount',
        'pkgtype'          => 'pkgtype',
        'invite_cashback'  => 'invite_cashback',
        'cover'            => 'cover',
    ];

    //开班详情
    const PackageInfo = [
        'id'               => 'pkgid',
        'name'             => 'pkgname',
        'price'            => 'price',
        'fullprice'        => 'fullprice',
        'starttime'        => 'starttime',
        'endtime'          => 'endtime',
        'description'      => 'pkg_info',
        'faq'              => 'faq',
        'course'           => 'course',
        'status'           => 'status',
        'pkgstatus'        => 'pkgstatus',
        'sold'             => 'sold',
        'max_discount_num' => 'max_discount_num',
        'saved'            => 'saved',
        'invite_discount'  => 'invite_discount',
        'pkgtype'          => 'pkgtype',
        'invite_cashback'  => 'invite_cashback',
        'pkg_video'        => 'pkg_video',
        'pkg_imgs'         => 'pkg_imgs',
        'pkg_feature_imgs' => 'pkg_feature_imgs',
        'pkg_teacher_imgs' => 'pkg_teacher_imgs',
        'pkg_covers'       => 'pkg_covers',
        'pkg_video_thumb'  => 'pkg_video_thumb',
        'buyname'          => 'name',
        'buyphone'         => 'phone',
        'transid'          => 'transid',
        'invitecode'       => 'invitecode',
        'createtime'       => 'createtime',
    ];

    //开班详情里的课程详情
    /*const CourseList = [
        'id'      => 'cid',
        'name'    => 'course_name',
        'status'  => 'status',
        'info'    => 'course_info',
        'preview' => 'preview',
        'lock'    => 'lock_status',
        'unlock'  => 'auto_unlock',
    ];*/

    //已购买课程列表
    const Purchased = [
        'id'     => 'id',
        'title'  => 'p_name',
        'cover'  => 'p_covers',
        'amount' => 'amount',
        'type'   => 'type',
    ];
    //课程详情
    const CourseInfo = [
        'id'              => 'cid',
        'name'            => 'course_name',
        'cover'           => 'cover',
        'cinfo'           => 'course_info',
        'vurl'            => 'video_url',
        'course_doc_imgs' => 'course_doc_imgs',
        'course_work'     => 'course_work'
    ];
    //后台用户列表
    const AdminMemberList = [
        'id'         => 'uid',
        'nickname'   => 'nickname',
        'gender'     => 'gender',
        'avatar'     => 'avatar',
        'invitecode' => 'invitecode',
        'balance'    => 'balance',
        'createdate' => 'binddate'
    ];

    //后台开班信息列表
    const AdminPackageList = [
        'id'      => 'pkgid',
        'name'    => 'pkgname',
        'cover'   => 'cover',
        'reserve' => 'reserve',
        'status'  => 'status',
    ];
    //后台课程详情
    const AdminPkgInfo = [
        'id'               => 'pkgid',
        'name'             => 'pkgname',
        'cover'            => 'cover',
        'fullprice'        => 'fullprice',
        'price'            => 'price',
        'status'           => 'status',
        'starttime'        => 'starttime',
        'endtime'          => 'endtime',
        'feature'          => 'pkg_feature_imgs',
        'teacher'          => 'pkg_teacher_imgs',
        'description'      => 'pkg_info',
        'faq'              => 'faq',
        'max_discount_num' => 'max_discount_num',
        'listorder'        => 'listorder',
        'pkgtype'          => 'pkgtype',
        'invite_discount'  => 'invite_discount',
        'invite_cashback'  => 'invite_cashback',
        'pkg_imgs'         => 'pkg_imgs',
        'pkg_video'        => 'pkg_video',
        'pkg_feature_imgs' => 'pkg_feature_imgs',
        'pkg_teacher_imgs' => 'pkg_teacher_imgs',
        'pkg_covers'       => 'pkg_covers',
        'pkg_video_thumb'  => 'pkg_video_thumb',
    ];

//    //后台课程列表
//    const AdminCourseList = [
//        'id'          => 'cid',
//        'name'        => 'course_name',
//        'cover'       => 'cover',
//        'preview'     => 'preview',
//        'lock_status' => 'lock_status',
//        'auto_unlock' => 'auto_unlock',
//        'video_url'   => 'video_url',
//        'course_info' => 'course_info',
//        'show_order'  => 'show_order',
//        'status'      => 'status',
//    ];

    const AdminPurchaseList = [
        'id'           => 'id',
        'pkgname'      => 'pkgname',
        'price'        => 'price',
        'time'         => 'createtime',
        'code'         => 'invitecode',
        'uid'          => 'uid',
        'username'     => 'username',
        'avatar'       => 'avatar',
        'name'         => 'name',
        'phone'        => 'phone',
        'remarks'      => 'remarks',
        'additioninfo' => 'additioninfo',
        'transid'      => 'transid',
    ];

    //后台课程二维码
    const AdminQrCodeInfo = [
        'pkgid'       => 'pkgid',
        'channel'     => 'channel',
        'pkgname'     => 'pkgname',
        'qrcode_path' => 'qrcode_path',
        'create_time' => 'create_time',
    ];


    //app课程列表
    const ProjectList = [
        'id'        => 'pid',
        'title'     => 'p_name',
        'status'    => 'p_status',
        'purchased' => 'p_purchased',
        'target'    => 'p_target_number',
        'price'     => 'p_price',
        'cover'     => 'p_covers',
        'timeleft'  => 'timeleft',
        'amount'    => 'p_collected_amount',
    ];

    const ProjectInfo = [
        'id'        => 'pid',
        'title'     => 'p_name',
        'status'    => 'p_status',
        'purchased' => 'p_purchased',
        'target'    => 'p_target_number',
        'price'     => 'p_price',
        'cover'     => 'p_covers',
        'video'     => 'p_video',
        'result'    => 'p_result',
        'demotip'   => 'p_demo_tip',
        'guarantee' => 'p_platform_img',
        'sponsor'   => 'p_sponsor',
        'prodesc'   => 'p_project_detail',
        'total'     => 'p_collected_amount',
        'endtime'   => 'p_end_time',
        'timeleft'  => 'timeleft',
        'contact'   => 'p_pay_config',
    ];

    const ProjectOrderInfo = [
        'id'        => 'oid',
        'cover'     => 'cover',
        'title'     => 'title',
        'transid'   => 'transid',
        'tradetime' => 'createtime',
        'contact'   => 'contact',
        'memo'      => 'buyer_remarks',
        'name'      => 'buyer_name',
        'phone'     => 'buyer_phone',
        'payamount' => 'amount',
        'price'     => 'price',
        'targetid'  => 'pid',
        'type'      => 'transtype',
        'zcprice'   => 'zcprice',
    ];

    const BannerList = [
        'url'       => "b_img_url",
        'link'      => "b_jump_url",
        'starttime' => "starttime",
        'endtime'   => "endtime",
        'bid'       => "bid",
    ];

    const AdminBannerList = [
        'url'        => "b_img_url",
        'link'       => "b_jump_url",
        'status'     => "status",
        'createtime' => "createtime",
        'id'         => "bid",
        'order'      => "banner_order",
    ];
    const AdminBannerDetail = [
        'url'        => "b_img_url",
        'link'       => "b_jump_url",
        'status'     => "status",
        'createtime' => "createtime",
        'id'         => "bid",
        'order'      => "banner_order",
        'starttime'  => "starttime",
        'endtime'    => "endtime",
    ];

    const  AdminProjectList = [
        'id'         => 'pid',
        'title'      => 'p_name',
        'cover'      => 'p_covers',
        'status'     => "p_status",
        'result'     => "p_result",
        'createtime' => "p_createtime",
        'endtime'    => 'p_end_time',
        'purchased'  => 'p_purchased',
        'target'     => 'p_target_number',
        'total'      => 'p_collected_amount',
    ];

    const AdminProjectInfo = [
        'id'           => 'pid',
        'title'        => 'p_name',
        'status'       => 'p_status',
        'purchased'    => 'p_purchased',
        'target'       => 'p_target_number',
        'price'        => 'p_price',
        'cover'        => 'p_covers',
        'video'        => 'p_video',
        'result'       => 'p_result',
        'demotip'      => 'p_demo_tip',
        'guarantee'    => 'p_platform_img',
        'prodesc'      => 'p_project_detail',
        'total'        => 'p_collected_amount',
        'endtime'      => 'p_end_time',
        'contact'      => 'p_pay_config',
        'sponsorname'  => 'name',
        'sponsorintro' => 'introduce',
        'sponsorcover' => 'avatar',
    ];

    const  AdminContactList = [
        'name'       => 'contact_name',
        'cid'        => 'id',
        'phone'      => 'contact_phone',
        'createtime' => "createtime",
    ];

    const AdminRefundList = [
        'id'             => 'id', //退款记录ID
        'nickname'       => 'nickname', //退款的用户昵称
        'avatar'         => 'avatar', //退款用户头像
        'uid'            => 'uid', //退款用户ID
        'transid'        => 'transid', //链接地址
        'totalfee'       => 'total_fee', //订单总金额
        'refundfee'      => 'refund_fee', //申请退款金额
        'settlementfee'  => 'settlement_refund_fee', //实际退款金额
        'refundable_fee' => 'refundable_fee', //剩余可退款金额
        'status'         => 'status', //退款状态,1:已申请,2:退款成功,3:退款失败
        'createtime'     => 'createtime', //申请退款时间
        'remarks'        => 'remarks' //退款备注
    ];


    const AdminRefundCheckList = [
        'id'           => 'id', //退款记录ID
        'title'        => 'title', //退款请求的名称
        'img'          => 'img', //退款请求的图片
        'refund_money' => 'refundamount', //申请退款金额
        'status'       => 'status', //退款状态,1:已申请,2:退款成功,3:退款失败
        'createtime'   => 'createtime', //申请退款时间
        'updatetime'   => 'updatetime', //申请退款时间
        'remarks'      => 'refund_remarks', //退款备注
        'memo'         => 'memo' //退款操作备注
    ];


	const AdminRefundConfirmList = [
        'id'           => 'id', //退款记录ID
        'title'        => 'title', //退款请求的名称
        'img'          => 'img', //退款请求的图片
        'refund_money' => 'refundamount', //没人退款金额,0为退全款
        'status'       => 'status', //退款状态,1:已申请,2:退款成功,3:退款失败
        'createtime'   => 'starttime', //申请退款时间
        'lasttime'     => 'lasttime', //申请退款时间
        'remarks'      => 'remarks', //退款备注
        'type'         => 'refundtype', //退款类型,1单笔,2批量
        'totaltrans'   => 'totaltrans', //应退还总笔数
        'successtrans' => 'successtrans', //成功退还笔数
        'failtrans'    => 'failtrans', //退还失败笔数
        'totalamount'  => 'totalamount', //申请退款总金额
        'finishamount' => 'finishamount', //已完成退款总金额
        'checkid'      => 'checkid', //审核记录ID
        'targetid'     => 'targetid', //产品或众筹id
        'targettype'   => 'targettype', //交易类型,1表示众筹,2表示商品
        'memo'         => 'memo' //退款操作备注
	];

	const AdminWishList = [
        'wid'       => 'id',
        'teacher'   =>'teacher_name',
        'content'   => 'course_content',
        'creattime' => 'creattime',
        'nick'      => 'uname',
        'avatar'    => 'uavatar',
        'uid'       => 'uid',
	];


	const LiveChatRoomList = [
        'chatroom'    => 'cid', //直播间ID
        'title'       => 'title', //退款请求的名称
        'students'    => 'member_numbers', //学生数量
        'status'      => 'status', //退款状态,1:已申请,2:退款成功,3:退款失败
        'createtime'  => 'createtime', //申请退款时间
        'type'        => 'createtype', //创建类型
        'relative_id' => 'rid', //相关联的课程或者众筹项目ID
        'cover'       => 'cover', //直播间封面
        'teacher'     => 'teacher_name', //授课老师名字
        'starttime'   => 'starttime', //开课时间
	];

	const LiveChatRoomDetail = [
//        'chatroom'      => 'cid', //直播间ID
        'title'         => 'title', //退款请求的名称
        'status'        => 'status', //
        'type'          => 'createtype', //创建类型
        'relative_id'   => 'rid', //相关联的课程或者众筹项目ID
        'cover'         => 'cover', //直播间封面
        'starttime'     => 'starttime', //开课时间
        'endtime'       => 'endtime', //开课时间
        'teacher'       => 'teacher_name', //授课老师名字
        'avatar'        => 'teacher_img', //授课老师头像
        'about_teacher' => 'teacher_desc', //老师简介
        'timeleft'      => 'timeleft', //开课时间
        'role'          => 'role', //角色,1表示老师,2表示观众
		'chatroomid'   => 'chatroomid', //第三方聊天室id
		'teacherid'   => 'teacher_id', //授课老师id
	];

	const LiveChatRommMsg = [
        'msgid'       => 'id', //消息ID
        'msg_content' => 'content', //消息内容
        'avatar'      => 'sender_avatar', //发送者头像
        'nick'        => 'sender_name', //发送者昵称
        'fromid'      => 'senderid', //发送者id
        'msg_time'    => 'createtime', //消息时间
        'type'        => 'msg_type', //消息类型
        'att_url'     => 'url', //附件下载地址
        'resourceid'  => 'resourceid', //微信资源id,仅对音频消息有效
        'duration'    => 'duration', //音视频文件时长
        'readed'      => 'i_read', //是否已读
        'replyMsg'    => 'i_replyMsg', //回复消息
	];


	const AdminProjectPriceConfig = [
        'id'      => 'id',
        'amount'  => 'amount',
        'p_desc'  => 'p_desc',
        'p_name'  => 'p_name',
        'total'   => 'total',
        'p_type'  => 'p_type',
        'fulfill' => 'fulfill',
	];
	const ProjectPriceConfig = [
        'id'        => 'id',
        'amount'    => 'amount',
        'desc'      => 'p_desc',
        'name'      => 'p_name',
        'total'     => 'total',
        'type'      => 'p_type',
        'fulfill'   => 'fulfill',
        'purchased' => 'purchased',
	];


	const ProductList = [
        'id'        => 'pid',
        'title'     => 'p_name',
        'status'    => 'p_status',
        'purchased' => 'p_purchased',
        'target'    => 'p_target_number',
        'price'     => 'p_price',
        'cover'     => 'p_covers',
        'timeleft'  => 'timeleft',
        'amount'    => 'p_collected_amount',
        'teacher'   => 'teacher',
        'type'      => 'type',
	];
    //课程详情
    const CourseDetail = [
        'id'         => 'cid',
        'type'       => 'p_type',
        'cover'      => 'p_covers',
        'title'      => 'p_name',
        'video'      => 'p_video',
        'status'     => 'p_status',
        'demotip'    => 'p_demo_tip',
        'guarantee'  => 'p_platform_img',
        'teacher'    => 'p_teacher',
        'prodesc'    => 'p_course_detail',
        'purchased'  => 'p_purchased',
        'relatedpid' => 'p_proid',
        'supported'  => 'supported',
        'price'      => 'p_price',
        'chatroom'   => 'chatroom',
    ];
    //课程列表
    const CourseList = [
        'id'        => 'cid',
        'cover'     => 'p_covers',
        'title'     => 'p_name',
        'status'    => 'p_status',
        'purchased' => 'p_purchased',
        'teacher'   => 'p_teacher',
        'price'     => 'p_price',
        'type'      => 'p_type',
    ];

	//后台管理系统课程列表
	const AdminCourseList = [
		'id'        => 'cid',
		'cover'     => 'p_covers',
		'title'     => 'p_name',
		'status'    => 'p_status',
		'purchased' => 'p_purchased',
		'price'     => 'p_price',
		'createtime'     => 'p_createtime',
	];

	//后台管理系统课程列表
	const AdminCourseDetail = [
		'id'           => 'cid',
		'cover'        => 'p_covers',
		'title'        => 'p_name',
		'status'       => 'p_status',
		'purchased'    => 'p_purchased',
		'price'        => 'p_price',
		'video'        => 'p_video',
		'demotip'      => 'p_demo_tip',
		'guarantee'    => 'p_platform_img',
		'prodesc'      => 'p_course_detail',
		'contact'      => 'p_pay_config',
		'sponsorname'  => 'name',
		'sponsorintro' => 'introduce',
		'sponsorcover' => 'avatar',
		'starttime'    => 'starttime',

	];
}