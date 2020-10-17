<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;


class Room extends Lock{
	public function index(){
		//获取搜索框的内容
   		$search = input("get.search");
   		
   		// 查询数据 
		$list = db("room")
		//按机房名字查询
		->where("roomName like '%$search%'")
		//按机房编号查询
		->whereOr("roomNum like '%$search%'")
		//按机房楼层查询
		->where("floor like '%$search%'")
		//按学院查询
		->whereOr("academy like '%$search%'")
		//按教学楼查询
		->whereOr("roomBuilding like '%$search%'")
		//按机房容量查询
		->whereOr("capacity like '%$search%'")
		//排列
		->order("id ASC")
		//每页显示10条数据
		->paginate(10,false,['query'=>request()->param()]);	
		
		//统计当前查询的数据条数
		$total = db("room")
		->where("roomName like '%$search%'")
		->whereOr("roomNum like '%$search%'")
		->whereOr("floor like '%$search%'")
		->whereOr("academy like '%$search%'")
		->whereOr("roomBuilding like '%$search%'")
		->whereOr("capacity like '%$search%'")
		->count();
		
		// 分配数据
		$this->assign("list",$list);
		$this->assign("total",$total);
		return view();
	}
	
	//检测机房编号是否已经存在
	public function num(){
		$roomNum = input('get.roomNum');
		
		$data = db("room")->where("roomNum = '$roomNum'")->find();
		
		if($data){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	//插入数据
	public function add(){
		$data = input('post.');
		if(db('room')->insert($data)){
			$this->success('插入成功');
		}else{
			$this->error('插入失败');
		}
	} 
	
	// 修改
	public function edit(){
		// 接收要修改的ID
		$id = input("get.id");
		// 从数据库中查找出对应的对象
		$data = db("room")->find($id);
		// 将数据转换成json
		$this->assign("data",$data);
		//加载页面
		return view();
	}

	//更新数据
	public function update(){
		$post = input('post.');
		$arr = array();
		if($post['roomName']){
			if($post['capacity']){
				$arr = [
					'id' => $post['id'],
					'roomName' => $post['roomName'],
					'capacity' => $post['capacity'],
				];
			}else{
				$arr = [
					'id' => $post['id'],
					'roomName' => $post['roomName'],
				];
			}
		}else{
			if($post['capacity']){
				$arr = [
					'id' => $post['id'],
					'capacity' => $post['capacity'],
				];
			}else{
				$this->error('请输入要更新的数据');
			}
		}
		
		if(db('room')->update($arr)){
			$this->success('更新成功','Room/index');
		}else{{
			$this->error('更新失败');
		}}
		
	}
	
	
	public function del(){
		// 接收要删除的ID
		$id = input("get.str");

		// 判断是否删除成功
		if (db("room")->delete($id)) {
			echo 1;
		}else{
			echo 0;
		}
	}
}

?>