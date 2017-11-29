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

//数据库数据缓存时间，秒数
define('TMPSQLTIME',300);

//数据库缓存数据存储路径
define( 'TMPSQLPATH' , TURE_PATH.'sixty/tmpsqlfile/' );


$cilentversionkeyarr = array(
		'100' => 'fd5112f036eea77f23bac0bbbadbe592',
);
$cilentversionjson = json_encode($cilentversionkeyarr);

//客户端校验值JSON字符串
define('CILENTVERSIONJSON',$cilentversionjson);


//短信验证码下发校验key
define('MSGSENDKEY','e0f8978c0677a01aeac12cc90eed0949');

//网站根目录定义
define('BASEURL','http://127.0.0.1:8005/');

define('QINIUURL','http://127.0.0.1/hyqiniu/init/');


// //信游下的七牛账号
// $bucketarr = array(
// 		'sixty-basic'      => 'http://oys7hzyf8.bkt.clouddn.com/',  //基础公共图片存放，循环展示图片，默认图片等公共静态资源图片
// 		'sixty-user'       => 'http://oys7i4dcy.bkt.clouddn.com/',  //用户图片存放，头像，用户其他数据
// 		'sixty-video'      => 'http://oys78eqga.bkt.clouddn.com/',  //视频存放
// 		'sixty-videoimage' => 'http://oys7tcwkg.bkt.clouddn.com/',  //视频封面图片存放
// 		'sixty-imgpinglun' => 'http://oys72yckt.bkt.clouddn.com/',  //带图片投稿评论存放
// 		'sixty-jihemsg'    => 'http://oys7xme11.bkt.clouddn.com/',  //集合封面图片存放
		
// );

//极光云下的七牛账号
$bucketarr = array(
		'sixty-basic'      => 'http://p05s45h9l.bkt.clouddn.com/',  //基础公共图片存放，循环展示图片，默认图片等公共静态资源图片
		'sixty-user'       => 'http://p05srrm5u.bkt.clouddn.com/',  //用户图片存放，头像，用户其他数据
		'sixty-video'      => 'http://p05sfdtdh.bkt.clouddn.com/',  //视频存放
		'sixty-videoimage' => 'http://p05samtwb.bkt.clouddn.com/',  //视频封面图片存放
		'sixty-imgpinglun' => 'http://p05syy7rg.bkt.clouddn.com/',  //带图片投稿评论存放
		'sixty-jihemsg'    => 'http://p05svs60z.bkt.clouddn.com/',  //集合封面图片存放
		
);


$bucketstr = json_encode($bucketarr);
define('QINIUBUCKETSTR',$bucketstr);




