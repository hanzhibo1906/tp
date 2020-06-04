<?php /*a:5:{s:63:"D:\phpStudy\WWW\tp1810\application\admin\view\addres\lists.html";i:1554701294;s:57:"D:\phpStudy\WWW\tp1810\application\admin\view\layout.html";i:1550652520;s:64:"D:\phpStudy\WWW\tp1810\application\admin\view\public\header.html";i:1556502170;s:62:"D:\phpStudy\WWW\tp1810\application\admin\view\public\left.html";i:1554699641;s:64:"D:\phpStudy\WWW\tp1810\application\admin\view\public\footer.html";i:1551083535;}*/ ?>
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
    <div style="padding: 15px;"><style type="text/css">
  .layui-table-cell {height:auto;line-height:25px;}
</style>
<table>
	<tr>
		<th colspan="7"><a href="<?php echo url('Addres/add'); ?>"><button class="layui-btn">添加</button></a></th>
    </tr>
</table>

<table border="1" width="600">
  <tr align="center">
    <td>id</td>
    <td>收货地址</td>
    <td>联系人</td>
    <td>性别</td>
    <td>手机号</td>
  </tr>
  <?php if(is_array($AddresInfo) || $AddresInfo instanceof \think\Collection || $AddresInfo instanceof \think\Paginator): $i = 0; $__LIST__ = $AddresInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>

  <tr align="center">
    <td><?php echo htmlentities($v['a_id']); ?></td>
    <td><?php echo htmlentities($v['a_addres']); ?></td>
    <td><?php echo htmlentities($v['a_name']); ?></td>
    <td><?php echo $v['a_sex']==1 ? '先生' : '女士'; ?></td>
    <td><?php echo htmlentities($v['a_tel']); ?></td>
  </tr>
  <?php endforeach; endif; else: echo "" ;endif; ?>
</table>

<script>
layui.use(['form'], function(){
  var form = layui.form;
  
});
</script></div>
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