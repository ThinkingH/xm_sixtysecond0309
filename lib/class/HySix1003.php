<?php
/*
 * 极光推送用户关联id
 */

class HySix1003 extends HySix{
	
	private $jiguangid;
	
	//数据的初始化
	function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
		$this->jiguangid   = isset($input_data['jiguangid'])? $input_data['jiguangid']:'';  //极光id
	
	}
	
	
	public function controller_jiguang(){
		
		$jiguangid = trim($this->jiguangid);
		
		if($jiguangid!=''){
			
			
			//更新用户表插入该字段
			$tuisong_sql = "update sixty_user set jiguangid='".$jiguangid."' where id='".parent::__get('userid')."'";
			$tuisong_list = parent::__get('HyDb')->execute($tuisong_sql);
			
			$echojsonstr = HyItems::echo2clientjson('100','极光id关联成功');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return true;
			
		}else{
			$echojsonstr = HyItems::echo2clientjson('101','极光关联id不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
		}
		
	}
	
	
	
	//用户意见反馈操作入口
	public function controller_init(){
	
		//判断正式用户通讯校验参数
		$r = parent::func_oneusercheck();
		if($r===false){
			return false;
		}
		
	
		//进行意见反馈操作
		$this->controller_jiguang();
	
		return true;
	
	
	}
}