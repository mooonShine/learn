<!DOCTYPE HTML>
<html dir="ltr" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>米饭加盐（测试版）</title>

    <!--- CSS --->
    <link rel="stylesheet" href="/css/fallback.css" type="text/css" />

    <!--- Javascript libraries (jQuery and Selectivizr) used for the custom checkbox --->

    <!--[if (gte IE 6)&(lte IE 8)]><![endif]-->
    <script type="text/javascript" src="/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="/js/selectivizr.js"></script>
    <script type="text/javascript" src="/js/login.js"></script>


</head>

<body>
<div id="container">
    <form action="/login/login" method="post"  id="loginForm" >
        <div class="login">登录</div>
        <div class="username-text">用户名:</div>
        <div class="password-text">密码:</div>
        <div class="username-field">
            <input type="text" name="username" value="" placeholder="azmind"/>
        </div>
        <div class="password-field">
            <input type="password" name="password" value=""  />
        </div>
        <div >
            <input name="code" type="text" value="" placeholder="验证码"/>
        </div>
        <div class="forgot-usr-pwd">
            <img src="/index/yzm?time=<?php echo time(); ?>" width="70" height="33" id="yzmHere"/>
            <span id="changeYZM">看不清换一张</span>
        </div>
        <input type="checkbox" name="remember-me" id="remember-me" /><label for="remember-me">保存账号</label>
        <div class="forgot-usr-pwd">忘记<a href="#">用户名</a> 或 <a href="#">密码</a>?</div>
        <input type="submit" name="submit" value="GO" />
    </form>
</div>
<div id="footer">
    版权 &copy; 2014.米饭加盐保留所有权。<a href="/index/index">返回首页</a>
</div>

</body>
</html>