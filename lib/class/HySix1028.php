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
		
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';
		if(''==$this->imgwidth) {
			$this->imgwidth = 200;
		}
		if(''==$this->imgheight) {
			$this->imgheight = 200;
		}
		
	}
	
	
	protected function controller_exec1(){
		
		$sql_getvideo = "select id,id as vid,showimg,videosavename,biaoti,biaotichild,create_datetime 
						from sixty_video 
						where flag='1'
						and sflag='1'
						order by rand() limit 1";
// 		echo $sql_getvideo;
		$list_getvideo =  parent::__get('HyDb')->get_row($sql_getvideo);
		
		if(count($list_getvideo)<=0) {
			$echojsonstr = HyItems::echo2clientjson('100','数据获取失败');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
		}else {
			$list_getvideo['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$list_getvideo['showimg'],$this->imgwidth,$this->imgheight);
			$list_getvideo['videourl'] = HyItems::hy_qiniubucketurl('sixty-video',$list_getvideo['videosavename']);
			
			
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