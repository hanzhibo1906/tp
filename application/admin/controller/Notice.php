<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\admin\model\Notice as NoticeModel;
use app\admin\controller\Common;
class Notice extends Common
{
	public function lists()
	{
		$count=NoticeModel::count();
		$data=NoticeModel::select();
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
		return view();
	}
	public function doadd()
	{
		$post=input('post.');
		$validate=new \app\admin\validate\Notice;
		if(!$validate->check($post)){
			return view('add',['data'=>$post,'error'=>$validate->getError()]);
		}
		$post['release_time']=time();
		$res=Db::name('notice')->where('title',$post['title'])->find();	
		if($res){
			$this->error('标题重复，添加失败！','Notice/add');
		}else{
			NoticeModel::create($post);
			$this->redirect('Notice/lists');
		}
	}
	public function delete()
	{
		if(request()->isAjax()){
			$id=input('id');
			$res=NoticeModel::destroy($id);
			if($res){
				echo json_encode(['code'=>0,'msg'=>'删除成功！']);
			}else{
				echo json_encode(['code'=>1,'msg'=>'删除失败！']);
			}
		}
	}
	
	// 验证唯一性
    public function checkOnly (Request $request)
    {
    	$title=$request->get('title');
    	if(empty($title)){
    		echo json_encode(['code'=>1,'msg'=>'为空！']);
    	}
    	$res=NoticeModel::where('title',$title)->find();
    	if($res){
    		echo json_encode(['code'=>1,'msg'=>'已存在！']);
    	}else{
    		echo json_encode(['code'=>0,'msg'=>'可使用！']);
    	}
    }

    //状态显示
    public function ajaxupd()
    {
    	if(request()->isAjax()){
    		$id=input('get.id');
    		$field=input('get.key');
    		$value=input('get.value');
    		$data[$field]=$value==1?0:1;
    		$NoticeModel=new NoticeModel;
    		$res=$NoticeModel->save($data,['id'=>$id]);
    		if($res){
    			echo 1;
    		}else{
    			echo 0;
    		}
    	}
    }
}