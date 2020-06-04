<?php  
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Books as BooksModel;
use app\admin\controller\Common;
use app\admin\validate\Books as ValidateBooks;
class Books extends Common
{
	//展示
	public function lists()
	{
		$books_model=model('Books');
		$booksInfo=$books_model->order('b_id','desc')->select();
		$this->assign('booksInfo',$booksInfo);
		return view();
	}

	//添加
	public function add()
	{
		return view();
	}
	public function doadd()
	{
		if(request()->isPost()&&request()->isAjax()){
			$data=input('post.');
			//dump($data);die;
			
			$validate_books=new ValidateBooks();
            $res=$validate_books->check($data);
            if(!$res){
                 fail($validate_books->getError());
            }

		    $data['open_time']=strtotime($data['open_time']);
			if($data['open_time']<=time()){
				fail('开始时间必须比当前时间大');
			}
			$BooksModel=new BooksModel;
			$res=$BooksModel->save($data);
			if($res){
	        	successly('添加成功');
	        }else{
	        	fail('添加失败');
	        }
		}
	}


}