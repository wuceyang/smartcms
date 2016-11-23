<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Menu;
    use \App\M\UserGroup;
    use \App\M\GroupPrivilege;

    class Index extends Base{

        public function index(Request $req, Response $resp){

            

            return $resp->withView('admin/index.html')->display();
        }
    }