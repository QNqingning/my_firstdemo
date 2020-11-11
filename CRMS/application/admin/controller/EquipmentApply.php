<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;

class EquipmentApply extends Lock{
	public function index(){
		$time = date("Y-m-d");
		$this->assign("time",$time);
		return view();
	}
	
	public function apply(){
		$data = input("post.");
		if($data){
			if(!db("equipment_apply")->where('application',$data['application'])->where('time',$data['time'])->where('equipment',$data['equipment'])->find()){
				$arr[] = [
					'application' => $data['application'],
					'phone' => $data['phone'],
					'time' => $data['time'],
					'equipment' => $data['equipment'],
					'reason' => $data['reason'],
					'check_result' => '待审核',
				];				
				if(db("equipment_apply")->insertAll($arr)){
					$this->redirect("EquipmentApply/SendMail");
				}
			}			
		}
	}
	
	//申请成功
	public function succeed(){
		$user = session("name");
		$data = db("equipment_apply")->where("application",$user)->order("id DESC")->select();
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
        $mail->Subject = '申请设备'; //邮件标题

        $mail->From = '508401036@qq.com';  //发件人地址（也就是你的邮箱）
        $mail->FromName = 'jxxy';  //发件人姓名

        $mail->AddAddress('508401036@qq.com', "亲"); //添加收件人（地址，昵称）
        $mail->IsHTML(true); //支持html格式内容
        $mail->Body = '有人申请设备，请及时审核'; //邮件主体内容

        //发送成功就删除
        if ($mail->Send()) {
       		$this->redirect("EquipmentApply/succeed");
        }else{
       		$this->redirect("EquipmentApply/index");
        }
	}
	
}
?>