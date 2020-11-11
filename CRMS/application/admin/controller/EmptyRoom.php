<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;

class EmptyRoom extends Lock {
	public function index(){
		$search = input("get.search");

		if($search){
			$list = db("syllabus")->where('message',null)->where('number|day|lesson',$search)
			->field("syllabus.*,room.roomName")->join("room","room.floor = syllabus.number")
			->paginate(15,false,['query'=>request()->param()]);
			$total = db("syllabus")->where('message',null)->where('number|day|lesson',$search)->count();	
		}else{
			$list = db("syllabus")->where('message',null)->field("syllabus.*,room.roomName")->join("room","room.floor = syllabus.number")
			->paginate(15,false,['query'=>request()->param()]);
			$total = db("syllabus")->whereNull('message')->count();	
		}
		
		
		$this->assign("list",$list);
		$this->assign("total",$total);
		return view();
	}
	
	
	

}
?>