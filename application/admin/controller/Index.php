<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\controller\Common;
class Index extends Common
{
	//展示列表
	public function index()
	{
		return view();
	}
}
?>