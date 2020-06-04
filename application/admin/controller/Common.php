<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
class Common extends Controller
{
	public function initialize()
	{
		$this->checkLogin();
	}
	//非法登录
	public function checkLogin()
	{
		if (!session('adminuser')) {
            $this->redirect('Login/index');
        }
	}
}
?>