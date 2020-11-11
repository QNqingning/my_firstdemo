<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;

class Notice extends Lock{
	public function index(){
		
		return view();
	}
	public function add(){
		$data = input("post.");
		
		$data['time'] = date("Y-m-d");
		
		if(db("notice")->insert($data)){
			$this->redirect("Index/index");
		}else{
			$this->redirect("Notice/index");
		}
	}
}
?>