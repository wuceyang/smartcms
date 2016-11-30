<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Category;
    use \App\Helper\Enum;
    use \App\M\Recommend;
    use \App\M\ContentType;
    use \App\M\Content AS mContent;

    class Content extends Base{

        public function index(Request $req, Response $resp){

            $pagesize     = 20;

            $page         = max(1, intval($req->get('page')));

            $kw           = trim($req->get('kw'));

            $status       = intval($req->get('status'));

            $typeid       = intval($req->get('type'));

            $catid        = intval($req->get('catid'));

            $content      = new mContent();

            $contentList  = $content->getContentList($kw, $catid, $typeid, $status, $page, $pagesize);

            $contentTotal = $content->getContentTotal($kw, $catid, $typeid, $status);

            $param        = [];

            if($kw){

                $param['kw']     = $kw;
            }

            if($status){

                $param['status'] = $status;
            }

            if($typeid){

                $param['type']   = $typeid;
            }

            if($catid){

                $param['catid']  = $catid;
            }

            $pageInfo             = $this->getPageInfo("/admin/content", $page, $contentTotal, $param, $pagesize);

            $param['list']        = $contentList;

            $param                = $param + $pageInfo;

            return $resp->withVars($param)->withView('admin/content_list.html')->display();
        }

        public function addContent(Request $req, Response $resp){

            if(!$req->isPost()){

                $catid          = intval($req->get('catid'));

                $catInfo        = $category->getInfoById($catid);

                if(!$catInfo){

                    return $this->error("找不到指定的栏目信息",101, "/admin/content/add-content");
                }

                $this->setFormToken($req, $resp);

                $contentType    = new ContentType();

                $recommend      = new Recommend();

                $category       = new Category();

                $params = [
                            'typeList'      => $contentType->getTypeList(Enum::STATUS_NORMAL),
                            'recommentList' => $recommend->getRecommendList(Enum::STATUS_NORMAL),
                            'catinfo'       => $catInfo, 
                          ]; 

                return $resp->withVars($param)->withView('admin/content_add.html')->display();
            }

            if(!$this->formTokenValidate($req, $resp)){

                return $this->error("请不要提交非法数据", 102, "/admin/content/add-content");
            }
        }
    }