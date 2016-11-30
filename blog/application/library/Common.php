<?php

/**
 * 获取远程内容（接口数据获取）
 * @param $url
 * @param array $keysArr
 * @param string $mothod
 * @param bolen $is_header
 * @param int $flag
 * @return mixed
 */
function get_contents( $url, $keysArr = array(), $mothod = 'get',$is_header=1, $flag = 0 )
{
    $ch = curl_init();
    if (!$flag)
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    if (strtolower( $mothod ) == 'post')
    {
        curl_setopt( $ch, CURLOPT_POST, TRUE );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $keysArr );
    } else
    {
        $url = $url . "?" . http_build_query( $keysArr );
    }
    curl_setopt( $ch, CURLOPT_URL, $url );
    
    if( $is_header )
    {
        $_time = time();
        $headers['ktime'] = $_time;
        $headers['kmd5'] = md5( $_time.  fn_get_interface_key());
        foreach( $headers as $n => $v ) 
        { 
            $headerArr[] = md5($n) .':' . $v;  
        }
        curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr );
    }
    
    $ret = curl_exec( $ch );
    curl_close( $ch );
    return $ret;
}


//判断是否是json

function is_json( $str )
{
    $ret = FALSE;

    $str = json_decode( $str );

    if ($str)
    {
        $ret = TRUE;
    }
    return $ret;
}

/**
 * 获取值
 * @param  [type] $name [description]
 * @return [type]       [description]
 */
function getVal( $name, $default = '' )
{
    if (isset( $_POST[$name] ))
    {
        return $_POST[$name];
    }
    if (isset( $_GET[$name] ))
    {
        return $_GET[$name];
    }
    return $default;
}

/**
 * JS跳转
 * @param  string $msg [提示消息]
 * @param  string $url [跳转url]
 * @return [type]      [description]
 */
function jsRedirect( $msg = '', $url = '' )
{
    header( 'Content-Type:text/html;charset=utf-8' );

    $js = '<script type="text/javascript">';

    if (!empty( $msg ))
    {
        $js .= "alert('$msg');";
    }

    if (!empty( $url ))
    {
        $js .= "window.location = '$url';";
    }

    echo $js . '</script>';
}

/**
 * Ajax 返回JSON
 * @param  integer $return 0：失败， 1：成功
 * @param  string $message 提示信息
 * @param  array $data 返回的数据
 * @return JSON
 * */
function ajaxReturn( $return = 0, $message = NULL, $data = NULL )
{
    $r_data['ret'] = $return;
    if ($message)
    {
        $r_data['msg'] = $message;
    }
    if ($data)
    {
        $r_data['data'] = $data;
    }

    exit( json_encode( $r_data ) );
}

function createJSForAdvertising( $id, $width, $height )
{
    $widthValue = $width . "px";
    $heightValue = $height . "px";

    $jsContent = <<<EOT
(function() {
    var pd = '$id';
    var ltu = location.host;
    var psr = window.screen.width + 'x' + window.screen.height;
    var di = 'u1166210';
    var BAIDU_DUP_lcr = getCookie('BAIDU_DUP_lcr');
    var width = '$width';
    var height = '$height';
    function getCookie(c_name) {
        if (document.cookie.length > 0) {
            var c_start = document.cookie.indexOf(c_name + "=");
            if (c_start != -1) {
                c_start = c_start + c_name.length + 1;
                var c_end = document.cookie.indexOf(";", c_start);
                if (c_end == -1) {
                    c_end = document.cookie.length;
                }
                return unescape(document.cookie.substring(c_start, c_end));
            }
        }
        return "";
    }
    document.write('<iframe src="http://ssp.juhuisuan.com/show?pd=' + pd + '&ltu=' + ltu + '&psr=' + psr + '&di=' + di + '&ltr=' + BAIDU_DUP_lcr + '" width="' + width + 'px" height="' + height + 'px" border="0" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="true"></iframe>');
})();

EOT;

    $realPath = realpath( BASE_PATH ) . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "pjs" . DIRECTORY_SEPARATOR . "{$id}.js";
    $fp = fopen( $realPath, "w+" );
    fputs( $fp, $jsContent );
    fclose( $fp );
}

