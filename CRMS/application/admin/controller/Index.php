<?php

namespace app\admin\controller;
use think\Controller;
use Think\Db;

	class Index extends Lock {
		public function index(){
			
			$data = db("notice")->where('top',1)->order("id DESC")->find();
//			var_dump($data);
			
			$this->assign("data",$data);
			return view();
		}
		
	}
?>