<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Actor;
    use \App\M\Moment AS mMoment;

    class Moment extends Base{

        public function index(Request $req, Response $resp){

            $page       = max(intval($req->get('page')), 1);

            $pagesize   = 20;

            $actorId    = '';

            $moment     = new mMoment();

            $momentList = $moment->getMomentList($actorId, $page, $pagesize); 

            $actors     = [];

            $contentType= [
                            1 => '文本',
                            2 => '图片',
                            3 => '视频',
                            4 => '音频',
                          ];

            $totalMoment = 0;

            if($momentList){

                $actorsId = array_column($momentList, 'aid');

                $actor    = new Actor();

                $actors   = $actor->getInfoById($actorsId, 'aid');

                $totalMoment = $moment->getTotalMoment($actorId);
            }

            $param = [];

            $pageInfo   = $this->getPageInfo("/admin/moment", $page, $totalMoment, $param, $pagesize);

            $param      = [
                            'list'        => $momentList
                            ,
                            'actors'      => $actors,

                            'contentType' => $contentType,
                          ];

            $param      = $param + $pageInfo;

            return $resp->withView('admin/moment_list.html')->withVars($param)->display();
        }

        public function doPost(Request $req, Response $resp){

            if(!$req->isPost()){

                return $resp->withView('admin/moment_add.html')->display();
            }

            $aid        = intval($req->post('actorId'));

            $content    = trim($req->post('content_text'));

            $type       = intval($req->post('type'));

            $video      = trim($req->post('video'));

            $image      = $req->post('image');

            $needPay    = intval($req->post('needPay'));

            $uid        = 1;

            if(!in_array($type, [1, 2, 3, 4])){

                return $this->error("参数错误，话题类型不正确", 101, "");
            }
            //文字话题
            if($type == 1){

                $content = strip_tags($content);
            }

            $audio  = trim($req->post('audio'));

            $moment = new mMoment();

            if(!$momentId = $moment->doPost($content, $type, $video, $audio, $image, $aid, $uid, $needPay)){

                return $this->error('动态发表失败' . var_export($topic->getError(), true), 201, '');
            }

            return $this->success('话题发表成功','');
        }

        public function editMoment(Request $req, Response $resp){

            if(!$req->isPost()){

                $momentId    = intval($req->get('id'));

                $moment      = new mMoment();

                $momentInfo  = $moment->getInfoById($momentId, 'mid');

                if(!$momentInfo){

                    return $this->error('指定的动态不存在', 202, 'javascript:history.back();');
                }

                $actor      = new Actor();

                $actorInfo  = $actor->getInfoById($momentInfo['aid'], 'aid');

                $param = [
                        'moment' => $momentInfo,
                        'actor' => $actorInfo ? $actorInfo : ['id' => '', 'nickname' => ''],
                        ];

                return $resp->withView('admin/moment_edit.html')->withVars($param)->display();
            }

            $momentId    = intval($req->post('momentId'));

            $aid        = intval($req->post('actorId'));

            $content    = trim($req->post('content_text'));

            $type       = intval($req->post('type'));

            $video      = trim($req->post('video'));

            $image      = $req->post('image');

            $needPay    = intval($req->post('needPay'));

            $uid        = 1;

            if(!in_array($type, [1, 2, 3, 4])){

                return $this->error("参数错误，动态类型不正确", 101, "");
            }
            //文字话题
            if($type == 1){

                $content = strip_tags($content);
            }

            $audio  = trim($req->post('audio'));

            $moment = new mMoment();

            $params = [
                        'uid'                => $uid,
                        'aid'                => $aid,
                        'content_text'       => $content,
                        'content_type'       => $type,
                        'video_url'          => $video,
                        // 'audio_url'          => $audio,
                        'img_url'            => json_encode($image),
                        'img_count'          => count($image),
                        'needPay'            => $needPay,
                      ];

            if(!$moment->setInfo('mid = ?', [$momentId], $params)){

                return $this->error('动态更新失败' . var_export($moment->getError(), true), 201, '');
            }

            return $this->success('动态更新成功','');
        }
    }