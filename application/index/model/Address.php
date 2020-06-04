<?php  
namespace app\index\model;
use think\Model;
class Address extends Model
{
	protected $pk = 'address_id';
	protected $insert=['user_id'];
	//数据完成
	protected function setUserIdAttr()
	{
		return session('userInfo.u_id');
	}
}



?>