<?php
/*
 * 用户信息修改
 */

class HySix1007 extends HySix{
	
	private $sex;
	private $birthday;
	private $nickname;
	private $describes;
	
	//数据的初始化
	function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
		$this->sex      = isset($input_data['sex'])? $input_data['sex']:'';  
		$this->birthday = isset($input_data['birthday'])?$input_data['birthday']:'';
		$this->nickname = isset($input_data['nickname'])?$input_data['nickname']:'';
		$this->describes = isset($input_data['describes'])?$input_data['describes']:'';
	
	}
	
	
	protected function controller_edituserinfo(){
		
		if($this->sex!='' || $this->birthday!='' || $this->nickname!='' || $this->describes!=''){
			
			$useredit_sql = "update sixty_user set ";
			
			if($this->sex!=''){
				$useredit_sql .= " sex='".$this->sex."', ";
			}
			if($this->birthday!=''){
				$useredit_sql .= " birthday='".$this->birthday."', ";
			}
			if($this->nickname!=''){
				$useredit_sql .= " nickname='".$this->nickname."', ";
			}
			if($this->describes!=''){
				$useredit_sql .= " describes='".$this->describes."', ";
			}
			
			$useredit_sql = rtrim($useredit_sql,', ');
			$useredit_sql .= " where id='".parent::__get('userid')."' and tokenkey='".parent::__get('userkey')."' ";
			
			$useredit_list = parent::__get('HyDb')->execute($useredit_sql);
			parent::hy_log_str_add($useredit_sql."\n");
			
			$echojsonstr = HyItems::echo2clientjson('100','信息修改成功');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return true;
			
			
		}else{
			$echojsonstr = HyItems::echo2clientjson('100','修改参数为空，无法执行修改');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return true;
			
		}
		
	}
	
	
	
	
	//操作入口--用户信息修改，正常用户功能
	public function controller_init(){
		
		//判断正式用户通讯校验参数
		$r = parent::func_oneusercheck();
		if($r===false){
			return false;
		}
		
		
		//用户信息修改入口
		$this->controller_edituserinfo();
	
		return true;
	
	
	}
	
	
	
	
}