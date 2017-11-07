<?php
/**
 * 评论数据获取-带图片-图片投稿---个人页进入
 */

class HySix1029 extends HySix{
	
	private $now_page;
	private $pagesize;
	private $imgwidth;
	private $imgheight;
	private $nowid;
	private $typeid;
	
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->now_page = isset($input_data['page'])?$input_data['page']:'1';
		$this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'200';
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'200';
		$this->nowid     = isset($input_data['nowid'])?$input_data['nowid']:'0';
		$this->typeid     = isset($input_data['typeid'])?$input_data['typeid']:'2';
		if(!is_numeric($this->nowid)) {
			$this->nowid = 0;
		}
		if(!is_numeric($this->typeid)) {
			$this->typeid = 1;
		}
		if(''==$this->imgwidth) {
			$this->imgwidth = 200;
		}
		if(''==$this->imgheight) {
			$this->imgheight = 200;
		}
	}
	
	
	protected function controller_exec2(){
		
		$sql_where = " where userid='".parent::__get('userid')."' and type='2' and id='".$this->nowid."' ";
		
		$sql_getvideopinglun = "select id,vid,userid,content,showimg,create_datetime 
								from sixty_video_pinglun
								".$sql_where." order by id desc limit 1";
		$list_getvideopinglun =  parent::__get('HyDb')->get_row($sql_getvideopinglun);
		//echo $sql_getvideopinglun;
		
		if(count($list_getvideopinglun)>0) {
			$thevid = $list_getvideopinglun['vid'];
			$sql_getvideoimg = "select showimg from sixty_video where id='".$thevid."' order by id desc limit 1";
			$list_getvideoimg = parent::__get('HyDb')->get_one($sql_getvideoimg);
			
			
			$retarr = parent::func_retsqluserdata(array(parent::__get('userid')),50,50);
			
			$list_getvideopinglun['create_date'] = date('Y年m月d日',strtotime($list_getvideopinglun['create_datetime']));
			$list_getvideopinglun['videoimg']  = HyItems::hy_qiniuimgurl('sixty-videoimage',$list_getvideoimg,50,50);
			$list_getvideopinglun['pinglunimg']  = HyItems::hy_qiniuimgurl('sixty-imgpinglun',$list_getvideopinglun['showimg'],$this->imgwidth,$this->imgheight);
			$list_getvideopinglun['nickname'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun['userid'],'nickname');
			$list_getvideopinglun['touxiang'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun['userid'],'touxiang');
			
		}else {
			$list_getvideopinglun = array();
		}
		
		
		$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$list_getvideopinglun);
		parent::hy_log_str_add($echojsonstr."\n");
		echo $echojsonstr;
		return true;
		
		
	}
	
	
	//用户信息--操作入口
	public function controller_init(){
		
		if($this->typeid==2) {
			//获取该用户的所有投稿
			$this->controller_exec2();
		}else {
			//获取视频id下的所有投稿
			$this->controller_exec2();
		}
		
	
		return true;
	
	
	}
	
	
	
	
}