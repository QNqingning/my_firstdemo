<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;

class Repair extends Lock{
	public function index(){
		$time = date("Y-m-d");
		$this->assign("time",$time);
		return view();
	}	
	
	public function apply(){
		$data  = input("post.");
		$file = request()->file('image');
		if($file){
			$info = $file->move(ROOT_PATH.'/public/uploads/images');
			$img =  $info->getSaveName();
			$data['image']= $img;
		}
		$data['repair_result']= '待维修';
		if(db("repair")->insert($data)){		
//			$data[] = ['user_email'=>'508401036@qq.com','content'=>'有设备出现故障，请及时维修'];//user_email：收邮件人邮箱号，content：发送的信息
	    	$this->redirect("Repair/SendMail");
		}
	}
	
	//申请成功
	public function succeed(){
		$user = session("name");
		$data = db("repair")->where("application",$user)->order("id DESC")->select();
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
        $mail->Subject = '设备故障'; //邮件标题

        $mail->From = '508401036@qq.com';  //发件人地址（也就是你的邮箱）
        $mail->FromName = 'jxxy';  //发件人姓名

        $mail->AddAddress('508401036@qq.com', "亲"); //添加收件人（地址，昵称）
        $mail->IsHTML(true); //支持html格式内容
        $mail->Body = '有设备出现异常，请及时查看'; //邮件主体内容
        
        //发送成功就删除
        if ($mail->Send()) {
       		$this->redirect("Repair/succeed");
        }else{
       		$this->redirect("Repair/index");
        }
    }
}

?>