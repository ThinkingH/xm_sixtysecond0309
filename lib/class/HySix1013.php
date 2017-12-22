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
		//按逗号把字符串转为数组
		$delidarr1 = explode(',',$this->delid);
		//遍历数组
		foreach($delidarr1 as $val1) {
			$val1 = trim($val1);
			//判断键值是否为数字且数值大约0
			if(is_numeric($val1) && $val1>0) {//符合条件
                //把数据压入新数组
				array_push($delidarr,$val1);
			}
		}

		//判断数组是否为空
		if(count($delidarr)<=0) {//数组为空
            //数据转为json，写入日志并输出
			$echojsonstr = HyItems::echo2clientjson('101','数据删除失败，id数据为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}else {//数组不为空
            //查询数据库
			$sql_getdata = "select count(id) as con from client_data where userid='".parent::__get('userid')."' and id in (".implode(',',$delidarr).")";
			$list_getdata = parent::__get('HyDb')->get_one($sql_getdata);

			//判断查询结果是否为空
			if($list_getdata<=0) {//结果为空
                //数据转为json，写入日志并输出
				$echojsonstr = HyItems::echo2clientjson('101','数据删除失败，未找到符合删除数据');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
			}else {//结果不为空

                //执行删除
				$sql_deletedata = "delete from client_data where userid='".parent::__get('userid')."' and id in (".implode(',',$delidarr).")";
				parent::__get('HyDb')->execute($sql_deletedata);

                //数据转为json，写入日志并输出
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