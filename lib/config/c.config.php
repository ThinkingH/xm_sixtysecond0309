<?php


/** 
 * c.config.php 业务接口配置
 * author yu
 * 
 */


//日志存放根目录
define( 'LOGPATH' , TURE_PATH.'sixty/log/' );

//临时图片存储路径
define( 'TMPPICPATH' , TURE_PATH.'sixty/tmppicfile/' );

$cilentversionkeyarr = array(
		'100' => 'fd5112f036eea77f23bac0bbbadbe592',
);
$cilentversionjson = json_encode($cilentversionkeyarr);

//客户端校验值JSON字符串
define('CILENTVERSIONJSON',$cilentversionjson);


//短信验证码下发校验key
define('MSGSENDKEY','e0f8978c0677a01aeac12cc90eed0949');

//网站根目录定义
define('BASEURL','http://127.0.0.1:8009/');

define('QINIUURL','http://127.0.0.1:8001/hyqiniu/init/');

// $bucketarr = array(
// 		'sixty-basic'      => 'http://ox1bxmb6a.bkt.clouddn.com/',
// 		'sixty-user'       => 'http://ox1br6s4h.bkt.clouddn.com/',
// 		'sixty-video'      => 'http://ox1b34md9.bkt.clouddn.com/',
// 		'sixty-videoimage' => 'http://ox1cq2koa.bkt.clouddn.com/',
// 		'sixty-imgpinglun' => 'http://oy1shb1ug.bkt.clouddn.com/',
// 		'sixty-jihemsg'    => 'http://oy24w69i6.bkt.clouddn.com/',
		
// );
$bucketarr = array(
		'sixty-basic'      => 'http://oys7hzyf8.bkt.clouddn.com/',  //基础公共图片存放，循环展示图片，默认图片等公共静态资源图片
		'sixty-user'       => 'http://oys7i4dcy.bkt.clouddn.com/',  //用户图片存放，头像，用户其他数据
		'sixty-video'      => 'http://oys78eqga.bkt.clouddn.com/',  //视频存放
		'sixty-videoimage' => 'http://oys7tcwkg.bkt.clouddn.com/',  //视频封面图片存放
		'sixty-imgpinglun' => 'http://oys72yckt.bkt.clouddn.com/',  //带图片投稿评论存放
		'sixty-jihemsg'    => 'http://oys7xme11.bkt.clouddn.com/',  //集合封面图片存放
		
);

$bucketstr = json_encode($bucketarr);
define('QINIUBUCKETSTR',$bucketstr);


// $bucketarr = array(
// 		//本地不在存储图片，上传完毕后直接删除，严格要求不使用的图片必须删除(调用封装的七牛删除接口)
// 		'duibao-basic'    => 'http://oymkhn027.bkt.clouddn.com/',  //基础公共图片存放，公共图片图标，循环展示图片，默认图片，抽奖小页面等公共静态资源图片
// 		'duibao-user'     => 'http://oyojv7be2.bkt.clouddn.com/',  //用户图片存放，头像，用户其他数据
// 		'duibao-business' => 'http://oyojteo81.bkt.clouddn.com/',  //商家图片存放，如商家营业执照，认证扫描图片，合同等
// 		'duibao-find'     => 'http://oyoj423p4.bkt.clouddn.com/',  //发现图片存放，用户发布的发现数据内容图片
// 		'duibao-shop'     => 'http://oyojvph72.bkt.clouddn.com/',  //商城图片存放，各种商品图片
		
// );



// //积分与金钱转换的折扣
// define('DISCOUNT','100');

// //图片访问的链接地址
// define('URLPATH','http://xbapp.xinyouxingkong.com/duidui/picture/');

// //apk更新后台的地址
// define('URLUPDATE','http://xbapp.xinyouxingkong.com/dd_system/');

// //流量的下发接口
// define('XAIFALIULIANGURL','http://xbapp.xinyouxingkong.com/dh_work/interface/dhinit.php');

// //对内转发调用地址
// define('URLSEND','http://xbapp.xinyouxingkong.com/duidui/interface/xbinit.php');

// //上传头像的存放位置
// define('IMAGEPATH','/data/wwwroot/duibao/duidui/picture/touxiang/');

// //广告图片的保存
// define('ADIMAGEPATH','/data/wwwroot/duibao/duidui/picture/advertisement/');


