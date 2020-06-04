<?php /*a:5:{s:59:"D:\phpStudy\WWW\tp1810\application\admin\view\news\add.html";i:1554685531;s:57:"D:\phpStudy\WWW\tp1810\application\admin\view\layout.html";i:1550652520;s:64:"D:\phpStudy\WWW\tp1810\application\admin\view\public\header.html";i:1556502170;s:62:"D:\phpStudy\WWW\tp1810\application\admin\view\public\left.html";i:1554699641;s:64:"D:\phpStudy\WWW\tp1810\application\admin\view\public\footer.html";i:1551083535;}*/ ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>layout 后台大布局 - Layui</title>
  <link rel="stylesheet" href="/static/layui/css/layui.css">
  <script src="/static/js/jquery-3.3.1.min.js"></script>
  <script src="/static/layui/layui.js"></script> 
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo">layui 后台布局</div>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <ul class="layui-nav layui-layout-left">
      <li class="layui-nav-item"><a href="">控制台</a></li>
      <li class="layui-nav-item"><a href="">商品管理</a></li>
      <li class="layui-nav-item"><a href="">用户</a></li>
      <li class="layui-nav-item">
        <a href="javascript:;">其它系统</a>
        <dl class="layui-nav-child">
          <dd><a href="">邮件管理</a></dd>
          <dd><a href="">消息管理</a></dd>
          <dd><a href="">授权管理</a></dd>
        </dl>
      </li>
    </ul>
    <ul class="layui-nav layui-layout-right">
      <li class="layui-nav-item"><a href="/" target="_blank">站点首页</a></li>
      <li class="layui-nav-item">
        <a href="javascript:;">
          <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
        
        </a>
        <dl class="layui-nav-child">
          <dd><a href="">基本资料</a></dd>
          <dd><a href="">安全设置</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item"><a href="<?php echo url('Login/logout'); ?>">溜了溜了</a></li>
    </ul>
  </div>
  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree"  lay-filter="test">
      <?php
        $controller=request()->controller();
        if(in_array($controller,['Goods','Category','Brand'])){
      ?>
        <li class="layui-nav-item layui-nav-itemed">
        <?php
          }else{
        ?>
        <li class="layui-nav-item">
          <?php
            }
          ?>
          <a class="" href="javascript:;">商品管理</a>
          <dl class="layui-nav-child">
            <dd <?php if($controller == 'Goods'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Goods/lists'); ?>">商品管理</a></dd>
            <dd <?php if($controller == 'Category'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Category/lists'); ?>">商品分类</a></dd>
            <dd <?php if($controller == 'Brand'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Brand/lists'); ?>">商品品牌</a></dd>
          </dl>
        </li>
        <?php
        if(in_array($controller,['User','Jues','Menu'])){
        ?>
        <li class="layui-nav-item layui-nav-itemed">
        <?php
          }else{
        ?>
        <li class="layui-nav-item">
          <?php
            }
          ?>
          <a href="javascript:;">权限管理</a>
          <dl class="layui-nav-child">
            <dd <?php if($controller == 'User'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('User/index'); ?>">管理员管理</a></dd>
            <dd <?php if($controller == 'Jues'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Jues/index'); ?>">角色管理</a></dd>
            <dd <?php if($controller == 'Menu'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Menu/lists'); ?>">菜单管理</a></dd>
          </dl>
        </li>
        </li>
        <?php
        if(in_array($controller,['Notice'])){
        ?>
        <li class="layui-nav-item layui-nav-itemed">
        <?php
          }else{
        ?>
        <li class="layui-nav-item">
          <?php
            }
          ?>
          <a href="javascript:;">公告管理</a>
          <dl class="layui-nav-child">
            <dd <?php if($controller == 'Notice'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Notice/add'); ?>">公告添加</a></dd>
            <dd <?php if($controller == 'Notice'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Notice/lists'); ?>">公告管理</a></dd>
          </dl>
        </li>
      </li>
        <?php
        if(in_array($controller,['News'])){
        ?>
        <li class="layui-nav-item layui-nav-itemed">
        <?php
          }else{
        ?>
        <li class="layui-nav-item">
          <?php
            }
          ?>
          <a href="javascript:;">新闻发布</a>
          <dl class="layui-nav-child">
            <dd <?php if($controller == 'News'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('News/add'); ?>">新闻添加</a></dd>
          </dl>
        </li>
      </li>
      <?php
        if(in_array($controller,['Books'])){
        ?>
        <li class="layui-nav-item layui-nav-itemed">
        <?php
          }else{
        ?>
        <li class="layui-nav-item">
          <?php
            }
          ?>
          <a href="javascript:;">图书管理</a>
          <dl class="layui-nav-child">
            <dd <?php if($controller == 'Books'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Books/lists'); ?>">展示</a></dd>
          </dl>
        </li>
        </li>
        <?php
        if(in_array($controller,['Yhq'])){
        ?>
        <li class="layui-nav-item layui-nav-itemed">
        <?php
          }else{
        ?>
        <li class="layui-nav-item">
          <?php
            }
          ?>
          <a href="javascript:;">优惠券管理</a>
          <dl class="layui-nav-child">
            <dd <?php if($controller == 'Yhq'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Yhq/add'); ?>">添加</a></dd>
          </dl>
        </li>
        </li>
        <?php
        if(in_array($controller,['Addres'])){
        ?>
        <li class="layui-nav-item layui-nav-itemed">
        <?php
          }else{
        ?>
        <li class="layui-nav-item">
          <?php
            }
          ?>
          <a href="javascript:;">收货地址管理</a>
          <dl class="layui-nav-child">
            <dd <?php if($controller == 'Addres'): ?> class="layui-this"<?php endif; ?>><a href="<?php echo url('Addres/lists'); ?>">展示</a></dd>
          </dl>
        </li>
        </li>
        <li class="layui-nav-item"><a href="">发布商品</a></li>
      </ul>
    </div>
  </div>

  <div class="layui-body">
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">    <span class="layui-breadcrumb">
      <a href="<?php echo url('News/lists'); ?>"><button style="float:right" class="layui-btn">返回</button></a>
    </span>
    <p style="color:red"><?php if(isset($error)): ?><?php echo htmlentities($error); ?><?php endif; ?></p>
  <form class="layui-form" action="" method="post">
	
	<div class="layui-form-item">
    	<label class="layui-form-label">新闻名称</label>
    	<div class="layui-input-inline">
      		<input type="text" name="n_title" autocomplete="off" placeholder="请输入新闻名称" class="layui-input" value="">
    	</div>
  	</div>

    <div class="layui-form-item layui-form-text">
      <label class="layui-form-label">内容</label>
      <div class="layui-input-block">
        <textarea name="n_content" placeholder="请输入内容" class="layui-textarea"></textarea>
      </div>
    </div>
  
	<div class="layui-form-item">
    	<label class="layui-form-label">图片</label>
    	<div class="layui-upload">
            <input type="hidden" name="n_img" value="" id="n_img">
  			<button type="button" class="layui-btn" id="test1">上传图片</button>
  			<div class="layui-upload-list">
    			<img class="layui-upload-img" id="demo1">
    			<p id="demoText"></p>
  			</div>
		</div>  
	</div>
  	
  	<div class="layui-form-item">
    	<label class="layui-form-label">发布时间</label>
    	<div class="layui-input-block">
      		<input type="radio" name="release_time" lay-filter="test" value="1" title="立即发布" checked="">
      		<input type="radio" name="release_time" lay-filter="test" value="2" title="定时发布">
    	</div>
      <div id="n_time">
        
      </div>
  	</div>


  	<div class="layui-form-item">
    	<div class="layui-input-block">
      		<input type="button" class="layui-btn" lay-submit lay-filter="*" value="发布">
      		<button type="reset" class="layui-btn layui-btn-primary">重置</button>
    	</div>
  	</div>
</form>

<script>
  $(function(){
    layui.use(['upload','form','layer'], function(){
      var $ = layui.jquery
      ,upload = layui.upload
      ,form=layui.form
      ,layer=layui.layer;
      
      //普通图片上传
      var uploadInst = upload.render({
        elem: '#test1'
        ,url: "<?php echo url('News/upload'); ?>"
        ,before: function(obj){
          //预读本地文件示例，不支持ie8
          obj.preview(function(index, file, result){
            $('#demo1').attr('src', result); //图片链接（base64）
          });
        }
        ,done: function(res){
          //如果上传失败
          if(res.code > 0){
            return layer.msg('上传失败');
          }
          //上传成功
          $('#n_img').val(res.msg);
          layer.msg('上传成功');
        }
        ,error: function(){
          //演示失败状态，并实现重传
          var demoText = $('#demoText');
          demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
          demoText.find('.demo-reload').on('click', function(){
            uploadInst.upload();
          });
        }
      });

      //时间
      form.on('radio(test)',function(data){
        //被点击的radio的value值
        var _val=data.value;
        var _this=$(this);
        if(_val==1){
          $("#n_time").empty();
        }else{
          $("#n_time").empty();
          $("#n_time").append("<input type='text' name='n_time' />");
        }
      });

      //提交
      form.on('submit(*)', function(data){
        $.post(
          "<?php echo url('News/doadd'); ?>",
          data.field,
          function(res){
            layer.msg(res.font,{icon:res.code});
          },
          'json'
        )
        //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        //return false;
      });

    });
  });
</script>
</div>
  </div>
  
  <div class="layui-footer">
    <!-- 底部固定区域 -->
    © 2018-2098 凉凉软件有限公司，并保留所有权利。
  </div>
</div>

<script>
//JavaScript代码区域
layui.use('element', function(){
  var element = layui.element;
  
});
</script>
</body>
</html>