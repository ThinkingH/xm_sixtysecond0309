<?php
/*
 * 版本的升级
 * 
 */

class HySix1009 extends HySix{
	
	
	//数据的初始化
	function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
	
	}
	
	
	public function controller_shengji(){

	    //获取数据库版本信息
		$sql_version = "select id,system,version,uptype,apk_url,updesc,update_date from sixty_versioninfo where flag='1' and system='".parent::__get('system')."' order by id desc limit 1";
		$list_version = parent::__get('HyDb')->get_row($sql_version);

		//判断获取信息是否为空
		if(count($list_version)<=0) {//数据为空

            //数据转为json，写入日志并输出
			$echojsonstr = HyItems::echo2clientjson('101','版本信息获取失败');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}else {//数据不为空

            //数据转为json，写入日志并输出
			$echojsonstr = HyItems::echo2clientjson('100','版本信息获取成功',$list_version);
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
		}
		
		
	}
	
	
	
	
	//操作入口
	public function controller_init(){
		
		$this->controller_shengji();
	
		return true;
	
	}
	
	
}