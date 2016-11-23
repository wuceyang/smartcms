<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Menu;
    use \App\M\UserGroup;
    use \App\M\GroupPrivilege;

    class sideBar extends Base{

        public function getSideBar(Request $req, Response $resp){

            $userProfile = $req->session()->get($this->_sessKey);

            $userGroups  = explode(',', trim($userProfile, ','));

            if(!$userGroups){

                return $this->error("您暂无任务权限，请等待管理员分配权限", 103);
            }

            $privilege   = new GroupPrivilege();

            $groupMenus  = $privilege->getGroupPrivileges($userGroups);

            $menuIds     = array_column($userMenus, 'menu_id');

            $menuIds     = array_map('trim', $menuIds);

            $userMenus   = implode(',', $menuIds);

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
                                                    'sub'   => &$userMenu[$v['id']],
                                                ];
            }

            $param = [
                        'menu' => $userMenu,
                     ];

            return $resp->withView('admin/index.html')->withParams($param)->display();
        }
    }