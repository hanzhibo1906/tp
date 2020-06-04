<?php  
namespace app\index\controller;
use  app\index\controller\Common;
use think\Controller;
use think\Request;
use think\Exception;
use think\facade\Request as Frequest;
use think\facade\Config;
class Order extends Common
{
	//确认订单列表
	public function confirmOrder()
	{
		//获取左侧分类 导航的数据
		$this->getLeftCateInfo();
		$goods_id=input('get.goods_id','');
		//echo $goods_id;exit;
		//检测是否有商品
		if(empty($goods_id)){
			$this->error('请至少选择一件商品进行结算',url('Cart/cartList'));exit;
		}

		// 获取购买的购物车数据
		if($this->checkLogin()){
			$cart_model=model('Cart');
			$user_id=$this->getUserId();
			$where=[
				['c.goods_id','in',$goods_id],
				['user_id','=',$user_id],
				['is_del','=',1],
				['is_on_sale','=',1]
			];
			$cartInfo=$cart_model
				->field('g.goods_id,goods_name,goods_img,shop_price,buy_number')
				->alias('c')
				->join('goods g','c.goods_id=g.goods_id')
				->where($where)
				->select();
			// print_r($cartInfo);exit;
			$count=0;
			foreach ($cartInfo as $k => $v) {
				$count+=$v['shop_price']*$v['buy_number'];
			}
			foreach ($cartInfo as $k => $v) {
				$cartInfo[$k]['total']=$v['shop_price']*$v['buy_number'];
			}
			// echo $count;exit;
		}else{
			$this->error('请登录',url('Login/login'));exit;
		}

		//获取收货地址信息
		$addressInfo=$this->getAddressInfo();
		// dump($addressInfo);exit;

		$this->assign('cartInfo',$cartInfo);
		$this->assign('count',$count);
		$this->assign('addressInfo',$addressInfo);
		return view();
	}

	//点击提交 确认订单
	public function submitOrder()
	{
		//获取数据
		$goods_id=input('post.goods_id');
		$address_id=input('post.address_id');
		$pay_type=input('post.pay_type');
		$order_talk=input('post.order_talk');
		// print_r($goods_id);
		// print_r($address_id);
		// print_r($pay_type);
		// print_r($order_talk);die;
		$order_model=model('order');
		$user_id=$this->getUserId();
		//开启事务
		$order_model->startTrans();
		//异常处理
		try{
			if(empty($goods_id)){
				throw new Exception('请选择一件商品');
			}
			if(empty($address_id)){
				throw new Exception('请选择一个收货地址');
			}
			if(empty($pay_type)){
				throw new Exception('请选择一个支付方式');
			}


			//添加订单表数据
			$order_no=$this->createOrderNo();//订单号
			// echo $order_no;die;
			$order_amount=$this->getOrderAmount($goods_id);//订单总金额
			$orderInfo['order_no']=$order_no;
			$orderInfo['order_amount']=$order_amount;
			$orderInfo['pay_type']=$pay_type;
			$orderInfo['goods_id']=$goods_id;
			$orderInfo['user_id']=$user_id;
			$res1=$order_model->save($orderInfo);
			// print_r($res1);die;
			if(empty($res1)){
				//抛出一个异常
				throw new Exception('订单信息添加失败');
			}


			//订单收货地址添加
			$addressWhere=[
				['address_id','=',$address_id],
				['is_del','=',1]
			];
			$address_model=model('Address');
			$addressInfo=$address_model->where($addressWhere)->find()->toArray();
			// print_r($addressInfo);die;
			if(empty($addressInfo)){
				throw new Exception('没有此收货地址，请重新选择');
			}
			//获取order_id
			$order_id=$order_model->order_id;
			// echo $order_id;die;
			unset($addressInfo['create_time']);
			unset($addressInfo['update_time']);
			$addressInfo['order_id']=$order_id;
			$order_address=model('OrderAddress');
			$res2=$order_address->allowField(true)->save($addressInfo);
			// print_r($res2);die;
			if(empty($res1)){
				//抛出一个异常
				throw new Exception('订单收货地址添加失败');
			}


			//订单表详情添加
			$goodsInfo=$this->getOrderDetail($goods_id);//商品详情信息
			// print_r($goodsInfo);exit;
			foreach ($goodsInfo as $k => $v) {
				$goodsInfo[$k]['order_id']=$order_id;
				$goodsInfo[$k]['user_id']=$user_id;
			}
			// print_r($goodsInfo);exit;
			if(empty($goodsInfo)){
				//抛出一个异常
				throw new Exception('没有此商品');
			}
			$order_detail=model('OrderDetail');
			$res3=$order_detail->allowField(true)->saveAll($goodsInfo);
			// print_r($res3);die;
			if(empty($res3)){
				//抛出一个异常
				throw new Exception('订单表详情添加失败');
			}


			//删除购物车数据
			$cart_model=model('Cart');
			$cartWhere=[
				['user_id','=',$user_id],
				['goods_id','in',$goods_id],
				['is_del','=',1]
			];
			$res4=$cart_model->where($cartWhere)->update(['is_del'=>2]);
			// print_r($res4);die;
			if(empty($res4)){
				//抛出一个异常
				throw new Exception('删除购物车数据失败');
			}


			//减少库存
			$goods_model=model('Goods');
			// print_r($goodsInfo);//exit;
			//不能多条修改，修改多条要循环修改，有几件商品修改几条
			foreach ($goodsInfo as $k => $v) {
				$res5=$goods_model->where('goods_id',$v['goods_id'])->update(['goods_number'=>['dec',$v['buy_number']]]);
				if(empty($res5)){
					//抛出一个异常
					throw new Exception('减少库存失败');
				}
			}


			//提交
			$order_model->commit();
			//把订单表id返回给视图
			$arr=[
				'code'=>1,
				'font'=>'下单成功',
				'order_id'=>$order_id
			];
			echo json_encode($arr);
		}catch(Exception $e){
			//回滚
			$order_model->rollback();
			fail($e->getMessage());
		}
	}

