<?php
/*
 * 评论删除
 */

class HySix1021 extends HySix{
	
	
	private $tmpimgpath; //图片临时存储
	private $dataid;
	private $typeid;
	private $delid;
	
	//数据的初始化
	function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
		//头像的存放位置
		$this->tmpimgpath = TMPPICPATH;
		
		$this->dataid   = isset($input_data['dataid']) ? $input_data['dataid'] : '' ; //视频id字段
		$this->typeid   = isset($input_data['typeid']) ? $input_data['typeid'] : '' ; //类型id字段（1文字评论，2图片评论）
		$this->delid    = isset($input_data['delid']) ? $input_data['delid'] : '' ;  //评论删除id
		
	}
	
	
	public function controller_exec1(){
		
		//判断评论id是否存在
		if(!is_numeric($this->dataid)) {
			$echojsonstr = HyItems::echo2clientjson('101','视频id字段不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		if(2!=$this->typeid && 1!=$this->typeid) {
			$echojsonstr = HyItems::echo2clientjson('101','评论类型不存在');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		if(!is_numeric($this->delid)) {
			$echojsonstr = HyItems::echo2clientjson('101','删除评论id格式不正确');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
		
		$sql_pan = "select id,showimg from sixty_video_pinglun where type='".$this->typeid."' and vid='".$this->dataid."' and userid='".parent::__get('userid')."' and id='".$this->delid."'";
		$list_pan = parent::__get('HyDb')->get_row($sql_pan);
		
		if(count($list_pan)<=0) {
			$echojsonstr = HyItems::echo2clientjson('101','删除评论数据不存在');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}else {
			$showimg = $list_pan['showimg'];
			
			//调用删除接口删除评论图片
			$r = parent::delete_qiniu('sixty-imgpinglun',$showimg);
			
			//执行图片删除操作
			$sql_delete = "delete from sixty_video_pinglun where id='".$this->delid."' and userid='".parent::__get('userid')."' and type='".$this->typeid."' and vid='".$this->dataid."'";
			parent::__get('HyDb')->execute($sql_delete);
			
			
			$echojsonstr = HyItems::echo2clientjson('100','评论删除成功');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
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