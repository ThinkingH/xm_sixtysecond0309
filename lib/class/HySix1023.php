<?php
/*
 * 视频收藏添加
 */

class HySix1023 extends HySix{
	
	
	private $nowid;
	private $typeid;
	
	//数据的初始化
	function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
		//头像的存放位置
		$this->tmpimgpath = TMPPICPATH;
		
		$this->nowid          = isset($input_data['nowid']) ? $input_data['nowid'] : '' ;
		$this->typeid          = isset($input_data['typeid']) ? $input_data['typeid'] : '' ; //类型id字段,1为收藏，2为取消收藏
		
		if(1!=$this->typeid && 2!=$this->typeid) {
			$this->typeid = 2;
		}
		if(!is_numeric($this->nowid)) {
			$this->nowid = 0;
		}
	}
	
	
	public function controller_exec1(){
		
		$sql_pan = "select id from sixty_video_shoucang where dataid='".$this->nowid."' and userid='".parent::__get('userid')."' and type='1' order by id desc limit 1";
		$list_pan = parent::__get('HyDb')->get_one($sql_pan);
		
		if(1==$this->typeid) {
			$sql_videopan = "select id from sixty_video where id='".$this->nowid."' and userid='".parent::__get('userid')."' and type='1' order by id desc limit 1";
			$list_videopan = parent::__get('HyDb')->get_one($sql_pan);
			if($list_videopan>0 && $list_pan<=0) {
				$echojsonstr = HyItems::echo2clientjson('101','收藏失败');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
			}else {
				$sql_insert = "insert into sixty_video_shoucang (type,userid,dataid,create_datetime) values('1','".parent::__get('userid')."','".$this->nowid."','".date('Y-m-d H:i:s')."')";
				$list_insert =  parent::__get('HyDb')->execute($sql_insert);
				$echojsonstr = HyItems::echo2clientjson('100','收藏成功');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
				
			}
			
		}else {
			if($list_pan<=0) {
				$echojsonstr = HyItems::echo2clientjson('101','取消收藏失败');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
			}else {
				$sql_delete = "delete from sixty_video_shoucang where dataid='".$this->nowid."' and userid='".parent::__get('userid')."' and type='1'";
				$list_delete =  parent::__get('HyDb')->execute($sql_delete);
				$echojsonstr = HyItems::echo2clientjson('100','取消收藏成功');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
				
			}
			
			
			
			
		}
		
		
		
		
	}
	
	
	
	
	//操作入口--头像的上传
	public function controller_init(){
		
		//判断正式用户通讯校验参数
		$r = parent::func_oneusercheck();
		if($r===false){
			return false;
		}
		
		
		//头像的上传
		$this->controller_exec1();
	
		return true;
	
	
	}
	
}