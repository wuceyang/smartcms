<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Template;
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

            $tplid        = intval($req->get('tplid'));

            $catid        = intval($req->get('catid'));

            $content      = new mContent();

            $contentList  = $content->getContentList($kw, $catid, $tplid, $status, $page, $pagesize);

            $contentTotal = $content->getContentTotal($kw, $catid, $tplid, $status);

            $param        = [];

            if($kw){

                $param['kw']     = $kw;
            }

            if($status){

                $param['status'] = $status;
            }

            if($typeid){

                $param['tplid']   = $tplid;
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

                $category       = new Category();

                $catid          = intval($req->get('catid'));

                $catInfo        = $category->getInfoById($catid);

                if(!$catInfo){

                    return $this->error("找不到指定的栏目信息",101, "/admin/content/add-content");
                }

                $catlist = $category->getCategoryList(null, Enum::STATUS_NORMAL, true);

                $cats    = [];

                foreach($catlist as $k => $v){

                    if(!isset($cats[$v['parent_id']])){

                        $cats[$v['parent_id']] = [];
                    }

                    $cats[$v['parent_id']][] = [
                                                'info' => $v,
                                                'sub'  => &$cats[$v['id']]
                                                ];
                }

                $this->setFormToken($req, $resp);

                $contentType    = new ContentType();

                $recommend      = new Recommend();

                $category       = new Category();

                $template       = new Template();

                $params = [
                            'typeList'      => $contentType->getTypeList(Enum::STATUS_NORMAL),
                            'recommendList' => $recommend->getRecommendList(Enum::STATUS_NORMAL),
                            'catinfo'       => $catInfo, 
                            'catlist'       => $cats,
                            'tpllist'       => $template->getTemplateList(Enum::STATUS_NORMAL),
                            'authorinfo'    => $this->userinfo,
                          ];

                $params['tags'] = [
                                    ['id' => 1, 'tagName' => '精华'],
                                    ['id' => 2, 'tagName' => '置顶'],
                                  ];

                return $resp->withVars($params)->withView('admin/content_add.html')->display();
            }

            if(!$this->formTokenValidate($req, $resp)){

                return $this->error("请不要提交非法数据", 102, "/admin/content/add-content");
            }

            $title   = trim($req->post('title'));

            $catid   = intval($req->post('catid'));

            $summary = trim($req->post('summary'));

            $author  = trim($req->post('author'));

            $source  = trim($req->post('source'));

            $kw      = trim($req->post('kw'));

            $tplid   = intval($req->post('tplid'));

            $content = trim($req->post('content'));

            $data    = json_encode($req->post('data', []));

            if(!$title){

                return $this->error("标题不能为空", 101, 'javascript:history.back()');
            }

            $title    = htmlspecialchars($title);

            $category = new Category();

            if(!$catid || !($catinfo = $category->getInfoById($catid))){

                return $this->error("找不到指定的分类", 101, "javascript:history.back()");
            }

            $summary = $summary ? $summary : htmlspecialchars($summary);

            $author  = $author ? htmlspecialchars($author) : '';

            $source  = $source ? htmlspecialchars($source) : '';

            $kw      = $kw ? htmlspecialchars($kw) : '';

            if(!$tplid){

                $tplid = intval($catInfo['tplid']);
            }

            $content = new Content();

            if(!$content->addContent($title, $catid, $summary, $content, $kw, $author, $source, $tplid, $data, $this->userinfo['id'])){

                return $this->error("文章录入失败", "javascript:history.back()");
            }

            return $this->success("文章录入成功", "javascript:history.back()");
        }
    }