$(function(){
    //banner
    //login
    $('#login input[type="text"],#login input[type="password"]').focus(function(){
        $(this).parent().addClass('ofocus');
    }).blur(function(){
        $(this).parent().removeClass('ofocus');
    });
    
    $('#loginForm').submit(function () {//登录表单提交前验证
        var msgAry = [];
        if (!$('#loginForm input[name="username"]').val()) {
            msgAry.push('请填写用户名');
        }
        if (!$('#loginForm input[name="password"]').val()) {
            msgAry.push('请填写密码');
        }
        if (!$('#loginForm input[name="code"]').val()) {
            msgAry.push('请填写验证码');
        }
        if (msgAry.length) {
            alert(msgAry.join('\n'));
            return false;
        }
    })
    //验证码
    $('#yzmHere').click(function() {
        $('#yzmHere').attr('src', '/index/yzm?time=' + ((new Date()) * 1));
    });
})