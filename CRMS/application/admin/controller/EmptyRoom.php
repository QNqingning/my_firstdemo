<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;

class EmptyRoom extends Lock {
	public function index(){
//		$search = input("get.search");
//		$day = input("get.day");
//		$lesson = input("get.lesson");


		$list = db("syllabus")->whereNull('message')->field("syllabus.*,room.roomName")
				->join("room","room.floor = syllabus.number")->paginate(15,false,['query'=>request()->param()]);
		$total = db("syllabus")->whereNull('message')->count();
		
		
		$this->assign("list",$list);
		$this->assign("total",$total);
		return view();
	}
	
	
	

}
?>