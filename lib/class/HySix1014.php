<?php
/*
 * 客户端存储，添加更新存储数据
 */

class HySix1014 extends HySix{
	
	private $box;
	private $key1;
	private $val1;
	private $val2;
	private $val3;
	
	//数据的初始化
	function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->box  = isset($input_data['box']) ? trim($input_data['box']) : '';
		$this->key1  = isset($input_data['key1']) ? trim($input_data['key1']) : '';
		$this->val1  = isset($input_data['val1']) ? trim($input_data['val1']) : '';
		$this->val2  = isset($input_data['val2']) ? trim($input_data['val2']) : '';
		$this->val3  = isset($input_data['val3']) ? trim($input_data['val3']) : '';
		
		if((string)$this->box==='') {
			$this->box = 'none';
		}
		
	}
	
	
	public function controller_clientexec(){
		
		$pattern16 = '/^[a-zA-Z0-9_-]{1,16}$/';
		$pattern32 = '/^[a-zA-Z0-9_-]{1,32}$/';
		
		preg_match($pattern16, $this->box, $matches_box);
		if(empty($matches_box)) {
			$echojsonstr = HyItems::echo2clientjson('100','数据添加失败，box必须由字母数字下划线构成，长度16位以内');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		preg_match($pattern32, $this->key1, $matches_key1);
		if(empty($matches_key1)) {
			$echojsonstr = HyItems::echo2clientjson('100','数据添加失败，key1必须由字母数字下划线构成，长度32位以内');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
		$this->val1 = base64_encode($this->val1);
		$this->val2 = base64_encode($this->val2);
		$this->val3 = base64_encode($this->val3);
		
		//查询该数据是否存在，存在则更新
		$sql_haspan = "select id from client_data where userid='".parent::__get('userid')."' and key1='".$this->key1."' order by id desc limit 1";
		$list_haspan = parent::__get('HyDb')->get_one($sql_haspan);
		
		if($list_haspan>0) {
			//更新
			$sql_update  = "update client_data set box='".$this->box."',val1='".$this->val1."',val2='".$this->val2."',val3='".$this->val3."' where
							id='".$list_haspan."' and userid='".parent::__get('userid')."' and key1='".$this->key1."'";
			parent::__get('HyDb')->execute($sql_update);
		}else {
			//插入
			$sql_insert = "insert into client_data (userid,box,key1,val1,val2,val3) values('".parent::__get('userid')."','".$this->box."','".$this->key1."','".$this->val1."','".$this->val2."','".$this->val3."')";
			parent::__get('HyDb')->execute($sql_insert);
			
		}
		
		
		
		//获取该数据id
		$sql_haspan = "select id from client_data where userid='".parent::__get('userid')."' and key1='".$this->key1."' order by id desc limit 1";
		$list_haspan = parent::__get('HyDb')->get_one($sql_haspan);
		
		if($list_haspan<=0) {
			$echojsonstr = HyItems::echo2clientjson('100','数据添加更新失败，系统错误');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}else {
			$echojsonstr = HyItems::echo2clientjson('100','数据添加成功',array($list_haspan));
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return true;
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