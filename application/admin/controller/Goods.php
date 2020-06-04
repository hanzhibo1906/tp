<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Category;
use app\admin\model\Brand;
use app\admin\model\Goods as goodsModel;
use app\admin\model\Goods_photo;
use think\facade\Log;
use app\admin\controller\Common;
class Goods extends Common
{
	public function lists()
	{
		$page=input('page')??1;
		//获取page项的值，每页显示条数
		$limit=input('limit')??config('pageSize');
		$count=goodsModel::count();
		$data=goodsModel::order('goods_id','desc')->page($page,$limit)->select();
		if(request()->isAjax()){
			$result=[
				'code'=>0,
				'msg'=>'success',
				'count'=>$count,
				'data'=>$data
			];
			echo json_encode($result);die;
		}
		return view();
	}
	public function add()
	{
		$category=Category::select();
		$category=createTree($category);
		//dump($category);die;
		$brand=Brand::where('is_show',1)->select();
		$this->assign('category',$category);
		$this->assign('brand',$brand);
		return view();
	}
	public function doadd(Request $request)
	{
		$post=$request->post();
		// dump($post);die;
		//开启事务
		goodsModel::startTrans();
		try{
			$post['content']=$post['editorValue'];
			$goods=new goodsModel();
			//allowField(true)
			$goods_id=$goods->strict(false)->insertGetId($post);
			//商品货号,如果不存在则系统生成  unset释放
			//$post['goods_sn']='';

			$goods_sn=isset($post['goods_sn']) && $post['goods_sn']!=''?:$this->createGoodsSn($goods_id);
			$goods->save(['goods_sn'=>$goods_sn],['goods_id'=>$goods_id]);
			//echo $goods_sn;
			//商品相册
			$this->addGoodsPhoto($goods_id,$post['goods_photo']);
			goodsModel::commit();
			$this->redirect('Goods/lists');
		}catch(\think\Exception $ex){
			goodsModel::rollback();
			Log::write('错误文件是:'.$ex->getFile().'错误信息是:'.$ex->getMessage(),'info');
		}
	}
	//随机生成货号
	private function createGoodsSn($goods_id)
	{
		$goods_sn='TH0000'.$goods_id;
		$count=goodsModel::where('goods_sn',$goods_sn)->count();
		//防止货号重复
		if($count){
			$newgoods_id=$goods_id.'_'.rand(10,99);
			$this->createCode($newgoods_id);
		}
		return $goods_sn;
	}
	//商品相册
	private function addGoodsPhoto($goods_id,$photos)
	{
		if($photos){
			$temp_array=explode('|', trim($photos,'|'));
			$data=[];
			foreach ($temp_array as $key => $val) {
				$data[]=[
					'goods_id'=>$goods_id,
					'url'=>$val
				];
			}
			$goods_photo=new Goods_photo();
			$goods_photo->allowField(true)->saveAll($data);
		}
		// $this->redirect('Goods/lists');
	}
	//验证唯一性
	public function checkGoodsSn()
	{
		if(request()->isAjax()){
			$goods_sn=input('goods_sn');
			$count=goodsModel::where('goods_sn',$goods_sn)->count();
			echo $count;
		}
	}
	//单图片上传
	public function upload()
	{
		$result=upload('file');
	    $result=json_decode($result,true);
	    if($result['code']==0){
	    	$thumb=$this->imgthumb($result['msg']);
	    	echo json_encode(['code'=>0,'msg'=>$result['msg'],'thumb'=>$thumb]);
	    }else{
	    	echo $result;
	    }
	}
	//多文件上传
	public function mulupload()
	{
		$result=upload('file');
		echo $result;
	}
	//生成缩略图
	public function imgthumb($img)
	{
		//$img=dirname(dirname(dirname(dirname(__FILE__)))).'/public/uploads/'.$img;
		$image = \think\Image::open('./uploads/'.$img);
		// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
		//截取字符串
		$length=stripos($img,'.');
		$thumb=substr($img,0,$length).'_thumb'.substr($img,$length);
		$image->thumb(150, 150)->save('./uploads/'.$thumb);
	}
	//ajax状态显示操作
	public function ajaxupd()
	{
		if(request()->isAjax()){
			$goods_id=input('get.goods_id');
			$field=input('get.key');
			$value=input('get.value');
			$data[$field]=$value==1?0:1;
			$goodsModel=new goodsModel;
			$res=$goodsModel->save($data,['goods_id'=>$goods_id]);
			if($res){
        		echo 1;
	        }else{
        		echo 0;
	        }
		}
	}
	//即点即改
	public function ajaxupdd(Request $request){
		if ($request->isAjax()) {
			$name=input('name');
			$id=input('id');
			$goodsModel=new goodsModel();
			$res=$goodsModel->save(['goods_name'=>$name],['goods_id'=>$id]);
			if ($res) {
				echo 1;
			}else{
				echo 0;
			}
		}
	}
	//批删
	public function ajaxdel(){
		if (request()->isAjax()) {
			$id=input('allid');
			$all=[];
			foreach ($id as $k => $v) {
				$all[]=$v['goods_id'];
			}
			// print_r($all);die;
			$res=goodsModel::destroy($all);
			if ($res) {
				echo json_encode(['code'=>1,'msg'=>'删除成功']);
			}else{
				echo json_encode(['code'=>0,'msg'=>'删除失败']);
			}
		}
	}

}