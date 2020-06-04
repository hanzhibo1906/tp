<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\News as NewsModel;
use app\admin\controller\Common;
class News extends Common
{
	public function add()
	{
		return view();
	}
	public function doadd()
	{
		if(request()->isPost()&&request()->isAjax()){
			$data=input('post.');
			// dump($data);die;
			if($data['release_time']==2){
				$data['n_time']=strtotime($data['n_time']);
				if($data['n_time']<=time()){
					fail('定时发布时间必须比当前时间大');
				}
			}else if($data['release_time']==1){
				$data['n_time']=time();
			}
			$NewsModel=new NewsModel;
			$res=$NewsModel->save($data);
			if($res){
	        	successly('添加成功');
	        }else{
	        	fail('添加失败');
	           
	        }
		}		
	}

	//图片上传
	public function upload()
	{
		$result=upload('file');
	    echo $result;
	}

}