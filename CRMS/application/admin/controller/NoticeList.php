<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;

class NoticeList extends Lock{
	public function index(){
		//获取搜索框的内容
		$search = input("get.search");
			
		$data = db("notice")->where("title like '%$search%'")->whereOr("content like '%$search%'")->order("id ASC")->paginate(10,false,['query'=>request()->param()]);
		
		$this->assign("data",$data);
		return view();
	}
	
	public function isTop(){
		$data = input("get.");
		
		if($data['top'] == 1){
			$top = 0;
		}else{
			$top = 1;
		}

		$sql = db("notice")->where("id",$data['id'])->setField('top',$top);
		if($sql){
			return 1;
		}else{
			return 0;
		}
		
	}
	
}
?>