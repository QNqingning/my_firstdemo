<?php
	
namespace app\admin\controller;
use think\Controller;
use Think\Db;

	//继承Lock控制器，防止未登录进行恶意操作
	class Teacher extends Lock
	{
		
	    public function index(){
			//获取搜索框的内容
			$search = input("get.search");
			// 从数据库中读取数据
			$list = db("teacher")->where("number like '%$search%'")->order("id ASC")->paginate(7,false,['query'=>request()->param()]);
			$total = db("teacher")->where("number like '%$search%'")->count();
		
			// 将数据分配给页面
			$this->assign("list",$list);
			$this->assign("total",$total);
			
            return view();
		}
		
		//查询当前用户的信息
		public function teacher(){
			$number = session('number');
			$message = db("teacher")->where("number like $number")->select();
			$this->assign("message",$message);
			return view();
		}
		
		public function change(){
			//接收修改的数据
			$data = input("post.");
//			var_dump($data);
//			exit;
			
			//判断要修改的数据是否存在
			if($data['email']){
				if($data['phone']){
					$arr = [
						'id' => $data['id'],
						'email' => $data['email'],
						'phone' => $data['phone'],
					];
				}else{
					$arr = [
						'id' => $data['id'],
						'email' => $data['email'],
					];
				}
			}else{
				if($data['phone']){
					$arr = [
						'id' => $data['id'],
						'phone' => $data['phone'],
					];
				}else{
						$this->error("请输入要修改的邮箱或者手机号");
				}
			}
			
			// 在数据库中修改数据
			if (db("teacher")->update($arr)){
				$this->redirect('Admin/ad');
			}else{
				$this->error("修改失败");
			}
		}
		
		
		// 检测用户number是否存在
		public function check_number(){
			// 接受提交的数据
			$number  = input("get.number");
	
			// 查询是否有对应数据
			$data  = db("teacher")->where("number = '$number'")->find();
	
			// 判断是否存在
			if ($data) {
				echo 1;
			}else{
				echo 0;
			}
		}	
		
		//添加数据
		public function add(){
			//获取表单的数据
			$data = input("post.");
			//判断number是否为空
			if($data['number']){
				//判断密码是否为空
				if($data['password']){
					//判断密码和确认密码是否一致
					if($data['password']==$data['repassword']){
						//插入数据
						$arr['number'] = $data['number'];
						$arr['password'] = md5($data['password']);
						$arr['name'] = $data['name'];	
						$arr['role'] = $data['role'];	
						$arr['power'] = $data['power'];	
						$arr['create'] = date('Y-m-d h:i:s');
						
						//判断数据是否插入成功
						if(db("teacher")->insert($arr)){
							return $this->redirect('teacher/index');
						}
						else{
							$this->error("添加失败");
						}			
					}else{
						$this->error("密码和确认密码不一致");
					}
				}else{
					$this->error("用户密码不能为空");
				}
			}else{
				$this->error("请输入添加用户的number");
			}
		}
		
		
		
		//删除数据
		public function del(){
			// 接收要删除的ID
			$id = input("get.str");
	
			// 判断是否删除成功
			if (db("teacher")->delete($id)) {
				echo 1;
			}else{
				echo 0;
			}	
		}
		
		
		//保存修改信息
		public function updata(){
			//接收要修改的数据
			$data = input("post.");
			
			//判断用户是否修改密码
			if($data['password'] && $data['repassword']){
				//判断密码和确认密码是否一致
				if($data['password']==$data['repassword']){
					$arr = [
						"id"=>$data['id'],
						"password"=>$data['password'],
						"power"=>$data['power'],
					];
				}
			}else{
				$arr = [
					"id"=>$data['id'],
					"power"=>$data['power'],
				];
			}
			
			// 在数据库中修改数据
			if (db("teacher")->update($arr)){
				$this->redirect('teacher/index');
			}else{
				$this->error("修改失败");
			}
		}
		
	}
		
?>