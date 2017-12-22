<?php
/**
 * 首页介绍视频图片获取--PC，WAP版
 */

class HySix1025 extends HySix{
	
	private $imgwidth;
	private $imgheight;
	
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'960';//图片宽度，默认960
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'540';//图片高度，默认540
		
		
	}
	
	
	protected function controller_exec1(){
//
//		$echoarr = array(
//				'mainpic' => HyItems::hy_qiniuimgurl('sixty-basic','mainvideopic.jpg',$this->imgwidth,$this->imgheight),
//				'mainmp4' => 'http://192.168.1.11:8888/mp4/mainvideo.mp4',
//		);

        //查询视频表，取出3条数据
		$sql = "select id, showimg, biaoti, videosavename from sixty_video where sflag <> 0 order by rand() limit 3";
        $data = parent::func_runtime_sql_data($sql);

        //遍历结果集
        foreach($data as $k_d => $v_d){
            //获取七牛云图片地址
            $data[$k_d]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$v_d['showimg'],$this->imgwidth,$this->imgheight,true);
        }

        //数据转为json，写入日志并输出
		$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$data);
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