function deleteJSForAdvertising( $id )
{
    try {
        $realPath = realpath( BASE_PATH ) . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "pjs" . DIRECTORY_SEPARATOR . "{$id}.js";
        if (file_exists( $realPath ))
        {
            unlink( $realPath );
        }
    } catch (Exception $e) {

    }
}

/**
 * 获取用户ID
 * @return [type] [description]
 */
function getUserId()
{
    $s = new Component_Session();
    $user = $s->get( 'user' );
    if ($user)
    {
        return '136';
    }
}

function drawOnePixelPng()
{
    header( "Content-type: image/png" );
    $im = @imagecreate( 1, 1 ) or die( "无法生成图片,有可能是GD扩展有问题" );
    imagecolorallocate( $im, 255, 255, 255 );
    imagepng( $im );
    imagedestroy( $im );
}

function getFileExt( $pathIncludeFile )
{
    return strtolower( trim( substr( strrchr( $file, '.' ), 1 ) ) );
}

function fn_get_show_type()
{
    $show_type = array(
        'exhibition' => 1,
        'classification' => 2,
        'my' => 3,
        'thrid' => 4,
    );
    return $show_type;
}

/**
 * 获取聚会算广告尺寸
 * @return array
 */
function getJHSAdSize()
{
    $ary = Yaf_Application::app()->getConfig();

    if (!empty( $ary['adsize'] ))
    {
        return explode( ',', $ary['adsize'] );
    }

    return array();
}

//获取IP
function fn_getIP()
{
    if (getenv( "HTTP_CLIENT_IP" ))
        $ip = getenv( "HTTP_CLIENT_IP" );
    else if (getenv( "HTTP_X_FORWARDED_FOR" ))
        $ip = getenv( "HTTP_X_FORWARDED_FOR" );
    else if (getenv( "REMOTE_ADDR" ))
        $ip = getenv( "REMOTE_ADDR" );
    else
        $ip = "Unknow";
    return $ip;
}

//生成验证码
function getCode( $w, $h )
{
    $im = imagecreate( $w, $h );

    //imagecolorallocate($im, 14, 114, 180); // background color
    $red = imagecolorallocate( $im, 255, 0, 0 );
    $white = imagecolorallocate( $im, 255, 255, 255 );

    $num1 = rand( 1, 20 );
    $num2 = rand( 1, 20 );

    $_SESSION['sspcode'] = $num1 + $num2;

    $gray = imagecolorallocate( $im, 118, 151, 199 );
    $black = imagecolorallocate( $im, mt_rand( 0, 100 ), mt_rand( 0, 100 ), mt_rand( 0, 100 ) );

    //画背景
    imagefilledrectangle( $im, 0, 0, 100, 24, $black );
    //在画布上随机生成大量点，起干扰作用;
    for ($i = 0; $i < 80; $i++)
    {
        imagesetpixel( $im, rand( 0, $w ), rand( 0, $h ), $gray );
    }

    imagestring( $im, 5, 5, 4, $num1, $red );
    imagestring( $im, 5, 30, 3, "+", $red );
    imagestring( $im, 5, 45, 4, $num2, $red );
    imagestring( $im, 5, 70, 3, "=", $red );
    imagestring( $im, 5, 80, 2, "?", $white );

    header( "Content-type: image/png" );
    imagepng( $im );
    imagedestroy( $im );
}

/**
 * 验证码 字符类型
 * getCode(4,60,20);
 * @param type $num
 * @param type $w
 * @param type $h
 */
