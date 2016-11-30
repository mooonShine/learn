<?php

/**
 * User: Administrator
 */
class Controller_Error extends Blog_Web
{
        public function init()
        {
            parent::init();
        }

        public function errorAction($exception){
//            Yaf_Dispatcher::getInstance()->disableView();
            switch($exception->getCode())
            {
                case YAF_ERR_NOTFOUND_MODULE:
                case YAF_ERR_NOTFOUND_CONTROLLER:
                case YAF_ERR_NOTFOUND_ACTION:
                case YAF_ERR_NOTFOUND_MODULE:
                    $this->E404($exception);
                    break;
                case YAF_ERR_URL:
                    $this->showMsg($exception->getMessage(), YAF_ERR_URL);
                    break;
                case YAF_ERR_DATA:
                    $this->showMsg($exception->getMessage(), YAF_ERR_DATA);
                    break;
                default:
                    $message = $exception->getMessage();
                    echo 0, ":", $exception->getMessage();
                    break;
            }
        }

        private function E404($error)
        {
            echo $error." 404";
        }

        /**
         * url错误。
         * 如参数非法数据
         */
        private function showMsg($msg,$code)
        {
            echo sprintf('错误消息：%s，错误代码：%d 点击<a href="http://zhi.9xu.com">这里</a>返回首页',$msg,$code);
        }
}
