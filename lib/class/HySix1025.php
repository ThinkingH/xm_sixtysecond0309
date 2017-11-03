<?php
/**
 * 首页介绍视频图片获取
 */

class HySix1025 extends HySix{
	
	private $imgwidth;
	private $imgheight;
	
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';
		
		
	}
	
	
	protected function controller_exec1(){
		
		$echoarr = array(
				'mainpic' => HyItems::hy_qiniuimgurl('sixty-basic','mainvideopic.jpg',$this->imgwidth,$this->imgheight),
				'mainmp4' => 'http://192.168.1.11:8888/mp4/mainvideo.mp4',
		);
		
		
		$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$echoarr);
		parent::hy_log_str_add($echojsonstr."\n");
		echo $echojsonstr;
		return true;
		
		
	}
	
	
	//用户信息--操作入口
	public function controller_init(){
		
		
		//用户信息获取入口
		$this->controller_exec1();
	
		return true;
	
	
	}
	
	
	
	
}