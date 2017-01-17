<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Actor AS mActor;

    class Actor extends Base{

        public function getActor(Request $req, Response $resp){

            $actorName = trim($req->post('actorName'));

            if(!$actorName){

                return $this->error("");
            }

            $actor      = new mActor();

            $actorinfo  = $actor->getActorByName($actorName);

            if(!$actorinfo){

                return $this->error("找不到任何主播", 101);
            }

            return $this->success('', '', ['id' => $actorinfo['aid'], 'nickname' => $actorinfo['nickname']]);
        }

        public function search(Request $req, Response $resp){

            $kw = trim($req->post('kw'));

            if(!$kw){

                return $this->success('','',[]);
            }

            $actor      = new mActor();

            $actors     = $actor->actorSearch($kw);

            $actorlist  = [];

            foreach($actors as $k => $v){

                $actorlist[] = [
                    'id'    => $v['aid'],
                    'name'  => $v['nickname'],
                ];
            }

            return $this->success('','', $actorlist);
        }
    }