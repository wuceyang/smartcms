<?php
	namespace App\C\Admin;

	use \App\C\BaseController;
	use \App\M\GroupPrivilege;
	use \App\M\Menu;
	use \Request;
	use \Response;
	use \PbParser;
	use \Config;
	use \Log;

	class Base extends BaseController{

		protected $_sesskey = 'userinfo';

		protected $_retAjax = true;

		protected $userinfo = [];

		public function __construct(Request $req, Response $resp){

			parent::__construct($req, $resp);

			$this->_retAjax = $req->isAjax();

			$noLoginRequest = [

				'user' => [
					'login',
				],
			];

			$controller = strtolower($req->getController());

			$action 	= strtolower($req->getAction());

			$req->session()->start();

			$this->userinfo = $req->session()->get($this->_sesskey);

			$this->initViewVars($req, $resp);

			if(!isset($noLoginRequest[$controller]) || !in_array($action, $noLoginRequest[$controller])){

				$this->checkLogin($req, $resp);
			}

			if($this->userinfo){

				$resp->withVars(['userinfo' => $this->userinfo]);
			}
		}

		protected function initViewVars($req, $resp){

			$resp->withVars(['__THEME_PATH__' => '/theme/default/']);

			$params = [
						'menu' => $this->initSideBar(),
					  ];

			$resp->withVars($params);
		}
		//指定返回的数据类型
		protected function dataType($isAjax = true){

			$this->_retAjax = $isAjax;
		}

        //返回错误信息
        protected function error($message = '', $err_code = 101, $uri = 'javascript:history.back();'){

			$data = ['code' => $err_code, 'message' => $message, 'uri' => $uri];

			return $this->response($data);
		}

        //返回成功信息
		protected function success($message = '', $uri = 'javascript:;', $retdata = ''){

			$data = ['code' => 200, 'message' => $message, 'uri' => $uri];

			$data['data'] = $retdata;

			return $this->response($data);
		}
		//信息返回
		private function response($retdata){

			$this->_retAjax ? $this->retAjax($retdata) : $this->retHtml($retdata);
		}
		//返回json
		private function retAjax($data){

			exit(json_encode($data, JSON_UNESCAPED_UNICODE));
		}
		//返回HTML信息提示页面
		private function retHtml($data){

			$req  = Request::getInstance();

			$resp = Response::getInstance($req);

			return $resp->withVars($data)->withView('admin/info.html')->display();
		}

		//检查用户登录
		protected function checkLogin($req, $resp, $return = false){

			$flag = true;

			if(!$this->userinfo){

				$flag = false;
			}

            if($return){

				return $flag;
			}

			if(!$return && !$flag){

				$this->error("用户登录过期，请重新登录", 101, '/admin/user/login');
			}
		}

		/**
		 * 获取分页信息
		 * @param  string  	$baseUrl     页面链接地址
		 * @param  int  	$currentPage 当前页码
		 * @param  int  	$totalRecord 记录总条数
		 * @param  array   	$params      url上传递的其他参数
		 * @param  int 		$pagesize    每页显示的记录数量
		 * @return array
		 */
		public function getPageInfo($baseUrl, $currentPage, $totalRecord, $params = [], $pagesize = 20){

			$baseUrl    = $baseUrl . ($params ? '?' . http_build_query($params) : '');

            $pageParams = [
                            'baseUrl'       => $baseUrl,

                            'curPage'       => $currentPage,

                            'totalPage'     => ceil($totalRecord / $pagesize),

                            'wrapTag'       => 'li',

                            'curpageClass'  => 'active',

                            'wrapTagClass'  => true,
                          ];

            $pageParams['pageStr'] = (new \App\Helper\Pager($pageParams))->getPageLinks();

            $pageParams['totalRecord'] = $totalRecord;

            return $pageParams;
		}

		/**
		 * 生成唯一token，防止重复提交表单
		 */
		public function setFormToken($req, $resp, $tokenName = 'token'){

			$token 						=  md5(uniqid());

			$this->userinfo['token'] 	= $token;

			$req->session()->set($this->_sesskey, $this->userinfo);

			$resp->withVars([$tokenName => $token]);
		}

		/**
		 * 验证表单中提交的token
		 * @param  Request 	$req       Request实例
		 * @param  Response $resp      Response实例
		 * @param  string 	$tokenName 表单中的token字段名
		 * @return bool
		 */
		public function formTokenValidate($req, $resp, $tokenName = 'token'){

			$flag  = false;
			
			$token = trim($req->post($tokenName, ""));

			if($token && isset($this->userinfo['token']) && $this->userinfo['token'] == $token){

				$flag = true;
			}
			//销毁token
			unset($this->userinfo['token']);

			$req->session()->set($this->_sesskey, $this->userinfo);

			return $flag;
		}
		/**
		 * 初始化菜单列表
		 * @return array
		 */
		public function initSideBar(){

			if(isset($this->userinfo['menu'])){

				return $this->userinfo['menu'];
			}

			if(!$this->userinfo){

				return;
			}

            $userGroups  = explode(',', trim($this->userinfo['group_id'], ','));

            if(!$userGroups){

                return $this->error("您暂无任何权限，请等待管理员分配权限", 103, '/admin/user/login');
            }

            $privilege   = new GroupPrivilege();

            $groupMenus  = $privilege->getGroupPrivileges($userGroups);

            if(!$groupMenus){

                return $this->error("当前分组暂无任何权限，请等待管理员分配权限", 103, '/admin/user/login');
            }

            $menuIds     = array_column($groupMenus, 'menu_id');

            $menuIds     = array_map('trim', $menuIds);

            $userMenus   = explode(',', implode(',', $menuIds));

            $menu        = new Menu();

            $menuInfo    = $menu->getMenuInfoById($userMenus);

            $userMenu    = [];

            foreach($menuInfo as $k => $v){

                if(!isset($userMenu[$v['parent_id']])){

                    $userMenu[$v['parent_id']] = [];
                }

                $userMenu[$v['parent_id']][] = [
                                                    'id'    => $v['id'],
                                                    'name'  => $v['title'],
                                                    'url'   => $v['url'],
                                                    'icon'  => $v['icon'],
                                                    'sub'   => &$userMenu[$v['id']],
                                                ];
            }

            $this->userinfo['menu'] = $userMenu;

            return $userMenu;
		}
	}