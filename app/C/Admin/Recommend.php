<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\Helper\Enum;
    use \App\M\Recommend AS mRecommend;

    class Recommend extends Base{

        public function index(Request $req, Response $resp){

            $status         = intval($req->get('status'));
            
            $page           = intval($req->get('page'));
            
            $page           = max(1, $page);
            
            $pagesize       = 20;
            
            $recommend      = new mRecommend();
            
            $recommendList  = $recommend->getRecommendList($status, $page, $pagesize);
            
            $recommendTotal = $recommend->getRecommendCount();

            $param          = [];

            if($status){

                $param['status'] = $status;
            }

            $pageInfo      = $this->getPageInfo("/admin/recommend", $page, $recommendTotal, $param, $pagesize);
            
            $param['list'] = $recommendList;
            
            $param         = $param + $pageInfo;

            return $resp->withVars($param)->withView('admin/recommend_list.html')->display();
        }

        public function addRecommend(Request $req, Response $resp){

            if(!$req->isPost()){

                $this->setFormToken($req, $resp);

                return $resp->withView('admin/recommend_add.html')->display();
            }

            $name       = trim($req->post('name', ''));

            if(!$name){

                return $this->error("推荐位名称不能为空", 101, "/admin/recommend/add-recommend");
            }

            $recommend  = new mRecommend();

            if($recommend->add($name, $this->userinfo['id'])){

                return $this->success("推荐位添加成功,<a href=\"/admin/recommend/add-recommend\">点击这里添加更多</a>", "/admin/recommend");
            }

            return $this->error("推荐位添加失败", 102, "/admin/recommend/add-recommend");
        }

        public function editRecommend(Request $req, Response $resp){

            $rid            = intval($req->post('id'));

            $recommend      = new mRecommend();

            $recommendInfo  = $recommend->getInfoById($rid);

            if(!$recommendInfo){

                return $this->error("找不到指定的推荐位信息", 101);
            }

            if(!$req->isPost()){

                $this->setFormToken($req, $resp);

                return $resp->withView('admin/recommend_add.html')->withVars($recommendInfo)->display();
            }

            $url = "/admin/recommend/edit-recommend?id=" . $rid;

            $name       = trim($req->post('name', ''));

            if(!$name){

                return $this->error("推荐位名称不能为空", 101, $url);
            }

            $status     = intval($req->post('status'));

            if(!in_array($status, [Enum::STATUS_NORMAL, Enum::STATUS_DISABLED])){

                return $this->error("推荐位状态不正确，请重新选择", 101, $url);
            }

            if($name == $recommendInfo['name'] && $status == $recommendInfo['status']){

                return $this->success("推荐位编辑成功", "/admin/recommend");
            }

            $param = [
                        'name'   => $name,
                        'status' => $status,
                      ];

            if(!$recommend->setInfo('id = ?', [intval($rid)], $param)){

                return $this->error("推荐位编辑失败", 102, $url);
            }

            return $this->success("推荐位编辑成功", "/admin/recommend");
        }

        public function disableRecommend(Request $req, Response $resp){

            $rid       = intval($req->post('id'));

            $status    = intval($req->post('status'));
            
            $recommend = new mRecommend();

            $recommendInfo = $recommend->getInfoById($rid);

            if(!$recommendInfo){

                return $this->error("找不到指定的推荐位信息", 101, 'javascript:history.back()');
            }

            if(!in_array($status, [Enum::STATUS_NORMAL, Enum::STATUS_DISABLED])){

                return $this->error("推荐位状态不正确，请重新选择", 101);
            }

            $action = $status == Enum::STATUS_DISABLED ? '禁用' : '启用';

            if(!$recommend->setInfo('id = ?', [$rid], ['status' => $status])){

                return $this->error("推荐位" . $action . "失败", 102);
            }

            return $this->success("推荐位" . $action . "成功");
        }
    }