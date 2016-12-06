<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use App\Helper\Storage\Qiniu;

    class Upload extends Base{

        public function token(Request $req, Response $resp){

            $bucket = trim($req->get('bucket'));

            $qiniu  = new Qiniu();

            $token  = $qiniu->getToken($bucket);

            echo json_encode(['uptoken' => $token]);
        }
    }