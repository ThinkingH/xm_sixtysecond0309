<?php

/** 
 * HyItems
 * 静态方法库
 * author:yu
 * 
 */

class HyItems {
	
	
	public static function hy_qiniu_urlstoptime($url='',$addtime=3600) {
		//七牛提供的 key
		$key = '1111111111111111111111111111111111111';
		
		if(''==$url) {
			return false;
		}
		// 对URL进行解析
		$parse = parse_url($url);
		
		// 设置有效时间为一个小时
		$time = time()+3600;
		
		// 对有效时间进行16进制转换
		$T=dechex($time);
		
		// 按规定格式拼接字符串
		$S=$key.$parse['path'].$T;
		
		// 进行md5加密并进行小写转换
		$sign =strtolower(md5($S));
		
		// 拼接URL
		if(isset($parse['query'])){
			$url = $url.'&sign='.$sign.'&t='.$T;
			
		} else {
			$url = $url.'?sign='.$sign.'&t='.$T;
		}
		
		// 返回
		return $url;
			
	}
	
	public static function hy_getfiletype($filepathname='') {
		if(''==$filepathname || !file_exists($filepathname)) {
			return false;
		}else {
			$path     = dirname($filepathname).'/';
			$basename = pathinfo($filepathname, PATHINFO_FILENAME);
			$extname  = pathinfo($filepathname, PATHINFO_EXTENSION);
			
			$file = fopen($filepathname, "rb");
			$bin = fread($file, 2); //只读2字节
			fclose($file);
			$strInfo = @unpack("C2chars", $bin);
			$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
			$fileType = 'jpg';
			switch ($typeCode)
			{
				case 7790:
					$fileType = 'exe';
					break;
				case 7784:
					$fileType = 'midi';
					break;
				case 8297:
					$fileType = 'rar';
					break;
				case 8075:
					$fileType = 'zip';
					break;
				case 255216:
					$fileType = 'jpg';
					break;
				case 7173:
					$fileType = 'gif';
					break;
				case 6677:
					$fileType = 'bmp';
					break;
				case 13780:
					$fileType = 'png';
					break;
				default:
					$fileType = 'jpg'; //unknown
			}
			$newpathname = $path.$basename.'.'.$fileType;
			
			return $newpathname;
		}
		
	}
	
	
	/**
	 * 将图片以自定义品质，另存为JPG格式,将会删除源图片
	 * @param string $filepathname 图片名称，包含路径
	 * @param int    $quality  图片品质，0到100，默认90，100为最高品质
	 */
	public static function hy_resave2jpg($filepathname='', $quality = 85) {
		if(''==$filepathname) {
			return false;
		}else {
			
			$path     = dirname($filepathname).'/';
			$basename = pathinfo($filepathname, PATHINFO_FILENAME);
			$extname  = pathinfo($filepathname, PATHINFO_EXTENSION);
			$im = null;
			switch($extname) {
				case 'jpg':
					$im = imagecreatefromjpeg($filepathname);
					break;
				case 'png':
					$im = imagecreatefrompng($filepathname);
					break;
				case 'gif':
					$im = imagecreatefromgif($filepathname);
					break;
			}
			$newpathname = $path.$basename.'.jpg';
			$r = imagejpeg($im, $newpathname, $quality);
			imagedestroy($im);
			if($r) {
				if(in_array($extname, array('png','gif'))) {
					@unlink($filepathname);
				}
				
				return $newpathname;
			}else {
				return false;
			}
			
		}
		
	}
	
	
	
	
	public static function hy_qiniuimgurl($bucketname='',$imgname='',$width='',$height='',$canshu=true) {
		$qiniubucketarr = json_decode(QINIUBUCKETSTR,true);
		$returnimgurl = '';
		if(''==$imgname) {
			$bucketurl = isset($qiniubucketarr['sixty-basic'])?$qiniubucketarr['sixty-basic']:'';
			if(''==$bucketurl) {
				return '';
			}else {
				$returnimgurl = $bucketurl.'notfounddata.png';
				if($canshu) {
					$returnimgurl .= '?imageView2/1';
					if($width!='') {
						$returnimgurl .= '/w/'.$width;
					}
					if($height!='') {
						$returnimgurl .= '/h/'.$height;
					}
					//$returnimgurl .= '/format/webp';
					//$returnimgurl .= '/q/100';
					//$returnimgurl .= '|imageslim';
					//增加时间戳
					$returnimgurl = HyItems::hy_qiniu_urlstoptime($returnimgurl,7200);
				}
				return $returnimgurl;
			}
			
		}else {
			
			$bucketurl = isset($qiniubucketarr[$bucketname])?$qiniubucketarr[$bucketname]:'';
			if($bucketurl!='') {
				if(substr($imgname,0,4)=='http') {
					$returnimgurl = $imgname;
				}else {
					$returnimgurl = $bucketurl.$imgname;
				}
				if($canshu) {
					$returnimgurl .= '?imageView2/1';
					if($width!='') {
						$returnimgurl .= '/w/'.$width;
					}
					if($height!='') {
						$returnimgurl .= '/h/'.$height;
					}
					//$returnimgurl .= '/format/webp';
					//$returnimgurl .= '/q/100';
					//$returnimgurl .= '|imageslim';
					//增加时间戳
					$returnimgurl = HyItems::hy_qiniu_urlstoptime($returnimgurl,7200);
				}
			}
			return $returnimgurl;
		}
		
	}
	
