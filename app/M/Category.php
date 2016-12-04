<?php
    namespace App\M;

    class Category extends Model{

        public static $table = 'content_category';

        /**
         * 获取指定状态的文章分类列表
         * @param  int  $parentId       父分类ID
         * @param  int  $status         指定的推荐位状态,1:正常,2:禁用
         * @param  bool $allSubCategory 是否取所有的子分类以及子分类下的子分类
         * @param  int  $page           当前页码
         * @param  int  $pagesize       每页显示的数量
         * @return array
         */
        public function getCategoryList($parentId, $status = 0, $allSubCategory = false, $page = 0, $pagesize = 0){

            $where = $param = [];

            if((is_array($parentId) && $parentId) || is_numeric($parentId)){

                $parentId = is_array($parentId) ? array_map('intval', $parentId) : [intval($parentId)];

                if(!$allSubCategory){
                    
                    $where[]  = 'parent_id IN (' . implode(',', $parentId) . ')';
                }else{

                    $like = [];

                    foreach ($parentId as $k => $v) {
                        
                        $like[] = 'parent_path LIKE ?';

                        $param[] = '%,' . $v . ',%';
                    }

                    $where[] = '(' . implode(' OR ', $like) . ')';
                }
            }

            if($status){

                $where[] = 'status = ?';

                $param[] = intval($status);
            }

            return $this->orderBy(['show_order ASC'])->page($page)->pagesize($pagesize)->getRows(implode(' AND ', $where), $param);
        }

        /**
         * 统计适合条件的推荐位数量
         * @param  int $status 推荐状态
         * @return int
         */
        public function getCategoryCount($parentId, $status = 0, $allSubCategory = false){

            $where = $param = [];

            if($parentId){

                if($allSubCategory){

                    $where[] = 'parent_id = ?';

                    $param[] = intval($parentId);
                }else{

                    $where[] = 'parent_path LIKE ?';

                    $param[] = '%,' . intval($parentId) . ',%';
                }
            }

            if($status){

                $where[] = 'status = ?';

                $param[] = intval($status);
            }

            return $this->getValue($where, $param);
        }

        /**
         * 录入推荐位
         * @param string $name       推荐位名称
         * @param int    $create_uid 推荐位创建人ID
         * @param string $parentPath 全部的父分类ID,用英文逗号","连接，两头也必须有逗号
         * @param int    $showOrder  在同类中的显示顺序
         * @param int    $createUid  创建者用户ID
         * @return int
         */
        public function addCategory($name, $parentId, $parentPath, $showOrder, $tplId, $createUid){

            $param = [
                        'category_name' => $name, 
                        'create_uid'    => $createUid, 
                        'parent_id'     => intval($parentId), 
                        'parent_path'   => $parentPath,
                        'tplid'         => intval($tplId),
                        'show_order'    => intval($showOrder),
                     ];

            return $this->insert($param);
        }

        /**
         * 按照分类的父ID重新组织分类
         * @param  array $categories 分类列表
         * @return array
         */
        public function formatCategory($categories){

            $allCategory  = [];

            foreach ($categories as $k => $v) {

                if(!isset($allCategory[$v['parent_id']])){

                    $allCategory[$v['parent_id']] = [];                        
                }

                $allCategory[$v['parent_id']][] = $v;
            }

            return $allCategory;
        }

        /**
         * 同级栏目排序
         * @param int $parentId 父分类ID
         * @param int $oldOrder 原始排序
         * @param int $newOrder 新排序
         */
        public function setCategoryOrder($parentId, $oldOrder, $newOrder){

            $oldOrder = intval($oldOrder);
            
            $newOrder = intval($newOrder);
            
            $asend    = 'IF(show_order > ' . $oldOrder . ' , show_order - 1, ' . $newOrder . ')';
            
            $desend   = 'IF(show_order < ' . $oldOrder . ' , show_order + 1, ' . $newOrder . ')';
            
            $expr     = $oldOrder > $newOrder ? $desend : $asend;
            
            $minOrder = min($oldOrder, $newOrder);
            
            $maxOrder = max($oldOrder, $newOrder);

            return $this->where('parent_id = ? AND show_order >= ? AND show_order <= ?', [intval($parentId), $minOrder, $maxOrder])->update(['`show_order`' => $expr]);
        }

        /**
         * 更新栏目信息
         * @param int   $catid        需要更新的栏目id
         * @param array $categoryInfo 需要更新的栏目信息
         */
        public function setCategoryInfo($catid, $categoryInfo){

            return $this->where('id = ?', [intval($catid)])->update($categoryInfo);
        }
    }