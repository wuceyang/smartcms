<?php
    namespace App\M;

    class User extends Model{

        public static $table = 'cms_user';

        public function passwdEncrypt($account, $passwd){

            return md5($passwd . '|' . md5($account . $passwd));
        }

        public function userLogin($account, $passwd){

            return $this->where('account = ? AND passwd = ?', [$account, $this->passwdEncrypt($account, $passwd)])->getRow();
        }

        public function addUser($username, $account, $passwd, $groupid){

            $params = [

                'account'   => $account,

                'username'  => $username,

                'passwd'    => $this->passwdEncrypt($account, $passwd),

                'group_id'  => ',' . implode(',', $groupid) . ',',

                'reg_time'  => time(),
            ];

            return $this->insert($params);
        }

        /**
         *获取用户列表
         *@param int $groupId 用户所在分组
         *@return array
         */ 
        public function userList($groupId, $status = null, $page = 0, $pagesize = 20){

            $where = $param = [];

            if($status){

                $where = ['status = ' . intval($status)];
            }

            if($groupId){

                $where[] = 'group_id LIKE ?';

                $param[] = '%,' . caddslahshes($groupId, '%-') . ',%';
            }

            $this->orderBy(['id DESC']);

            if($where){

                $this->where(implode(' AND ', $where), $param);
            }

            if($page && $pagesize){

                $this->page($page)->pagesize($pagesize);
            }

            return $this->getRows();
        }

        public function updateUserInfo($userId, $updateInfo){

            return $this->where('id = ?', [intval($userId)])->update($updateInfo);
        }

        public function getTotalUser($groupId){

            $where = $param = [];

            $where = ['status = 1'];

            if($groupId){

                $where[] = 'group_id LIKE ?';

                $param[] = '%,' . caddslahshes($groupId, '%-') . ',%';
            }

            if($where){

                $this->where(implode(' AND ', $where), $param);
            }

            return $this->getCount();
        }
    }