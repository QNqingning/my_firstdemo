<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;

class RoomApply extends Lock{
	public function index(){
		$day = input("get.day");
		$lesson = input("get.lesson");
		$search = input("get.search");
		
		
		if($day!=' ' && $lesson!=' '){
			$list = db("syllabus")->whereNull('message')->where('day',$day)->where('lesson',$lesson)->field("syllabus.*,room.roomName,capacity")
				->join("room","room.floor = syllabus.number")->select();
		}else if($lesson!=' '){
			$list = db("syllabus")->whereNull('message')->where('lesson',$lesson)->field("syllabus.*,room.roomName,capacity")
				->join("room","room.floor = syllabus.number")->select();
		}else if($day!=' '){
			$list = db("syllabus")->whereNull('message')->where('day',$day)->field("syllabus.*,room.roomName,capacity")
				->join("room","room.floor = syllabus.number")->select();
		}else {
			$list = db("syllabus")->whereNull('message')->where('number',$search)->field("syllabus.*,room.roomName,capacity")
				->join("room","room.floor = syllabus.number")->select();
		}
		
		$data = db("syllabus")->whereNull('message')->field("syllabus.*,room.roomName,capacity")->join("room","room.floor = syllabus.number")->select();
	
		$time = $this->current_week('2020-9-7');
		
		$this->assign("time",$time);
		$this->assign("data",$data);
		$this->assign("day",$day);
		$this->assign("lesson",$lesson);
		$this->assign("list",$list);
		$this->assign("total",count($list));
		return view();
	}
	
	//要申请的机房	
	public function apply(){
		// 接收要修改的ID
		$id = input("get.id");	
		// 从数据库中查找出对应的对象
		$data = db("syllabus")->find($id);	
		
		$now = date("Y-n-d");
		$this->assign("time",$now);
		$this->assign("data",$data);	
		//加载页面
		return view();
	}
	

	public function isApply(){
		$get = input("get.");
		
		if($get){
			$arr = [
				'use_time' => $get['val'][0],
				'use_number' => $get['val'][1],
				'use_day' => $get['val'][2],
				'use_lesson' => $get['val'][3],
			];
			if(db("room_apply")->where($arr)->find()){
				echo 1;
			}
			else{
				echo 0;
			}
		}
		else{
			$this->error("申请信息不能为空");
		}
	}
	
	//申请机房数据
	public function list(){
		$post = input("post.");
				
		if($post){
			$arr[] = [
				'application' => $post['application'],
				'phone' => $post['phone'],
				'time' => $post['time'],
				'reason' => $post['reason'],
				'use_time' => $post['useTime'],
				'use_number' => $post['number'],
				'use_day' => $post['day'],
				'use_lesson' => $post['lesson'],
				'check_result' => '待审核',
			];
			
			if(db("room_apply")->insertAll($arr)){

	    		$this->redirect("RoomApply/SendMail");
			}else{
				$this->error('申请失败');
			}
		}
	}
	
	//申请成功
	public function succeed(){
		$user = session("name");
		$data = db("room_apply")->where("application",$user)->order("id DESC")->select();
		$this->assign("data",$data);
		return view();
	}
	
	//发送邮件
	public function SendMail(){
        Vendor('phpmailer.phpmailer'); //引入扩展类文件
        $mail = new \phpmailer\PHPMailer(); //实例化
        
        $mail->IsSMTP(); // 启用SMTP
        $mail->Host = 'smtp.qq.com'; //SMTP服务器（QQ邮箱）
        $mail->Port = 465;  //邮件发送端口
        $mail->SMTPAuth = true;  //启用SMTP认证
        $mail->SMTPSecure = "ssl";   // 设置安全验证方式为ssl

        $mail->CharSet = "UTF-8"; //字符集
        $mail->Encoding = "base64"; //编码方式

        $mail->Username = '508401036@qq.com';  //发送方邮箱
        $mail->Password = 'euqmfqaocooxbgib';  //授权码
        $mail->Subject = '申请机房'; //邮件标题

        $mail->From = '508401036@qq.com';  //发件人地址（也就是你的邮箱）
        $mail->FromName = 'jxxy';  //发件人姓名

        $mail->AddAddress('508401036@qq.com', "亲"); //添加收件人（地址，昵称）
        $mail->IsHTML(true); //支持html格式内容
        $mail->Body = "有人申请机房，请及时审核"; //邮件主体内容

        //发送成功就删除
        if ($mail->Send()) {
           	$this->redirect("RoomApply/succeed");
        }else{
        	$this->redirect("RoomApply/index");      	
        }
    
    }
    
    
    /*获取当前属于第几周
     */
    function current_week($date_of_firstday){
    	//开学第一天的时间戳
		$year1 = substr($date_of_firstday,0,4);
		$month1 = substr($date_of_firstday,5,1);
		$day1 = substr($date_of_firstday,7,2);
		$time_chuo_of_first_day = mktime(0,0,0,$month1,$day1,$year1);
		
		//要计算的时间戳
		$month2 = date('n'); //获取月 n	
		$day2 = date('d'); //获取日 d	
		$year2 = date('Y'); //获取年 Y	
		$time_chuo_of_current_day = mktime(0,0,0,$month2,$day2,$year2);	
		$cha = ($time_chuo_of_current_day-$time_chuo_of_first_day)/60/60/24;	
		$zhou = (int)($cha/7 +1);		
		return $zhou;
	}

	
}
?>