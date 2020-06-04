<?php  
namespace app\index\controller;
use think\Controller;
use think\Request;
use email\PHPMailer;
use think\facade\Session;
use app\index\model\User;
use app\index\validate\User as ValidateUser;
use think\facade\Request as Re;
class Login extends Common
{
	//展示列表
	public function login()
	{
        if(request()->isPost()&&request()->isAjax()){
            $account=input('post.account');
            $u_pwd=input('post.u_pwd');
            //字符串类型 true fales
            $remember_me=input('post.remember_me');
            //验证
            if(empty($account)){
                fail('账号必填');
            }
            if(empty($u_pwd)){
                fail('密码必填');
            }
            $where=[
                'u_email'=>$account,
                //'u_phone'=>'123456'
            ];
            $user_model=new User();
            //数据库里数据
            $userInfo=$user_model->where($where)->find();
            // print_r($userInfo);exit;
            // echo $user_model->getLastSql();exit;
            if(!empty($userInfo)){
                $now=time();
                $last_error_time=$userInfo['last_error_time'];
                $error_num=$userInfo['error_num'];
                $user_model=model('User');
                $where=[
                    'u_id'=>$userInfo['u_id']
                ];
                //账号正确
                if($userInfo['u_pwd']==md5($u_pwd)){
                    //echo '密码正确';
                    if($error_num>=3&&$now-$last_error_time<3600){
                        $second=60-ceil((time()-$last_error_time)/60);
                        fail('账号已锁定，请'.$second.'分钟后重新登录');
                    }

                    //错误次数清零 时间改为null
                    $updateInfo=[
                        'error_num'=>0,
                        'last_error_time'=>null
                    ];
                    $user_model->save($updateInfo,$where);
                    // echo $user_model->getLastSql();exit;
                    //判断是否记住账号密码10天
                    if($remember_me=='true'){
                        $rememberInfo=[
                            'account'=>$account,
                            'u_pwd'=>$u_pwd
                        ];
                        cookie('rememberInfo',$rememberInfo,60*60*24*10);
                    }
                    //存用户id 账号进入session
                    $userInfos=[
                        'u_id'=>$userInfo['u_id'],
                        'account'=>$account
                    ];
                    session('userInfo',$userInfos);

                    //同步浏览历史记录
                    $this->asyncHistory();
                    // exit;
                    
                    //同步购物车
                    $this->asyncCart();
                    // exit;

                    //提示登陆成功
                    successly('登陆成功');
                }else{
                    //echo '密码有误';
                    if($now-$last_error_time>3600){
                        $updateInfo=[
                            'error_num'=>1,
                            'last_error_time'=>$now
                        ];
                        $res=$user_model->save($updateInfo,$where);
                        // echo $user_model->getLastSql();exit;
                        if($res){
                            fail('账号或密码有误，您还有2次机会');
                        }
                    }else{
                        if($error_num>=3){
                            $second=60-ceil((time()-$last_error_time)/60);
                            fail('账号已锁定，请'.$second.'分钟后重新登录');
                        }else{
                            $updateInfo=[
                                'error_num'=>$error_num+1,
                                'last_error_time'=>$now
                            ];
                            $res=$user_model->save($updateInfo,$where);
                            if($res){
                                $count=3-($error_num+1);
                                fail('账号或密码有误，您还有'.$count.'次机会');
                            }
                        }
                    }
                }
            }else{
                fail('账号或密码有误');
            }
        }else{
            return view();
        }	
	}
	//显示常见资源表单
	public function register()
    {
        if(Re::isPost()&&Re::isAjax()){
            $data=input('post.');
            //dump($data);
            //验证
            $validate_user=new ValidateUser();
            $res=$validate_user->scene('register')->check($data);
            if(!$res){
                 fail($validate_user->getError());
            }

            //入库
            $user_model=model('User');
            $res=$user_model->allowField(true)->save($data);
            if($res){
                successly('注册成功');
            }else{
                fail('注册失败');
            }
           
        }else{
        //配置中开启了layout
        $this->view->engine->layout(false);
        return view();   
        }
    	 
    }
    //检测邮箱
    public function checkEmail()
    {
    	$email=input('post.email');
    	$user_model=model('User');
    	$where=[
    		'u_email'=>$email
    	];
    	$data=$user_model->where($where)->find();
    	if(empty($data)){
    		successly();
    	}else{
    		fail('此邮箱已被注册');
    	}
    }
    //发送邮箱
    public function send()
    {
    	$email=input('post.email');
        //清空上一次的session信息
        session('emailInfo',null);
    	//验证
    	$data=[
    		'u_email'=>$email
    	];
    	$validate_user=new ValidateUser();
    	$res=$validate_user->scene('checkEmail')->check($data);
    	if(!$res){
    		fail($validate_user->getError());
    	}
    	//随机生成6位验证码
    	$code=createCode();
    	//发送邮件
    	$res1=sendEmail($email,$code);
    	if($res1){
    		//把邮箱、验证码、发送时间存入到session中
    		$emailInfo=[
    			'u_email'=>$email,
    			'u_code'=>$code,
    			'send_time'=>time(),
    		];
    		session('emailInfo',$emailInfo);
    		successly('发送成功');
    	}else{
    		fail('发送失败');
    	}
    }

