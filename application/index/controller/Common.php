<?php

namespace app\index\controller;

use think\App;
use think\Controller;
use think\Request;
use think\facade\Request as Frequest;
use app\index\model\Cart;

class Common extends Controller
{
    //构造方法
    public function __construct()
    {
        parent::__construct();
        //获取访问的控制器名称
        $controller_name=Frequest::controller();
        if($controller_name=='Index'){
            $is_show='leftNav';
        }else{
            $is_show='leftNav none';
        }
        $this->assign('is_show',$is_show);
    }

    //获取分类 (左侧分类，导航分类)
    public function getLeftCateInfo()
    {
    	$cate_model=model('Category');
    	//获取导航分类
    	$navWhere=[
    		'is_nav_show'=>1
    	];
    	$navInfo=$cate_model->where($navWhere)->select();

    	//获取左侧分类
    	$cateWhere=[
    		'is_show'=>1
    	];
    	$cateInfo=$cate_model->where($cateWhere)->select();
    	$cateInfo=getLeftCateInfo($cateInfo);
        /*
            假数据
            $cateInfo=[
                [
                    'cate_id'=>1,
                    'cate_name'=>'女装',
                    'son'=>[
                        [
                            'cate_id'=>10,
                            'cate_name'=>'当季流行',
                            'son'=>[
                                [
                                    'cate_id'=>23,
                                    'cate_name'=>'防晒服'
                                ],
                                [
                                    'cate_id'=>30,
                                    'cate_name'=>'晒服'
                                ]
                            ]
                        ],
                        [
                            'cate_id'=>10,
                            'cate_name'=>'当季流行',
                            'son'=>[
                                [
                                    'cate_id'=>23,
                                    'cate_name'=>'防晒服'
                                ],
                                [
                                    'cate_id'=>30,
                                    'cate_name'=>'晒服'
                                ]
                            ]
                        ]
                    ],
                ],
                [
                    'cate_id'=>1,
                    'cate_name'=>'男装',
                    'son'=>[
                        [
                            'cate_id'=>10,
                            'cate_name'=>'流行',
                            'son'=>[
                                [
                                    'cate_id'=>23,
                                    'cate_name'=>'乞丐'
                                ],
                                [
                                    'cate_id'=>30,
                                    'cate_name'=>'破衣服'
                                ]
                            ]
                        ],
                        [
                            'cate_id'=>10,
                            'cate_name'=>'流行',
                            'son'=>[
                                [
                                    'cate_id'=>23,
                                    'cate_name'=>'乞丐'
                                ],
                                [
                                    'cate_id'=>30,
                                    'cate_name'=>'破衣服'
                                ]
                            ]
                        ]
                    ],
                ]
            ];
        */
        
    	//print_r($cateInfo);exit;
    	$this->assign('navInfo',$navInfo);
    	$this->assign('cateInfo',$cateInfo);
    }

    //检测是否登录
    public function checkLogin()
    {
        return session('?userInfo');
    }

    //获取用户Id
    public function getUserId()
    {
        return session('userInfo.u_id');
    }

    //同步浏览历史记录
    public function asyncHistory()
    {
        //把cookie中的数据取出
        $str=cookie('history');
        // echo $str;die;
        //判断是否有浏览历史记录信息
        if(!empty($str)){
            //解密
            $historyInfo=getBase64Info($str);
            // print_r($historyInfo);exit;
            
            //数组中补全 用户id
            foreach ($historyInfo as $k => $v) {
                //从session中获取用户Id
                $historyInfo[$k]['user_id']=$this->getUserId();
            }
            // print_r($historyInfo);exit;
            //添加到浏览历史表中
            $history_model=model('History');
            $res=$history_model->saveAll($historyInfo);
            if($res){
                //把cookie删除
                cookie('history',null); 
            }
        }
    }

    //同步购物车
    public function asyncCart()
    {
        //把cookie中的数据取出
        $str=cookie('cartInfo');
        // echo $str;die;
        //判断是否有浏览历史记录信息
        if(!empty($str)){
            $user_id=$this->getUserId();
            //解密
            $cookieInfo=getBase64Info($str);
            // print_r($cookieInfo);exit;
            $cart_model=model('Cart');
            foreach ($cookieInfo as $k => $v) {
                $where=[
                    'user_id'=>$user_id,
                    'goods_id'=>$v['goods_id'],
                    'is_del'=>1
                ];
                //查询每条商品是否已经加入购物车
                $info=$cart_model->where($where)->find();
                if(!empty($info)){
                    // dump($info);exit;
                    // echo'有值，累加';die;
                    //检测库存并入库
                    $res=$this->checkGoodsNumber($v['goods_id'],$v['buy_number'],$info['buy_number']);
                    // dump($res);die;
                    //累加
                    if($res){
                        $updateInfo=[
                            'buy_number'=>$v['buy_number']+$info['buy_number'],
                            'update_time'=>time()
                        ];
                        // print_r($updateInfo);exit;
                        $cart_model->where($where)->update($updateInfo);
                        cookie('cartInfo',null);
                    } 
                }else{
                    // echo'无有值，添加';die;
                    //检测库存
                    $res=$this->checkGoodsNumber($v['goods_id'],$v['buy_number']);
                    // print_r($res);
                    if($res){
                        //添加
                        $arr=[
                            'goods_id'=>$v['goods_id'],
                            'buy_number'=>$v['buy_number'],
                            'user_id'=>$user_id
                        ];
                        // print_r($arr);exit;
                        Cart::create($arr);
                        cookie('cartInfo',null);
                    } 
                }
            }
        }
    }

    //监测商品库存
    public function checkGoodsNumber($goods_id,$buy_number,$number=0)
    {
        //根据id查询商品库存
        $goods_model=model('Goods');
        //获取库存
        $goods_number=$goods_model->where('goods_id',$goods_id)->value('goods_number');
        // echo $goods_number;exit;
        if(($buy_number+$number)>$goods_number){
            return false;
        }else{
            return true;
        }
    }

    //获取收货地址信息
    public function getAddressInfo()
    {
        //获取用户id
        $user_id=$this->getUserId();
        $where=[
            ['is_del','=',1],
            ['user_id','=',$user_id]
        ];
        $address_model=model('Address');
        $addressInfo=$address_model->where($where)->select();
        // dump($addressInfo);exit;
        if(!empty($addressInfo)){
            //处理省市区
            $area_model=model('Area');
            foreach ($addressInfo as $k => $v) {
                $addressInfo[$k]['province']=$area_model->where('id',$v['province'])->value('name');
                $addressInfo[$k]['city']=$area_model->where('id',$v['city'])->value('name');
                $addressInfo[$k]['area']=$area_model->where('id',$v['area'])->value('name');
            }
            return $addressInfo;
        }else{
            return false;
        }
    }

}