function getCodeChar( $num, $w, $h )
{
    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $text = "";
    $num = array();
    for ($i = 0; $i < 4; $i++)
    {
        $num[$i] = rand( 0, 25 );
        $text.=$str[$num[$i]];
    }
    $_SESSION['blogcode'] = strtolower( $text );
    $im_x = 160;
    $im_y = 40;
    $im = imagecreatetruecolor( $im_x, $im_y );
    $text_c = ImageColorAllocate( $im, mt_rand( 0, 100 ), mt_rand( 0, 100 ), mt_rand( 0, 100 ) );
    $tmpC0 = mt_rand( 100, 255 );
    $tmpC1 = mt_rand( 100, 255 );
    $tmpC2 = mt_rand( 100, 255 );
    $buttum_c = ImageColorAllocate( $im, $tmpC0, $tmpC1, $tmpC2 );
    imagefill( $im, 16, 13, $buttum_c );
    $font = realpath( BASE_PATH . '/public/t1.ttf' );
    for ($i = 0; $i < strlen( $text ); $i++)
    {
        $tmp = substr( $text, $i, 1 );
        $array = array(-1, 1);
        $p = array_rand( $array );
        $an = $array[$p] * mt_rand( 1, 10 ); //角度
        $size = 28;
        imagettftext( $im, $size, $an, 15 + $i * $size, 35, $text_c, $font, $tmp );
    }


    $distortion_im = imagecreatetruecolor( $im_x, $im_y );

    imagefill( $distortion_im, 16, 13, $buttum_c );
    for ($i = 0; $i < $im_x; $i++)
    {
        for ($j = 0; $j < $im_y; $j++)
        {
            $rgb = imagecolorat( $im, $i, $j );
            if ((int) ($i + 20 + sin( $j / $im_y * 2 * M_PI ) * 10) <= imagesx( $distortion_im ) && (int) ($i + 20 + sin( $j / $im_y * 2 * M_PI ) * 10) >= 0)
            {
                imagesetpixel( $distortion_im, (int) ($i + 10 + sin( $j / $im_y * 2 * M_PI - M_PI * 0.1 ) * 4), $j, $rgb );
            }
        }
    }
    //加入干扰象素;
    $count = 160; //干扰像素的数量
    for ($i = 0; $i < $count; $i++)
    {
        $randcolor = ImageColorallocate( $distortion_im, mt_rand( 0, 255 ), mt_rand( 0, 255 ), mt_rand( 0, 255 ) );
        imagesetpixel( $distortion_im, mt_rand() % $im_x, mt_rand() % $im_y, $randcolor );
    }

    $rand = mt_rand( 5, 30 );
    $rand1 = mt_rand( 15, 25 );
    $rand2 = mt_rand( 5, 10 );
    for ($yy = $rand; $yy <= +$rand + 2; $yy++)
    {
        for ($px = -80; $px <= 80; $px = $px + 0.1)
        {
            $x = $px / $rand1;
            if ($x != 0)
            {
                $y = sin( $x );
            }
            $py = $y * $rand2;

            imagesetpixel( $distortion_im, $px + 80, $py + $yy, $text_c );
        }
    }

    //设置文件头;
    Header( "Content-type: image/JPEG" );

    //以PNG格式将图像输出到浏览器或文件;
    ImagePNG( $distortion_im );

    //销毁一图像,释放与image关联的内存;
    ImageDestroy( $distortion_im );
    ImageDestroy( $im );
}
/**
 * 获取远程内容（接口数据获取）
 * @param $url
 * @param array $keysArr
 * @param string $mothod
 * @param bolen $is_header
 * @param int $flag
 * @return mixed
 */
