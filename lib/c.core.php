<?php

/** 
 * c.core.php 业务接口核心
 * 
 * @author yu
 * 
 */


// 页面超时设置
set_time_limit(300);


//设置时区
date_default_timezone_set('Asia/Shanghai');


//项目根目录文件夹
$truepath = dirname(dirname(dirname(__FILE__))).'/';
define('TURE_PATH', $truepath);


//定义配置参数存放数组、
$cfg = array();

//引入数据库链接参数文件
require( dirname(__FILE__).'/'.'config'.'/'.'db.config.php' );

//引入核心配置参数文件
require( dirname(__FILE__).'/'.'config'.'/'.'c.config.php' );




//定义数据库常量参数
if( empty($cfg['host']) || empty($cfg['db']) ){
	//如果数据库引入文件中没有对应参数
	exit("Database Config Error!");
}

define('DBUSER', $cfg['db'][0]);
define('DBPASS', $cfg['db'][1]);
define('DBNAME', $cfg['db'][2]);
define('DBHOST', $cfg['host'][0]);
define('DBPORT', $cfg['host'][1]);




/**
 * 
 * __autoload 自动加载函数
 * 
 */
function __autoload($class_name) {
	
	$file_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.$class_name.'.php';
	
	if( !file_exists($file_path) ){
		throw new Exception('No such file as '.$class_name.'.php');
	}
	
	require_once($file_path);
	
	if( ! class_exists($class_name) ){
		throw new Exception('No such class as '.$class_name);
	}
	
	
}



