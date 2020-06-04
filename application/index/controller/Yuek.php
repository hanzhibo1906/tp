<?php  
namespace app\index\controller;
use think\Controller;
use think\Request;
use app\index\model\Lob;
class Yuek extends Controller
{
	//展示列表
	public function first()
	{
		$aa=Lob::select();
		$this->assign('vv',$aa);
		return $this-> fetch();
	}
	//添加列表
	public function add(Request $request)
	{
		$res=$request->isPost();
		 if($res){
            //接收方式 input 助手函数
            $post=input('post.');
            $res=Lob::create($post);
            if($res){
            	$this->redirect('Yuek/first');
            }else{
                $this->error('添加失败！','Yuek/add');
            }
        }
        return view();
	}
	//修改列表
	public function update($id)
	{
		if($id){
            $data=Lob::get($id);
            $this->assign('v',$data);
            return view();
        }
	}
	public function doupdate(Request $request,$id)
	{
		$post=$request->post();
        $user=new Lob;
        $res=$user->save($post,['id'=>$id]);
        if($res){
            $this->redirect('Yuek/first');
        }else{
            $this->error('修改失败！','Yuek/update');
        }
	}
	//删除列表
	public function del($id)
	{
		if($id){
            $res=Lob::destroy($id);
            if($res){
                $this->redirect('Yuek/first');
            }
        }
	}
	public function getUser()
    {
      /*
    	$aa=input('get.');
    	print_r($aa);
        $data=Lob::select()->toArray();
        echo json_encode($data);
      */
    	echo "123";
    }
    public function checkUsername()
    {
    	$username=input('get.username');
    	$result=Lob::where(['username'=>$username])->count();
    	if($result){
    		echo 1;
    	}else{
    		echo 0;
    	}
    }
}