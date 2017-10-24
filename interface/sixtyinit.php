<?php

// //断开连接后继续执行，参数用法详见手册
// ignore_user_abort(true);

//error_reporting(E_ALL);
//ini_set('display_errors', '1');


//引入主文件
require_once("../lib/c.core.php");


//获取当前文件名称
$mname = basename(__FILE__,'.php');


if( empty($_REQUEST) ){
	exit('error,no parameter');
}

$true_ip = HyItems::hy_get_client_ip();
$getbaseurl   = $_SERVER["REQUEST_URI"]; //当前目录地址及get参数
$postbaseurl  = file_get_contents("php://input"); //原始post数据

$log_str = "\n".'SIXTYSECOND-------------------------------------------------------'."\n".
			date('Y-m-d H:i:s').'    '.$getbaseurl."\n".$postbaseurl."\n";


//文件的路径
$filepath = LOGPATH.date('Y_m').'/';

//文件的名称
$filename = date('Y_m_d').'_'.$mname;


$inputdataarr = array();

$inputdataarr['version']         = trim(HyItems::arrayItem ( $_REQUEST, 'version' ));    //接口版本,后台接口版本，与app无关,每个version版本均对应一个版本秘钥,100~999
$inputdataarr['system']          = trim(HyItems::arrayItem ( $_REQUEST, 'system' ));    //操作系统，IOS/ANDROID/PC，非IOS、PC全部默认为ANDROID
$inputdataarr['sysversion']      = trim(HyItems::arrayItem ( $_REQUEST, 'sysversion' ));    ////APP系统版本，例100
$inputdataarr['thetype']         = trim(HyItems::arrayItem ( $_REQUEST, 'thetype' ));    //操作类型编号，4位长度，第一位为内部层级版本号，后三位自定义
$inputdataarr['nowtime']         = trim(HyItems::arrayItem ( $_REQUEST, 'nowtime' ));    //时间戳，预留字段，用于后期校验增加安全性使用
$inputdataarr['md5key']          = trim(HyItems::arrayItem ( $_REQUEST, 'md5key' ));     //MD5校验值采用md5(version+system+sysversion+thetype+nowtime+ckey)//通讯层校验

$inputdataarr['usertype']        = trim(HyItems::arrayItem ( $_REQUEST, 'usertype' ));   //用户类型，1为正常用户，其他为非用户
$inputdataarr['userid']          = trim(HyItems::arrayItem ( $_REQUEST, 'userid' ));     //用户在平台的标识编号
$inputdataarr['userkey']         = trim(HyItems::arrayItem ( $_REQUEST, 'userkey' ));    //用户通讯的校验密钥

$inputdataarr['phone']           = trim(HyItems::arrayItem ( $_REQUEST, 'phone' ));       //手机号必须为11位
$inputdataarr['vcode']           = trim(HyItems::arrayItem ( $_REQUEST, 'vcode' ));       //手机下发验证码

$inputdataarr['pagesize']        = trim(HyItems::arrayItem ( $_REQUEST, 'pagesize' ));            //每页的条数，数值介于1到200之间
$inputdataarr['page']            = trim(HyItems::arrayItem ( $_REQUEST, 'page' ));            //数据请求对应页数

$inputdataarr['yijian']          = trim(HyItems::arrayItem ( $_REQUEST, 'yijian' ));       //用户反馈的意见内容
$inputdataarr['sex']             = trim(HyItems::arrayItem ( $_REQUEST, 'sex' ));           //性别，1男，2女，3保密
$inputdataarr['birthday']        = trim(HyItems::arrayItem ( $_REQUEST, 'birthday' ));      //生日
$inputdataarr['nickname']        = trim(HyItems::arrayItem ( $_REQUEST, 'nickname' ));       //用户昵称
$inputdataarr['describes']        = trim(HyItems::arrayItem ( $_REQUEST, 'describes' ));       //用户描述介绍
$inputdataarr['headimgurl']        = trim(HyItems::arrayItem ( $_REQUEST, 'headimgurl' ));       //headimgurl

$inputdataarr['houzhui']        = trim(HyItems::arrayItem ( $_REQUEST, 'houzhui' ));             //图片的后缀
$inputdataarr['imgdata']        = trim(HyItems::arrayItem ( $_REQUEST, 'imgdata' ));             //图片

