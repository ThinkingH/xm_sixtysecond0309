<?php
/**
 * 系统通知内容获取
 */

class HySix1024 extends HySix{
	
	private $now_page;
	private $pagesize;
	private $imgwidth;
	private $imgheight;
	
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->now_page = isset($input_data['page'])?$input_data['page']:'1';
		$this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';
		if(''==$this->imgwidth) {
			$this->imgwidth = 300;
		}
		if(''==$this->imgheight) {
			$this->imgheight = 300;
		}
		
		
	}
	
	
	protected function controller_exec1(){
		
		$sql_count_gettongzhi = "select count(*) as con from sixty_tongzhi ";
		$list_count_gettongzhi = parent::__get('HyDb')->get_one($sql_count_gettongzhi);
		$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_gettongzhi);
		$pagemsg = $pagearr['pagemsg'];
		$pagelimit = $pagearr['pagelimit'];
		
		$sql_gettongzhi  = "select id,message,create_datetime 
							from sixty_tongzhi
							order by id desc ".$pagelimit;
		
		$list_gettongzhi =  parent::__get('HyDb')->get_all($sql_gettongzhi);
		
		foreach($list_gettongzhi as $keygz => $valgz) {
			$list_gettongzhi[$keygz]['create_date'] = date('Y年m月d日',strtotime($list_gettongzhi[$keygz]['create_datetime']));
		}
		
		
		$rarr = array(
				'pagemsg' => $pagemsg,
				'list' => $list_gettongzhi,
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