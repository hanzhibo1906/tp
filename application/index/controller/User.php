<?php  
namespace app\index\controller;
use  app\index\controller\Common;
use think\Controller;
use think\Request;
use think\facade\Request as Frequest;
class User extends Common
{
	// public function __construct()
	// {
	// 	parent::__construct();
	// 	//防止非法登录
	// 	if(!$this->checkLogin()){
	// 		$this->error('请先登录',url('Login/login'));exit;
	// 	}
	// }
	
	//个人中心页面
	public function index()
	{
		return view();
	}

	//
	
	
}
?>