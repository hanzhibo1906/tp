<?php

namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'u_email'=>'require|email|unique:user|checkEmail',
        'u_code' => 'require|checkCode',
        'u_pwd' => 'require|checkPwd',
        'u_pwd1' => 'require|checkPwd1'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'u_email.require'=>'邮箱必填',
        'u_email.email'=>'请输入正确邮箱格式',
        'u_email.unique'=>'邮箱已被注册',
        'u_code.require'=>'验证码必填',
        'u_pwd.require'=>'密码必填',
    ];

    protected $scene = [
        'checkEmail' => ['u_email'],
        'register' => ['u_email','u_code','u_pwd','u_pwd1'],
    ];

    //验证器规则验证
    protected function checkEmail($value,$rule,$data=[]){
        $u_email=session('emailInfo.u_email');
        //467523
        //有session值 发送了邮件注册验证    没有session值 没有发送邮件  获取验证
        if(empty($u_email)){
            return true;
        }else{
            if($u_email!=$value){
                return '您发送邮件的邮箱与当前邮箱不一致';
            }else{
                return true;
            }
        }
    }
    protected function checkCode($value,$rule,$data=[]){
        $u_code=session('emailInfo.u_code');
        $send_time=session('emailInfo.send_time');
        //可以添加一个正则验证，判断纯数字
        
        if($value!=$u_code){
            return '验证码有误';
        }else if((time()-$send_time)>30000){
            return '验证码已失效，五分钟内输入有效';
        }else{
            return true;
        }
    }
    protected function checkPwd($value,$rule,$data=[]){
        $reg='/^.{6,12}$/';
        if(!preg_match($reg,$value)){
            return '密码允许6-12位';
        }else{
            return true;
        }
    }
    protected function checkPwd1($value,$rule,$data=[]){
        if($value!=$data['u_pwd']){
            return '确认密码必须与密码一致';
        }else{
            return true;
        }
    }

}


