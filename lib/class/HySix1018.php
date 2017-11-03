<?php
/**
 * 评论数据获取-视频文字评论
 */

class HySix1018 extends HySix{
	
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
			$this->imgwidth = 200;
		}
		if(''==$this->imgheight) {
			$this->imgheight = 200;
		}
		if(!is_numeric($this->nowid)) {
			$this->nowid  =0;
		}
	}
	
	
	protected function controller_exec1(){
		
		$sql_where = " where vid='".$this->nowid."' and type='1' ";
		
		$sql_count_getvideopinglun = "select count(*) as con from sixty_video_pinglun ".$sql_where;
		$list_count_getvideopinglun = parent::__get('HyDb')->get_one($sql_count_getvideopinglun);
		$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideopinglun);
		$pagemsg = $pagearr['pagemsg'];
		$pagelimit = $pagearr['pagelimit'];
		
		$sql_getvideopinglun = "select id,userid,content,create_datetime 
								from sixty_video_pinglun
								".$sql_where." order by dianzan desc,id desc ".$pagelimit;
		
		$list_getvideopinglun =  parent::__get('HyDb')->get_all($sql_getvideopinglun);
		
		$useridarr = array();
		foreach($list_getvideopinglun as $valgp) {
			if(!in_array($valgp['userid'], $useridarr)) {
				array_push($useridarr, $valgp['userid']);
			}
		}
		$retarr = parent::func_retsqluserdata($useridarr,50,50);
		foreach($list_getvideopinglun as $keygp => $valgp) {
			$list_getvideopinglun[$keygp]['create_date'] = date('Y年m月d日',strtotime($list_getvideopinglun[$keygp]['create_datetime']));
			$list_getvideopinglun[$keygp]['nickname'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun[$keygp]['userid'],'nickname');
			$list_getvideopinglun[$keygp]['touxiang'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun[$keygp]['userid'],'touxiang');
		}
		
		
		
		
// 		foreach($list_getvideopinglun as $keygv => $valgv) {
// 			$list_getvideopinglun[$keygv]['showimg'] = HyItems::hy_qiniuimgurl('sixty-imgpinglun',$list_getvideopinglun[$keygv]['showimg'],$this->imgwidth,$this->imgheight);
// 		}
		
		$rarr = array(
				'pagemsg' => $pagemsg,
				'list' => $list_getvideopinglun,
		);
		
		$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$rarr);
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