<!doctype html>
<html>
<head>
<!--    <meta charset="gb2312">-->
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>米饭加盐（测试版）</title>
    <meta name="keywords" content="米饭加盐（测试版）,博客模板" />
    <meta name="description" content="测试版主题的个人博客模板，优雅、稳重、大气,低调。" />
    <link href="/css/base.css" rel="stylesheet">
    <link href="/css/index.css" rel="stylesheet">
    <link href="/css/about.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <script src="/js/modernizr.js"></script>
</head>
<body>
<header>
    <div id="logo"><a href="/"></a></div>
    <nav class="topnav" id="topnav">
            <a href="/index/index">
            <span>首页</span>
            <span class="en">Protal</span></a>
        <a href="/index/about">
            <span>关于我</span>
            <span class="en">About</span>
        </a>
        <a href="/index/life">
            <span>慢生活</span>
            <span class="en">Life</span>
        </a>
<!--        <a href="moodlist.html">-->
<!--            <span>碎言碎语</span>-->
<!--            <span class="en">Doing</span>-->
<!--        </a>-->
<!--        <a href="share.html">-->
<!--            <span>技术分享</span>-->
<!--            <span class="en">Share</span>-->
<!--        </a>-->
        <a href="/index/knowledge">
            <span>学无止境</span>
            <span class="en">Learn</span>
        </a><a href="/index/book">
            <span>留言版</span>
            <span class="en">Gustbook</span>
        </a>
        <?php  if(isset($_SESSION['user']))
        {

        ?>
            </a><a href="/index/logout/">
                <span style="color:#999;">欢迎：<?php echo $_SESSION['user']; ?></span>
                <span class="en">退出</span>
            </a>
        <?php
        }else
        {
            ?>
            </a><a href="/index/login/">
                <span style="color:#999;">登录/注册</span>
                <span class="en">blogLogin</span>
            </a>
        <?php
        }

        ?>
    </nav>

</header>
<?php echo $_content_ ?>
<footer>
    <p>Design by DanceSmile <a href="http://www.miitbeian.gov.cn/" target="_blank">蜀ICP备11002373号-1</a> <a href="/">网站统计</a></p>
</footer>
<script src="/js/silder.js"></script>
<script type="text/javascript">
//    alert(Math.ceil(new Date()/3600000));
</script>
</body>
</html>


