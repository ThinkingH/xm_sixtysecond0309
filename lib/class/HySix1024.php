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
		
		$this->now_page = isset($input_data['page'])?$input_data['page']:'1';//当前页数
		$this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';//单页显示条数
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';//图片宽度
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';//图片高度
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

	    //获取分页数据
		$sql_count_gettongzhi = "select count(*) as con from sixty_tongzhi ";
		$list_count_gettongzhi = parent::__get('HyDb')->get_one($sql_count_gettongzhi);
		$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_gettongzhi);
		$pagemsg = $pagearr['pagemsg'];
		$pagelimit = $pagearr['pagelimit'];

		//查询通知表
		$sql_gettongzhi  = "select id,message,create_datetime 
							from sixty_tongzhi
							order by id desc ".$pagelimit;
		
		$list_gettongzhi =  parent::__get('HyDb')->get_all($sql_gettongzhi);

		//遍历结果集
		foreach($list_gettongzhi as $keygz => $valgz) {
		    //转换日期格式并存入结果集
			$list_gettongzhi[$keygz]['create_date'] = date('Y年m月d日',strtotime($list_gettongzhi[$keygz]['create_datetime']));
		}
		

		//准备输出数字
		$rarr = array(
				'pagemsg' => $pagemsg,
				'list' => $list_gettongzhi,
		);


        //数据转为json，写入日志并输出
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