	//七牛云bucket存储内容获取
	public static function hy_qiniubucketurl($bucketname='',$dataname='') {
		$returnurl = '';
		$qiniubucketarr = json_decode(QINIUBUCKETSTR,true);
		$bucketurl = isset($qiniubucketarr[$bucketname])?$qiniubucketarr[$bucketname]:'';
		if($bucketurl!='') {
			if(substr($dataname,0,4)=='http') {
				$returnurl = $dataname;
			}else {
				$returnurl = $bucketurl.$dataname;
				//增加时间戳
				$returnurl = HyItems::hy_qiniu_urlstoptime($returnurl,3600);
			}
		}
		return $returnurl;
		
	}
	
	/**
	 * 
	 * @param number $nowpage  当前页数
	 * @param number $pagesize  每页数量
	 * @param number $allcount  总条数
	 */
	public static function hy_pagepage($nowpage=1,$pagesize=10,$allcount=0) {
		if(!is_numeric($nowpage)||$nowpage<=0) {
			$nowpage = 1;
		}
		if(!is_numeric($pagesize)|| $pagesize<=0) {
			$pagesize = 10;
		}
		if($pagesize<1 || $pagesize>200) {
			$pagesize = 10;
		}
		$sumpage = ceil($allcount/$pagesize);
		
		$firstpage = ($nowpage-1)*$pagesize;
		
		$retarr = array(
				'pagemsg' => array(
						'nowpage' => (string)$nowpage,
						'sumpage' => (string)$sumpage,
						'pagesize' => (string)$pagesize,
						'allcount' => (string)$allcount,
				),
				'pagelimit' => ' limit '.$firstpage.','.$pagesize,
				
		);
		return $retarr;
	}
	
	/**
	 * 从数组中取特定值
	 * 
	 * @param unknown_type $arr 数组名
	 * @param unknown_type $key 字段关键字
	 * @return unknown
	 */
	public static function arrayItem($arr, $key){
		if( ! is_array($arr) || ! array_key_exists($key, $arr) ){
			return FALSE;
		}else {
			return $arr[$key];
		}
	}
	
	
	
	/**
	 * 将xml字符串转换为xml对象
	 * @param string $xmlstr
	 * @return boolean|string
	 */
	public static function hy_xmldecode($xmlstr='') {
	
		$xmlstr = trim($xmlstr);
		$xml_obj = '';
	
		if($xmlstr=='') {
			return false;
		}else {
			$xml_obj = @simplexml_load_string($xmlstr);
			if(is_object($xml_obj)) {
				return $xml_obj;
			}else {
				return false;
			}
		}
	
	}
	
	
	
	/**
	 * 将数组中的字段拼接成url参数
	 * @param unknown $urlarr
	 * @return string
	 */
	public static function hy_urlcreate( $urlarr=array()) {
	
		$baseurl = '';
	
		if( is_array($urlarr) && count($urlarr)>0 ) {
				
			foreach($urlarr as $key => $val) {
				$baseurl .= $key.'='.urlencode($val).'&';
			}
				
			$baseurl = substr($baseurl,0,(strlen($baseurl)-1));
		}
	
		return $baseurl;
	
	}
	
	
	
