<?php
    namespace App\M;

    class Menu extends \App\M\Model{

        public static $table = 'cms_menu';

        /**
         * 获取列表
         * @param int $parentId 父菜单id
         * @param int $page     当前页码
         * @param int $pagesize 每页显示的记录数量
         *
         */
        public function getList($parentId = null, $enable = 1, $page = 0, $pagesize = 0){

        	$queryBuilder = self::orderBy(['show_order ASC']);

            if(is_numeric($enable)){

                $queryBuilder = $queryBuilder->where('enable = ' . intval($enable));
            }

        	if($parentId !== null){

        		$queryBuilder = $queryBuilder->where('parent_id = ?', [$parentId]);

        	}

        	if(is_numeric($page) && is_numeric($pagesize) && $pagesize){

        		$queryBuilder->skip(($page - 1) * $pagesize) -> limit($pagesize);

        	}

            return  $queryBuilder->getRows();

        }

        /**
         * 统计总数
         * @param int $parentId 父菜单id
         */
        public function getCount($parentId = null){

        	$queryBuilder = self::fields(['count(1) AS num']);

            $queryBuilder = $queryBuilder->where('enable = 1');

        	if(is_numeric($parentId)){

        		$queryBuilder->where('parent_id = ?', [$parentId]);
        	}

        	return $queryBuilder->getValue();
        }

        /**
         * 获取菜单详情
         * @param int $mid 菜单id
         */
        public function getMenuInfoById($mid){

            return $this->getInfoById($mid);
        }

        /**
         * 更新菜单信息
         * @param int       $mid        菜单id
         * @param string    $title      菜单文字
         * @param string    $url        菜单链接地址
         * @param int       $enabled    是否启用
         * @param int       $order      菜单排序顺序
         * @param int       $parentId   父菜单id
         * @param string    $allowurl   顶级菜单下面包含的子菜单的路径，如:/admin/(menu|privilege)
         * @param string    $icon       顶级菜单前面的图标样式
         */
        public function updateMenuInfo($mid, $title, $url, $enabled, $order, $parentId, $icon = ''){

            $info = [];

            if($title){

                $info['title']      = trim($title);
            }

            $info['url']        = trim($url);

            if($order){

                $info['show_order'] = intval($order);
            }

            if($icon){

                $info['icon']       = trim($icon);
            }

            if($parentId){

                $parentInfo         = $this->getMenuInfo($parentId);

                if(!$parentInfo){

                    return 0;
                }

                $info['level']      = $parentInfo['level'] + 1;
            }

            if(is_numeric($enabled)){

                $info['enable'] = intval($enabled);
            }

            $info['parent_id']  = intval($parentId);

            if(!$info){

                return 0;
            }

            return self::where('id = ?', [$mid])->update($info);
        }

        /**
         * 添加新菜单信息
         * @param string    $title      菜单文字
         * @param string    $url        菜单链接地址
         * @param int       $show_order 菜单排序顺序
         * @param int       $parentId   父菜单id
         * @param string    $icon       顶级菜单前面的图标样式
         */
        public function addMenu($title, $url, $parentId, $icon, $show_order){

            $params = [
                "title"       => $title,
                "url"         => $url,
                "parent_id"   => $parentId,
                "icon"        => $icon,
                'level'       => $parentId = 0 ? 1 : 2,
                'show_order'  => $show_order,
            ];

            return self::insert($params);
        }

        /**
         * 菜单排序
         * @param int $sourceOrder 原始排序顺序
         * @param int $targetOrder 目标排序顺序
         * @param int $parentId    需要排序的菜单的父菜单ID
         * @return int
         */
        public function setMenuOrder($sourceOrder, $targetOrder, $parentId){

            if($sourceOrder == $targetOrder) return 1;

            $where          = '';

            $params         = $updateInfo = [];

            $params[]       = intval($parentId);

            if($sourceOrder > $targetOrder){

                $where      = 'show_order >= ? AND show_order < ?';

                $params[]   = intval($targetOrder);

                $params[]   = intval($sourceOrder);

                $updateInfo = ['`show_order`' => '`show_order` + 1'];
            }else{

                $where      = 'show_order <= ? AND show_order > ?';

                $params[]   = intval($targetOrder);

                $params[]   = intval($sourceOrder);

                $updateInfo = ['`show_order`' => '`show_order` - 1'];
            }
            return self::where('parent_id = ? AND ' . $where, $params)->update($updateInfo);
        }

    }
