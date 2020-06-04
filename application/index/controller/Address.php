<?php  
namespace app\index\controller;
use  app\index\controller\Common;
use think\Controller;
use think\Request;
use think\facade\Request as Frequest;
class Address extends Common
{
	public function __construct()
	{
		parent::__construct();
		//防止非法登录
		if(!$this->checkLogin()){
			$this->error('请先登录',url('Login/login'));exit;
		}
	}

	//收货地址页面
	public function address()
	{
		//查询收货地址列表
		$addressInfo=$this->getAddressInfo();
		//print_r($addressInfo);exit;
		//查询省份
		$provinceInfo=$this->getAreaInfo(0);
		// print_r($provinceInfo);exit;
		$this->assign('provinceInfo',$provinceInfo);
		$this->assign('addressInfo',$addressInfo);
		return view();
	}

	//获取地区
	public function getAreaInfo($pid)
	{
		$area_model=model('Area');
		$where=[
			['pid','=',$pid]
		];
		$areaInfo=$area_model->where($where)->select();
		return $areaInfo;
	}
	
	//获取区域
	public function getArea()
	{
		$id=input('post.id',0,'intval');
		$aresInfo=$this->getAreaInfo($id);
		// print_r($aresInfo);die;
		echo json_encode($aresInfo);
	}

	//添加收货地址
	public function addressDo()
	{
		$data=input('post.');
		// print_r($data);die;
		//添加时修改状态
		$address_model=model('Address');
		if($data['is_default']==1){
			$user_id=$this->getUserId();
			$where=[
				['user_id','=',$user_id],
				['is_del','=',1]
			];
			$address_model->where($where)->update(['is_default'=>2]);
		}
		//添加
		$res=$address_model->save($data);
		//dump($res);
		if($res){
			successly('添加成功');
		}else{
			fail('添加失败');
		}
	}

	//设置默认收货地址
	public function setAddressDefault()
	{
		$address_id=input('get.address_id','','intval');
		//echo $address_id;exit;
		if(empty($address_id)){
			$this->error('请至少选择一个收货地址',url('Address/address'));exit;
		}
		$address_model=model('Address');
		//开启事务
		$address_model->startTrans();
		//把所有收货地址状态改成2
		$user_id=$this->getUserId();
		$where=[
			['user_id','=',$user_id],
			['is_del','=',1]
		];
		$res1=$address_model->where($where)->update(['is_default'=>2]);
		//把当前收货地址状态改为1
		$res2=$address_model->where('address_id',$address_id)->update(['is_default'=>1]);
		// $res2=false;
		if($res1!==false&&$res2){
			//提交
			$address_model->commit();
			$this->redirect('Address/address');
		}else{
			//回滚
			$address_model->rollback();
			$this->error('设置失败');
		}
	}

	//修改收货地址
	public function addressUpdate()
	{
		$address_id=input('get.address_id','','intval');
		// echo $address_id;exit;
		if(empty($address_id)){
			$this->error('请至少选择一个收货地址',url('Address/address'));exit;
		}
		//根据收货地址id查询出要修改的一条数据 作为默认值
		$address_model=model('Address');
		$where=[
			['address_id','in',$address_id],
			['is_del','=',1]
		];
		$addressInfo=$address_model->where($where)->find();
		// print_r($addressInfo);die;
		//查询所有省份信息
		$provinceInfo=$this->getAreaInfo(0);
		//查询市信息
		$cityInfo=$this->getAreaInfo($addressInfo['province']);
		//查询区信息
		$areaInfo=$this->getAreaInfo($addressInfo['city']);

		$this->assign('addressInfo',$addressInfo);
		$this->assign('provinceInfo',$provinceInfo);
		$this->assign('cityInfo',$cityInfo);
		$this->assign('areaInfo',$areaInfo);
		return view();
	}

	//执行修改
	public function addressUpdateDo()
	{
		$data=input('post.');
		// print_r($data);die;
		//修改时修改状态
		$address_model=model('Address');
		if($data['is_default']==1){
			$user_id=$this->getUserId();
			$where=[
				['user_id','=',$user_id],
				['is_del','=',1]
			];
			$address_model->where($where)->update(['is_default'=>2]);
		}
		//修改
		$res=$address_model->where('address_id',$data['address_id'])->update($data);
		//dump($res);
		if($res){
			successly('修改成功');
		}else{
			fail('修改失败');
		}
	}

	//删除
	public function addressDel()
	{
		$address_id=input('post.address_id','','intval');
		// echo $address_id;exit;
		if(empty($address_id)){
			$this->error('请至少选择一个收货地址进行删除',url('Address/address'));exit;
		}
		$address_model=model('Address');
		$where=[
			['address_id','in',$address_id]
		];
		$info=[
			'is_del'=>2
		];
		$res=$address_model->where($where)->update($info);
		if($res){
			successly('删除成功');
		}else{
			fail('删除失败');
		}
	}
}