	/**
	 * 模拟POST
	 * @param unknown $url
	 * @param unknown $data
	 * @param unknown $header
	 * @param number $timeout
	 * @return arr $retarr
	 */
	public static function vpost($url,$data,$header=array(),$timeout=5000 ){ // 模拟提交数据函数
		
		if( ! function_exists('curl_init') ){
			return FALSE;
		}
		
		$headerArr = array();
		foreach( $header as $n => $v ) {
			$headerArr[] = $n.':'.$v;
		}
		
		
		$curl = curl_init(); // 启动一个CURL会话
		
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_NOBODY, 0); // 显示返回的body区域内容
		
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 2); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		
		//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		
		curl_setopt($curl, CURLOPT_NOSIGNAL,1); //注意，毫秒超时一定要设置这个
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS,$timeout); //设置连接等待毫秒数
		curl_setopt($curl, CURLOPT_TIMEOUT_MS,$timeout); //设置超时毫秒数
		
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); // 获取的信息以文件流的形式返回
		if(count($headerArr)>0) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);//设置HTTP头
		}
		
		$content  = curl_exec($curl); //返回结果
		$httpcode = curl_getinfo($curl,CURLINFO_HTTP_CODE); //页面状态码
		$run_time = (curl_getinfo($curl,CURLINFO_TOTAL_TIME)*1000); //所用毫秒数
		$errorno  = curl_errno($curl);
		
		//关闭curl
		curl_close($curl);
		
		
		//定义return数组变量
		$retarr = array();
		$retarr['content']  = $content;
		$retarr['httpcode'] = $httpcode;
		$retarr['run_time'] = $run_time;
		$retarr['errorno']  = $errorno;
		
		return $retarr;
		
	}
	
	
	
	
	/**
	 * 模拟POST  同时获取header头和body
	 * @param unknown $url
	 * @param unknown $data
	 * @param unknown $header
	 * @param number $timeout
	 * @return arr $retarr
	 * 
	 * 关于$header,请使用 $arr[key] = val; 的方式
	 * 
	 */
	public static function hvpost($url,$data,$header=array(),$timeout=5000 ){ // 模拟提交数据函数
		
		if( ! function_exists('curl_init') ){
			return FALSE;
		}
		
		$headerArr = array();
		foreach( $header as $n => $v ) {
			$headerArr[] = $n.':'.$v;
		}
		
		
		$curl = curl_init(); // 启动一个CURL会话
		
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_HEADER, 1); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_NOBODY, 0); // 显示返回的body区域内容
		
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 2); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		
		//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		
		curl_setopt($curl, CURLOPT_NOSIGNAL,1); //注意，毫秒超时一定要设置这个
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS,$timeout); //设置连接等待毫秒数
		curl_setopt($curl, CURLOPT_TIMEOUT_MS,$timeout); //设置超时毫秒数
		
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); // 获取的信息以文件流的形式返回
		if(count($headerArr)>0) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);//设置HTTP头
		}
		
		$content    = curl_exec($curl); //返回结果
		$httpcode   = curl_getinfo($curl,CURLINFO_HTTP_CODE); //页面状态码
		$run_time   = (curl_getinfo($curl,CURLINFO_TOTAL_TIME)*1000); //所用毫秒数
		$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$errorno    = curl_errno($curl);
		
		//关闭curl
		curl_close($curl);
		
		if($httpcode == '200') { //如果是正常返回，分割header和body
			$headerx = substr($content, 0, $headerSize);
			$bodyx   = substr($content, $headerSize);
		}
		
		//定义return数组变量
		$retarr = array();
		$retarr['headers']  = $headerx;
		$retarr['content']  = $bodyx;
		$retarr['httpcode'] = $httpcode;
		$retarr['run_time'] = $run_time;
		$retarr['errorno']  = $errorno;
		
		return $retarr;
		
	}
	
	
	
	
	
	/**
	 * get模拟
	 * @param unknown $url
	 * @param number $timeout
	 * @param unknown $header
	 * @param string $useragent
	 * @return boolean|multitype:number unknown
	 * 关于$header,请使用 $arr[key] = val; 的方式
	 * CLIENT-IP
	 * X-FORWARDED-FOR
	 */
	public static function vget( $url, $timeout=5000, $header=array(), $useragent='' ) {
	
		if( !function_exists('curl_init') ){
			return false;
		}
	
		if(substr($url,0,7)!='http://' && substr($url,0,8)!='https://') {
			return 'url_error';
		}
	
		//对传递的header数组进行整理
		$headerArr = array();
		foreach( $header as $n => $v ) {
			$headerArr[] = $n.':'.$v;
		}
	
	
		$curl = curl_init(); // 启动一个CURL会话
	
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_NOBODY, 0); // 显示返回的body区域内容
	
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
	
		if(trim($useragent)!='') {
			//当传递useragent参数时，模拟用户使用的浏览器
			curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
		}
	
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	
		curl_setopt($curl, CURLOPT_NOSIGNAL,1); //注意，毫秒超时一定要设置这个
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS,$timeout); //设置连接等待毫秒数
		curl_setopt($curl, CURLOPT_TIMEOUT_MS,$timeout); //设置超时毫秒数
	
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); // 获取的信息以文件流的形式返回
		if(count($headerArr)>0) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);//设置HTTP头
		}
		$content  = curl_exec($curl); //返回结果
		$httpcode = curl_getinfo($curl,CURLINFO_HTTP_CODE); //页面状态码
		$run_time = (curl_getinfo($curl,CURLINFO_TOTAL_TIME)*1000); //所用毫秒数
		$errorno  = curl_errno($curl);
	
		//关闭curl
		curl_close($curl);
	
	
		//定义return数组变量
		$retarr = array();
		$retarr['content']  = $content;
		$retarr['httpcode'] = $httpcode;
		$retarr['run_time'] = $run_time;
		$retarr['errorno']  = $errorno;
	
		return $retarr;
	
	}
	
	
	
	
	/**
	 * 生成唯一数字单号,基于毫秒时间戳+随机数
	 * @param number $length
	 * @return unknown
	 */
	public static function hy_onlynumid($length=16) {
	
		$length  = intval($length);
	
		if($length <= 15) {
			$length = 15;
		}else if($length >= 24){
			$length = 24;
		}else {
			//$length长度不变
		}
	
		list($s1, $s2) = explode(' ', microtime());
	
	
		$k1 = substr($s2,-9,1);
		$k2 = substr($s2,-8,8);
		if($k1==0) {
			$k1 = 5;
		}else if($k1=1) {
			$k1 = 6;
		}else if($k1=2) {
			$k1 = 7;
		}else if($k1=3) {
			$k1 = 8;
		}else if($k1=4) {
			$k1 = 9;
		}
		$s2 = $k1.$k2;
	
		$millsec = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	
		$echo_onlynum = $millsec;
	
		$addlen = $length - strlen($millsec);
	
		for($i=0;$i<$addlen;$i++) {
			$echo_onlynum .= mt_rand(0,9);
		}
	
		return $echo_onlynum;
	
	}
	
	
	
	
	
	/**
	 * 获取客户端IP地址
	 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
	 * @return mixed
	 */
	public static function get_client_ip($type = 0) {
		$type       =  $type ? 1 : 0;
		static $ip  =   NULL;
		if ($ip !== NULL) return $ip[$type];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos    =   array_search('unknown',$arr);
			if(false !== $pos) unset($arr[$pos]);
			$ip     =   trim($arr[0]);
		}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip     =   $_SERVER['HTTP_CLIENT_IP'];
		}elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip     =   $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = sprintf("%u",ip2long($ip));
		$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
		return $ip[$type];
	}
	
	
	
	/**
	 * 获取访问者ip地址，防止用户伪造ip
	 * @return unknown|string
	 */
	public static function hy_get_client_ip() {
		$ip = '';
		if(isset($_SERVER['REMOTE_ADDR'])) {
			$ip  = $_SERVER['REMOTE_ADDR'];
		}else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos = array_search('unknown',$arr);
			if(false !== $pos) unset($arr[$pos]);
			$ip  = trim($arr[0]);
		}else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip  = $_SERVER['HTTP_CLIENT_IP'];
		}
		// IP地址合法验证
		$long = sprintf("%u",ip2long($ip));
		if($long) {
			return $ip;
		}else {
			return '0.0.0.0';
		}
	}
	
	
	
	
	
	/**
	 * 日志写入函数
	 * @param unknown $path
	 * @param unknown $name
	 * @param unknown $data
	 */
	public static function hy_writelog($path, $name, $data) {
		//判断该日志文件存放路径是否存在，不存在则进行创建
		
		if(!is_dir($path)) {
			//创建该目录
			mkdir($path, 0777, true);
		}
		
		//生成文件路径名称
		$filepathname = $path.$name;
		
		$fp = fopen($filepathname,'a'); //打开句柄
		fwrite($fp, $data);  //将文件内容写入字符串
		fclose($fp); //关闭句柄
		
		
	}
	
	
	
	
	/**
	 * 将bigint数据转成大字符串
	 * @param unknown $long
	 * @return unknown
	 */
	public static function hytobigstr($long) {
		$long = sprintf("%u",$long);
		return $long;
	}
	
	
	/**
	 * 替换制表符、回车、换行为空格，主要用于对多行的sql语句转换为单行，便于写入日志及日志提取等
	 */
	public static function hy_trn2space($str) {
	
		$replace = array("\t", "\r", "\n",);
		return str_replace($replace, ' ', $str);
	
	}
	
	
	/**
	 * 替换制表符和回车为空格，主要用于日志写入前的处理---将逐步使用另一个，逐步替换
	 */
	public static function hy_tospace($str) {
		
		$replace = array("\t", "\r", "\n",);
		return str_replace($replace, ' ', $str);
		
	}
	
	
	/**
	 * 替换制表符和回车为空格，主要用于日志写入前的处理
	 */
	public static function hy_tosqlstr($str) {
		
		$replace = array("\t", "\r", "\n", "'", '"',);
		$string = str_replace($replace, ' ', $str);
		
		return mb_convert_encoding($string, "UTF-8", "auto");
		
	}
	
	
	
	
