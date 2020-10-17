<?php
	
namespace app\admin\controller;
use think\Controller;
use Think\Db;

	//继承Lock控制器，防止未登录进行恶意操作
	class Student extends Lock
	{
		
	    public function index(){
			//获取搜索框的内容
			$search = input("get.search");
			// 从数据库中读取数据
			$list = db("student")->where("number like '%$search%'")->order("id ASC")->paginate(7,false,['query'=>request()->param()]);
			$total = db("student")->where("number like '%$search%'")->count();
		
			// 将数据分配给页面
			$this->assign("list",$list);
			$this->assign("total",$total);
			
            return view();
		}
		
		
		//查询当前用户的信息
		public function student(){
			$number = session('number');
			$message = db("student")->where("number like $number")->select();
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
			if (db("student")->update($arr)){
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
			$data  = db("student")->where("number = '$number'")->find();
	
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
						if(db("student")->insert($arr)){
							return $this->redirect('student/index');
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
			if (db("student")->delete($id)) {
				echo 1;
			}else{
				echo 0;
			}	
		}
		
		// 修改
		public function edit(){
			// 接收要修改的ID
			$id = input("get.id");
	
			// 从数据库中查找出对应的对象
			$data = db('student')->find($id);
	
			// 将数据转换成json
			$this->assign("data",$data);
	
			//加载页面
			return view();
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
			if (db("student")->update($arr)){
				$this->redirect('student/index');
			}else{
				$this->error("修改失败");
			}
		}
		
	}
		
?>