$inputdataarr['jiguangid']        = trim(HyItems::arrayItem ( $_REQUEST, 'jiguangid' ));             //极光id
$inputdataarr['openid']           = trim(HyItems::arrayItem ( $_REQUEST, 'openid' ));             //微信的openid
$inputdataarr['dataid']           = trim(HyItems::arrayItem ( $_REQUEST, 'dataid' ));             //视频id字段
$inputdataarr['nowid']       = trim(HyItems::arrayItem ( $_REQUEST, 'nowid' ));  //请求的数据id字段
$inputdataarr['typeid']       = trim(HyItems::arrayItem ( $_REQUEST, 'typeid' ));  //类型id字段（1文字评论，2图片评论）
$inputdataarr['contentdata']       = trim(HyItems::arrayItem ( $_REQUEST, 'contentdata' ));  //内容数据


$inputdataarr['delid']        = trim(HyItems::arrayItem ( $_REQUEST, 'delid' ));             //客户端存储--数据删除id，多个id用逗号分隔
$inputdataarr['box']        = trim(HyItems::arrayItem ( $_REQUEST, 'box' ));             //客户端存储--分类盒子指定
$inputdataarr['key1']        = trim(HyItems::arrayItem ( $_REQUEST, 'key1' ));             //客户端存储--分类键名指定，后面可模糊匹配，例，a可匹配ab,abc,aaa等a开头键名
$inputdataarr['val1']        = trim(HyItems::arrayItem ( $_REQUEST, 'val1' ));             //客户端存储--值1
$inputdataarr['val2']        = trim(HyItems::arrayItem ( $_REQUEST, 'val2' ));             //客户端存储--值1
$inputdataarr['val3']        = trim(HyItems::arrayItem ( $_REQUEST, 'val3' ));             //客户端存储--值1


$inputdataarr['sharequan']           = trim(HyItems::arrayItem ( $_REQUEST, 'sharequan' ));//是否分享到朋友圈  设置固定值666
$inputdataarr['sharefriend']         = trim(HyItems::arrayItem ( $_REQUEST, 'sharefriend' ));//是否分享到好友    设置固定值888

$inputdataarr['lat']         = trim(HyItems::arrayItem ( $_REQUEST, 'lat' ));//纬度
$inputdataarr['lng']         = trim(HyItems::arrayItem ( $_REQUEST, 'lng' ));//经度

$inputdataarr['imgwidth']        = trim(HyItems::arrayItem ( $_REQUEST, 'imgwidth' ));
$inputdataarr['imgheight']        = trim(HyItems::arrayItem ( $_REQUEST, 'imgheight' ));
$inputdataarr['classtype']        = trim(HyItems::arrayItem ( $_REQUEST, 'classtype' )); //分类字段str
$inputdataarr['searchstr']        = trim(HyItems::arrayItem ( $_REQUEST, 'searchstr' )); //查询字符串
$inputdataarr['classify1']        = trim(HyItems::arrayItem ( $_REQUEST, 'classify1' )); //分类1
$inputdataarr['classify2']        = trim(HyItems::arrayItem ( $_REQUEST, 'classify2' )); //分类2
$inputdataarr['classify3']        = trim(HyItems::arrayItem ( $_REQUEST, 'classify3' )); //分类3
$inputdataarr['classify4']        = trim(HyItems::arrayItem ( $_REQUEST, 'classify4' )); //分类4
$inputdataarr['msgjihe']        = trim(HyItems::arrayItem ( $_REQUEST, 'msgjihe' )); //特辑集合id




$inputdataarr['touserid']  = trim(HyItems::arrayItem ( $_REQUEST, 'touserid' ));
$inputdataarr['cid']      = trim(HyItems::arrayItem ( $_REQUEST, 'cid' ));
$inputdataarr['dtype']   = trim(HyItems::arrayItem ( $_REQUEST, 'dtype' ));  //删除层级类型id---m主表评论---c字表回复


$inputdataarr['code']  = trim(HyItems::arrayItem ( $_REQUEST, 'code' ));  //微信请求code