// 	//将数组转化为字符串
// 	public static function hy_array2string($arr=array()) {
		
// 		if(!is_array($arr) || count($arr)<=0) {
// 			return '';
// 		}else {
// 			$retstring = '';
// 			foreach($arr as $key => $val) {
// 				//大于500个字符长度的内容不写入日志字符串
// 				if(strlen($val)>500) {
// 					$val = '';
// 				}
// 				$retstring .= $key.'=>'.$val.'|';
// 			}
// 			return $retstring;
			
// 		}
		
// 	}
	
	
	public static function hy_func_str32($keyid=0) {
		$keyid = intval($keyid);
		//32进制数组
		$yuarr = array(
				0  => '0',
				1  => '1',
				2  => '2',
				3  => '3',
				4  => '4',
				5  => '5',
				6  => '6',
				7  => '7',
				8  => '8',
				9  => '9',
				10 => 'a',
				11 => 'b',
				12 => 'c',
				13 => 'd',
				14 => 'e',
				15 => 'f',
				16 => 'g',
				17 => 'h',
				18 => 'j',
				19 => 'k',
				20 => 'm',
				21 => 'n',
				22 => 'p',
				23 => 'q',
				24 => 'r',
				25 => 's',
				26 => 't',
				27 => 'u',
				28 => 'v',
				29 => 'w',
				30 => 'x',
				31 => 'y',
				32 => 'z',
		);
	
		$r = isset($yuarr[$keyid])?$yuarr[$keyid]:'0';
		return $r;
	
	}
	
	
	public static function hy_func_cintkey($num='') {
		if(''===$num || !is_numeric($num)) {
			return mt_rand(0,32);
		}else {
			$ccnum = 0;
			if($num>99) {
				$ccnum = substr($num,-2);
			}else {
				$ccnum = $num;
			}
	
			for($i=0;$i<8;$i++) {
				if($ccnum>32) {
					$ccnum = $ccnum-11;
				}else {
					break;
				}
			}
				
			return $ccnum;
				
		}
	}
	
	
	//检测手机号对应运营商
	public static function hy_yunyingshangcheck($phone='',$type='num') {
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
	
	
	public static function topstr_str2num($aa='') {
		$topstr = '';
		if($aa=='t') {
			$topstr = '2';
		}else if($aa=='d') {
			$topstr = '3';
		}else if($aa=='y') {
			$topstr = '1';
		}else {
			$topstr = '';
		}
		return $topstr;
	
	}
	
	
	//客户端数据输出格式化
	public static function echo2clientjson($code='',$msg='',$data=array()) {
		$jsonarr = array();
		$jsonarr['code'] = '100';
		$jsonarr['sucerr'] = (string)$code;
		$jsonarr['msg']  = (string)$msg;
		$jsonarr['data'] = $data;
		
		return str_replace("\/", "/",  json_encode($jsonarr));
// 		return json_encode($jsonarr);
		
	}
	
	

}
