<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\Helper\Enum;
    use \App\M\UserGroup;
    use \App\M\User as mUser;

    class User extends Base{

        public function login(Request $req, Response $resp){

            if(!$req->isPost()){

                return $resp->withView("admin/login.html")->display();
            }

            $account = trim($req->post('account'));

            $passwd  = trim($req->post('passwd'));

            if(!$account){

                return $this->error("登录帐号不能为空", 101);
            }

            if(!$passwd){

                return $this->error("登录密码不能为空", 101);
            }

            $user        = new mUser();

            $userProfile = $user->userLogin($account, $passwd);

            if(!$userProfile){

                return $this->error("登录帐号或密码错误",102);
            }

            if($userProfile && $userProfile['status'] != 1){

                return $this->error("当前帐号已经被禁用",102);
            }

            $req->session()->set($this->_sesskey, $userProfile);

            return $this->success("登录成功");
        }

        public function logout(Request $req, Response $resp){

            $req->session()->destroy();

            $this->success("您已退出登录", "/admin/user/login");
        }

        public function resetPwd(Request $req, Response $resp){

            if(!$req->isPost()){

                return $resp->withView('admin/reset_passwd.html')->display();
            }

            $oldPass = trim($req->post('oldpass'));

            $newPass = trim($req->post('newpass'));

            $cnfPass = trim($req->post('cnfpass'));

            if(!$oldPass){

                return $this->error("原始密码不能为空", 101);
            }

            if(!$newPass){

                return $this->error("新密码不能为空", 101);
            }

            if($newPass != $cnfPass){

                return $this->error("新密码和确认密码不一致", 101);
            }

            if(strlen($newPass) < 6 || strlen($newPass) > 16){

                return $this->error("新密码长度必须为6-16位", 101);
            }

            $user = new mUser();

            if($this->userinfo['passwd'] != $user->passwdEncrypt($this->userinfo['account'],$oldPass)){

                return $this->error("原始密码错误，请检查输入", 102);
            }

            if(!$user->updateUserInfo($this->userinfo['id'], ['passwd' => $user->passwdEncrypt($this->userinfo['account'], $newPass)])){

                return $this->error("密码更新失败", 201);
            }

            return $this->success("密码更新成功", "/admin/user/index");
        }

        public function allUser(Request $req, Response $resp){

            $page       = intval($req->get('page'));

            $page       = max(1, $page);

            $pagesize   = 20;

            $user       = new mUser();

            $groupId    = [];

            $userList   = $user->userList(null, null, $page, $pagesize);

            foreach ($userList as $k => $v) {

                $gid                        = explode(',', trim($v['group_id'], ','));
                
                $userList[$k]['groupid']   = $gid;

                $groupId                    = array_merge($groupId, $gid);
            }

            $userGroup  = new UserGroup();

            $groups     = $userGroup->getGroupsById($groupId);

            foreach ($groups as $k => $v) {
                
                $groupMap[$v['id']] = $v['group_name'];
            }

            foreach ($userList as $k => $v) {
                
                $v['groups'] = [];

                foreach ($v['groupid'] as $sk => $sv) {
                    
                    if(isset($groupMap[$sv])){

                        $v['groups'][] = $groupMap[$sv];
                    }
                }

                $userList[$k] = $v;
            }

            $totalUser  = $user->getTotalUser(null);

            $pageInfo   = $this->getPageInfo("/admin/user/all-user", $page, $totalUser, [], $pagesize);

            $allGroups  = $userGroup->getAllGroups();

            $params = [
                        'list'   => $userList,

                        'groups' => $allGroups
                      ];

            $params = $params + $pageInfo;

            return $resp->withVars($params)->withView("admin/user_list.html")->display();

        }

        public function addUser(Request $req, Response $resp){

            $username = trim($req->post('username'));
            
            $account  = trim($req->post('account'));

            $passwd   = trim($req->post('passwd'));
            
            $groupid  = $req->post('groupid');

            if(!$username){

                return $this->error('用户姓名不能为空', 101, 'javascript:history.back();');
            }

            if(!$account){

                return $this->error('登录帐号不能为空', 101, 'javascript:history.back();');
            }

            if(!$groupid){

                return $this->error('请给帐号设置分组', 101, 'javascript:history.back();');
            }

            $groupid = array_map('intval', $groupid);

            $groupid = array_unique($groupid);

            $user    = new mUser();

            $passwd  = $user->passwdEncrypt($account, $passwd);

            if(!$user->addUser($username, $account, $passwd, $groupid)){

                return $this->error('帐号添加失败', 101, 'javascript:history.back();');
            }

            return $this->success('帐号添加成功', '/admin/user/all-user');
        }

        public function editUser(Request $req, Response $resp){

            $userid   = intval($req->post('userid'));

            $username = trim($req->post('username'));
            
            $account  = trim($req->post('account'));

            $passwd   = trim($req->post('passwd'));
            
            $groupid  = $req->post('groupid');
            
            $status   = intval($req->post('status'));

            if(!$username){

                return $this->error('用户姓名不能为空', 101, 'javascript:history.back();');
            }

            if(!$account){

                return $this->error('登录帐号不能为空', 101, 'javascript:history.back();');
            }

            if(!$groupid){

                return $this->error('请给帐号设置分组', 101, 'javascript:history.back();');
            }

            $groupid = array_map('intval', $groupid);

            $groupid = array_unique($groupid);

            if(!in_array($status, [Enum::STATUS_NORMAL, Enum::STATUS_DISABLED])){

                return $this->error('帐号状态不正确', 101, 'javascript:history.back();');
            }

            $user = new mUser();

            $updateInfo = [
                            'account'  => $account,
                            'username' => $username,
                            'status'   => $status,
                            'group_id' => ',' . implode(',', $groupid) . ',',
                          ];

            if($passwd){

                $updateInfo['passwd'] = $user->passwdEncrypt($account, $passwd);
            }

            if(!$user->updateUserInfo($userid, $updateInfo)){

                return $this->error('帐号信息更新失败', 101, 'javascript:history.back();');
            }

            return $this->success('帐号信息更新成功', 'javascript:history.back();');
        }

        public function switchUser(Request $req, Response $resp){

            $userid   = intval($req->post('id'));
            
            $status   = intval($req->post('status'));

            $user     = new mUser();

            if(!in_array($status, [Enum::STATUS_NORMAL, Enum::STATUS_DISABLED])){

                return $this->error('帐号目标状态不正确', 101, 'javascript:history.back();');
            }

            if(!$user->updateUserInfo($userid, ['status' => $status])){

                return $this->error('帐号状态更新失败', 101, 'javascript:history.back();');
            }

            return $this->success('帐号状态更新成功', 'javascript:history.back();');
        }
    }