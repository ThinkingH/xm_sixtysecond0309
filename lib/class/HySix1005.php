<?php
/**
 * 用户的信息获取
 */

class HySix1005 extends HySix{
	
	
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
	
	}
	
	
	protected function controller_getuserinfo(){
		
		$userlistdata = parent::__get('userlistdata');
		
		$retarr = array(
				'id',
				'phone',
				'sex',
				'birthday',
				'nickname',
				'touxiang',
				'describes',
				'create_datetime',
		);
		$newuserlist = array();
		foreach($userlistdata as $keyu => $valu) {
			if(in_array($keyu, $retarr)) {
				$newuserlist[$keyu] = (string)$valu;
			}
		}
		
		if($newuserlist['touxiang']!='') {
			if(substr($newuserlist['touxiang'],0,4)!='http') {
				//拼接七牛云头像链接
				$newuserlist['touxiang'] = HyItems::hy_qiniuimgurl('sixty-user',$newuserlist['touxiang'],100,100,true);
			}else {
				//链接为微信的，不做处理
			}
		}
		
		$echojsonstr = HyItems::echo2clientjson('100','信息获取成功',$newuserlist);
		parent::hy_log_str_add($echojsonstr."\n");
		echo $echojsonstr;
		return true;
		
		
	}
	
	
	//用户信息--操作入口
	public function controller_init(){
		
		//判断正式用户通讯校验参数
		$r = parent::func_oneusercheck();
		if($r===false){
			return false;
		}
		
		//用户信息获取入口
		$this->controller_getuserinfo();
	
		return true;
	
	
	}
	
	
	
	
}