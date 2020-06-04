<?php  
namespace app\index\controller;
use  app\index\controller\Common;
use think\Controller;
use think\Request;
use think\facade\Request as Frequest;
class Cart extends Common
{
	//计入购物车
	public function addCart()
	{
		//接受商品id  购买数量
		//intval 获取变量整数值
		$goods_id=input('post.goods_id','','intval');
		$buy_number=input('post.buy_number','','intval');
		// dump($goods_id);
		// dump($buy_number);exit;
		$goods_model=model('Goods');
		$goodsInfo=$goods_model->find();
		// print_r($goodsInfo);die;
		$shop_price=$goodsInfo['shop_price'];
		// print_r($shop_price);die;
		//验证
		if(empty($goods_id)){
			fail('请选择一件商品');
		}
		if(empty($buy_number)){
			fail('请选择要购买的数量');
		}
		//判断是否登录
		if($this->checkLogin()){
			//已登录 把商品数据存入购物车表中
			$this->saveCartDb($goods_id,$buy_number,$shop_price);
		}else{
			//未登录 把商品数据存入cookie中
			$this->saveCartCookie($goods_id,$buy_number);
		}
		exit;
	}
	//已登录 把商品数据存入购物车表中
	public function saveCartDb($goods_id,$buy_number,$add_price)
	{
		// echo '1';die;
		//根据用户id，商品id判断用户是否买过此商品
		$user_id=$this->getUserId();
		$where=[
			'user_id'=>$user_id,
			'goods_id'=>$goods_id,
			'is_del'=>1
		];
		$cart_model=model('Cart');
		$cartInfo=$cart_model->where($where)->find();
		// dump($cartInfo);die;
		if(!empty($cartInfo)){
			//用户买过之后 判断库存 累加
			$res=$this->checkGoodsNumber($goods_id,$buy_number,$cartInfo['buy_number']);
			// dump($res);die;
			if($res){
				// echo"通过";die;
				//没超库存执行修改数据
				$updateInfo=[
					//已加入购车的数量+将要购买数量
					'buy_number'=>$cartInfo['buy_number']+$buy_number,
					'update_time'=>time(),
				];
				$result=$cart_model->save($updateInfo,$where);
			}else{
				// echo"超过";die;
				fail('购买数量超过库存量');
			}
		}else{
			//没买过 判断库存 添加
			//监测商品库存
			$res=$this->checkGoodsNumber($goods_id,$buy_number);
			if($res){
				$info=[
					'goods_id'=>$goods_id,
					'buy_number'=>$buy_number,
					'add_price'=>$add_price,
					'user_id'=>$user_id
				];
				// print_r($info);die;
				$result=$cart_model->save($info);
			}else{
				fail('购买数量超过库存量');
			}
		}
		if($result){
			successly('添加购物车成功');
		}else{
			fail('添加购物车失败');
		}
	}
	//未登录 把商品数据存入cookie中
	public function saveCartCookie($goods_id,$buy_number)
	{
		// echo '2';die;
		//取出cookie的值 判断cookie中是否有数据
		$str=cookie('cartInfo');
		// print_r($str);die;
		if(!empty($str)){
			// echo '有数据';die;
			//有数据 把cookie从字符串转化成数组
			$cartInfo=getBase64Info($str);
			$flag=0;
			//判断当前商品是否在cookie中
			foreach ($cartInfo as $k => $v) {
				if($goods_id==$v['goods_id']){
					// echo '存在';die;
					//检测库存
					$res=$this->checkGoodsNumber($goods_id,$buy_number,$v['buy_number']);
					if(!$res){
						fail('购买数量超过库存');
					}
					//累加
					$cartInfo[$k]['buy_number']=$buy_number+$v['buy_number'];
					// print_r($cartInfo);die;
					$cartStr=createBase64Str($cartInfo);
					cookie('cartInfo',$cartStr);
					successly('添加购物车成功');
					exit;
				}else{
					// echo '不存在';die;
					$flag=1;
				}
			}
			if($flag==1){
				// echo '不存在';die;
				//检测库存
				$res=$this->checkGoodsNumber($goods_id,$buy_number);
				if(!$res){
					fail('购买数量超过库存');
				}
				//不在cookie中 添加
				$cartInfo[]=[
					'goods_id'=>$goods_id,
					'buy_number'=>$buy_number,
					'create_time'=>time()
				];
				// print_r($cartInfo);die;
				//加密
				$cartStr=createBase64Str($cartInfo);
				cookie('cartInfo',$cartStr);
			}
		}else{
			// echo '无数据';die;
			//检测库存
			$res=$this->checkGoodsNumber($goods_id,$buy_number);
			// dump($res);die;
			if(!$res){
				fail('购买数量超过库存');
			}
			//未存过 添加
			$cartInfo[]=[
				'goods_id'=>$goods_id,
				'buy_number'=>$buy_number,
				'create_time'=>time()
			];
			// print_r($cartInfo);die;
			//加密
			$cartStr=createBase64Str($cartInfo);
			cookie('cartInfo',$cartStr);
		}
		successly('添加购物车成功');
	}

