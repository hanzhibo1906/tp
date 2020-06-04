<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Category as CategoryModel;
use app\admin\controller\Common;
class Category extends Common
{
	public function lists()
	{
		$count=CategoryModel::count();
		$data=CategoryModel::select();
		//无限极分类
		$data=createTree($data);
		if($data){
			foreach ($data as $key => $v) {
				$data[$key]['level']=str_repeat("&nbsp;&nbsp;&nbsp;",$v['level']-1);
			}
		}
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
		$data=CategoryModel::select();
		//无限极分类
		$data=createTree($data);
		$this->assign('data',$data);
		return view();
	}
	public function doadd()
	{
		$post=input('post.');
		$res=CategoryModel::create($post);
		if($res){
        	$this->redirect('Category/lists');
        }else{
            $this->error('添加失败！','Category/add');
        }
	}
	public function delete()
	{
		if(request()->isAjax()){
			$cate_id=input('cate_id');
			$res=CategoryModel::destroy($cate_id);
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
		$data=CategoryModel::get($id);
		$all=CategoryModel::select();
		$all=createTree($all);
		$this->assign('data',$data);
		$this->assign('all',$all);
		return view();
	}
	public function doedit(Request $request,$id)
	{
		$post=$request->post();
		$CategoryModel=new CategoryModel;
		$res=$CategoryModel->save($post,['cate_id'=>$id]);
		if($res){
        	$this->redirect('Category/lists');
        }else{
            $this->error('修改失败！','Category/edit');
        }
	}
	//ajax状态操作
	public function ajaxupd()
	{
		if(request()->isAjax()){
			$cate_id=input('get.cate_id');
			$field=input('get.key');
			$value=input('get.value');
			$data[$field]=$value==1?0:1;
			$CategoryModel=new CategoryModel;
			$res=$CategoryModel->save($data,['cate_id'=>$cate_id]);
			if($res){
        		echo 1;
	        }else{
        		echo 0;
	        }
		}
	}
	//ajax导航显示操作
	public function ajaxupddh()
	{
		if(request()->isAjax()){
			$cate_id=input('get.cate_id');
			$field=input('get.key');
			$value=input('get.value');
			$data[$field]=$value==1?0:1;
			$CategoryModel=new CategoryModel;
			$res=$CategoryModel->save($data,['cate_id'=>$cate_id]);
			if($res){
        		echo 1;
	        }else{
        		echo 0;
	        }
		}
	}
}