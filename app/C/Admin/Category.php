<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Category AS mCategory;

    class Category extends Base{

        public function index(Request $req, Response $resp){

            $status         = intval($req->get('status'));
            
            $page           = intval($req->get('page'));

            $parentId       = intval($req->get('pid'));
            
            $page           = max(1, $page);
            
            $pagesize       = 10;

            $subCategoryList= [];
            
            $category       = new mCategory();
            
            $categoryList   = $category->getCategoryList($parentId, $status, false, $page, $pagesize);

            if($categoryList){

                $subParentId     = array_column($categoryList, "id");
                
                $subCategories   = $category->getCategoryList($subParentId, $status, false);
                
                $subCategoryList = $category->formatCategory($subCategories);
            }

            $allCategory   = $category->getCategoryList(null, 0, 0);
            
            $categoryTotal = $category->getCategoryCount($parentId, $status, false);
            
            $param         = [];

            if($status){

                $param['status'] = $status;
            }

            if($parentId){

                $param['pid'] = $parentId;
            }

            $pageInfo             = $this->getPageInfo("/admin/category", $page, $categoryTotal, $param, $pagesize);

            $param['list']        = $categoryList;

            $param['sublist']     = $subCategoryList;

            $param['allCategory'] = $category->formatCategory($allCategory);

            $param                = $param + $pageInfo;

            return $resp->withVars($param)->withView('admin/category_list.html')->display();
        }

        public function addCategory(Request $req, Response $resp){

            $catname    = trim($req->post('name'));

            if(!$catname){

                return $this->error("分类名称不能为空", 101, "/admin/category");
            }

            $parentid   = intval($req->post('parent'));
            
            $category   = new mCategory();
            
            $parentpath = ',0,';

            $maxOrder   = 0;

            $brotherCategories = [];

            if($parentid){

                $parentCategory = $category->getInfoById($parentid);

                if(!$parentCategory){

                    return $this->error("找不到指定的父分类信息", 101, '/admin/category');
                }

                $parentpath         = $parentCategory['parent_path'] . $parentid . ',';

                $brotherCategories  = $category->getCategoryList($parentid, 0, false, 0, 0);

                $maxOrder           = !$brotherCategories ? 0 : max(array_column($brotherCategories, 'show_order'));
            }
            //标记是否需要重新排序
            $resetOrder = false;

            $order      = intval($req->post('show_order'));

            if($order <= $maxOrder){

                $resetOrder = true;
            }

            $order = $order < $maxOrder ? $order : $maxOrder + 1;

            if(!$category->addCategory($catname, $parentid, $parentpath, $order, $this->userinfo['id'])){
                //重新排序
                if($resetOrder){

                    $category->setCategoryOrder($parentid, $maxOrder, $order);
                }

                return $this->error("分类添加失败" . var_export($category->getError(), true), 102, "/admin/category");
            }

            return $this->success("分类添加成功", '/admin/category');
        }

        public function editCategory(Request $req, Response $resp){

            $catname   = trim($req->post('name'));
            
            // $parentid  = intval($req->post('parent'));
            
            $status    = intval($req->post('status'));
            
            $showOrder = intval($req->post('show_order'));

            $catid     = intval($req->post('catid'));

            if(!$catid){

                return $this->error("请指定要编辑的栏目", 101);
            }

            if(!$catname){

                return $this->error("栏目名称不能为空", 101);
            }

            if(!$status){

                return $this->error("栏目状态不正确", 101);
            }

            if(!$showOrder){

                return $this->error("栏目排序必须填写", 101);
            }

            $category = new mCategory();

            $catInfo = $category->getInfoById($catid);

            if(!$catInfo){

                return $this->error("找不到指定的栏目信息", 101);
            }

            if($catname == $catInfo['category_name'] && $status == $catInfo['status'] && $showOrder == $catInfo['show_order'] && $status == $catInfo['status']){

                return $this->success("栏目信息编辑成功");
            }

            $catParam = [
                        'category_name' => $catname,
                        'status'        => $status,
                        'show_order'    => $showOrder,
                        ];

            $msg    = '';

            $flag   = false;

            $category->transaction(function() use($category, $catInfo, $catParam, &$flag, &$msg){

                $newOrder = $catParam['show_order'];

                unset($catParam['show_order']);

                $infoUpdate = $catParam['category_name'] != $catInfo['category_name'] || $catParam['status'] != $catInfo['status'];

                if($infoUpdate && !$category->setCategoryInfo($catInfo['id'], $catParam)){

                    $msg = "更新栏目信息发生错误" . var_export($category->getError(), true);

                    return false;
                }

                //标记是否需要重新排序
                $resetOrder = $catInfo['show_order'] != $newOrder;

                if($resetOrder && !$category->setCategoryOrder($catInfo['parent_id'], $catInfo['show_order'], $newOrder)){

                    $msg = "更新栏目排序发生错误". var_export($category->getSqls(), true);

                    return false;
                }

                $flag =  true;

                return true;
            });

            if(!$flag){

                return $this->error("更新栏目信息失败:" . $msg, 102, "/admin/category");
            }

            return $this->success("栏目信息更新成功" . var_export($category->getSqls(), true), "/admin/category");
        }

        public function switchCategory(Request $req, Response $resp){

            $catid = intval($req->get('id'));

            $status = intval($req->get('status'));
        }
    }