<?php

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'admin_name'  => 'require|max:30|min:6|chsDash',
        'email' => 'require|email',
        'pwd' => 'require|max:18|min:6',
        'qpwd' => 'require|confirm:pwd',

    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'admin_name.require'  => '管理员名称必填',
        'admin_name.max'  => '管理员名称长度不超过30个字符',
        'admin_name.min'  => '管理员名称长度不小于6个字符',
        'admin_name.chsDash'  => '名称只能使用数字字母下划线破折号',
        'email.require' => '邮箱必填',
        'email.email' => '请输入正确邮箱格式',
        'pwd.require' => '密码必填',
        'pwd.max' => '密码长度不超过18个字符',
        'pwd.min' => '密码长度不小于6个字符',
        'qpwd.require' => '确认密码必填',
        'qpwd.confirm' => '两次密码不一致',
    ];
}
