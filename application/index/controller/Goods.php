<?php  
namespace app\index\controller;
use  app\index\controller\Common;
use think\Controller;
use think\Request;
use think\facade\Request as Frequest;
class Goods extends Common
{
	//商品展示列表
	public function goodsList()
	{
		//获取左侧分类 导航的数据
		$this->getLeftCateInfo();

		$cate_id=input('get.cate_id',0,'intval');
		// dump($cate_id);exit;
		$cate_model=model('Category');
		$goods_model=model('Goods');
		$brand_model=model('Brand');
		//品牌
		if(empty($cate_id)){
			$where=[];
			session('cate_id',null);
		}else{
			//根据顶级分页 查找子类
			$cateInfo=$cate_model->where('is_show',1)->select();
			$cate_id=getCateId($cateInfo,$cate_id);
			// print_r($cate_id);
			session('cate_id',$cate_id);
			$where=[
				['cate_id','in',$cate_id]
			];
			// print_r($where);
		}
		// exit;
		$brand_id=$goods_model->where($where)->column('brand_id');
		// print_r($brand_id);exit;
		if(!empty($brand_id)){
			//去重
			$brand_id=array_unique($brand_id);
			// print_r($brand_id);exit;
			$brandWhere=[
				['is_show','=',1],
				['brand_id','in',$brand_id]
			];
			$brandInfo=$brand_model->where($brandWhere)->select();
			// print_r($brandInfo);exit;
			
			//价格区间
			$max_price=$goods_model->where($where)->order('shop_price','desc')->value('shop_price');
			// echo $max_price;exit;
			$priceInfo=$this->getPrice($max_price);

			//商品数据+分页
			$page_num=4;
			$p=1;
			$where[]=['is_on_sale','=',1];
			$goodsInfo=$goods_model->where($where)->page($p,$page_num)->select();
			$count=$goods_model->where($where)->count();
			$page_class=new \page\AjaxPage();
			$str=$page_class->ajaxpager($p,$count,$page_num);

			//获取浏览历史记录
			if($this->checkLogin()){
				$historyInfo=$this->getHistoryDb();
			}else{
				$historyInfo=$this->getHistoryCookie();
			}
			// dump($historyInfo);exit;
			$this->assign('historyInfo',$historyInfo);

			$this->assign('brandInfo',$brandInfo);
			$this->assign('cate_id',$cate_id);
			$this->assign('priceInfo',$priceInfo);
			$this->assign('goodsInfo',$goodsInfo);
			$this->assign('str',$str);
			return view();
		}else{
			$this->error('此分类下无数据','Goods/goodsList');
		}
	}

	//从数据库获取浏览数据
	public function getHistoryDb()
	{
		$where=[
			'user_id'=>$this->getUserId()
		];
		$history_model=model('History');
		//浏览历史表中查询当前用户的浏览商品id
		$goods_id=$history_model->where($where)->order('look_time','desc')->column('goods_id');
		if(!empty($goods_id)){
			$goods_model=model('Goods');
			//将商品id从数组中取出一段并去重
			$goods_id=array_slice(array_unique($goods_id),0,4);
			// print_r($goods_id);die;
			//将商品id 由数组转化为字符串
			$goods_id=implode(',', $goods_id);
			$goodsWhere=[
				['goods_id','in',$goods_id]
			];

			//准备自定义排序
			$exp=new \think\db\Expression("field(goods_id,$goods_id)");
			$historyInfo=$goods_model->where($goodsWhere)->order($exp)->select();
			return $historyInfo;
		}else{
			return false;
		}
	}

	//从cookie中获取浏览记录数据
	public function getHistoryCookie()
	{	
		//判断是否有cookie历史记录
		$str=cookie('history');
		if(!empty($str)){
			//取cookie时转换成字符串
			$history=getBase64Info($str);
			// dump($historyInfo);die;
			
			$goods_id=[];
			foreach ($history as $k => $v) {
				$goods_id[]=$v['goods_id'];
			}
			// print_r($goods_id);die;
			//倒序排序、去重、取4个id
			$goods_id=array_slice(array_unique(array_reverse($goods_id)),0,4);
			// print_r($goods_id);exit;
			//将商品id 由数组转化为字符串
			$goods_id=implode(',',$goods_id);
			// echo $goods_id;exit;
			$goods_model=model('Goods');
			$goodsWhere=[
				['goods_id','in',$goods_id]
			];
			//准备自定义排序
			$exp=new \think\db\Expression("field(goods_id,$goods_id)");
			$historyInfo=$goods_model->where($goodsWhere)->order($exp)->select();
			return $historyInfo;
		}else{
			return false;
		}
	}

	//价格区间
	public function getPrice($max_price)
	{
		$arr=[];
		$price=$max_price/7;
		for ($i=0; $i <= 6; $i++) { 
			$start=$price*$i;
			$end=$price*($i+1)-0.01;
			$arr[]=number_format($start,2).'-'.number_format($end,2);
		}
		// print_r($arr);
		$arr[]=$end.'及以上';
		return $arr;
	}

