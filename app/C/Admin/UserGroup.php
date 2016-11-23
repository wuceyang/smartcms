<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\UserGroup as UGroup;

    class UserGroup extends Base{

        public function groupList(Request $req, Response $resp){

            $group = new UGroup();

            $groupList = $group->getAllGroups();

            $param = ['list' => $groupList];

            return $resp->withView('admin/grouplist.html')->withVars($param)->display();
        }

        public function addGroup(Request $req, Response $resp){

            if(!$req->isPost()){

                return $resp->withView('admin/groupadd.html')->display();
            }

            $groupName = trim($req->post('groupName'));

            if(!$groupName){

                return $this->error("分组名称不能为空", 101);
            }

            $group = new UGroup();

            if(!$group->createGroup($groupName, $this->userinfo['id'])){

                return $this->error("用户分组创建失败", 102);
            }

            return $this->success("用户分组创建成功,<a href=\"/admin/user-group/add-group\">点击这里创建更多</a>", "/admin/user-group");
        }

        public function editGroup(Request $req, Response $resp){

            if(!$req->isPost()){

                $groupid = intval($req->get('id'));

                $group = new UGroup();

                $groupInfo = $group->getMenuInfoById($groupid);

                if(!$groupInfo){

                    return $this->error("找不到指定的用户分组信息", 101);
                }

                $param = ['info' => $groupInfo];

                return $resp->withView('admin/groupedit.html')->withVars($param)->display();
            }

            $groupid   = intval($req->post('id'));

            $groupName = trim($req->post('groupName'));

            $status    = intval($req->post('groupStatus'));

            if(!$groupName){

                return $this->error("分组名称不能为空", 101);
            }

            $group = new UGroup();

            $groupInfo = $group->getMenuInfoById($groupid);

            if(!$groupInfo){

                return $this->error("找不到指定的用户分组信息", 101);
            }

            if($groupName == $groupInfo['group_name'] && $status == $groupInfo['status']){

                return $this->success("用户分组信息编辑成功", "/admin/user-group");
            }

            if(!$group->updateGroupInfo($groupid, $updateParam)){

                return $this->error("用户分组信息编辑失败", 103, "/admin/user-group/edit-group?id=" . $groupid);
            }

            return $this->success("用户分组信息编辑成功", "/admin/user-group");
        }

    }