    public function test()
    {
    	$userInfo=session('userInfo');
        $rememberInfo=cookie('rememberInfo');
        print_r($userInfo);
    	print_r($rememberInfo);
    }

    /*
        测试发送邮件
        public function aaa(){
            $res1=sendEmail('1131770192@qq.com','1212312312312313212313');
            dump($res1);
        }
    */
   
    //修改密码发送邮件
    public function forgetPwd()
    {
        if(request()->isPost()&&request()->isAjax()){
            $u_email = input('post.u_email');
            // dump($u_email);die;
            $where = [
                'u_email' => $u_email
            ];
            //判断该邮箱是否存在
            $user = new User;
            $res = $user -> where($where) -> find();
            if (!$res) {
                fail('请输入正确的注册邮箱');
            }
            // print_r($res);die;
            //给u_id u_email 和 u_pwd 加密
            $str = md5($res['u_id'].$res['u_email'].$res['u_pwd']);
            //加密
            $newStr = createBase64Str($res['u_id'].'.'.$str);
            // dump($newStr);
            $body = '
                    <p>欢迎使用本站的找回密码功能，如果你确定此找回密码功能是你启用的请点击以下链接，按照流程进行重设密码</p>
                    <p>点击找回密码 <a href="http://www.1810.com/index/login/new.html?p='.$newStr.'">找回密码</a></p>
                    <p>或者复制以下链接到浏览器进行重置密码</p>
                    <p>http://www.1810.com/index/login/new.html?p='.$newStr.'</p>
                ';
            $send = updateEmail('找回密码',$body,$u_email);
            // dump($send);die;
            if ($send == 1) {
                successly('请注意查收邮件，进行修改密码');
            }
        }else{
            return view();
        }
        
    }

    //修改密码视图
    public function new()
    {
        $msg = input('p');
        //将密文进行解密
        $msg = getBase64Info($msg);
        // dump($msg);die;
        //将密文拆分用户id . 密文 为数组
        $info = explode('.',$msg);
        // dump($info);
        //根据用户的id查询数据库 
        $userInfo = User::find($info[0]);
        // dump($userInfo);die;
        //给u_id u_email 和 u_pwd 加密
        $str = md5($userInfo['u_id'].$userInfo['u_email'].$userInfo['u_pwd']);
        //判断密文是否一致
        if ($str === $info[1]) {
            //如果密文一致  则显示修改密码的页面
            $this -> assign(['u_id'=>$info[0]]);
            return view();
        }else{
            echo '信息有误，请重新执行修改密码的操作';
        }
    } 

    //修改密码
    public function updatePwd()
    {
        if (request() -> isAjax() && request() -> isPost()) {
            $data['u_pwd'] = input('post.pwd');
            $u_id['u_id'] = input('post.u_id');
            // print_r($data);
            // print_r($u_id);die;
            $user = new User;
            //给密码加密
            $data['u_pwd'] = md5($data['u_pwd']);
            // print_r($data['u_pwd']);
            // print_r($data);die;
            //修改
            $res = $user -> save($data,$u_id);
            if ($res) {
                successly('密码修改成功');
            }else{
                fail('密码修改失败');
            } 
        }
    }

    //退出
    public function quit(){
        session('userInfo', null);
        cookie('rememberInfo', null);
        $this->redirect('Login/login');
    }

}