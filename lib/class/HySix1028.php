<?php
/**
 * app首页随机视频
 */

class HySix1028 extends HySix{
	
	private $imgwidth;
	private $imgheight;
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:''; //图片宽度
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:''; //图片高度
        //默认图片宽度
		if(''==$this->imgwidth) {
			$this->imgwidth = 300;
		}
		//默认图片高度
		if(''==$this->imgheight) {
			$this->imgheight = 300;
		}
		
	}
	
	
	protected function controller_exec1(){

	    //从视频表中随机取出一条数据
		$sql_getvideo = "select id,id as vid,showimg,videosavename,biaoti,biaotichild,create_datetime 
						from sixty_video 
						where flag='1'
						and sflag='1'
						order by rand() limit 1";
// 		echo $sql_getvideo;
		$list_getvideo =  parent::__get('HyDb')->get_row($sql_getvideo);


		//判断数据是否获取成功
		if(count($list_getvideo)<=0) {//获取失败
            //数据转为json，写入日志并输出
			$echojsonstr = HyItems::echo2clientjson('101','数据获取失败');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
		}else {//获取成功
			$list_getvideo['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$list_getvideo['showimg'],$this->imgwidth,$this->imgheight);
			$list_getvideo['videourl'] = HyItems::hy_qiniubucketurl('sixty-video',$list_getvideo['videosavename']);

            //数据转为json，写入日志并输出
			$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$list_getvideo);
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return true;
		}
		
	}
	
	
	//用户信息--操作入口
	public function controller_init(){
		
		
		//用户信息获取入口
		$this->controller_exec1();
	
		return true;
	
	
	}
	
	
	
	
}