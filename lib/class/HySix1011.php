<?php
/*
 * 客户端存储，获取盒子分类
 */

class HySix1011 extends HySix{
	
	
	
	//数据的初始化
	function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
	}
	
	
	public function controller_clientexec(){
		
		$sql_clientgroup = "select box,count(id) as con from client_data where userid='".parent::__get('userid')."' group by box order by box";
		$list_clientgroup = parent::__get('HyDb')->get_all($sql_clientgroup);
		
		$retarr = array();
		
		foreach($list_clientgroup as $valcg) {
			$tmpbox = array($valcg['box'],$valcg['con'],);
			array_push($retarr,$tmpbox);
			
		}
		
		$echojsonstr = HyItems::echo2clientjson('100','box数据获取成功',$retarr);
		parent::hy_log_str_add($echojsonstr."\n");
		echo $echojsonstr;
		return true;
		
		
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