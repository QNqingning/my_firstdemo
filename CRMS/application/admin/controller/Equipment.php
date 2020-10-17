<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;


class Equipment extends Lock{
	public function index(){
		$search = input("get.search");
//		$list = db("equipment")->where('administrator',$search)->whereOr('number',$search)->whereOr('name',$search)->order("id ASC")->paginate(10,false,['query'=>request()->param()]);
//		$total = db("equipment")->where('administrator',$search)->whereOr('number',$search)->whereOr('name',$search)->count();			
		$list = db("equipment")->order("id ASC")->paginate(10,false,['query'=>request()->param()]);
		$total = db("equipment")->count();
		// 将数据分配给页面
		$this->assign("list",$list);
		$this->assign("total",$total);
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
                $file_name = ROOT_PATH . 'public'.DS.'uploads' . DS  . $exclePath;//上传文件的地址
                $objReader =\PHPExcel_IOFactory::createReader("Excel2007");
                $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array=$obj_PHPExcel->getSheet(0)->toArray();   //转换为数组格式
                array_shift($excel_array);  //删除第一个数组(标题);                
//              $arr = array_chunk($excel_array,10); 
                
                $city = [];
                $i=1;
                
                $num = count($excel_array);
//              var_dump($num);
//              exit;
                
                
                foreach($excel_array as $k=>$v) {

 					$city[$k]['dept'] = $v[1];
                    $city[$k]['kind'] = $v[2];
                    $city[$k]['number'] = $v[3];
                    $city[$k]['name'] = $v[4];
                    $city[$k]['brand'] = $v[5];
                    $city[$k]['model'] = $v[6];
                    $city[$k]['amount'] = $v[8];
                    $city[$k]['cost'] = $v[9];
                    $city[$k]['gain'] = $v[10];
                    $city[$k]['place'] = $v[11];
                    $city[$k]['use_department'] = $v[12];
                    $city[$k]['administrator'] = $v[13];
                    $city[$k]['old_number'] = $v[14];
                    $city[$k]['old_use'] = $v[15];

                    $i++;
                }

				db("equipment")->insertAll($city);
				$this->redirect('Equipment/index');
            }else{
            	$this->error('上传失败');
            }
        }
	}
	
	public function add(){
		$time = date("Y-m-d");
		$this->assign("time",$time);
		return view();
	}
	
	public function addEquipment(){
		$data = input('post.');
		var_dump($data);
		if($data){
			$arr[] = [
				'dept' => $data['dept'],
				'kind' => $data['kind'],
				'number' => $data['number'],
				'name' => $data['name'],
				'brand' => $data['brand'],
				'model' => $data['model'],
				'amount' => $data['amount'],
				'cost' => $data['cost'],
				'gain' => $data['gain'],
				'place' => $data['place'],
				'use_department' => $data['use_department'],
				'administrator' => $data['administrator'],
				'old_number' => $data['old_number'],
				'old_use' => $data['old_use'],
			];
			
			$add = db("equipment")->insertAll($arr);
			if($add){
				$this->redirect('Equipment/index');
			}
 		}
	}
	
	public function edit(){
		// 接收要修改的ID
		$id = input("get.id");

		// 从数据库中查找出对应的对象
		$data = db("equipment")->find($id);

		// 将数据转换成json
		$this->assign("data",$data);

		//加载页面
		return view();
	}
	
	
	public function update(){
		//获取表单的数据
		$post = input("post.");
		
		$data = [
				'place' => $post['place'],
				'use_department' => $post['use_department'],
				'administrator' => $post['administrator'],
				'old_use' => $post['old_use'],
			];
		//把接收到要修改数据为空的项去掉
		$arr = array_filter($data);

		//在数据库中修改
		if(db("equipment")->where('id',$post['id'])->update($arr)){
			$this->redirect('Equipment/index');
		}else{
			$this->error("修改失败");
		}		
	}
	
	public function del(){
		// 接收要删除的ID
		$id = input("get.str");

		// 判断是否删除成功
		if (db("equipment")->delete($id)) {
			echo 1;
		}else{
			echo 0;
		}	
	}
	
	
}
?>