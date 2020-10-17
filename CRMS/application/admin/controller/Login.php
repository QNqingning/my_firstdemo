<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Login extends Controller{
	public function index(){
		return view();
	}
	
	//	处理登录
	public function check(){
		//接收表单的数据
		$number = input('post.username');
		$password = input('post.password');
		$verify = input('post.verify');
		$role = input('post.role');
		
		//判断用户身份
		if($role == '管理员'){
			$table = 'admin';
		}
		else if($role == '教师'){
			$table = 'teacher';
		}
		else{
			$table = 'student';
		}
		
		//判断验证码
		if($verify){
			$captcha = new \think\captcha\Captcha();
			if($captcha->check($verify)){
				//验证用户名
			 	if($number){
			 		//验证密码
			 		if($password){
			 			//将数据保存到数组，方便与数据库数据进行匹配
			 			$where = [
			 				'number' => $number,
			 				'password' => $password,
			 			];
			 			//在数据库中查询
			 			$data = Db::table($table)->where($where)->find();
			 			
			 			//判断是否登录成功
			 			if($data){
			 				//登录成功把登录信息存到session中，方便页面判断用户是否进行登录
			 				session('number',$data['number']);
			 				session('id',$data['id']);
			 				session('name',$data['name']);
			 				session('role',$data['role']);
			 				session('power',$data['power']);
			 				
			 				//修改用户最后一次登录时间
			 				$arr = [
			 					"id" => $data['id'],
			 					"login" => date('Y-m-d h:i:s',time()),
			 				];
			 				
			 				//判断当前用户所在的表
			 				if(session('role')=='管理员'){
			 					Db::table("admin")->update($arr);		 					
			 				}else if(session('role')=='教师'){
			 					Db::table("teacher")->update($arr);		 					
			 				}else{
			 					Db::table("student")->update($arr);		 								 					
			 				}
			 				
			 				$this->success('登录成功','Index/index');
			 			}else{						
							$this->error('账号或密码错误');
			 			}
			 		}else{	 
						$this->error('密码不能为空');
			 		}
			 	}else{
			 		$this->error('用户名不能为空');
			 	}
			}else{
				$this->error('验证码错误');
			}
		}else{
			$this->error('验证码不能为空');	
		}
	}


	//退出
	public function quit(){
		session(null);
		$this->redirect('Login/index');
	}


}
?>