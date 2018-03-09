<?php
/*
 * 基础处理数据的父类文件
 */
class HySix{
	
	
	/*******************************************************************
	 * 初始化
	 *******************************************************************/
	protected static $server_time;
	protected static $create_datetime;
	protected static $create_date;
	protected static $log_filepath;    //日志文件对应目录
	protected static $log_filename;    //日志文件对应民名称
	
	
	protected $HyDb;         //数据库初始化变量
	protected $JiPush;         //极光推送
	protected $qiniubucketarr;
	
	//短信下发传递的校验参数
	protected $md5key = MSGSENDKEY;
	
	
	protected $version;
	protected $system;
	protected $sysversion;
	protected $thetype;
	protected $nowtime;
	protected $usertype;
	protected $userid;
	protected $userkey;
	
	protected $userlistdata; //用户信息表
	
	private $send_sms_ua = 'XBSC';  //验证码发送账户名称
	private $send_sms_pw = '012534';  //验证码发送账户密码
	//private $send_sms_url = 'http://121.42.205.244:18002/send.do';  //验证码发送接收地址
	private $send_sms_url = 'http://121.42.228.34/duanxinfasong/interface/smssend.php';  //验证码发送接收地址
	private $send_sms_max_time  = '120'; //验证码发送时间间隔描述，单个类型
	private $send_sms_max_count = '10';  //验证码当日发送最大次数，单个类型
	private $send_sms_vcode_minutes = '15';  //验证码有效分钟数
	
	//地球半径，平均半径为6371km
	private $earth_radius = '6371';
	
	
	
	public function __construct($input_data){
	
		//初始化数据库
		$this->HyDb = new HyDb();
		
		//极光推送的引入
		$this->JiPush = new JiPush();
	
		$this->server_time     = $_SERVER['REQUEST_TIME'];
		$this->create_date     = date('Y-m-d', $this->server_time);
		$this->create_datetime = date('Y-m-d H:i:s', $this->server_time);
		
		
		$this->version = isset($input_data['version']) ? $input_data['version']:'';  //
		$this->system = isset($input_data['system']) ? $input_data['system']:'';  //
		$this->sysversion = isset($input_data['sysversion']) ? $input_data['sysversion']:'';  //
		$this->thetype = isset($input_data['thetype']) ? $input_data['thetype']:'';  //
		$this->nowtime = isset($input_data['nowtime']) ? $input_data['nowtime']:'';  //
		$this->md5key = isset($input_data['md5key']) ? $input_data['md5key']:'';  //
		$this->usertype = isset($input_data['usertype']) ? $input_data['usertype']:'';  //
		$this->userid = isset($input_data['userid']) ? $input_data['userid']:'';  //
		$this->userkey = isset($input_data['userkey']) ? $input_data['userkey']:'';  //
		
		
		$this->log_filepath    = LOGPATH.date('Y_m').'/'.date('Y_m_d').'/';
		$this->log_filename    = date('Y_m_d').'_'.$this->version.'_'.$this->system.'_'.$this->sysversion.'_'.$this->thetype;
		$this->log_str         = '';
		
		$this->qiniubucketarr = json_decode(QINIUBUCKETSTR,true);
		
		
		$input_data['imgdata'] = '';//图片内容置空
		//日志数据开始写入
		$this->log_str   = "\n".'BEGINSIXTY----------------------------------------------'."\n".
						date('Y-m-d H:i:s').'    '.$_SERVER["REQUEST_URI"]."\n".
						json_encode($input_data)."\n";
				
		unset($input_data);
		
		
	}
	
	
	function __destruct() {
		//调用日志写入函数，将日志数据写入对应日志文件
		if($this->log_str!='') {
			$this->write_file_log();
		}
	}
	
	
	function __get($property_name){
		return isset($this->$property_name) ? $this->$property_name : false;
	}
	
	
	function __set($property_name, $value){
		$this->$property_name = $value;
	}
	
	
	
