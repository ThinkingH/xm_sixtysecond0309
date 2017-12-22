<?php
/*
 * 用户登录--提交验证码
 */

class HySix1002 extends HySix{
	
	private $phone;
	private $vcode;
	
	//数据的初始化
	public function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
	
		//接受验证码的手机号
		$this->phone = isset($input_data['phone']) ? $input_data['phone']:'';  //
	
		//接受验证码
		$this->vcode = isset($input_data['vcode']) ? $input_data['vcode']:'';  //
	
	}
	
	
	//校验验证码操作
	protected function controller_checkcode(){
		
		//验证码校验函数
		$r = parent::func_vcode_check($type='1',$this->phone,$this->vcode);
		
		if($r===true) {
			//数据库中查询用户信息
			$userregistersql  = "select id,tokenkey,nickname,touxiang from sixty_user where phone='".$this->phone."'";
			$userregisterlist = parent::__get('HyDb')->get_row($userregistersql);

			//判断用户信息查询结果
			if(count($userregisterlist)>0){//用户信息查询成功

                //用户数据存入输出数组
				$userarr = array(
						'userid' => $userregisterlist['id'],
						'userkey'=> $userregisterlist['tokenkey'],
						
				);

				//判断用户昵称和用户头像是否为空，判断用户是否首次登陆
				if(''==$userregisterlist['nickname'] || ''==$userregisterlist['touxiang']) {//首次登陆
					$userarr['firstlogin'] = 'yes';
				}else {//不是首次登陆
					$userarr['firstlogin'] = 'no';
				}
				
				$echojsonstr = HyItems::echo2clientjson('100','登录成功',$userarr);
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return true;
				
			}else{//该用户首次登录，数据插入到用户表中
				
				//随机生成的userkey
				$userkey = parent::func_create_randkey();
				
				//数据的插入
				$userdatasql = "insert into sixty_user (phone,tokenkey,create_datetime)
									values ('".$this->phone."','".$userkey."','".date('Y-m-d H:i:s')."')";
				$userdatalist = parent::__get('HyDb')->execute($userdatasql);

				//查询刚刚插入的数据信息
				$useridsql = "select id,tokenkey from sixty_user where phone='".$this->phone."' order by id desc limit 1";
				$useridlist = parent::__get('HyDb')->get_row($useridsql);
				
				
				if(count($useridlist)>0){//数据插入成功

                    //数据插入输出数组
					$userarr = array(
							'userid' => $useridlist['id'],
							'userkey'=> $useridlist['tokenkey'],
							'firstlogin'=> 'yes',
					);

					//数据转为json，写入日志并输出
					$echojsonstr = HyItems::echo2clientjson('100','登录成功',$userarr);
					parent::hy_log_str_add($echojsonstr."\n");
					echo $echojsonstr;
					return true;
					
				}else{//数据插入失败

                    //数据转为json，写入日志并输出
					$echojsonstr = HyItems::echo2clientjson('101','登录失败，系统错误');
					parent::hy_log_str_add($echojsonstr."\n");
					echo $echojsonstr;
					return false;
					
				}
				
			}
			
			
		}
		
		
	}
	
	
	
	
	//操作入口--提交验证码
	public function controller_init(){
	
		//判断手机号是否为空
		if($this->phone==''){
			$echojsonstr = HyItems::echo2clientjson('101','手机号不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
		if( !is_numeric($this->phone) || strlen($this->phone)!='11'){
			$echojsonstr = HyItems::echo2clientjson('101','手机号码格式不正确');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		//判断手机号是否为空
		if($this->vcode==''){
			$echojsonstr = HyItems::echo2clientjson('101','验证码不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
		if( !is_numeric($this->vcode) || strlen($this->vcode)<4){
			$echojsonstr = HyItems::echo2clientjson('101','验证码格式不正确');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
	
		//校验验证码
		$r = $this->controller_checkcode();
	
		return $r;
	}
	
}