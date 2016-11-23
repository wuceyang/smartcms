<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\Helper\Enum;
    use \App\M\Template AS mTemplate;

    class Template extends Base{

        public function index(Request $req, Response $resp){

            $status       = intval($req->get('status'));

            $template     = new mTemplate();

            $templateList = $template->getTemplateList($status);

            return $resp->withVars(['list' => $templateList])->withView('admin/template_list.html')->display();
        }

        public function switchTemplate(Request $req, Response $resp){

            $tplid        = intval($req->post('tplid'));

            $status       = intval($req->post('status'));

            $template     = new mTemplate();

            $templateInfo = $template->getInfoById($tplid);

            if(!$templateInfo){

                return $this->error("找不到指定的模板信息", 101, "/admin/template");
            }

            if($templateInfo['status'] == $status){

                return $this->success("模板状态更新成功", "/admin/template");
            }

            $updateInfo = [
                'status'        => $status,
                'modify_uid'    => $this->userinfo['id'],
                'modify_time'   => date('Y-m-d H:i:s'),
            ];

            if(!$template->setTemplateInfo($tplid, $updateInfo)){

                return $this->error("更新模板状态发生错误", 101, "/admin/template");
            }

            return $this->success("模板状态更新成功", "/admin/template");
        }

        public function addTemplate(Request $req, Response $resp){

            if(!$req->isPost()){

                $this->setFormToken($req, $resp);

                $param = [
                    'formInfo' => $req->session()->get('formInfo'),
                ];

                return $resp->withVars($param)->withView('admin/template_add.html')->display();
            }

            if(!$this->formTokenValidate($req, $resp)){

                return $this->error("请不要提交非法数据", 101, "/admin/template/add-template");
            }

            $tplName      = htmlspecialchars(trim($req->post('tplName')));

            $tplContent   = trim($req->post('tplContent'));

            $template     = new mTemplate();

            $templateInfo = $template->getTemplateByName($tplName);

            if($templateInfo){

                $req->session()->set('formInfo', ['tplName' => $tplName, 'tplContent' => $tplContent]);

                return $this->error("当前模板名称已经存在，请指定一个不同的名称", "/admin/template/add-template");
            }

            if(!$template->addTemplate($tplName, $tplContent, $this->userinfo['id'])){

                return $this->error("模板信息录入失败", 102, "/admin/template/add-template");
            }

            $req->session()->set('formInfo', []);

            $this->success("模板信息录入成功", "/admin/template");
        }

        public function editTemplate(Request $req, Response $resp){

            if(!$req->isPost()){

                $tplid = intval($req->get('id'));

                $template     = new mTemplate();

                $templateInfo = $template->getInfoById($tplid);

                if(!$templateInfo){

                    return $this->error("找不到指定的模板信息", 101, "/admin/template");
                }

                $this->setFormToken($req, $resp);

                return $reqp->withVars(['info' => $templateInfo])->withView('admin/template_edit.html')->display();
            }

            if(!$this->formTokenValidate($req, $resp)){

                return $this->error("请不要提交非法数据", 101, "/admin/template/add-template");
            }

            $tplid = intval($req->post('id'));

            $tplName = htmlspecialchars($req->post('tplName'));

            $tplContent = trim($req->post('tplContent'));

            $template     = new mTemplate();

            $templateInfo = $template->getInfoById($tplid);

            if(!$templateInfo){

                return $this->error("找不到指定的模板信息", 101, "/admin/template");
            }

            if($templateInfo['template_name'] == $tplName && $templateInfo['template_content'] == $tplContent && $templateInfo['status'] == $status){

                return $this->success("模板信息编辑成功", "/admin/template");
            }

            $tplInfo = [
                'template_name'    => $tplName,
                'template_content' => $tplContent,
                'modify_time'      => date('Y-m-d H:i:s'),
                'modify_uid'       => $this->userinfo['id'], 
            ];

            if(!$template->setTemplateInfo($tplid, $tplInfo)){

                return $this->error("更新模板信息发生错误", 102, "/admin/template");
            }

            return $this->success("模板信息更新成功", "/admin/template");
        }
    }