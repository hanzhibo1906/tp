<?php

namespace app\admin\validate;

use think\Validate;

class Books extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
    
	protected $rule = [
        'b_title'=>'require|unique:books|min:2|chs',
        'b_name'=>'require',
        'b_content'=>'require'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'b_title.require'=>'标题必填',
        'b_title.unique'=>'标题重复',
        'b_title.min'=>'标题长度不小于2个汉字',
        'b_title.chs'=>'标题是汉字',
        'b_name.require'=>'姓名必填',
        'b_content.require'=>'内容必填'
    ];
}


