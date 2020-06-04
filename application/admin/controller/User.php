<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Admin;
use think\Db;
use app\admin\controller\Common;
class User extends Common
{
	//展示列表
	public function index()
	{
		//$aa=request()->controller();
		$field=input('get.field');
		$keys=input('get.keys');
		$where=[];
		if($field=='admin_name'){
			$where[]=['admin_name','like',"%$keys%"];
		}
		if($field=='email'){
			$where['email']=$keys;
		}
		//config('pageSize')接收page项每页显示条数
		$aa=Db::name('admin')->where($where)->order('admin_id','desc')->paginate(config('pageSize'),false,["query"=>['field'=>$field,'keys'=>$keys]]);
		$this->assign('vv',$aa);
		//无刷新分页使用
		if(request()->isAjax()){
			return view('ajaxindex');
		}
		return view();
	}
	//添加列表
	public function add()
	{
		return view();
	}
	public function doadd(Request $request)
	{
        $post=$request->post();
        //dump($post);
        $validate = new \app\admin\validate\Admin;
	    if (!$validate->check($post)) {
	    	//页面跳转效果
	        //dump($validate->getError());
	        return view('add',['data'=>$post,'error'=>$validate->getError()]);
	    }
	    $post['salt']=setSalt();
	    $post['pwd']=crestePwd($post['pwd'],$post['salt']);
	    $post['ip']=$request->ip();
	    $post['addtime']=time();
	    $post['lasttime']=time();
        //$res=Admin::create($post);
        $res=Db::name('admin')->strict(false)->insert($post);
        if($res){
        	$this->redirect('User/index');
        }else{
            $this->error('添加失败！','User/add');
        }
	}
	//修改列表
	public function update($admin_id)
	{
		$data=Db::name('admin')->where('admin_id',$admin_id)->find();
        //$data=Admin::get($admin_id);
        $this->assign('v',$data);
        return view();
	}
	public function doupdate(Request $request,$admin_id)
	{
		$post=input('post.');
		$validate = new \app\admin\validate\Admin;
	    if (!$validate->check($post)) {
	        return view('add',['data'=>$post,'error'=>$validate->getError()]);
	    }
	    $post['pwd']=md5(md5($post['pwd']));
	    $res=Db::name('admin')->strict(false)->where('admin_id',$admin_id)->update($post);
        if($res){
            $this->redirect('User/index');
        }else{
            $this->error('修改失败！','User/update');
        }
	}
	//删除列表
	public function del()
	{
		$admin_id=input('admin_id');
		$res=Db::name('admin')->delete($admin_id);
        //$res=Admin::destroy($admin_id);
        if($res){
            $this->redirect('User/index');
        }
	}
	//批删
	public function dels()
	{
		$ids=input('ids');
		if(is_array($ids)&&$ids){
			$res=\think\Db::name('admin')->delete($ids);
			print_r($res);
		}
	}
    public function checkOnlyName()
    {
    	$admin_name=input('post.admin_name');
    	$admin_id=input('post.admin_id')??'';
		if($admin_name){
			$where=[];
			if($admin_id){
				$where[]=['admin_id','<>',$admin_id];
			}
			$count=Db::name('admin')->where('admin_name',$admin_name)->where($where)->count();
			echo $count;
		}
    }
    //即点即改
    public function ajaxupd(Request $request)
    {
    	if($request->isAjax()){
    		$admin_name=input('admin_name');
    		$admin_id=input('admin_id');
    		$res=Db::name('admin')->where('admin_id',$admin_id)->update(['admin_name'=>$admin_name]);
    		if($res){
    			echo 1;
    		}else{
    			echo 0;
    		}
    	}
    }
}
