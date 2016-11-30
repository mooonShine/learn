<?php
/**
 * User: Administrator
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */

class Bootstrap extends Yaf_Bootstrap_Abstract
{
    private $_config;

    //配置拷贝
    public function _initBootstrap()
    {
        $this->_config = Yaf_Application::app()->getConfig();
    }

    //为当前脚本设置 include_path 运行时的配置选项。
    public function _initIncludePath()
    {
        set_include_path(get_include_path().PATH_SEPARATOR.$this->_config->application->library);
    }

    //是否开启错误语法，上线后要关掉
    public function _initErrors()
    {
        if ($this->_config->application->showErrors) {
           ini_set('display_errors', 'On');
        } else {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        }
    }

    //导入公共库
    public function _initCommon()
    {
        Yaf_Loader::import("Common.php");
    }

    public function _initRoutes(Yaf_Dispatcher $dispatcher)
    {
        $router = $dispatcher->getRouter();
        //Index前页
        //对应控制器和里面的action
        $route = new Yaf_Route_Rewrite(
            'index.html/',
            array('controller'=>'Index',
                    'action'=>'index'
                    )
        );
        $router->addRoute('Index_index',$route);
    }

    /**
     * 自定义视图引擎
     * */

    public function _initLayout(Yaf_Dispatcher $dispatcher)
    {
        $layout = new Layout($this->_config->application->layout->directory);
        $dispatcher->setView($layout);
    }

    /**
     * 定义错误常量
     */
    public function _initErrorConst()
    {
        //URL参数错误
        define("YAF_ERR_URL", 600);
        //数据错误
        define("YAF_ERR_DATA", 601);
    }



}
