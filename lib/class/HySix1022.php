<?php
/**
 * 视频收藏数据获取
 */

class HySix1022 extends HySix{
	
	private $now_page;
	private $pagesize;
	private $imgwidth;
	private $imgheight;
	private $nowid;
	
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->now_page = isset($input_data['page'])?$input_data['page']:'1';
		$this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';
		$this->nowid     = isset($input_data['nowid'])?$input_data['nowid']:'0';
		if(''==$this->imgwidth) {
			$this->imgwidth = 300;
		}
		if(''==$this->imgheight) {
			$this->imgheight = 300;
		}
		if(!is_numeric($this->nowid)) {
			$this->nowid  =0;
		}
	}
	
	
	protected function controller_exec1(){
		
		$sql_where = " where userid='".parent::__get('userid')."' and type='1' ";
		
		//收藏数量
		$sql_count_getvideoshoucang = "select count(*) as con from sixty_video_shoucang ".$sql_where;
		$list_count_getvideoshoucang = parent::__get('HyDb')->get_one($sql_count_getvideoshoucang);
		
		$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideoshoucang);
		$pagemsg = $pagearr['pagemsg'];
		$pagelimit = $pagearr['pagelimit'];
		
		$sql_getvideoshoucang = "select id,userid,dataid,dataid as vid,create_datetime 
								from sixty_video_shoucang
								".$sql_where." order by id desc ".$pagelimit;
		//echo $sql_getvideoshoucang;
		$list_getvideoshoucang =  parent::__get('HyDb')->get_all($sql_getvideoshoucang);
		
		if(count($list_getvideoshoucang)>0) {
			$useridarr = array();
			$videoarr = array();
			foreach($list_getvideoshoucang as $valgp) {
				if(!in_array($valgp['userid'], $useridarr)) {
					array_push($useridarr, $valgp['userid']);
				}
				if(!in_array($valgp['dataid'], $videoarr)) {
					array_push($videoarr, $valgp['dataid']);
				}
			}
			
			$sql_getvideo_showdata = "select id,biaoti,showimg from sixty_video where id in (".implode(',',$videoarr).")";
			//echo $sql_getvideo_showdata;
			$list_getvideo_showdata = parent::__get('HyDb')->get_all($sql_getvideo_showdata);
			$newvideoarr = array();
			foreach($list_getvideo_showdata as $valgd) {
				$newvideoarr[$valgd['id']] = $valgd;
			}
			
			
			
			$retarr = parent::func_retsqluserdata($useridarr,50,50);
			foreach($list_getvideoshoucang as $keygp => $valgp) {
				$list_getvideoshoucang[$keygp]['biaoti'] = parent::func_userid_datatiqu($newvideoarr,$list_getvideoshoucang[$keygp]['dataid'],'biaoti');
				$list_getvideoshoucang[$keygp]['showimg'] = parent::func_userid_datatiqu($newvideoarr,$list_getvideoshoucang[$keygp]['dataid'],'showimg');
				$list_getvideoshoucang[$keygp]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$list_getvideoshoucang[$keygp]['showimg'],$this->imgwidth,$this->imgheight,true);
			}
			
			$rarr = array(
					'pagemsg' => $pagemsg,
					'list' => $list_getvideoshoucang,
			);
			
		}else {
			$rarr = array();
		}
		
		
		
		
		$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$rarr);
		parent::hy_log_str_add($echojsonstr."\n");
		echo $echojsonstr;
		return true;
		
		
	}
	
	
	//用户信息--操作入口
	public function controller_init(){
		
		//判断正式用户通讯校验参数
		$r = parent::func_oneusercheck();
		if($r===false){
			return false;
		}
		
		
		//用户信息获取入口
		$this->controller_exec1();
	
		return true;
	
	
	}
	
	
	
	
}