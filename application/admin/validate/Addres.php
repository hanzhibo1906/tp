<?php

namespace app\admin\validate;

use think\Validate;

class Addres extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'a_addres'=> ['require'],
        'a_name'=> ['require'],
        'a_tel'=> ['require','number','length:11']
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'a_addres.require'=> '收货地址不能为空',
        'a_name.require'=> '联系人不能为空',
        'a_tel.require'=> '手机号不能为空',
        'a_tel.number'=>'手机号必须为数字',
        'a_tel.length'=>'手机号必须为1~11位'
    ];
}
