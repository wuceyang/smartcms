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

                return $this->error(var_export($actor->getSqls(), true));
            }

            return $this->success('', '', ['id' => $actorinfo['aid'], 'nickname' => $actorinfo['nickname']]);
        }
    }