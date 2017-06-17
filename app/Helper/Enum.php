<?php

namespace App\Helper;

class Enum
{

	//通用状态
	const Status = [
		'normal' => 1,
		'disable' => 2,
	];

	//课程锁定状态
	const LockStatus = [
		'locked' => 1,
		'unlocked' => 2,
	];
	//课程预览状态
	const PreviewStatus = [
		'open' => 1,
		'close' => 2,
	];
	//开班状态
	const PkgStatus = [
		"hidden" => 1, //隐藏
		"subscribe" => 2, //可预约
		"purchase" => 3, //可购买
		'subscribed' => 4, //已预约
		'purchased' => 5, //已购买
		'none' => 6, //未预约，未购买
		'removed' => 7, //已删除
	];
	//折扣信息
	const Discount = [
		'subscribe' => 1, //预约
		'invite' => 2, //邀请
		'invited' => 3, //被邀请
	];

	//交易目标
	const TradeTarget = [
		'package' => 1, //课程包(开班信息)
	];

	//交易类型
	const TradeType = [
		'purchase' => 1, //购买
		'recharge' => 2, //充值
	];

	//交易平台
	const TradePlatform = [
		'wechat' => 1, //微信
	];
	//交易支付状态
	const TradeStatus = [
		'topay' => 1, //等待支付
		'paied' => 2, //支付成功
		'failed' => 3, //支付失败
	];

	//现金流水类型
	const CashFlowType = [
		'invite' => 1, //邀请用户获得资金
	];

	const UserType = [
		'administrator' => 1, //后台管理员
		'member' => 2, //普通前台用户
	];

	const APP_PAGE = [
		//开班详情
		'pkginfo' => "pages/course/courseDetailPage",
		'index' => 'pages/index/index',
	];

	//课程类型
	const PKG_TYPE = [
		'lesson' => 1,  //普通课程
		'youxue' => 2   //游学
	];


	//
	const  ProjectStatus = [
		'incoming' => 1, //预热中
		'publishing' => 2, //众筹中
		'finished' => 3, //众筹结束
		'disable' => 4, //隐藏
		'soldout' => 5, //售罄
		'deleted' => 6, //删除
	];
	const  ProjectResult = [
		'counting' => 0, //正在计算结果
		'success' => 1, //成功
		'failed' => 2, //失败
	];

	const BannerStatus = [
		'offboard' => 1, //已下架
		'onboard' => 2, //已上架
		'deleted' => 3, //删除
	];

	//商品状态
	const  ProductStatus = [
		'onsell' => 1, //预热中
		'soldout' => 2, //售罄
		'disable' => 3, //隐藏
		'deleted' => 4, //删除
	];

	//退款状态
	const RefundStatus = [
		'apply' => 1,  //申请中
		'success' => 2,  //退款成功
		'failed' => 3,  //退款失败
		'close' => 4,  //退款关闭
		'exception' => 5,  //退款异常
		'pending' => 6, //自定义状态，发起退款时，暂有一笔退款未完成，需等待后，再次退款
	];


	//退款二次确认状态
	const RefundCheckStatus = [
		'init' => 0,  //等待确认
		'apply' => 1,  //退款请求被接受
		'reject' => 2,  //退款被拒绝
		'removed' => 3,  //隐藏
	];

	//订单类型
	const TransType = [
		'project' => 1, //众筹
		'product' => 2, //产品
	];

	const RefundType = [
		'single' => 1, //单笔
		'batch'  => 2, //批量
	];


	const ChatRoomCreateType = [
		'project' => 1,		//中筹项目创建
		'course' => 2,		//课程创建
	];

	const ChatRoomStatus = [
		'waiting' => 1,		//未开课
		'started' => 2,		//直播中
		'finished' => 3,	//已结束
	];

	const ChatRoomFilter = [
		'started' => 1,		//直播中或者尚未开始
		'finished' => 2,		//已结束
		'openbyme' => 3,	//我发起的
	];

	const ChatRoomMsgType = [
		'txt' => 1,
		'img' => 2,
		'audio' => 3,
		'reply' => 4,
		'delete' => 5,
	];
	const ProjectPriceStatus = [
		'normal' => 1,
		'delete' => 2,
	];

	//课程状态
	const  CourseStatus = [
		'incoming' => 1, //待上架
		'publishing' => 2, //发售中
		'soldout' => 3, //售罄
		'deleted' => 4, //删除
	];

	/**
	 * 直播间角色
	 */
	const ChatroomRole = [
		'teacher' => 1,
		'audience' => 2,
	];

}