	//测试
	public function test()
	{
		$str=cookie('cartInfo');
		$cartInfo=getBase64Info($str);
		print_r($cartInfo);
	}


	//购物车列表
	public function cartList()
	{
		//获取左侧分类 导航的数据
		$this->getLeftCateInfo();
		//判断是否登录
		if($this->checkLogin()){
			//已登录 从数据库获取购物车数据
			$cartInfo=$this->getCartInfoDb();
		}else{
			//未登录 从cookie获取购物车数据
			$cartInfo=$this->getCartCookie();
		}
		// print_r($cartInfo);exit;
		 
		// 获取小计
		if(!empty($cartInfo)){
			foreach ($cartInfo as $k => $v) {
				$total=$v['shop_price']*$v['buy_number'];
				$cartInfo[$k]['total']=$total;
			}
		}
		 
		 
		// $this->assign('goodsInfo',$goodsInfo);
		$this->assign('cartInfo',$cartInfo);
		return view();
	}
	//从数据库获取购物车数据
	public function getCartInfoDb()
	{
		// echo '已登录';die;
		$cart_model=model('Cart');
		//根据用户id查询商品id
		$user_id=$this->getUserId();
		$where=[
			['user_id','=',$user_id],
			['is_on_sale','=',1],
			['is_del','=',1]
		];
		$cartInfo=$cart_model
			->alias('c')
			->join('goods g','c.goods_id=g.goods_id')
			->where($where)
			->order('cart_id')
			->select();
		// dump($cartInfo);exit;
		if(!empty($cartInfo)){
			return $cartInfo;
		}else{
			return false;
		}
	}
	//从cookie获取购物车数据
	public function getCartCookie()
	{
		// echo '未登录';die;
		$str=cookie('cartInfo');
		// dump($str);die;
		if(!empty($str)){
			//解密
			$cartInfo=getBase64Info($str);
			//数组倒序排序
			$cartInfo=array_reverse($cartInfo);
			// print_r($cartInfo);exit;
			$goods_id=[];
			$goods_model=model('Goods');
			//获取商品id for循环获取键值
			foreach ($cartInfo as $k => $v) {
				$goods_id[]=$v['goods_id'];
			}
			$goods_id=implode(',', $goods_id);
			// print_r($goods_id);exit;
			$where=[
				['goods_id','in',$goods_id],
				['is_on_sale','=',1]
			];
			//准备自定义排序 根据商品id获取商品数据
			$exp=new \think\db\Expression("field(goods_id,$goods_id)");
			$goodsInfo=$goods_model->where($where)->order($exp)->select();
			// print_r($cartInfo);
			// print_r($goodsInfo);exit;
			foreach ($goodsInfo as $k => $v) {
				foreach ($cartInfo as $key => $val) {
					if($v['goods_id']==$val['goods_id']){
						$goodsInfo[$k]['buy_number']=$val['buy_number'];
					}
				}
			}
			// print_r($goodsInfo);die;
			return $goodsInfo;
		}else{
			return false;
		}
	}


