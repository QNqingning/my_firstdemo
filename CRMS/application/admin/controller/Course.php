<?php
namespace app\admin\controller;
use think\Controller;
use Think\Db;
use think\Request;

class Course extends Lock{
	public function index(){	
		$list = db("course")->select();	
		$total = db("course")->count();
		$exist = db("syllabus")->where('message', 'not null' )->count();
//		var_dump($list);
//		
		
//		print_r($list);	
		$this->assign("list",$list);	
		$this->assign("total",$total);	
		$this->assign("exist",$exist);	
		
		return view();
	}
	
	
	//上传文件
	public function excel(){
		if(request() -> isPost())
        {
            vendor("PHPExcel.PHPExcel"); 
            $objPHPExcel =new \PHPExcel();
            //获取表单上传文件
            $file = request()->file('excel');
            $info = $file->validate(['ext' => 'xlsx'])->move(ROOT_PATH . 'public'.DS.'uploads');  //上传验证后缀名,以及上传之后移动的地址  E:\wamp\www\cls\public
            if($info)
            {
//              echo $info->getFilename();
                $exclePath = $info->getSaveName();  //获取文件名
                $file_name = ROOT_PATH . 'public'.DS.'uploads' . DS . $exclePath;//上传文件的地址
                $objReader =\PHPExcel_IOFactory::createReader("Excel2007");
                $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array=$obj_PHPExcel->getSheet(0)->toArray();   //转换为数组格式
                array_shift($excel_array);  //删除第一个数组(标题);
                $city = [];
                $i=0;
                
                foreach($excel_array as $k=>$v) {
                    $city[$k]['f1'] = $v[0];
                    $city[$k]['f2'] = $v[1];
                    $city[$k]['f3'] = $v[2];
                    $city[$k]['f4'] = $v[3];
                    $city[$k]['f5'] = $v[4];
                    $city[$k]['f6'] = $v[5];
                    $city[$k]['f7'] = $v[6];
                    $city[$k]['f8'] = $v[7];
                    $city[$k]['f9'] = $v[8];
                    $city[$k]['f10'] = $v[9];
                    $city[$k]['f11'] = $v[10];
                    $city[$k]['f12'] = $v[11];
                    $city[$k]['f13'] = $v[12];
                    $city[$k]['f14'] = $v[13];
                    $city[$k]['f15'] = $v[14];
                    $city[$k]['f16'] = $v[15];
                    $city[$k]['f17'] = $v[16];
                    $city[$k]['f18'] = $v[17];
                    $city[$k]['f19'] = $v[18];
                    $city[$k]['f20'] = $v[19];
                    $city[$k]['f21'] = $v[20];
                    $city[$k]['f22'] = $v[21];
                    $city[$k]['f23'] = $v[22];
                    $city[$k]['f24'] = $v[23];
                    $city[$k]['f25'] = $v[24];
                    $city[$k]['f26'] = $v[25];
                    $city[$k]['f27'] = $v[26];
                    $city[$k]['f28'] = $v[27];
                    $city[$k]['f29'] = $v[28];
                    $i++;
                }
                db("course")->insertAll($city);               
                $this->redirect('Course/index');
            }else
            {
                echo $file->getError();
            }
        }
	}
	
	public function save(){
		$major = $_POST['major'];
		$day = $_POST['day'];
		$lesson = $_POST['lesson'];
		$message = $_POST['message'];
		
//		var_dump($day,$lesson);
		
		$where = [];
		
		$number = array('402','404','405','409','410','505','509','610','510');
		$day1 = array('星期一','星期二','星期三','星期四','星期五','星期六','星期日');
		$lesson1 = array('1-2','3-5','6-7','8-9','10-11','12-12');

		for($a=0; $a<count($number); $a++){
			for($b=0; $b<count($day1); $b++){
				for($c=0; $c<count($lesson1); $c++){
					$data[] = [
						'number' => $number[$a],
						'day' => $day1[$b],
						'lesson' => $lesson1[$c],
					];
	 			}
			}
		}
		
		if(!db("syllabus")->select()){
			db("syllabus")->insertAll($data);
		}
		
		for($i=0; $i<count($major); $i++){
			if($day[$i]==''){
				$day[$i]=$day[$i-1];
			}
			$where['day']=$day[$i];
			$where['lesson']=$lesson[$i];
			for($j=0; $j<count($number); $j++){
				$where['number']=$number[$j];
				if(strpos($message[$i],$number[$j]) && db("syllabus")->where($where)->find()){
					db("syllabus")->where($where)->setField('message',$message[$i]);
				}
			}
			
		}
		
		$this->redirect('Course/index');
		
	}
	
	
	public function syllabus(){
		
		$search = input("get.search");
	
		$data = db("syllabus")->where("message like '%$search%'")->whereOr("day like '$search'")->whereOr("lesson like '%$search%'")->select();
		$total = db("syllabus")->where("message like '%$search%'")->whereOr("day like '$search'")->whereOr("lesson like '%$search%'")->count();
			
			
		$this->assign("data",$data);
		$this->assign("total",$total);	
		return view();	
	}
	


}
?>