<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;

class RepairList extends Lock{
	public function index(){
		$list = db("repair")->order("id DESC")->paginate(7,false,['query'=>request()->param()]);
		$total = db("repair")->count();
		$this->assign("list",$list);
		$this->assign("total",$total);
		return view();
	}
	
	public function edit(){
		$data = input('get.');
		var_dump($data);
		if(db("repair")->where('id',$data['id'])->setField('repair_result',$data['result'])){						
			$this->redirect("RepairList/index");			
		}
	}
	
	
	
}
?>