function fn_get_contents( $url, $keysArr = array(), $mothod = 'get',$is_header=1, $flag = 0 )
{
    $ch = curl_init();
    if (!$flag)
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    if (strtolower( $mothod ) == 'post')
    {
        curl_setopt( $ch, CURLOPT_POST, TRUE );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $keysArr );
    } else
    {
        $url = $url . "?" . http_build_query( $keysArr );
    }
    curl_setopt( $ch, CURLOPT_URL, $url );
    
    if( $is_header )
    {
        $_time = time();
        $headers['ktime'] = $_time;
        $headers['kmd5'] = md5( $_time.  fn_get_interface_key());
        foreach( $headers as $n => $v ) 
        { 
            $headerArr[] = md5($n) .':' . $v;  
        }
        curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr );
    }
    
    $ret = curl_exec( $ch );
    curl_close( $ch );
    return $ret;
}

/*
 * 获限interface接口，加密key
 * @author :Sgenmi
 * @date : 2014-07-13
 * 
 */

function fn_get_interface_key()
{
    $interface_key = "#&0%o#d8$*s&5u^@*^s456";
    $_config = Yaf_Application::app()->getConfig();
    if (isset( $_config->keys->interface_key ))
    {
        $interface_key = $_config->keys->interface_key;
    }
    return $interface_key;
}
function fn_replace_urlindex( $url )
{
    $_url = trim( $url, "/" );
    $url_array = explode( "/", $_url );
    if (!$url_array)
    {
        return $url;
    }

    if ($url_array[0] == 'index')
    {
        unset( $url_array[0] );
    }

    if (end( $url_array ) == "index")
    {
        array_pop( $url_array );
    }

    $r = "/" . implode( "/", $url_array );
    return $r;
}

function fn_Log( $word = '',$path="/",$file_name='log.txt',$show_time=1 )
{
    $_time = "";
    $file_path = LOG_PATH.$path;
    if (!is_dir( $file_path ))
    {
        fn_mkdir($file_path);
    }
    $file = rtrim($file_path, "/")."/".$file_name;

    if($show_time)
    {
        $_time = "执行日期：" . strftime( "%Y%m%d%H%M%S", time() ) . "\n";
    }
    $fp = fopen( $file, "a+" );
    flock( $fp, LOCK_EX );
    fwrite( $fp,  $_time. $word . "\n" );
    flock( $fp, LOCK_UN );
    fclose( $fp );
}

function fn_mkdir( $file_path )
{

    $_f = "/";
    if (!$file_path)
    {
        return;
    }
    $file_path =  array_filter(explode( "/", $file_path));
    foreach ($file_path as $v)
    {
        if (!$v)
        {
            continue;
        }
        $_f.=$v . "/";
        if (!is_dir( $_f ))
        {
            mkdir( $_f, 0777 );
        }
    }
}



/*
 * author:Sgenmi
 * date:2014-06-13
 * 判断是否在时间段
 */

function fn_time_interval( $time_interval,$time = null )
{
    $is_true = false;
    $week = array(
        0 => 6, //星期天
        1 => 0, //星期一
        2 => 1, //星期二
        3 => 2, //星期三
        4 => 3, //星期四
        5 => 4, //星期五
        6 => 5   //星期六
    );

    if (!isset($time)) $time = time();

    $time_arrya = explode( "|", $time_interval );
    $week_array = isset( $time_arrya[$week[date( 'w', $time )]] ) ? $time_arrya[$week[date( 'w', $time )]] : array();
    if ($week_array)
    {
        $hour_array = str_split( $week_array, 1 );

        $is_true = isset( $hour_array[date( 'G', $time )] ) ? $hour_array[date( 'G', $time )] : false;
    }

    return $is_true;
}

function d($str,$val=TRUE){
    echo "<pre>";
    print_r($str);
    echo "</pre>";
    if(!$val){
        die;
    }
}

/**
 * 实在不想在页面重复用date写Y-m-d格式了
 * @author lizhe
 */
function ymd($intval)
{
    return date('Y-m-d', $intval);
}