	//用户参数校验，非正式用户不调用此参数
	protected function func_oneusercheck() {
		if(1!=$this->usertype) {
			$echojsonstr = HyItems::echo2clientjson('408','用户类型传递错误');
			$this->log_str .= $echojsonstr."\n";
			echo $echojsonstr;
			return false;
		}else {
			//查询用户表，看该手机号用户是否存在
			$sql_getuserdata = "select * from sixty_user where id='".$this->userid."' order by id desc limit 1";
			$this->userlistdata = $this->HyDb->get_row($sql_getuserdata);
			if(count($this->userlistdata)<=0) {
				$echojsonstr = HyItems::echo2clientjson('409','用户id对应信息不存在');
				$this->log_str .= $echojsonstr."\n";
				echo $echojsonstr;
				return false;
			}else {
				//判断userkey是否正确
				$ser_tokenkey = $this->userlistdata['tokenkey'];
				if(''==$this->userkey || $ser_tokenkey!=$this->userkey) {
					$echojsonstr = HyItems::echo2clientjson('410','用户登录秘钥校验失败');
					$this->log_str .= $echojsonstr."\n";
					echo $echojsonstr;
					return false;
				}else {
					return true;
				}
			}
		}
	}
	
	
	
	
	//随机key值返回函数
	protected function func_create_randkey() {
		return md5(time().mt_rand(10000,99999).mt_rand(10000,99999));
	}
	
	
	
	//匿名用户唯一标识id返回函数
	protected function func_create_randid() {
		return time().mt_rand(1000,9999);
	}
	
	
	//6位数字验证码生成函数
	protected function func_create_tempvcode() {
		return mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
	}
	
	
	
	//type---1登陆，2注册，3重置
	//馅饼流量app目前登录即为注册，所以只有短信验证码直接登录方式，需要特别注意，其他方式均为预留
	protected function func_create_vcode_message($type='1',$vcode='') {
		$vcode = trim($vcode);
		if($vcode=='' || strlen($vcode)<4) {
			return false;
		}else {
			//返回的短信内容
			$sendmessage = '';
			
			if($type=='1') {
				//登录
				$sendmessage = '【60秒APP】本次验证码为：'.$vcode.'，用于登录60秒APP,'.$this->send_sms_vcode_minutes.'分钟内有效';
				
			}else if($type=='2') {
				//注册
				$sendmessage = '【60秒APP】本次验证码为：'.$vcode.'，用于注册60秒APP账号,'.$this->send_sms_vcode_minutes.'分钟内有效';
				
			}else if($type=='3') {
				//重置
				$sendmessage = '【60秒APP】本次验证码为：'.$vcode.'，用于重置60秒APP密码,'.$this->send_sms_vcode_minutes.'分钟内有效';
				
			}else {
				return false;
			}
			
			return $sendmessage;
			
		}
		
		
	}
	
	
	//返回用户列表信息
	protected function func_retsqluserdata($useridarr=array(),$imgwidth=50,$imgheight=50) {
		$returnarr = array();
		$uuarr = array();
		foreach($useridarr as $valu) {
			if(is_numeric($valu)) {
				array_push($uuarr,$valu);
			}
		}
		if(!is_array($uuarr) || count($uuarr)<=0) {
			return $returnarr;
		}else {
			$instr = '('.implode(',',$uuarr).')';
			$sql_userlist = "select id,nickname,touxiang,phone from sixty_user where id in ".$instr;
			$list_userlist = $this->HyDb->get_all($sql_userlist);
			foreach($list_userlist as $valus) {
				$tmparr = array();
				$tmparr['nickname'] = $valus['nickname'];
				$tmparr['phone'] = $valus['phone'];
				if(''==$valus['touxiang']) {
					$valus['touxiang'] = 'default_user.png';
				}
				$tmparr['touxiang'] = HyItems::hy_qiniuimgurl('sixty-user',$valus['touxiang'],$imgwidth,$imgheight,true);
				$returnarr[$valus['id']] = $tmparr;
			}
			unset($list_userlist,$uuarr,$useridarr,$tmparr,$sql_userlist,$instr);
			return $returnarr;
			
		}
	}
	
	
	protected function func_userid_datatiqu($usdataarr=array(),$userid=0,$ziduan='') {
		$list = isset($usdataarr[$userid])?$usdataarr[$userid]:array();
		$retstr = isset($list[$ziduan])?$list[$ziduan]:'';
		unset($usdataarr,$userid,$list);
		return $retstr;
	}
	
