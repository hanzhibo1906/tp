<?php
namespace app\admin\controller;
use think\controller;

class  Admin extends controller
{
    public function add(){
        return $this->fetch();
    }
  
}