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

	    //获取极光ID
		$jiguangid = trim($this->jiguangid);

        //判断极光ID是否为空
		if($jiguangid!=''){//极光ID不为空

			//更新用户表插入该字段
			$tuisong_sql = "update sixty_user set jiguangid='".$jiguangid."' where id='".parent::__get('userid')."'";
			$tuisong_list = parent::__get('HyDb')->execute($tuisong_sql);

            //数据转为json，写入日志并输出
			$echojsonstr = HyItems::echo2clientjson('100','极光id关联成功');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return true;
			
		}else{//极光ID为空

            //数据转为json，写入日志并输出
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