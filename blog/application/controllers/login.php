<?php
/**
 * User: Administrator
 */

class Controller_Login extends Blog_Web
{
    private  $model;
    private  $errorArray = array();
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->model = new Model_User();
    }


    /**
     * 登录
     * */
    public function LoginAction()
    {
        if($this->_request->getPost())
        {
            $username = $this->_request->getPost('username');
            $psd = $this->_request->getPost('password');
            $code = $this->_request->getPost('code');
            if(!$username)
            {
                $this->errorArray[] = '请输入用户名';
            }
            if(!$psd)
            {
                $this->errorArray[] = '请输入密码';
            }
            if(strlen($username)<4 || strlen($username)>32)
            {
                $this->errorArray[] = '用户名长度不正确';
            }
            if(!$code)
            {
                $this->errorArray[] = '请输入验证码';
            }
//            if($this->_session->get('blogcode')!=$code)
//            {
//                $this->errorArray[] = '验证码不正确';
//            }

            if(!$this->errorArray)
            {
                $info = $this->model->get('*',array('username'=>$username));
                if($info)
                {
                    if(!md5($psd)==$info['password'])
                    {
                        $this->errorArray[] = '账号或者密码不正确';
                    }else{
                        $this->_session->set('user', $username);
                        $this->redirect("/index/index");
                    }
                }else{
                    $this->errorArray[] = '账号或者密码不正确';
                }
            }
            jsRedirect($this->errorArray['0'], "/login/login");
        }else{
            $this->display('login/login');
        }
    }

    /**
     * 退出
     * */
    public function logoutAction()
    {
        session_unset();
        session_destroy();
        $this->redirect("/");
    }


}