<?php
namespace app\admin\controller;
use think\Controller;

class Lock extends Controller{
	// _initialize()每个页面都会加载一次
	//可以判断用户操作时是否登陆
	public function _initialize(){
		
		// 通过session判断用户是否已经登陆
		if (!session("number") && !session("id")) {
			//未登录就跳转到登录页面
			$this->redirect('Login/index');
		}
	}
}
?>