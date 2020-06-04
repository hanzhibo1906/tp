<?php

namespace app\admin\validate;

use think\Validate;

class Jues extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'username'  => 'require|max:30|chsDash',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'username.require'  => '管理员名称必填',
        'username.max'  => '管理员名称长度不超过30个字符',
        'username.chsDash'  => '名称只能使用数字字母下划线破折号',
    ];
}
