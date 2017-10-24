<?php
/*
 * 短信验证码登录--验证码下发
 */
class HySix1001 extends HySix{
	
	
	private $phone;
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
	
		//接受验证码的手机号
		$this->phone = isset($input_data['phone']) ? $input_data['phone']:'';  //
	
	}
	
	
	//短信下发验证码--操作
	protected function controller_sendcode(){
		
		//判断手机号是否正确
		$rightphone = parent::hy_yunyingshangcheck($this->phone);
		if($rightphone===false){
			$echojsonstr = HyItems::echo2clientjson('100','手机号码格式不正确');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
		//生成6位短信验证码
		$vcode = parent::func_create_tempvcode();
		
		//获取验证码的短信内容
		$message = parent::func_create_vcode_message($type='1',$vcode);
		
		//发送验证码
		if($vcode!=''){
			$r = parent::func_send_sms($type='1',$this->phone,$vcode,$message);
			if($r===true){
				$echojsonstr = HyItems::echo2clientjson('100','验证码下发成功');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return true;
			}else{
				//在父类中返回
				return false;
			}
		}
	}
	
	
	
	//操作入口--短信下发验证码
	public function controller_init(){
	
		//判断手机号是否为空
		if($this->phone==''){
			$echojsonstr = HyItems::echo2clientjson('100','手机号不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
		if( !is_numeric($this->phone) || strlen($this->phone)!='11'){
			$echojsonstr = HyItems::echo2clientjson('100','手机号码格式不正确');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
		//下发短信验证码函数调用
		$this->controller_sendcode();
	
		return true;
	}
	
	
	
	
}
