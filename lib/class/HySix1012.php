<?php
/*
 * 客户端存储，获取存储数据
 */

class HySix1012 extends HySix{
	
	private $box;
	private $key1;
	
	//数据的初始化
	function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->box  = isset($input_data['box']) ? trim($input_data['box']) : '';
		$this->key1 = isset($input_data['key1']) ? trim($input_data['key1']) : '';
		
		
	}
	
	
	public function controller_clientexec(){
		
		$sql_where = '';
		if(''!==(string)$this->box) {
			$sql_where .= " and box='".$this->box."'";
		}
		if(''!==(string)$this->key1) {
			$sql_where .= " and key1 like '".$this->key1."%'";
		}
		
		$sql_clientdata = "select id,userid,box,key1,val1,val2,val3 
							from client_data where userid='".parent::__get('userid')."' 
							".$sql_where." order by box,key1";
		$list_clientdata = parent::__get('HyDb')->get_all($sql_clientdata);
		
		
		$retarr = array();
		
		foreach($list_clientdata as $valcd) {
			$valcd['val1'] = base64_decode($valcd['val1']);
			$valcd['val2'] = base64_decode($valcd['val2']);
			$valcd['val3'] = base64_decode($valcd['val3']);
			$tmpbox = array($valcd['id'],$valcd['box'],$valcd['key1'],$valcd['val1'],$valcd['val2'],$valcd['val3'],);
			array_push($retarr,$tmpbox);
			
		}
		
		$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$retarr);
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