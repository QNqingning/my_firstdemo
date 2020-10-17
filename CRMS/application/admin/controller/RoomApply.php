<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;

class RoomApply extends Lock{
	public function index(){
		$day = input("get.day");
		$lesson = input("get.lesson");
		$capacity = input("get.capacity");
		$search = input("get.search");
		
		$list = [];
		if($day && $lesson){
			switch($capacity){
				case 50:
					$list = db("syllabus")->whereNull('message')->where('capacity','eq',50)->where(["day"=>$day,"lesson"=>$lesson])->field("syllabus.*,room.roomName,capacity")
					->join("room","room.floor = syllabus.number")->select();
				break;
				case 60:			
					$list = db("syllabus")->whereNull('message')->where('capacity','between','50,60')->where(["day"=>$day,"lesson"=>$lesson])->field("syllabus.*,room.roomName,capacity")
					->join("room","room.floor = syllabus.number")->select();				
				break;
				
				case 70:			
					$list = db("syllabus")->whereNull('message')->where('capacity','between','60,70')->where(["day"=>$day,"lesson"=>$lesson])->field("syllabus.*,room.roomName,capacity")
					->join("room","room.floor = syllabus.number")->select();
				break;
				
				case 80:	
					$list = db("syllabus")->whereNull('message')->where('capacity','between','80,90')->where(["day"=>$day,"lesson"=>$lesson])->field("syllabus.*,room.roomName,capacity")
					->join("room","room.floor = syllabus.number")->select();
				break;
				
				case 90:	
					$list = db("syllabus")->whereNull('message')->where('capacity','egt',90)->where(["day"=>$day,"lesson"=>$lesson])->field("syllabus.*,room.roomName,capacity")
					->join("room","room.floor = syllabus.number")->select();
				break;
				
				default:
					$list = db("syllabus")->whereNull('message')->where(["day"=>$day,"lesson"=>$lesson])->field("syllabus.*,room.roomName,capacity")
					->join("room","room.floor = syllabus.number")->select();
				break;
			}
		}else{
			if($day){
				$list = db("syllabus")->whereNull('message')->where('day',$day)->field("syllabus.*,room.roomName,capacity")
					->join("room","room.floor = syllabus.number")->select();
			}else if($lesson){
				$list = db("syllabus")->whereNull('message')->where('lesson',$lesson)->field("syllabus.*,room.roomName,capacity")
					->join("room","room.floor = syllabus.number")->select();
			}else {
				$list = db("syllabus")->whereNull('message')->where('number',$search)->field("syllabus.*,room.roomName,capacity")
					->join("room","room.floor = syllabus.number")->select();
			}
		}

		$this->assign("day",$day);
		$this->assign("lesson",$lesson);
		$this->assign("capacity",$capacity);
		$this->assign("list",$list);
		return view();
	}
	
	//要申请的机房	
	public function apply(){
		// 接收要修改的ID
		$id = input("get.id");	
		// 从数据库中查找出对应的对象
		$data = db("syllabus")->find($id);	
		// 将数据转换成json
		$time = date("Y-m-d");
		$this->assign("time",$time);
		$this->assign("data",$data);	
		//加载页面
		return view();
	}
	
	//申请机房数据
	public function list(){
		$post = input("post.");
		
		if($post){
			$arr[] = [
				'unit' => $post['unit'],
				'application' => $post['application'],
				'phone' => $post['phone'],
				'time' => $post['time'],
				'reason' => $post['reason'],
				'use_number' => $post['number'],
				'use_day' => $post['day'],
				'use_lesson' => $post['lesson'],
				'check_result' => '待审核',
			];
//			db("room_apply")->insertAll($arr);
			if(db("room_apply")->insertAll($arr)){
				$data[] = ['user_email'=>'508401036@qq.com','content'=>'有人申请机房，请及时审核'];//user_email：收邮件人邮箱号，content：发送的信息
	    		$this->SendMail($data);
			}else{
				$this->error('申请失败');
			}
		}
	}
	
	//发送邮件
	function SendMail($data=[]){
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
        $mail->Subject = '邮件标题'; //邮件标题

        $mail->From = '508401036@qq.com';  //发件人地址（也就是你的邮箱）
        $mail->FromName = 'jxxy';  //发件人姓名

        if($data && is_array($data)){
            foreach ($data as $k=>$v){
                $mail->AddAddress($v['user_email'], "亲"); //添加收件人（地址，昵称）
                $mail->IsHTML(true); //支持html格式内容
                $mail->Body = $v['content']; //邮件主体内容

                //发送成功就删除
                if ($mail->Send()) {
                   		$this->redirect("RoomApply/index");
                }
            }
        }
    }

	
}
?>