	//更改购买数量
	public function changeBuyNumber()
	{
		$goods_id=input('post.goods_id','','intval');
		$buy_number=input('post.buy_number','','intval');
		// echo $goods_id;
		// echo $buy_number;exit;
		if(empty($goods_id)){
			fail('请至少选择一个商品');
		}
		if(empty($buy_number)){
			fail('购买数量不能为空');
		}

		if($this->checkLogin()){
			//登录 更改数据库中购买数量
			$this->changeBuyNumberDb($goods_id,$buy_number);
		}else{
			//未登录 更改cookie中购买数量
			$this->changeBuyNumberCookie($goods_id,$buy_number);
		}
	}

	//登录 更改数据库中购买数量
	public function changeBuyNumberDb($goods_id,$buy_number)
	{
		// echo '登录';exit;
		//检测库存
		$res=$this->checkGoodsNumber($goods_id,$buy_number);
		if($res){
			// echo '未超过';die;
			$where=[
				'goods_id'=>$goods_id,
				'user_id'=>$this->getUserId()
			];
			$updateInfo=[
				'buy_number'=>$buy_number,
				'update_time'=>time()
			];
			$cart_model=model('Cart');
			$result=$cart_model->save($updateInfo,$where);
			// dump($result);die;
			if($result){
				successly('修改数量成功');
			}else{
				fail('修改数量失败');
			}
		}else{
			// echo '超过';die;
			fail('超过库存');
		}
		
	}
	//未登录 更改cookie中购买数量
	public function changeBuyNumberCookie($goods_id,$buy_number)
	{
		// echo '未登录';exit;
		$str=cookie('cartInfo');
		// print_r($str);exit;
		if(!empty($str)){
			//解密
			$cartInfo=getBase64Info($str);
			// print_r($cartInfo);exit;
			//判断当前商品是否在cookie中
			foreach ($cartInfo as $k => $v) {
				if($goods_id==$v['goods_id']){
					//检测库存
					$res=$this->checkGoodsNumber($goods_id,$buy_number);
					if(!$res){
						fail('购买数量超过库存');
					}
					//更改
					$cartInfo[$k]['buy_number']=$buy_number;
					// print_r($cartInfo);die;
					$cart_str=createBase64Str($cartInfo);
					cookie('cartInfo',$cart_str);
					successly('成功');
				}
			}
		}
	}


	//获取小计
	public function getSubTotal()
	{
		$goods_id=input('post.goods_id','','intval');
		// echo $goods_id;exit;
		if(empty($goods_id)){
			echo 0;exit;
		}

		if($this->checkLogin()){
			//登录 更改数据库中小计
			$this->getSubTotalDb($goods_id);
		}else{
			//未登录 更改cookie中小计
			$this->getSubTotalCookie($goods_id);
		}
	}
	//登录 更改数据库中小计
	public function getSubTotalDb($goods_id)
	{
		// echo '登录';die;
		//获取商品价格
		$goods_model=model('Goods');
		$goodsWhere=[
			['is_on_sale','=',1],
			['goods_id','=',$goods_id]
		];
		$shop_price=$goods_model->where($goodsWhere)->value('shop_price');
		// print_r($shop_price);
		//获取商品购买数量
		$cart_model=model('Cart');
		$user_id=$this->getUserId();
		$cartWhere=[
			['goods_id','=',$goods_id],
			['user_id','=',$user_id]
		];
		$buy_number=$cart_model->where($cartWhere)->value('buy_number');
		// print_r($buy_number);die;
		echo $shop_price*$buy_number;
	}
	//未登录 更改cookie中小计
	public function getSubTotalCookie($goods_id)
	{
		// echo '未登录';die;
		$str=cookie('cartInfo');
		// print_r($str);exit;
		if(!empty($str)){
			//解密
			$cartInfo=getBase64Info($str);
			// print_r($cartInfo);exit;
			//判断当前商品是否在cookie中
			foreach ($cartInfo as $k => $v) {
				if($goods_id==$v['goods_id']){
					//获取购买数量
					$buy_number=$v['buy_number'];
				}
			}
			// echo $buy_number;
			//获取商品价格
			$where=[
				['goods_id','=',$goods_id],
				['is_on_sale','=',1]
			];
			$shop_price=model('Goods')->where($where)->value('shop_price');
			echo $shop_price*$buy_number;
		}
	}


