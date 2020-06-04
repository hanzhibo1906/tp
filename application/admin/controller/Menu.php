<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Menu as MenuModel;
use app\admin\controller\Common;
class Menu extends Common
{
	public function lists()
	{
		$page=input('page')??1;
		//获取page项的值，每页显示条数
		$limit=input('limit')??config('pageSize');
		$count=MenuModel::count();
		$data=MenuModel::order('menu_id','desc')->page($page,$limit)->select();
		//无限极分类
		$data=createTree($data,0,1,"menu_id");
		if($data){
			foreach ($data as $key => $v) {
				$data[$key]['level']=str_repeat("&nbsp;&nbsp;&nbsp;",$v['level']-1);
			}
		}
		if(request()->isAjax()){
			$result=[
				'code'=>0,
				'msg'=>'',
				'count'=>$count,
				'data'=>$data
			];
			echo json_encode($result);die;
		}
		return view();
	}
	public function add()
	{
		$data=MenuModel::select();
		//无限极分类
		$data=createTree($data,0,1,"menu_id");
		$this->assign('data',$data);
		return view();
	}
	public function doadd()
	{
		$post=input('post.');
		$res=MenuModel::create($post);
		if($res){
        	$this->redirect('Menu/lists');
        }else{
            $this->error('添加失败！','Menu/add');
        }
	}
	public function delete()
	{
		if(request()->isAjax()){
			$menu_id=input('menu_id');
			$res=MenuModel::destroy($menu_id);
			if($res){
        		echo json_encode(['code'=>0,'msg'=>'删除成功']);
	        }else{
	            echo json_encode(['code'=>1,'msg'=>'删除失败']);
	        }
		}
	}
	//修改
	public function edit($menu_id)
	{
		$data=MenuModel::get($menu_id);
		$all=MenuModel::select();
		$all=createTree($all,0,1,"menu_id");
		$this->assign('data',$data);
		$this->assign('all',$all);
		return view();
	}
	public function doedit(Request $request,$menu_id)
	{
		$post=$request->post();
		$MenuModel=new MenuModel;
		$res=$MenuModel->save($post,['menu_id'=>$menu_id]);
		if($res){
        	$this->redirect('Menu/lists');
        }else{
            $this->error('修改失败！','Menu/edit');
        }
	}

	// 验证唯一性
    public function checkOnly (Request $request)
    {
    	$menu_name = $request->get('menu_name');
       
        if (empty($menu_name)) {
            echo json_encode(['code'=>1,'msg'=>'为空']);
        }
        $res =MenuModel::where('menu_name',$menu_name)->find();

        if ($res) {
            echo json_encode(['code'=>1,'msg'=>'已存在']);
        }else{
            echo json_encode(['code'=>0,'msg'=>'可使用']);
        }
    }

}