	//生成订单号
	public function createOrderNo()
	{	
		return time().rand(100,999).$this->getUserId();
	}
	//订单总金额
	public function getOrderAmount($goods_id)
	{
		$cart_model=model('Cart');
		$user_id=$this->getUserId();
		// print_r($user_id);die;
		$where=[
			['user_id','=',$user_id],
			['c.goods_id','in',$goods_id],
			['is_del','=',1],
			['is_on_sale','=',1]
		];
		$cartInfo=$cart_model
			->field('shop_price,buy_number')
			->alias('c')
			->join('goods g','c.goods_id=g.goods_id')
			->where($where)
			->select()
			->toArray();
		// print_r($cartInfo);die;
		$count=0;
		foreach ($cartInfo as $k => $v) {
			$count+=$v['shop_price']*$v['buy_number'];
		}
		// echo $count;die;
		return $count;
	}
	//获取商品详情信息
	public function getOrderDetail($goods_id)
	{
		$cart_model=model('Cart');
		$user_id=$this->getUserId();
		// print_r($user_id);die;
		$where=[
			['user_id','=',$user_id],
			['c.goods_id','in',$goods_id],
			['is_del','=',1],
			['is_on_sale','=',1]
		];
		$goodsInfo=$cart_model
			->field('g.goods_id,goods_name,goods_img,shop_price,buy_number,goods_number')
			->alias('c')
			->join('goods g','c.goods_id=g.goods_id')
			->where($where)
			->select()
			->toArray();
		// print_r($goodsInfo);die;
		return $goodsInfo;
	}

	//下单成功
	public function successOrder()
	{
		$order_id=input('get.order_id','','intval');
		// echo $order_id;die;
		//异常处理
		try{
			//验证订单号
			if(empty($order_id)){
				throw new \Exception('请选择正确订单号');
			}
			$order_model=model('Order');
			$user_id=$this->getUserId();
			// print_r($user_id);die;
			$where=[
				['user_id','=',$user_id],
				['order_id','=',$order_id],
				['is_del','=',1]
			];
			$orderInfo=$order_model->where($where)->find();
			// print_r($orderInfo);die;
			if(empty($orderInfo)){
				throw new \Exception('没有此订单信息');
			}
			//获取左侧分类 导航的数据
			$this->getLeftCateInfo();
			$this->assign('orderInfo',$orderInfo);
			return view();
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}


	//测试支付宝
	public function alipay()
	{
		$order_id=input('get.order_id');
		// print_r($order_id);die;
		$where=[
			['order_id','=',$order_id],
			['is_del','=',1]
		];
		$order_model=model('Order');
		$orderInfo=$order_model->where($where)->find();
		// print_r($orderInfo);die;
		$order_no=$orderInfo['order_no'];
		$order_amount=$orderInfo['order_amount'];
		$order_talk=$orderInfo['order_talk'];
		// echo $order_no;die;
		//引入文件
		$config=config('alipay.');
		// print_r($config);die;
		
		require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';
		require_once '../extend/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

	    //商户订单号，商户网站订单系统中唯一订单号，必填
	    $out_trade_no = $order_no;

	    //订单名称，必填
	    $subject = "测试订单";

	    //付款金额，必填
	    $total_amount = $order_amount;

	    //商品描述，可空
	    $body = $order_talk;

		//构造参数
		$payRequestBuilder = new \AlipayTradePagePayContentBuilder();
		$payRequestBuilder->setBody($body);
		$payRequestBuilder->setSubject($subject);
		$payRequestBuilder->setTotalAmount($total_amount);
		$payRequestBuilder->setOutTradeNo($out_trade_no);

		// die;

		$aop = new \AlipayTradeService($config);

		/**
		 * pagePay 电脑网站支付请求
		 * @param $builder 业务参数，使用buildmodel中的对象生成。
		 * @param $return_url 同步跳转地址，公网可以访问
		 * @param $notify_url 异步通知地址，公网可以访问
		 * @return $response 支付宝返回的信息
	 	*/
		$response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

		//输出表单
		var_dump($response);	
	}

	//同步通知
	public function returnUrl()
	{
		echo "同步通知";die;
	}
}