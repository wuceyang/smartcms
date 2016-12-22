<?php
	namespace App\C\Admin;

	use \Request;
	use \Response;

	use \App\M\Menu as mMenu;

	class Menu extends Base{

		public function index(Request $req, Response $resp){

			$parentId 	= $status = null;

			$menu 		= new mMenu();

			$pagesize   = 20;

			$menuList 	= $menu->getList($parentId, $status);

			$menus 		= [];

			foreach ($menuList as $k => $v) {
				
				if(!isset($menus[$v['parent_id']])){

					$menus[$v['parent_id']] = [];
				}

				$item = array_merge($v, ['sublist' => &$menus[$v['id']]]);

				$menus[$v['parent_id']][] = $item;
			}

			$params = ['list' => $menus];

			return $resp->withView('admin/menu_list.html')->withVars($params)->display();
		}

		public function switchMenu(Request $req, Response $resp){

			$mid 	= intval($req->post('mid'));

			$status = intval($req->post('status'));

			$menu 	= mMenu();

			if(!in_array($status, [Enum::STATUS_DISABLED, Enum::STATUS_NORMAL])){

				return $this->error('指定的菜单状态不正确', 101, 'javascript:history.back()');
			}

			if(!$menu->updateMenuInfo($mid, '', '', $status)){

				return $this->error('更新菜单状态失败', 101, 'javascript:history.back()');
			}

			return $this->success('更新菜单状态成功', 'javascript:history.back()');
		}

		public function editMenu(Request $req, Response $resp){

			$mid 		= intval($req->post('mid'));

			$title  	= trim($req->post('title'));

			$url 		= trim($req->post('url'));

			$order      = intval($req->post('order'));

			$parentid 	= intval($req->post('parentid'));

			$showorder 	= intval($req->post('order'));

			$icon 		= trim($req->post('icon'));

			if(!$title){

				return $this->error('菜单名称不能为空', 101, 'javascript:history.back()');
			}

			if($parentid && !$url){

				return $this->error('菜单链接地址不能为空', 101, 'javascript:history.back()');
			}

			if(!$showorder){

				return $this->error('菜单名称不能为空', 101, 'javascript:history.back()');
			}

			$brothers = $menu->getList($parentid, null);

			if(!$menu->updateMenuInfo($mid, $title, $url, 1, $order, $parentid, $icon)){

				return $this->error('更新菜单信息失败', 102, 'javascript:history.back()');
			}

			return $this->success('更新菜单信息成功', '/admin/menu/index');
		}

		public function addMenu(Request $req, Response $resp){

			$title  	= trim($req->post('title'));

			$url 		= trim($req->post('url'));

			$parentid 	= intval($req->post('parentid'));

			$icon 		= trim($req->post('icon'));

			if(!$title){

				return $this->error('菜单名称不能为空', 101, 'javascript:history.back()');
			}

			if($parentid && !$url){

				return $this->error('菜单链接地址不能为空', 101, 'javascript:history.back()');
			}

			if($parentid == 0){

				$url = '';
			}

			$menu = new mMenu();

			$brothers = $menu->getList($parentid, null);

			$order = ($brothers ? max(array_column($brothers, 'show_order')) : 0 ) + 1;

			if(!$menu->addMenu($title, $url, $parentid, $icon, $order)){

				return $this->error('菜单添加失败', 102, 'javascript:history.back()');
			}

			return $this->success('菜单添加成功', '/admin/menu/index');
		}
	}