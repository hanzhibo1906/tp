<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Addres as AddresModel;
use app\admin\controller\Common;
use app\admin\validate\Addres as ValidateAddres;
class Addres extends Common
{
	//展示
	public function lists()
	{
		$AddresModel=model('Addres');
		$AddresInfo=$AddresModel->order('a_id','desc')->select();
		$this->assign('AddresInfo',$AddresInfo);
		return view();
	}

	//添加
	public function add()
	{
		return view();
	}
	public function doadd()
	{
		if(request()->isPost()&&request()->isAjax()){
			$data=input('post.');
			//dump($data);die;
			
			$validate_addres=new ValidateAddres();
            $res=$validate_addres->check($data);
            if(!$res){
                 fail($validate_addres->getError());
            }

			$AddresModel=new AddresModel;
			$res=$AddresModel->save($data);
			if($res){
	        	successly('添加成功');
	        }else{
	        	fail('添加失败');
	        }
		}
	}


}