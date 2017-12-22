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
		
		$this->now_page = isset($input_data['page'])?$input_data['page']:'1';   //当前页数
		$this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';  //显示个数
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';    //图片宽度
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:''; //图片高度
		$this->searchstr = isset($input_data['searchstr'])?$input_data['searchstr']:''; //搜索字段
		$this->classify1 = isset($input_data['classify1'])?$input_data['classify1']:''; //分类1
		$this->classify2 = isset($input_data['classify2'])?$input_data['classify2']:''; //分类2
		$this->classify3 = isset($input_data['classify3'])?$input_data['classify3']:''; //分类3
		$this->classify4 = isset($input_data['classify4'])?$input_data['classify4']:''; //分类4
		$this->msgjihe = isset($input_data['msgjihe'])?$input_data['msgjihe']:'';       //合集

        //图片宽度默认值
		if(''==$this->imgwidth) {
			$this->imgwidth = 500;
		}
		//图片高度默认值
		if(''==$this->imgheight) {
			$this->imgheight = 500;
		}
		
	}
	
	
	protected function controller_exec1(){

	    //sql where语句开头
		$sql_where = " where flag='1' ";

		//判断文字搜索内容是否为空
		if(''===(string)$this->searchstr) {//内容为空
			if(''!==(string)$this->classify1) {//分类1不为空
				$sql_where .= " and classify1='".$this->classify1."' ";
			}
			if(''!==(string)$this->classify2) {//分类2不为空
				$sql_where .= " and classify2='".$this->classify2."' ";
			}
			if(''!==(string)$this->classify3) {//分类3不为空
				$sql_where .= " and classify3='".$this->classify3."' ";
			}
			if(''!==(string)$this->classify4) {//分类4不为空
				$sql_where .= " and classify4='".$this->classify4."' ";
			}
			if(''!==(string)$this->msgjihe) {//合集不为空
				$sql_where .= " and msgjihe='".$this->msgjihe."' ";
			}
			
		}else {//搜索文字内容不为空
			$sql_where .= " and ( biaoti like '%".$this->searchstr."%' or biaotichild like '%".$this->searchstr."%' ) ";
			
		}
		
		
		
		//拼接分页用sql语句并查询，获取总数
		$sql_count_getvideo = "select count(*) as con from sixty_video ".$sql_where;
		//echo $sql_count_getvideo;
		//$list_count_getvideoarr = parent::__get('HyDb')->get_all($sql_count_getvideo);
		$list_count_getvideoarr = parent::func_runtime_sql_data($sql_count_getvideo);
		//如果查询结果为空，则给默认值0
		$list_count_getvideo = isset($list_count_getvideoarr[0]['con'])?$list_count_getvideoarr[0]['con']:'0';

		//调用分页函数
		$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideo);
		$pagemsg = $pagearr['pagemsg'];
		$pagelimit = $pagearr['pagelimit'];

		//查询视频表数据
		$sql_getvideo = "select id,id as vid,classify1,classify2,classify3,classify4,showimg,biaoti,biaotichild,shicaititle,jieshao,create_datetime 
						from sixty_video
						".$sql_where." order by id desc ".$pagelimit;
// 		echo $sql_getvideo;
// 		$list_getvideo =  parent::__get('HyDb')->get_all($sql_getvideo);
		$list_getvideo = parent::func_runtime_sql_data($sql_getvideo);

		//遍历查询结果集
		foreach($list_getvideo as $keygv => $valgv) {
			//$list_getvideo[$keygv]['create_date'] = substr($list_getvideo[$keygv]['create_datetime'],0,10);
            //查询获取七牛云图片地址
			$list_getvideo[$keygv]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$list_getvideo[$keygv]['showimg'],$this->imgwidth,$this->imgheight);
		}

		//准备输出数组
		$rarr = array(
				'pagemsg' => $pagemsg,
				'list' => $list_getvideo,
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