	public function func_isImage($filename){
		$types = '.gif|.jpeg|.png|.bmp|.jpg';//定义检查的图片类型
		if(file_exists($filename)){
			$info = getimagesize($filename);
			$ext = image_type_to_extension($info['2']);
			return stripos($types,$ext);
		}else{
			return false;
		}
	}
	
	
	//校验用户提交的验证码是否和发送的验证码相等
	//馅饼流量app目前登录即为注册，所以只有短信验证码直接登录方式，需要特别注意，其他方式均为预留
	protected function func_vcode_check($type='1',$phone='',$vcode='') {
		
		$phone   = trim($phone);
		$vcode   = trim($vcode);
		
		if($phone=='' || $vcode=='') {
			$echojsonstr = HyItems::echo2clientjson('101','手机号或验证码不能为空');
			$this->log_str .= $echojsonstr."\n";
			echo $echojsonstr;
			return false;
			
		}else {
			
			//苹果测试账号
			if(($phone=='13800138008' || $phone=='15632181449' || $phone=='15111111111' || $phone=='15116951806'|| $phone=='13222222222') && $vcode=='123456') {
				return true;
			}
			
			//查询数据库获取改手机号最近的一个验证码
			$sql_getlast_vcode = "select vcode
								from sixty_vcode_send
								where type='".$type."'
								and phone='".$phone."'
								and sendtime>='".(time()-($this->send_sms_vcode_minutes*60))."'
								order by id desc limit 1";
			
			$list_getlast_vcode = $this->HyDb->get_one($sql_getlast_vcode);
			
			
			if($list_getlast_vcode=='' || strlen($list_getlast_vcode)<4) {
				$echojsonstr = HyItems::echo2clientjson('101','验证码超过有效期');
				$this->log_str .= $echojsonstr."\n";
				echo $echojsonstr;
				return false;
				
			}else {
				//判断查询到的验证码是否和提交过来的验证码相等
				if($list_getlast_vcode!=$vcode) {
					$echojsonstr = HyItems::echo2clientjson('101','验证码错误');
					$this->log_str .= $echojsonstr."\n";
					echo $echojsonstr;
					return false;
				}else {
					return true;
					
				}
				
			}
			
		}
		
	}
	
	
	