/**
 * 获取开始和结束日期，并验证日期合法性
 * @param $bstr, $estr。值例如：+5 hours，next Monday，+1 week 3 days 7 hours 5 seconds，灵活定义起止时间
 * @return array(bdate => $intval_1, edate => $intval_2)，验证传入数据非法，则起始日期默认返回昨天
 * @author lizhe
 */
function fn_get_date($bstr = '-1 day', $estr = '-1 day')
{
    $bdate = 0; //beginDate
    $edate = 0; //endDate
    if(isset($_GET['beginDate']) && isset($_GET['endDate'])){
        $bArr = explode('-', $_GET['beginDate']);
        $eArr = explode('-', $_GET['endDate']);
        if(checkdate($bArr[1], $bArr[2], $bArr[0]) && checkdate($eArr[1], $eArr[2], $eArr[0])){
            $bdate = intval(strtotime($_GET['beginDate'] . ' 00:00:00'));
            $edate = intval(strtotime($_GET['endDate'] . ' 23:59:59'));
        }
    }
    if($bdate == 0 || $edate == 0){
        $bdate = strtotime(date('Y-m-d 00:00:00', strtotime($bstr)));
        $edate = strtotime(date('Y-m-d 23:59:59', strtotime($estr)));
    }
    return array('bdate' => $bdate, 'edate' => $edate);
}

/**
 * 是否是测试环境
 * @return [type] [description]
 */
function fn_isDebug()
{
    $config = Yaf_Application::app()->getConfig();

    if (isset($config->application->debug) && $config->application->debug == 1)
    {
        return true;
    }

    return false;
}

/**
 * 各种验证
 * @author lz
 */
function fn_check($type, $value)
{
    if (!$value) {
        return false;
    }
    switch ($type) {
        case 'email':
            return preg_match("/^[0-9a-zA-Z_]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i", $value);
        case 'phone':
            return preg_match("/^1[34578][0-9]{9}$/", $value);
        case 'username':
            return preg_match("/^[A-Za-z0-9\x{4e00}-\x{9fa5}]+$/u", $value);
        case 'qq':
            return preg_match('/^[1-9][\d]{4,11}$/', $value);
        default:
            return false;
    }
}


/**
 * 获取配置文件
 */
function fn_getConfig()
{
    $_config = Yaf_Registry::get('config');
    if(!$_config)
    {
        $_config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config',$_config);
    }
    return $_config;
}

/**
 * 上传app
 * @return [type] [description]
 */
function fn_upload_app($name, $options = array(), $doUpload = true) {
    $ext = array('apk', 'ipa');
    $appServer = fn_getConfig()->approot;
    $webRoot = fn_getConfig()->webroot;
    $app_url = $appServer . '/upload';
    $result = $name;
    if ($doUpload) {
        $fileDriver = new FileUpload($name);
        $result = $fileDriver->run();
        if (is_array($result)) {
            return json_encode(array('ret' => 0, "msg" => $result[0], ""));
        }
        
        //临时用
        return json_encode(array('ret' => 1, "msg" => "上传成功", 'data' => $webRoot . trim($result)));
        
    }
    $file_name = realPath(BASE_PATH . '/public' . $result);
    $pathinfo = pathinfo($file_name);

    if (isset($pathinfo['extension']) && in_array($pathinfo['extension'], $ext)) {
        //读取文件内容
        if (file_exists($file_name)) {
            $content = file_get_contents($file_name);
            //删除文件
            @unlink($file_name);
            //参数构建
            $ext_name = $pathinfo['extension'];
            $post_options = array('content' => $content, 'ext' => $ext_name);

            $server_result = fn_get_contents($app_url, $post_options, 'post', 1);
            
            return json_encode(array('ret' => 1, "msg" => "上传成功", 'data' => 'http://img.9xu.com/' . trim($server_result)));
        } else {
            return json_encode(array('ret' => 0, "msg" => "文件不存在", ""));
        }
    } else {
        return json_encode(array('ret' => 0, "msg" => '文件扩展名不正确', ""));
    }
}
