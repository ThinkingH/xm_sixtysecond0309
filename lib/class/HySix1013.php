<?php
/*
 * 客户端存储，删除存储数据
 */

class HySix1013 extends HySix{
	
	private $delid;
	
	//数据的初始化
	function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->delid  = isset($input_data['delid']) ? trim($input_data['delid']) : '';
		
		
	}
	
	
	public function controller_clientexec(){
		
		$delidarr = array();
		$delidarr1 = explode(',',$this->delid);
		foreach($delidarr1 as $val1) {
			$val1 = trim($val1);
			if(is_numeric($val1) && $val1>0) {
				array_push($delidarr,$val1);
			}
		}
		if(count($delidarr)<=0) {
			$echojsonstr = HyItems::echo2clientjson('100','数据删除失败，id数据为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}else {
			$sql_getdata = "select count(id) as con from client_data where userid='".parent::__get('userid')."' and id in (".implode(',',$delidarr).")";
			$list_getdata = parent::__get('HyDb')->get_one($sql_getdata);
			
			if($list_getdata<=0) {
				$echojsonstr = HyItems::echo2clientjson('100','数据删除失败，未找到符合删除数据');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
			}else {
				$sql_deletedata = "delete from client_data where userid='".parent::__get('userid')."' and id in (".implode(',',$delidarr).")";
				parent::__get('HyDb')->execute($sql_deletedata);
				
				$echojsonstr = HyItems::echo2clientjson('100','数据删除成功，共'.$list_getdata.'条');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return true;
				
			}
			
			
		}
		
		
	}
	
	
	
	
	public function controller_init(){
		
		//判断正式用户通讯校验参数
		$r = parent::func_oneusercheck();
		if($r===false){
			return false;
		}
		
		
		$this->controller_clientexec();
	
		return true;
	
	
	}
	
}