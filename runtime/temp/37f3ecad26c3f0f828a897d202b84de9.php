<?php /*a:1:{s:62:"D:\phpStudy\WWW\tp1810\application\admin\view\login\index.html";i:1551265607;}*/ ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>后台管理-登陆</title>
    <link rel="stylesheet" href="/static/layui/css/layui.css">
    <link rel="stylesheet" href="/static/admin.css">
    <script src="/static/js/jquery-3.3.1.min.js"></script>
</head>
<body>
<div id="container">
    <div></div>
    <div class="admin-login-background">
        <!-- <div class="admin-header">
        <img src="image/ex_logo.png" class="admin-logo">
        </div> -->
        <form class="layui-form" action="" method="POST">
            <div>
                <i class="layui-icon layui-icon-username admin-icon admin-icon-username"></i>
                <input type="text" name="admin_name" placeholder="请输入用户名"
                       autocomplete="off" value="<?php echo isset($data['admin_name']) ? htmlentities($data['admin_name']) : ''; ?>"
                       class="layui-input admin-input admin-input-username">
            </div>
            <div>
                <i class="layui-icon layui-icon-password admin-icon admin-icon-password"></i>
                <input type="password" name="admin_pwd"
                       placeholder="请输入密码"
                       autocomplete="off" value="<?php echo isset($data['admin_pwd']) ? htmlentities($data['admin_pwd']) : ''; ?>"
                       class="layui-input admin-input">
            </div>
            <div>
                <input type="text" name="verify"
                       placeholder="请输入验证码"
                       autocomplete="off" value="<?php echo isset($data['verify']) ? htmlentities($data['verify']) : ''; ?>"
                       class="layui-input admin-input admin-input-verify">
                <img class="admin-captcha" width="90" height=30 src="<?php echo url('Login/verify'); ?>" alt="captcha"
                     onclick="this.src='/admin/login/verify.html';">
                <pstyle="color:red;"> <b><?php if(isset($errors)): ?> <?php echo htmlentities($errors); ?><?php endif; ?></b> </p>
            </div>
            <p><b style="color:red;" id="error"><?php echo isset($error) ? htmlentities($error) : ''; ?></b></p>
            <button class="layui-btn admin-button" lay-submit lay-filter="formDemo">登陆</button>
        </form>
    </div>
</div>
<script>
  $('.admin-button').click(function(){
    var admin_name=$('input[name=admin_name]').val();
    if(admin_name==''){
      $('#error').text('管理员名称不能为空');
      return false;
    }
    var admin_pwd=$('input[name=admin_pwd]').val();
    if(admin_pwd==''){
      $('#error').text('密码不能为空');
      return false;
    }
    var verify=$('input[name=verify]').val();
    if(verify==''){
      $('#error').text('验证码不能为空');
      return false;
    }
    if(admin_name&&admin_pwd&&verify){
      $('form').submit();
    }
  });
</script>
</body>
</html>
