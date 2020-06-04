<?php  
namespace app\index\controller;
// use  app\index\controller\Common;
use think\Controller;
use app\admin\model\Category;
class Index extends Common
{
	//前台展示列表
	public function index()
	{
		//查询左侧分类数据 左侧分类 导航栏分类
		$this->getLeftCateInfo();
		//获取楼层数据	调用获取楼层数据的方法，默认为第一楼数据
		$cate_id=1;
		$floorInfo=$this->getFloorInfo($cate_id);
		//exit;
		//楼层
		$floorInfo['floorNum']=1;
		$this->assign('floorInfo',$floorInfo);
		return view();
	}
	//获取楼层数据
	//cate_id获取楼层分类的id
	public function getFloorInfo($cate_id)
	{
		$cate_model=model('Category');
		$info=[];
		//1、顶级分类数据
		$where=[
			'is_show'=>1,
			'cate_id'=>$cate_id
		];
		//field 字段
		$info['topCate']=$cate_model->field('cate_id,cate_name')->where($where)->find();
		//echo $cate_model->getLastSql();exit;
		//2、顶级分类下二级分类 
		$where=[
			'is_show'=>1,
			'parent_id'=>$cate_id
		];
		$info['sonCate']=$cate_model->field('cate_id,cate_name')->where($where)->select();
		//print_r($sonCate);exit;
		//3、商品数据
		$cateInfo=$cate_model->whereOr(['is_show'=>1])->select();
		//print_r($cateInfo);
		//调用common.php的函数getCateId()	
		$c_id=getCateId($cateInfo,$cate_id);
		//print_r($c_id);
		//将得到的一一维数组分类id转化为字符串
		$c_id=implode(',',$c_id);
		//商品表查询分类下的商品信息
		$goods_model=model('Goods');
		$info['goodsInfo']=$goods_model->where('cate_id','in',$c_id)->select();
		return $info;
	}

	//获取更多楼层数据
	public function getMore()
	{
		//当前楼层分类 id 1
		$cate_id=input('post.cate_id');
		//楼层数字1
		$floorNum=input('post.floorNum');
		$cate_model=model('Category');
		//获取比当前分类id大的最小的一个分类id
		$where=[
			['parent_id','=',0],
			['cate_id','>',$cate_id]
		];
		$c_id=$cate_model->where($where)->order('cate_id','asc')->value('cate_id');
		//echo $cate_model->getLastSql();
		//echo $c_id;
		//exit;
		//获取下一楼的分类
		$floorInfo=$this->getFloorInfo($c_id);
		//print_r($floorInfo);exit;
		if(empty($c_id)){
			echo 'no';exit;
		}
		//把楼层数字+1
		$floorInfo['floorNum']=$floorNum+1;
		//把楼层数据显示到一个div的视图页面
		$this->view->engine->layout(false);
		//获取这个div视图页面 响应给js
		$this->assign('floorInfo',$floorInfo);
		echo $this->fetch('div');
	}
}