// $inputdataarr['mobile']          = trim(HyItems::arrayItem ( $_REQUEST, 'mobile' ));          //联系人手机号
// $inputdataarr['shouhuoren']      = trim(HyItems::arrayItem ( $_REQUEST, 'shouhuoren' ));      //收货人
// $inputdataarr['province']        = trim(HyItems::arrayItem ( $_REQUEST, 'province' ));        //省份
// $inputdataarr['city']            = trim(HyItems::arrayItem ( $_REQUEST, 'city' ));            //城市
// $inputdataarr['address']         = trim(HyItems::arrayItem ( $_REQUEST, 'address' ));         //详细的收货地址
// $inputdataarr['zipcode']         = trim(HyItems::arrayItem ( $_REQUEST, 'zipcode' ));         //邮政编码
// $inputdataarr['is_default']      = trim(HyItems::arrayItem ( $_REQUEST, 'is_default' ));      //是否设置为默认地址
// $inputdataarr['address_id']     = trim(HyItems::arrayItem ( $_REQUEST, 'address_id' ));      //收货地址的唯一标识编号




if('IOS'!=$inputdataarr['system'] && 'PC'!=$inputdataarr['system']) {
	$inputdataarr['system'] = 'ANDROID';
}
if(!is_numeric($inputdataarr['version'])) {
	$echojsonstr = HyItems::echo2clientjson('401','接口版本号不能为空');
	$log_str = $echojsonstr."\n";
	HyItems::hy_writelog($filepath, $filename, $log_str);
	exit($echojsonstr);
}
if(!is_numeric($inputdataarr['thetype']) || strlen($inputdataarr['thetype'])!=4) {
	$echojsonstr = HyItems::echo2clientjson('402','操作类型编号格式错误');
	$log_str = $echojsonstr."\n";
	HyItems::hy_writelog($filepath, $filename, $log_str);
	exit($echojsonstr);
}
if(!is_numeric($inputdataarr['nowtime']) || abs(time()-$inputdataarr['nowtime'])>31*24*60*60) {
	$echojsonstr = HyItems::echo2clientjson('403','时间戳错误');
	$log_str = $echojsonstr."\n";
	HyItems::hy_writelog($filepath, $filename, $log_str);
	exit($echojsonstr);
}
if(strlen($inputdataarr['md5key'])!=32) {
	$echojsonstr = HyItems::echo2clientjson('404','md5key格式错误');
	$log_str = $echojsonstr."\n";
	HyItems::hy_writelog($filepath, $filename, $log_str);
	exit($echojsonstr);
}



$clientversionkeyarr = json_decode(CILENTVERSIONJSON,true);
$ckey = isset($clientversionkeyarr[$inputdataarr['version']])?$clientversionkeyarr[$inputdataarr['version']]:'';

if($ckey=='') {
	$echojsonstr = HyItems::echo2clientjson('405','接口版本不存在');
	$log_str = $echojsonstr."\n";
	HyItems::hy_writelog($filepath, $filename, $log_str);
	exit($echojsonstr);
}

$checkstring = $inputdataarr['version'].$inputdataarr['system'].$inputdataarr['sysversion'].$inputdataarr['thetype'].$inputdataarr['nowtime'].$ckey;
if(md5($checkstring)!=$inputdataarr['md5key']) {
	if('127.0.0.1'==$true_ip) {
		//跳过md5key校验
		if($inputdataarr['md5key']=='00000000111111112222222233333333') {
			//通过
		}else {
			$echojsonstr = HyItems::echo2clientjson('406','md5key校验不通过');
			$log_str = $echojsonstr."\n";
			HyItems::hy_writelog($filepath, $filename, $log_str);
			exit($echojsonstr);
		}
	}
}else {
	//通过
}


//判断用户,当用户类型为1时，认为该用户为正式用户，需要进行用户校验
if(1==$inputdataarr['usertype']) {
	if($inputdataarr['userid']=='' || ''==$inputdataarr['userkey']) {
		$echojsonstr = HyItems::echo2clientjson('407','正式用户参数不正确');
		$log_str = $echojsonstr."\n";
		HyItems::hy_writelog($filepath, $filename, $log_str);
		exit($echojsonstr);
	}
}else {
	//当非正常用户时，置空
	$inputdataarr['userid'] = '';
	$inputdataarr['userkey'] = '';
}

//=======================================================
if('请输入手机号'==$inputdataarr['phone']) {
	$inputdataarr['phone'] = '';
}



$HySixCon = new HySixCon($inputdataarr);

$HySixCon->controller();



//将数据写入日志文件
HyItems::hy_writelog($filepath, $filename, $log_str);






