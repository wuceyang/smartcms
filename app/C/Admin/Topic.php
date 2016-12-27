<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Actor;
    use \App\Helper\Storage\Qiniu;
    use \App\M\Topic AS mTopic;

    class Topic extends Base{

        public function index(Request $req, Response $resp){

            $page       = $req->get('page');

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

                if($topicList[$i]['tag'] & 1 == 1){

                    $tags[] = '官方';
                }

                if($topicList[$i]['tag'] & 2 == 2){

                    $tags[] = '推广';
                }

                if($topicList[$i]['tag'] & 4 == 4){

                    $tags[] = '置顶';
                }

                if($topicList[$i]['tag'] & 8 == 8){

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
                            'tags' => [
                                        0 => '官方',
                                        1 => '推广',
                                        2 => '置顶',
                                        3 => '加精',
                                      ],
                         ];

                return $resp->withVars($param)->withView('admin/topic_add.html')->display();
            }

            $aid        = intval($req->post('actorId'));

            $title      = trim($req->post('title'));

            $content    = trim($req->post('content'));

            $type       = intval($req->post('type'));

            $video      = trim($req->post('video'));

            $image      = $req->post('image');

            $action     = intval($req->post('action'));

            $uid        = 1;

            $tag        = intval($req->post('tag'));

            $hotExpireAfter = $tag ? trim($req->post('hot_expire_after')) : null;

            if(!in_array($type, [1, 2, 3, 4])){

                return $this->error("参数错误，话题类型不正确", 101, "");
            }
            //文字话题
            if($type == 1){

                $content = strip_tags($content);
            }

            $audio = '';

            $topic = new mTopic();

            $tag   = 1 << $tag;

            if(!$topicId = $topic->doTopic($title, $content, $type, $action, $video, $audio, $image, $aid, $uid, $tag, $hotExpireAfter)){

                return $this->error('话题发表失败' . var_export($topic->getError(), true), 201, '');
            }
            //非文字话题，则需要生成html文件，并存储到七牛
            if($type != 1){

                $param = [
                            'title' => $title,
                            'content' => $content,
                            'video' => $video,
                            'audio' => $audio,
                            'image' => $image,
                         ];

                $html  = $resp->withVars($param)->withView('admin/topic_info.html')->toString();

                $qiniu = new Qiniu();

                $targetName = "topic_{$topicId}.html";

                $url   = Qiniu::DOMAIN_OTHER . '/' . $targetName;

                if($qiniu->doUpload(Qiniu::BUCKET_OTHER, '', $html, $targetName) && $topic->setInfo('tid = ?', [intval($topicId)], ['page_url' => $url])){

                    return $this->error('话题发表成功，上传文件失败' . var_export($topic->getError(), true), 201, '');
                }
            }

            return $this->success('话题发表成功','');
        }
    }