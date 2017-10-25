<?php
/**
 * 视频列表获取
 */

class HySix1015 extends HySix{
	
	private $now_page;
	private $pagesize;
	private $imgwidth;
	private $imgheight;
	private $searchstr;
	private $classify1;
	private $classify2;
	private $classify3;
	private $classify4;
	private $msgjihe;
	
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->now_page = isset($input_data['page'])?$input_data['page']:'1';
		$this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';
		$this->searchstr = isset($input_data['searchstr'])?$input_data['searchstr']:'';
		$this->classify1 = isset($input_data['classify1'])?$input_data['classify1']:'';
		$this->classify2 = isset($input_data['classify2'])?$input_data['classify2']:'';
		$this->classify3 = isset($input_data['classify3'])?$input_data['classify3']:'';
		$this->classify4 = isset($input_data['classify4'])?$input_data['classify4']:'';
		$this->msgjihe = isset($input_data['msgjihe'])?$input_data['msgjihe']:'';
		
		if(''==$this->imgwidth) {
			$this->imgwidth = 200;
		}
		if(''==$this->imgheight) {
			$this->imgheight = 200;
		}
		
	}
	
	
	protected function controller_exec1(){
		
		$sql_where = "";
		
		if(''===(string)$this->searchstr) {
			if(''!==(string)$this->classify1) {
				$sql_where .= " classify1='".$this->classify1."' and ";
			}
			if(''!==(string)$this->classify2) {
				$sql_where .= " classify2='".$this->classify2."' and ";
			}
			if(''!==(string)$this->classify3) {
				$sql_where .= " classify3='".$this->classify3."' and ";
			}
			if(''!==(string)$this->classify4) {
				$sql_where .= " classify4='".$this->classify4."' and ";
			}
			if(''!==(string)$this->msgjihe) {
				$sql_where .= " msgjihe='".$this->msgjihe."' and ";
			}
			
			if($sql_where!='') {
				$sql_where = " where ".rtrim($sql_where,'and ');
			}
			
			
		}else {
			$sql_where .= " biaoti like '%".$this->searchstr."%' or ";
			$sql_where .= " biaotichild like '%".$this->searchstr."%' or ";
			
			if($sql_where!='') {
				$sql_where = " where ".rtrim($sql_where,'or ');
			}
			
		}
		
		
		
		
		$sql_count_getvideo = "select count(*) as con from sixty_video ".$sql_where;
		//echo $sql_count_getvideo;
		$list_count_getvideo = parent::__get('HyDb')->get_one($sql_count_getvideo);
		$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideo);
		$pagemsg = $pagearr['pagemsg'];
		$pagelimit = $pagearr['pagelimit'];
		
		$sql_getvideo = "select id,classify1,classify2,classify3,classify4,showimg,biaoti,biaotichild,jieshao,create_datetime 
						from sixty_video
						".$sql_where." order by id desc ".$pagelimit;
// 		echo $sql_getvideo;
		$list_getvideo =  parent::__get('HyDb')->get_all($sql_getvideo);
		
		foreach($list_getvideo as $keygv => $valgv) {
			$list_getvideo[$keygv]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$list_getvideo[$keygv]['showimg'],$this->imgwidth,$this->imgheight);
		}
		
		$rarr = array(
				'pagemsg' => $pagemsg,
				'list' => $list_getvideo,
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