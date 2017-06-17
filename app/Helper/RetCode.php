<?php
	namespace App\Helper;

	class RetCode{

		const Client = [
						'empty_param'     => 101,	//参数为空
						'format_error'    => 102,	//参数格式错误
						'invalid_param'   => 103, //参数错误
						'invalid_request' => 104, //非法请求
						];

		const Server = [
						'ok'               => 200,//服务器处理成功
						'data_not_found'   => 201,//服务器找不到指定参数的数据
						'process_failed'   => 202,//服务器最终处理失败
						'incorrect_status' => 203, //指定对象状态不正确
						'internal_error'   => 204, //内部错误
					   ];

		const Remote = [
						'return_error' 	 => 301, //远程服务器错误
					   ];

		const User  = [
						'not_login' 	 => 401, //用户未登录
					  ];
	}