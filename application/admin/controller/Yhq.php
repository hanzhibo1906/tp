<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Yhq as yhqModel;
use app\admin\controller\Common;
class Yhq extends Common
{
	//添加
	public function add()
	{
		return view();
	}
	//处理添加
	public function doadd()
	{
		//判断是否ajax传输
        if(request()->isPost()&&request()->isAjax()){
        	//接收
			$data=input('post.');
			// dump($data);die;
			//strtotime生成对应的时间戳，输出的时候会自动转换为dateFormat属性定义的时间字符串格式
			$data['add_time']=strtotime($data['add_time']);
			if($data['add_time']<=time()){
				fail('开始时间必须比当前时间大');
			}
			$data['del_time']=strtotime($data['del_time']);
			if($data['del_time']<=$data['add_time']){
				fail('结束时间必须比开始时间大');
			}
			$yhqModel=new yhqModel;
			//入库
			$res=$yhqModel->save($data);
			if($res){
	        	successly('添加成功');
	        }else{
	        	fail('添加失败');
	        }
		}		
	}
	
}