	//获取商品总价
	public function countTotal()
	{
		$goods_id=input('post.goods_id','');
		if(empty($goods_id)){
			echo 0;exit;
		}

		if($this->checkLogin()){
			//已登录 从数据库中获取商品总价
			$this->countTotalDb($goods_id);
		}else{
			//未登录 从cookie中获取商品总价
			$this->countTotalCookie($goods_id);
		}
	}
	//已登录 从数据库中获取商品总价
	public function countTotalDb($goods_id)
	{
		// echo '已登录';die;
		$user_id=$this->getUserId();
		$where=[
			['c.goods_id','in',$goods_id],
			['is_on_sale','in',1],
			['user_id','=',$user_id]
		];
		$info=model('Cart')
			->field('buy_number,shop_price')
			->alias('c')->join('goods g','c.goods_id=g.goods_id')
			->where($where)
			->select();
		// print_r($info);exit;
		$count=0;
		foreach ($info as $k => $v) {
			$count+=$v['shop_price']*$v['buy_number'];
		}
		echo $count;
	}
	//未登录 从cookie中获取商品总价
	public function countTotalCookie($goods_id)
	{
		// echo '未登录';die;
		// print_r($goods_id);die;
		$str=cookie('cartInfo');
		// print_r($str);exit;
		if(!empty($str)){
			//解密
			$cartInfo=getBase64Info($str);
			// print_r($cartInfo);exit;
			$where=[
				['goods_id','in',$goods_id],
				['is_on_sale','=',1],
			];
			$goodsInfo=model('Goods')->where($where)->select();
			// print_r($goodsInfo);exit;
			$count=0;
			foreach ($cartInfo as $k => $v) {
				foreach ($goodsInfo as $key => $val) {
					if($v['goods_id']==$val['goods_id']){
						$count+=$v['buy_number']*$val['shop_price'];
					}
				}
			}
			echo $count;
		}
	}


	//点击删除，批删
	public function cartDel()
	{
		$goods_id=input('post.goods_id','');
		// print_r($goods_id);die;
		//验证
		if(empty($goods_id)){
			fail('请选择一件商品');
		}
		//判断是否登录
		if($this->checkLogin()){
			//已登录 从数据库中删除购物车数据
			$this->cartDelDb($goods_id);
		}else{
			//未登录 从cookie中删除购物车数据
			$this->cartDelCookie($goods_id);
		}
	}
	//已登录 从数据库中删除购物车数据
	public function cartDelDb($goods_id)
	{
		// echo '已登录';die;
		$cart_model=model('Cart');
		$user_id=$this->getUserId();
		//验证
		if(empty($goods_id)){
			fail('请选择一件商品');
		}
		$where=[
			['user_id','=',$user_id],
			//单删、批删用in
			['goods_id','in',$goods_id]
		];
		$updateWhere=[
			'is_del'=>2
		];
		$res=$cart_model->where($where)->update($updateWhere);
		if($res){
			successly('删除购物车成功');
		}else{
			fail('删除购物车失败');
		}
	}
	//未登录 从cookie中删除购物车数据
	public function cartDelCookie($goods_id)
	{
		// echo '未登录';die;
		$str=cookie('cartInfo');
		// print_r($str);exit;
		if(!empty($str)){
			//解密
			$cartInfo=getBase64Info($str);
			// print_r($goods_id);//exit;
			if(strpos($goods_id,',')){
				// echo '批删';die;
				$del_id=explode(',', $goods_id);
			}else{
				// echo '单删';die;
				$del_id=[$goods_id];
			}
			// print_r($del_id);
			foreach ($cartInfo as $k => $v) {
				if(in_array($v['goods_id'], $del_id)){
					unset($cartInfo[$k]);
				}
			}
			if(empty($cartInfo)){
				cookie('cartInfo',null);
			}else{
				$cart_str=createBase64Str($cartInfo);
				cookie('cartInfo',$cart_str);	
			}
			successly('删除成功');
		}
	}
}