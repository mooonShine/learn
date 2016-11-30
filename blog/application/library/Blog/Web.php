<?php
/**
 * User: Administrator
 */
class Blog_Web extends Yaf_Controller_Abstract
{
    protected $request_http;
    protected $layout;
    protected $_request;
    protected $_config;
    protected $_session;
    protected $view_path;

    public function init()
    {
        $this->_config = Yaf_Application::app()->getconfig();
        $this->request_http = new yaf_Request_Http;
        $this->_request = $this->getRequest();
        $this->_session = new Component_Session;
        $this->getView()->setLayout( $this->layout );//添加模板文件
        Yaf_Dispatcher::getInstance()->disableView();//关闭自动渲染
//        $this->_m = $this->getRequest()->getModuleName();
//        $this->_c = $this->getRequest()->getControllerName();
//        $this->_a = $this->getRequest()->getActionName();
    }

    //渲染视图
    protected function display( $view_path = NULL, array $tpl_vars = array() )
    {
        $this->set_view_path( $view_path );
        self::getView()->display( $this->view_path, $tpl_vars );
        return;
    }

    /**
     * 给视图赋值变量
     * */
    protected function assign ( $name = NULL, $value = NULL)
    {
        if(!$name){
            return ;
        }

        if(is_string($name)){
            if(!$value){
                return;
            }
            return $this->getview()->assign($name,$value);
        }else{
            return $this->getvies()->assign($name);
        }
    }

    private function set_view_path( $view_path )
    {
        $fileInfo = pathinfo( $view_path );
        if (!isset( $fileInfo['extension'] ))
        {
            $view_path .= "." . $this->_config->application->view->ext;
        }
        $this->view_path = $view_path;
        return TRUE;
    }

    /**
     * 处理权限信息
     */
    private function dealPermission()
    {
        $user = $this->_session->get('user');
        $gmodel = new Model_Group();
        $data = $gmodel->getOneGroup($user['group_id']);

//        var_dump($data);die;

        define("GROUP_ID", $data['id']);
        define("IS_ADMIN", $data['name'] == "SDK管理员");
        define("IS_ADVERT", $data['name'] == "SDK推广者");

        //后台跳转
        if (in_array($data['name'], array("SDK管理员")))
        {
            $this->redirect("/blogadmin/index/index");
            exit;
        }

        //导航处理
        $this->nav();

//        //生成权限标记
//        Permission::createPerssionTag();
//
//        //判断是否有权限
//        if (!Permission::check($this->m, $this->c, $this->a))
//        {
//            // $this->redirect("/index/index");
//        }
    }




}
