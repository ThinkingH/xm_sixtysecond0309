<?php
/*
 * QQ登录
 */
class HySix1026 extends HySix {
	
	
	private $qqid;
	private $sex;
	private $nickname;
	private $headimgurl;
	
	
	//数据的初始化
	public function __construct($input_data){
		
		//数据初始化
		parent::__construct($input_data);
		
		//接收qqid
		$this->qqid = isset($input_data['qqid']) ? $input_data['qqid']:'';  //
	
		$this->sex = isset($input_data['sex']) ? $input_data['sex']:'';  //性别
		$this->nickname = isset($input_data['nickname']) ? $input_data['nickname']:'';  //昵称
		$this->headimgurl = isset($input_data['headimgurl']) ? $input_data['headimgurl']:'';  //头像
		
	}
	
	
	//qqid主操作的插入
	public function controller_exec1(){
		
		//判断该用户是否注册过
		$sql_getuser  = "select id,tokenkey from sixty_user where qqid='".$this->qqid."' order by id desc limit 1";
		$list_getuser = parent::__get('HyDb')->get_row($sql_getuser);
		
		if(count($list_getuser)>0) {
			//已经存在，读取登录参数
				$userarr = array(
						'userid' => $list_getuser['id'],
						'userkey'=> $list_getuser['tokenkey'],
				);
				$echojsonstr = HyItems::echo2clientjson('100','登录成功',$userarr);
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return true;
			
		}else {
			
			//随机生成的userkey
			$userkey = parent::func_create_randkey();
			
			//插入用户
			$userdatasql = "insert into sixty_user (qqid,tokenkey,sex,nickname,touxiang,create_datetime)
								values ('".$this->qqid."','".$userkey."','".$this->sex."','".$this->nickname."',
								'".$this->headimgurl."','".parent::__get('create_datetime')."')";
			$userdatalist = parent::__get('HyDb')->execute($userdatasql);
			
			$useridsql = "select id,tokenkey from sixty_user where qqid='".$this->qqid."' order by id desc limit 1";
			$useridlist = parent::__get('HyDb')->get_row($useridsql);
			
			if(count($useridlist)>0){
				$userarr = array(
						'userid' => $useridlist['id'],
						'userkey'=> $useridlist['tokenkey'],
				);
				$echojsonstr = HyItems::echo2clientjson('100','登录成功',$userarr);
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return true;
				
			}else{
				$echojsonstr = HyItems::echo2clientjson('101','登录失败，系统错误');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
				
			}
			
			
		}
		
		
		
	}
	
	
	
	
	//操作入口--提交验证码
	public function controller_init(){
		
		//判断qqid是否为空
		if($this->qqid==''){
			$echojsonstr = HyItems::echo2clientjson('100','qqid不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
		}
		
	
		//qqid数据的插入
		$this->controller_exec1();
	
		return true;
	}
	
	
	
	
}