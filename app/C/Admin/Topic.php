<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Actor;
    use \App\Helper\Storage\Qiniu;
    use \App\M\Topic AS mTopic;

    class Topic extends Base{

        public function index(Request $req, Response $resp){

            $page       = max(intval($req->get('page')), 1);

            $pagesize   = 20;

            $actorId    = '';

            $topic      = new mTopic();

            $topicList  = $topic->getTopicList($actorId, $page, $pagesize); 

            $actors     = [];

            $contentType= [
                            1 => '文本',
                            2 => '图文',
                            3 => '视频',
                            4 => '音频',
                          ];
            $actionType = [
                            1 => 'APP内部',
                            2 => '内部浏览器',
                            3 => '外部浏览器',
                          ];

            $totalTopic = 0;

            if($topicList){

                $actorsId = array_column($topicList, 'aid');

                $actor    = new Actor();

                $actors   = $actor->getInfoById($actorsId, 'aid');

                $totalTopic = $topic->getTotalTopic($actorId);
            }

            for ($i = 0; $i < count($topicList); ++$i) {
                
                $tags = [];

                if(($topicList[$i]['tag'] & 1) == 1){

                    $tags[] = '官方';
                }

                if(($topicList[$i]['tag'] & 2) == 2){

                    $tags[] = '推广';
                }

                if(($topicList[$i]['tag'] & 4) == 4){

                    $tags[] = '置顶';
                }

                if(($topicList[$i]['tag'] & 8) == 8){

                    $tags[] = '加精';
                }

                $topicList[$i]['tags'] = implode(',', $tags);
            }

            $param = [];

            $pageInfo   = $this->getPageInfo("/admin/topic", $page, $totalTopic, $param, $pagesize);

            $param      = [
                            'list'        => $topicList
                            ,
                            'actors'      => $actors,

                            'contentType' => $contentType,
                          ];

            $param      = $param + $pageInfo;

            return $resp->withView('admin/topic_list.html')->withVars($param)->display();
        }

        public function doPost(Request $req, Response $resp){

            if(!$req->isPost()){

                $param = [
                            'tags' => \App\Helper\Enum::TOPICTAGS,
                         ];

                return $resp->withVars($param)->withView('admin/topic_add.html')->display();
            }

            $aid        = intval($req->post('actorId'));

            // $title      = trim($req->post('title'));

            $content    = trim($req->post('content_text'));

            $type       = intval($req->post('type'));

            $video      = trim($req->post('video'));

            $image      = $req->post('image');

            $action     = intval($req->post('action'));

            $template   = trim($req->post('template'));

            $uid        = 1;

            $tag        = $req->post('tag');

            // if(!$title){

            //     return $this->error("参数错误，话题标题不能为空", 101, "");
            // }

            $hotExpireAfter = $tag ? trim($req->post('hot_expire_after')) : null;

            if(!in_array($type, [1, 2, 3, 4])){

                return $this->error("参数错误，话题类型不正确", 101, "");
            }
            //文字话题
            if($type == 1){

                $content = strip_tags($content);
            }else{

                $tplpath = APP_ROOT . 'app/V/' . $template;

                if(!file_exists($tplpath) || !is_file($tplpath)){

                    return $this->error('模板不存在，请检查模板', 101, '');
                }
            }

            $audio  = trim($req->post('audio'));

            $topic  = new mTopic();

            $tagsum = 0;

            if(is_array($tag)){

                foreach ($tag as $k => $v) {
                    
                    $tagsum += 1 << $v;
                }
            }

            if(!$topicId = $topic->doTopic($content, $type, $action, $video, $audio, $image, $aid, $uid, $tagsum, $hotExpireAfter)){

                return $this->error('话题发表失败' . var_export($topic->getError(), true), 201, '');
            }
            //非文字话题，则需要生成html文件，并存储到七牛
            if($type != 1){
                //生成并上传html文件
                $this->uploadHtml($req, $resp, $topicId, $template);
            }

            return $this->success('话题发表成功','');
        }

        public function reupload(Request $req, Response $resp){

            $topicId    = intval($req->get('id'));

            $this->uploadHtml($req, $resp, $topicId);

            return $this->success('话题文件重新生成并上传成功','');
            
        }

        protected function uploadHtml($req, $resp, $topicId, $template = ''){

            $topic      = new mTopic();

            $topicInfo  = $topic->getInfoById($topicId, 'tid');

            if(!$topicInfo){

                return $this->error('找不到指定的话题信息', 201, '');
            }

            if($topicInfo['is_del']){

                return $this->error('指定的话题已被删除', 201, '');
            }

            $template = $template ? $template : 'admin/topic_info.html';

            $param = [
                        'topic' => $topicInfo,
                     ];

            $html  = $resp->withVars($param)->withView($template)->toString();

            $qiniu = new Qiniu();

            $targetName = "topic_{$topicId}.html";

            $url   = Qiniu::DOMAIN_OTHER . '/' . $targetName;

            if(!$qiniu->doUpload(Qiniu::BUCKET_OTHER, '', $html, $targetName)){

                return $this->error('上传文件到7牛发生错误', 201, '');
            }

            if(!$topic->setInfo('tid = ?', [intval($topicId)], ['page_url' => $url])){

                return $this->error('更新页面地址失败，请在列表中，重新尝试生成', 201, '');
            }

            return true;
        }

        public function editTopic(Request $req, Response $resp){

            if(!$req->isPost()){

                $topicId    = intval($req->get('id'));

                $topic      = new mTopic();

                $topicInfo  = $topic->getInfoById($topicId, 'tid');

                if(!$topicInfo){

                    return $this->error('指定的话题不存在', 202, 'javascript:history.back();');
                }

                $actor = new Actor();

                $actorInfo = $actor->getInfoById($topicInfo['aid'], 'aid');

                $param = [
                        'topic' => $topicInfo,
                        'actor' => $actorInfo ? $actorInfo : ['id' => '', 'nickname' => ''],
                        'tags' => \App\Helper\Enum::TOPICTAGS,
                        ];

                return $resp->withView('admin/topic_edit.html')->withVars($param)->display();
            }

            $topicId    = intval($req->post('topicId'));

            $aid        = intval($req->post('actorId'));

            $content    = trim($req->post('content_text'));

            $type       = intval($req->post('type'));

            $video      = trim($req->post('video'));

            $image      = $req->post('image');

            $action     = intval($req->post('action'));

            $template   = trim($req->post('template'));

            $uid        = 1;

            $tag        = $req->post('tag');

            $hotExpireAfter = $tag ? trim($req->post('hot_expire_after')) : null;

            if(!in_array($type, [1, 2, 3, 4])){

                return $this->error("参数错误，话题类型不正确", 101, "");
            }
            //文字话题
            if($type == 1){

                $content = strip_tags($content);
            }else{

                $tplpath = APP_ROOT . 'app/V/' . $template;

                if(!file_exists($tplpath) || !is_file($tplpath)){

                    return $this->error('模板不存在，请检查模板', 101, '');
                }
            }

            $audio  = trim($req->post('audio'));

            $topic  = new mTopic();

            $tagsum = 0;

            if(is_array($tag)){

                foreach ($tag as $k => $v) {
                    
                    $tagsum += 1 << $v;
                }
            }

            $params = [
                        'uid'                => $uid,
                        'aid'                => $aid,
                        'content_text'       => $content,
                        'content_type'       => $type,
                        'action'             => $action,
                        'video_url'          => $video,
                        'audio_url'          => $audio,
                        'img_url'            => json_encode($image),
                        'img_count'          => count($image),
                        'tag'                => $tagsum,
                        'hot_available_time' => $hotExpireAfter,
                      ];

            if(!$topic->setInfo('tid = ?', [$topicId], $params)){

                return $this->error('话题更新失败' . var_export($topic->getError(), true), 201, '');
            }
            //非文字话题，则需要生成html文件，并存储到七牛
            if($type != 1){
                //生成并上传html文件
                $this->uploadHtml($req, $resp, $topicId, $template);
            }

            return $this->success('话题更新成功','');
        }
    }