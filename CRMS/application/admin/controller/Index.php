<?php

namespace app\admin\controller;
use think\Controller;
use Think\Db;

	class Index extends Lock {
		public function index(){
//			echo "admin模块下的Index控制器的index方法";
			return view();
		}
		
	}
?>