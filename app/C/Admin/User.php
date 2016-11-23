<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
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

        public function addUser(Request $req, Response $resp){


        }

        public function editUser(Request $req, Response $resp){


        }
    }