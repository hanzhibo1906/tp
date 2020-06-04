<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\admin\controller\Common;
class Jues extends Common
{
	public function index()
	{
		$aa=Db::name('juese')->order('id','desc')->paginate(config('pageSize'));
		$this->assign('vv',$aa);
		return view();
	}
	public function add()
	{
		return view();
	}
	public function doadd(Request $request)
	{
		$post=$request->post();
		$validate = new \app\admin\validate\Jues;
	    if (!$validate->check($post)) {
	        return view('add',['data'=>$post,'error'=>$validate->getError()]);
	    }
		$res=Db::name('juese')->insert($post);
		if($res){
			$this->redirect('Jues/index');
		}else{
			$this->error('添加失败！','Jues/add');
		}
	}
	public function del()
	{
		$id=input('id');
		$res=Db::name('juese')->delete($id);
		if($res){
			$this->redirect('Jues/index');
		}
	}
	public function update($id)
	{
		$data=Db::name('juese')->where('id',$id)->find();
		$this->assign('v',$data);
		return view();
	}
	public function doupdate(Request $request,$id)
	{
		$post=input('post.');
		$validate = new \app\admin\validate\Jues;
	    if (!$validate->check($post)) {
	        return view('add',['data'=>$post,'error'=>$validate->getError()]);
	    }
	    $res=Db::name('juese')->where('id',$id)->update($post);
	    if($res){
			$this->redirect('Jues/index');
		}else{
			$this->error('修改失败！','Jues/update');
		}
	}
	public function checkOnlyName()
	{
		$username=input('post.username');
		$id=input('post.id')??'';
		if($username){
			$where=[];
			if($id){
				$where[]=['id','<>',$id];
			}
			$count=Db::name('juese')->where('username',$username)->where($where)->count();
			echo $count;
		}
		
	}
}
?>