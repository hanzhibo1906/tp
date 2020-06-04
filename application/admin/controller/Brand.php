<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Brand as brandModel;
use app\admin\controller\Common;
class Brand extends Common
{
	public function lists()
	{
		$page=input('page')??1;
		//获取page项的值，每页显示条数
		$limit=input('limit')??config('pageSize');
		$count=brandModel::count();
		$data=brandModel::order('brand_id','desc')->page($page,$limit)->select();
		if(request()->isAjax()){
			$result=[
				'code'=>0,
				'msg'=>'',
				'count'=>$count,
				'data'=>$data
			];
			echo json_encode($result);die;
		}
		return view();
	}
	public function add()
	{
		return view();
	}
	public function doadd(Request $request)
	{
		$post=$request->post();
		$brandModel=new brandModel;
		$res=$brandModel->allowField(true)->create($post);
		if($res){
        	$this->redirect('Brand/lists');
        }else{
            $this->error('添加失败！','Brand/add');
        }
	}
	//图片上传
	public function upload()
	{
		$result=upload('file');
	    echo $result;
	}
	public function delete()
	{
		if(request()->isAjax()){
			$brand_id=input('brand_id');
			$res=brandModel::destroy($brand_id);
			if($res){
        		echo json_encode(['code'=>0,'msg'=>'删除成功']);
	        }else{
	            echo json_encode(['code'=>1,'msg'=>'删除失败']);
	        }
		}
	}
	//修改
	public function edit($id)
	{
		$data=brandModel::get($id);
		$this->assign('data',$data);
		return view();
	}
	public function doedit(Request $request,$id)
	{
		$post=$request->post();
		$brandModel=new brandModel;
		$res=$brandModel->allowField(true)->save($post,['brand_id'=>$id]);
		if($res){
        	$this->redirect('Brand/lists');
        }else{
            $this->error('修改失败！','Brand/edit');
        }
	}
	//ajax状态操作
	public function ajaxupd()
	{
		if(request()->isAjax()){
			$brand_id=input('get.brand_id');
			$field=input('get.key');
			$value=input('get.value');
			$data[$field]=$value==1?0:1;
			$brandModel=new brandModel;
			$res=$brandModel->save($data,['brand_id'=>$brand_id]);
			if($res){
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
				$all[]=$v['brand_id'];
			}
			// print_r($all);die;
			$res=brandModel::destroy($all);
			if ($res) {
				echo json_encode(['code'=>1,'msg'=>'删除成功']);
			}else{
				echo json_encode(['code'=>0,'msg'=>'删除失败']);
			}
		}
	}
	//即点即改
	public function ajaxupdd(Request $request){
		if ($request->isAjax()) {
			$name=input('name');
			$id=input('id');
			$brandModel=new brandModel();
			$res=$brandModel->save(['brand_url'=>$name],['brand_id'=>$id]);
			if ($res) {
				echo 1;
			}else{
				echo 0;
			}
		}
	}
}