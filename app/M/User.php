<?php
    namespace App\M;

    class User extends Model{

        public static $table = 'user';

        public function passwdEncrypt($account, $passwd){

            return md5($passwd . '|' . md5($account . $passwd));
        }

        public function userLogin($account, $passwd){

            return $this->where('account = ? AND passwd = ?', [$account, $this->passwdEncrypt($account, $passwd)])->getRow();
        }

        public function addUser($username, $account, $passwd){

            $params = [

                'account'   => $account,

                'username'  => $username,

                'passwd'    => $this->passwdEncrypt($account, $passwd)
            ];

            return $this->insert($params);
        }

        /**
         *获取用户列表
         *@param int $groupId 用户所在分组
         *@return array
         */ 
        public function userList($groupId, $page, $pagesize){

            $where = $param = [];

            if($groupId){

                $where[] = 'group_id LIKE ?';

                $param[] = '%' . caddslahshes($groupId, '%-') . '%';
            }

            $queryBuilder = $this->orderBy(['id DESC']);

            if($where && $param){

                $queryBuilder = $queryBuilder->where(implode(' AND ', $where), $param);
            }

            if($page && $pagesize){

                $queryBuilder = $queryBuilder->page($page)->pagesize($pagesize);
            }

            return $queryBuilder->getRows();
        }

        public function updateUserInfo($userId, $updateInfo){

            return $this->where('id = ?', [intval($userId)])->update($updateInfo);
        }
    }