	//短信验证码发送函数
	//type---1登陆，2注册，3重置
	//馅饼流量app目前登录即为注册，所以只有短信验证码直接登录方式，需要特别注意，其他方式均为预留
	protected function func_send_sms($type='1',$phone='',$vcode='',$message=''){
		
		$phone   = trim($phone);
		$vcode   = trim($vcode);
		$message = trim($message);
		
		if($phone=='' || $vcode=='' || $message=='') {
			$echojsonstr = HyItems::echo2clientjson('100','手机号验证码短信内容不能为空');
			$this->log_str .= $echojsonstr."\n";
			echo $echojsonstr;
			return false;
			
		}else {
			//记录发送出去的内容
			$this->log_str .= 'func_send_sms---'.$type.'---'.$phone.'---'.$vcode.'---'.$message."\n"; //日志写入
			
			//查询数据库判断当天发送的最大次数
			$sql_getnow_count = "select count(id) as con 
								from sixty_vcode_send 
								where type='".$type."'
								and sendtime>='".strtotime(date('Y-m-d'))."'
								and phone='".$phone."'";
			$list_getnow_count = $this->HyDb->get_one($sql_getnow_count);
			if($list_getnow_count>=$this->send_sms_max_count) {
				$echojsonstr = HyItems::echo2clientjson('100','短信验证码获取次数已达当日上限');
				$this->log_str .= $echojsonstr."\n";
				echo $echojsonstr;
				return false;
				
			}else {
				//判断此次发送与上次发送的时间间隔
				//查询数据库判断当天发送的最大次数
				$sql_getlast_time = "select sendtime
									from sixty_vcode_send
									where type='".$type."'
									and phone='".$phone."'
									order by id desc limit 1";
				$list_getlast_time = $this->HyDb->get_one($sql_getlast_time);
				//间隔时间计算
				$fasong_jiangetime = time()-$list_getlast_time;
				if($fasong_jiangetime<=$this->send_sms_max_time) {
					$echojsonstr = HyItems::echo2clientjson('100','短信验证码获取频繁');
					$this->log_str .= $echojsonstr."\n";
					echo $echojsonstr;
					return false;
				}else {
					//调用验证码发送函数
					$url = 'http://121.42.228.34/duanxinfasong/interface/smssend.php?md5key=e0f8978c0677a01aeac12cc90eed0949&nowtime='.time().'&phone='.$phone.'&message='.urlencode($message);
					
					$res = HyItems::vget($url,3000);
//                    $this->log_str .= '<-------'.$res."\n".'----->';
					$content  = isset($res['content'])  ? trim($res['content']) : '';
					$httpcode = isset($res['httpcode']) ? $res['httpcode'] : '';
					$run_time = isset($res['run_time']) ? $res['run_time'] : '';
					$errorno  = isset($res['errorno'])  ? $res['errorno'] : '';
					
					
					//将curl数据日志写入数据库
					$this->log_str   .= $httpcode.'  -  '.
										$run_time.'  -  '.
										$errorno.'  -  '.
										$url.'  -  '.
										HyItems::hy_trn2space($content)."\n";
					if($httpcode!=200) {
						$echojsonstr = HyItems::echo2clientjson('100','验证码发送失败，系统错误');
						$this->log_str .= $echojsonstr."\n";
						echo $echojsonstr;
						return false;
					}else {
						
						if(trim($content)!='ok') {
							$echojsonstr = HyItems::echo2clientjson('100','验证码发送失败，系统错误');
							$this->log_str .= $echojsonstr."\n";
							echo $echojsonstr;
							return false;
							
						}else {
							$sql_insert_vcode = "insert into sixty_vcode_send (type,sendtime,phone,vcode,content) values(
												'".$type."','".time()."','".$phone."','".$vcode."','".$message."')";
							$this->log_str .= HyItems::hy_trn2space($sql_insert_vcode)."\n";
							$this->HyDb->execute($sql_insert_vcode);
							
							return true;
						}
						
						
					}
					
					
				}
				
				
			}
			
			
		}
		
		
	}
	
	//极光推送
	public function func_jgpush($jiguangid,$messagee,$m_type='',$m_txt='',$m_time='86400'){
		
		
		//极光推送的设置
		/* $m_type = '';//推送附加字段的类型
		$m_txt = '';//推送附加字段的类型对应的内容(可不填) 可能是url,可能是一段文字。
		$m_time = '86400';//离线保留时间 */
		$receive = array('alias'=>array($jiguangid));//别名
		//$receive = array('alias'=>array('073dc8672c25d8d023328d06dbbd1230'));//别名
		$content = $messagee;
		//$message="";//存储推送状态
		$result = $this->JiPush->push($receive,$content,$m_type,$m_txt,$m_time);
		
		if($result){
			$res_arr = json_decode($result, true);
		
			if(isset($res_arr['error'])){                       //如果返回了error则证明失败
				echo $res_arr['error']['message'];          //错误信息
				$error_code=$res_arr['error']['code'];             //错误码
				switch ($error_code) {
					case 200:
						$message= '发送成功！';
						break;
					case 1000:
						$message= '失败(系统内部错误)';
						break;
					case 1001:
						$message = '失败(只支持 HTTP Post 方法，不支持 Get 方法)';
						break;
					case 1002:
						$message= '失败(缺少了必须的参数)';
						break;
					case 1003:
						$message= '失败(参数值不合法)';
						break;
					case 1004:
						$message= '失败(验证失败)';
						break;
					case 1005:
						$message= '失败(消息体太大)';
						break;
					case 1008:
						$message= '失败(appkey参数非法)';
						break;
					case 1020:
						$message= '失败(只支持 HTTPS 请求)';
						break;
					case 1030:
						$message= '失败(内部服务超时)';
						break;
					default:
						$message= '失败(返回其他状态，目前不清楚额，请联系开发人员！)';
						break;
				}
			}else{
				$message="ok";
			}
		}else{//接口调用失败或无响应
			$message='接口调用失败或无响应';
		}
		
		//return $message;
	}
	
	
	
	//检测手机号对应运营商
	protected function hy_yunyingshangcheck($phone='',$type='num') {
		
		$phone = trim($phone);
		
		if($phone=='') {
			return false;
		}
		//截取手机号吗前三位
		$top3_phone = substr($phone,0,3);
		
		//运营商号段定义
		$topphonearr = array(
				'133' => '中国电信', '153' => '中国电信', '180' => '中国电信', '181' => '中国电信', '189' => '中国电信', '177' => '中国电信', '173' => '中国电信',
				'130' => '中国联通', '131' => '中国联通', '132' => '中国联通', '155' => '中国联通', '156' => '中国联通', '145' => '中国联通', '185' => '中国联通',
				'186' => '中国联通', '176' => '中国联通', '185' => '中国联通',
				'134' => '中国移动', '135' => '中国移动', '136' => '中国移动', '137' => '中国移动', '138' => '中国移动', '139' => '中国移动', '150' => '中国移动',
				'151' => '中国移动', '152' => '中国移动', '158' => '中国移动', '159' => '中国移动', '182' => '中国移动', '183' => '中国移动', '184' => '中国移动',
				'157' => '中国移动', '187' => '中国移动', '188' => '中国移动', '147' => '中国移动', '178' => '中国移动', '184' => '中国移动',
		);
		
		if(isset($topphonearr[$top3_phone])) {
				
			if($type=='num') {
				$y_yunying = false;
				if($topphonearr[$top3_phone]=='中国移动') {
					$y_yunying = 1;
				}else if($topphonearr[$top3_phone]=='中国联通') {
					$y_yunying = 2;
				}else if($topphonearr[$top3_phone]=='中国电信') {
					$y_yunying = 3;
				}
				return $y_yunying;
	
			}else {
				return $topphonearr[$top3_phone];
			}
				
		}else {
			return false;
		}
	}
	
	
	
	//检测手机号对应运营商
	protected function yunyingshangcheck($phone='',$type='num') {
		$phone = trim($phone);
	
		if($phone=='') {
			return false;
		}
		//截取手机号吗前三位
		$top3_phone = substr($phone,0,3);
	
		//运营商号段定义
		$topphonearr = array(
				'133' => '中国电信', '153' => '中国电信', '180' => '中国电信', '181' => '中国电信', '189' => '中国电信', '177' => '中国电信', '173' => '中国电信',
				'130' => '中国联通', '131' => '中国联通', '132' => '中国联通', '155' => '中国联通', '156' => '中国联通', '145' => '中国联通', '185' => '中国联通',
				'186' => '中国联通', '176' => '中国联通', '185' => '中国联通',
				'134' => '中国移动', '135' => '中国移动', '136' => '中国移动', '137' => '中国移动', '138' => '中国移动', '139' => '中国移动', '150' => '中国移动',
				'151' => '中国移动', '152' => '中国移动', '158' => '中国移动', '159' => '中国移动', '182' => '中国移动', '183' => '中国移动', '184' => '中国移动',
				'157' => '中国移动', '187' => '中国移动', '188' => '中国移动', '147' => '中国移动', '178' => '中国移动', '184' => '中国移动',
		);
	
		if(isset($topphonearr[$top3_phone])) {
				
			if($type=='num') {
				$y_yunying = false;
				if($topphonearr[$top3_phone]=='中国移动') {
					$y_yunying = 1;
				}else if($topphonearr[$top3_phone]=='中国联通') {
					$y_yunying = 2;
				}else if($topphonearr[$top3_phone]=='中国电信') {
					$y_yunying = 3;
				}
				return $y_yunying;
	
			}else {
				return $topphonearr[$top3_phone];
			}
				
		}else {
			return false;
		}
	
	
	}
	
	
	//经纬坐标的计算
	
	/**
	 *计算某个经纬度的周围某段距离的正方形的四个点
	 *
	 *@param lng float 经度
	 *@param lat float 纬度
	 *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
	 *@return array 正方形的四个点的经纬度坐标
	*/
	protected function returnSquarePoint($lng, $lat,$distance = 300){
	
	$dlng =  2 * asin(sin($distance / (2 * $this->earth_radius)) / cos(deg2rad($lat)));
	$dlng = rad2deg($dlng);
	
	$dlat = $distance/$this->earth_radius;
	$dlat = rad2deg($dlat);
	
	
	return array(
			'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
			'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
			'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
			'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
			);
	}
	
	
	/**
	 * 计算两组经纬度坐标 之间的距离
	 * params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km);
	 * return m or km
	 */
	protected function getDistance($lat1, $lng1, $lat2, $lng2, $len_type = 2, $decimal = 2){
		//$EARTH_RADIUS=6378.137;//6371
		$EARTH_RADIUS=6371;
		$PI=3.1415926;
		$radLat1 = $lat1 * $PI / 180.0;
		$radLat2 = $lat2 * $PI / 180.0;
		$a = $radLat1 - $radLat2;
		$b = ($lng1 * $PI / 180.0) - ($lng2 * $PI / 180.0);
		$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
		$s = $s * $EARTH_RADIUS;
		$s = round($s * 1000);
		if ($len_type > 1)
		{
			$s /= 1000;
		}
		return round($s,$decimal);
	}
	
	
	//地理位置转换
	protected function getlnglat($address){
		
		$url = 'http://api.map.baidu.com/geocoder?address=urlencode('.$address.')&output=json&key=WPzUoVnSMWZXrUuSR5Vs22Cd17yhCZeD';
		
		$data = vget($url);
		
		$truepath = json_decode($data['content'], true);
		
		
		if($truepath['status']=='OK'){//请求成功
			
			return $truepath['result']['location'];
			
		}else{
			return false;
		}
		
	}
	
	
	
	
	
	//6位字母+数字的组合
	function getRandomString($len, $chars=null)
	{
		if (is_null($chars)) {
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		}
		mt_srand(10000000*(double)microtime());
		for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
			$str .= $chars[mt_rand(0, $lc)];
		}
		return $str;
	}
	
	
	//七牛图片上传
	public function upload_qiniu($bucket,$filepath,$savename,$rewrite='no'){
		$qiniurl = QINIUURL.'hy_upload.php';
		//$this->log_str .= $qiniurl."\n";
		$dataarr = array(
				'bucket'   => $bucket,
				'filepath' => $filepath,
				'savename' => $savename,
				'rewrite' => $rewrite,
		);
		$datastr = HyItems::hy_urlcreate($dataarr);
		//模拟数据访问
		$r = HyItems::vpost($qiniurl,$datastr,$header=array(),$timeout=5000 );
		$this->log_str .= var_export($dataarr,1)."\n";
		$this->log_str .= var_export($r,1)."\n";
		if(substr($r['content'],0,1)!='#' && $r['httpcode']=='200'){
			$truepath = json_decode($r['content'], true);
			//$arr = unserialize(BUCKETSTR);//获取七牛访问链接
			$filename= $truepath['key'];
			return $filename;
		}else{
			return false;
		}
	}
	
	
	//七牛图片删除
	public function delete_qiniu($bucket,$delname){
		$qiniurl = QINIUURL.'hy_delete.php';
		$dataarr = array(
				'delbucket'   => $bucket,
				'delname' => $delname,
		);
		$datastr = HyItems::hy_urlcreate($dataarr);
		//模拟数据访问
		$r = HyItems::vpost($qiniurl,$datastr,$header=array(),$timeout=5000 );
		//$this->log_str .= var_export($dataarr,1)."\n";
		//$this->log_str .= var_export($r,1)."\n";
		if(substr($r['content'],0,1)!='#' && $r['httpcode']=='200'){
			return true;
		}else{
			return false;
		}
	}
	
	
	
	
	//sql语句查询缓存输出
	protected function func_runtime_sql_data($selectsql='') {
		$list = array();
		$selectsql = trim($selectsql);
		$tmp_sql_md5str = md5($selectsql);
		$tmpsqlfilepathname = TMPSQLPATH.$tmp_sql_md5str;
		if(file_exists($tmpsqlfilepathname)) {
			//获取文件上次修改更新时间
			$lastuptime = filemtime($tmpsqlfilepathname);
			if((time()-$lastuptime)<TMPSQLTIME) {
				//直接使用缓存数据
				$list = json_decode(file_get_contents($tmpsqlfilepathname),true);
			}else {
				$list  = $this->HyDb->get_all($selectsql);
				if(is_array($list)) {
					file_put_contents($tmpsqlfilepathname, json_encode($list));
				}
			}
		}else {
			$list = $this->HyDb->get_all($selectsql);
			if(is_array($list)) {
				file_put_contents($tmpsqlfilepathname, json_encode($list));
			}
		}
		return $list;
	}
	
	
	
	
	
	/**
	 * 日志变量数据追加，即将子类的日志变量数据追加到父类的日志变量数据中
	 */
	protected function hy_log_str_add($addlog) {
		$this->log_str .= $addlog;
	}
	
	
	/**
	 * 日志写入封装函数
	 */
	protected function write_file_log() {
	
		$path = $this->log_filepath;
		$name = $this->log_filename.'.log';
		$data = $this->log_str;
	
		//将数据写入日志文件
		HyItems::hy_writelog($path, $name, $data);
	
	}
	
	
	
	
}