	//重新获取价格
	public function getPriceInfo()
	{
		$brand_id=input('post.brand_id',0,'intval');
		if(session("?cate_id")){
			$cate_id=session('cate_id');
			$where[]=['cate_id','in',$cate_id];
		}else{
			$where=[
				['brand_id','=',$brand_id]
			];
		}
		$goods_model=model('Goods');
		$max_price=$goods_model->where($where)->order('shop_price','desc')->value('shop_price');
		// echo $max_price;exit;
		$priceInfo=$this->getPrice($max_price);
		// print_r($priceInfo);exit;
		echo json_encode($priceInfo);
	}

	//商品数据+分页
	public function getGoodsInfo()
	{
		$p=input('post.p',1);
		$brand_id=input('post.brand_id','');
		$price=input('post.price','');
		$order_field=input('post.order_field','');
		$order_type=input('post.order_type','');
		$field=input('post.field','');
		
		// echo $p;
		// echo $brand_id;
		// echo $price;
		// echo $order_field;
		// echo $order_type;
		// echo $field;die;

		$where=[];
		if(session("?cate_id")){
			$cate_id=session('cate_id');
			$where[]=['cate_id','in',$cate_id];
		}
		if(!empty($brand_id)){
			$where[]=['brand_id','=',$brand_id];
		}
		// echo $brand_id;
		if(!empty($price)){
			if(strpos($price,'-')){
			 	$price=explode('-',$price);
			 	// print_r($price);exit;
			 	$one=str_replace(',','',$price[0]);
			 	$two=str_replace(',','',$price[1]);
			 	// echo $one;
			 	// echo $two;exit;
			 	$where[]=['shop_price','>=',$one];
			 	$where[]=['shop_price','<=',$two];
			 	// print_r($where);exit;
			}else{
				$price=(int)$price;
				// echo $price;exit;
				$where[]=['shop_price','>=',$price];
				// print_r($where);exit;
			}
		}
		if(!empty($field)){
			$where[]=['is_new','=',1];
		}
		//获取数据
		$goods_model=model('Goods');
		if(!empty($order_field)){
			$goodsInfo=$goods_model->where($where)->order($order_field,$order_type)->page($p,4)->select();
		}else{
			$goodsInfo=$goods_model->where($where)->page($p,4)->select();
		}
		// echo $goods_model->getLastSql();exit;
		

		//商品数据+分页
		$count=$goods_model->where($where)->count();
		$page_class=new \page\AjaxPage();
		$str=$page_class->ajaxpager($p,$count,4);
		//echo $str;exit;
		
		$this->assign('goodsInfo',$goodsInfo);
		$this->assign('str',$str);
		$this->view->engine->layout(false);
		echo $this->fetch('show_info');
	}

	//商品详情
	public function goodsDetail()
	{
		//获取左侧分类 导航的数据
		$this->getLeftCateInfo();
		//接收商品id
		$goods_id=input('get.goods_id','','intval');
		if(empty($goods_id)){
			$this->error("请选择一个商品");exit;
		}
		//根据商品id查询一条商品数据
		$goods_model=model('Goods');
		$goodsInfo=$goods_model->where('goods_id','=',$goods_id)->find();
		// print_r($goodsInfo);
		if(empty($goods_id)){
			$this->error("请选择一个商品");exit;
		}

		//记录浏览历史
		if($this->checkLogin()){
			//已登录
			$this->saveHistoryDb($goods_id);
		}else{
			//未登录
			// echo 1;die;
			$this->saveHistoryCookie($goods_id);
		}

		$this->assign('goodsInfo',$goodsInfo);
		return view();
	}

	//已登录 存浏览历史记录到数据库中
	public function saveHistoryDb($goods_id)
	{
		$historyInfo=[
			'goods_id'=>$goods_id,
			'look_time'=>time(),
			//从session中获取用户Id
			'user_id'=>$this->getUserId()
		];
		$history_model=model('History');
		$res=$history_model->save($historyInfo);
	}

	//未登录 存浏览历史记录到cookie中
	public function saveHistoryCookie($goods_id)
	{
		//判断是否有cookie历史记录
		$str=cookie('history');
		if(!empty($str)){
			//取cookie时转换成字符串
			$historyInfo=getBase64Info($str);
			// dump($historyInfo);die;
			
			//判断存储cookie的内容是否超过4000字节，超过了就去除cookie数组的第一个元素，再继续向cookie数组中添加浏览数据
			if(strlen($str)>4000){
				array_shift($historyInfo);
			}

			$historyInfo[]=[
				'goods_id'=>$goods_id,
				'look_time'=>time()
			];	
		}else{
			$historyInfo=[
				[
					'goods_id'=>$goods_id,
					'look_time'=>time()
				]
			];
		}
		//存cookie时转换成数组
		$historyStr=createBase64Str($historyInfo);
		cookie('history',$historyStr);
	}

	//测试
	public function test()
	{
		// print_r(unserialize(base64_decode(cookie('history'))));
		// dump(cookie('history'));
		// 
		// 测试cookie存储条数
		$str=cookie('history');
		// dump($str);
		$info=getBase64Info($str);
		print_r($info);
	}
}