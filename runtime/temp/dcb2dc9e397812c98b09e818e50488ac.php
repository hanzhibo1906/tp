<?php /*a:1:{s:60:"D:\phpStudy\WWW\tp1810\application\index\view\index\div.html";i:1553601834;}*/ ?>
<div cate_id="<?php echo htmlentities($floorInfo['topCate']['cate_id']); ?>">
    <div class="i_t mar_10">
        <span class="floor_num"><font class="floor"><?php echo htmlentities($floorInfo['floorNum']); ?></font>F</span>
        <span class="fl"><?php echo htmlentities($floorInfo['topCate']['cate_name']); ?></span>
        <span class="i_mores fr">
            <?php if(is_array($floorInfo['sonCate']) || $floorInfo['sonCate'] instanceof \think\Collection || $floorInfo['sonCate'] instanceof \think\Paginator): $i = 0; $__LIST__ = $floorInfo['sonCate'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <a href="#"><?php echo htmlentities($v['cate_name']); ?></a> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </span>
    </div>
    <div class="content">
        <div class="fresh_mid">
            <ul>
                <?php if(is_array($floorInfo['goodsInfo']) || $floorInfo['goodsInfo'] instanceof \think\Collection || $floorInfo['goodsInfo'] instanceof \think\Paginator): $i = 0; $__LIST__ = $floorInfo['goodsInfo'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <li>
                    <div class="name"><a href="#"><?php echo htmlentities($v['goods_name']); ?></a></div>
                    <div class="price">
                        <font>￥<span><?php echo htmlentities($v['shop_price']); ?></span></font> &nbsp; 26R
                    </div>
                    <div class="img">
                        <a href="<?php echo url('Goods/goodsDetail'); ?>?goods_id=<?php echo htmlentities($v['goods_id']); ?>">
                            <img src="./uploads/<?php echo htmlentities($v['goods_img']); ?>" width="185" height="155" />
                        </a>
                    </div>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>          
            </ul>
        </div>
    </div> 
</div>