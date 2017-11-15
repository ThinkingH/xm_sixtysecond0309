<?php
/*
 * 用户意见反馈的提交
 */

class HySix1006 extends HySix{
	
	private $yijian;
	private $contact; //联系方式
	
	
	//数据的初始化
	function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
		//意见
		$this->yijian   = isset($input_data['yijian'])? $input_data['yijian']:'';  //用户提交的意见
		$this->contact   = isset($input_data['contact'])? $input_data['contact']:'';  //用户提交的意见
	
	}
	
	
	//操作入口
	protected function controller_gesuggest(){
		
		$usertype = parent::__get('usertype');
		if($usertype=='') {
			$usertype = 0;
		}
		$userid = parent::__get('userid');
		if($userid=='') {
			$userid = 0;
		}
		
		
		
		//数据库入库
		$yijian_sql  = "insert into sixty_yijian(type,userid,contact,content,create_datetime) values 
				      ('".$usertype."','".$userid."','".$this->contact."','".$this->yijian."','".date('Y-m-d H:i:s')."')";
		$yijian_list = parent::__get('HyDb')->execute($yijian_sql);
		
		$echojsonstr = HyItems::echo2clientjson('100','意见提交成功');
		parent::hy_log_str_add($echojsonstr."\n");
		echo $echojsonstr;
		return true;
		
	}
	
	
	//用户意见反馈操作入口
	public function controller_init(){
	
		//判断正式用户通讯校验参数
// 		$r = parent::func_oneusercheck();
// 		if($r===false){
// 			return false;
// 		}
		
		
	
		//判断yijian提交的参数是否为空
		if($this->yijian==''){
			$echojsonstr = HyItems::echo2clientjson('101','反馈意见不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
		}
		//判断contact提交的参数是否为空
		if($this->contact==''){
			$echojsonstr = HyItems::echo2clientjson('101','联系方式不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
		}
		
		//进行意见反馈操作
		$this->controller_gesuggest();
	
		return true;
	
	
	}
	
	
}