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
		
		$this->now_page = isset($input_data['page'])?$input_data['page']:'1';//当前页数
		$this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';//单页显示数据
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';//图片宽度
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';//图片高度
		$this->nowid     = isset($input_data['nowid'])?$input_data['nowid']:'0';//视频id
        //默认图片宽度
		if(''==$this->imgwidth) {
			$this->imgwidth = 300;
		}
		//默认图片高度
		if(''==$this->imgheight) {
			$this->imgheight = 300;
		}
		//视频ID是否是数值
		if(!is_numeric($this->nowid)) {
			$this->nowid  =0;
		}
	}
	
	
	protected function controller_exec1(){
		
		$sql_where = " where userid='".parent::__get('userid')."' and type='1' ";
		
		//收藏数量
		$sql_count_getvideoshoucang = "select count(*) as con from sixty_video_shoucang ".$sql_where;
		$list_count_getvideoshoucang = parent::__get('HyDb')->get_one($sql_count_getvideoshoucang);

		//执行分页
		$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideoshoucang);
		$pagemsg = $pagearr['pagemsg'];
		$pagelimit = $pagearr['pagelimit'];

		//根据ID查询收藏表
		$sql_getvideoshoucang = "select id,userid,dataid,dataid as vid,create_datetime 
								from sixty_video_shoucang
								".$sql_where." order by id desc ".$pagelimit;
		//echo $sql_getvideoshoucang;
		$list_getvideoshoucang =  parent::__get('HyDb')->get_all($sql_getvideoshoucang);

		//判断结果集是否为空
		if(count($list_getvideoshoucang)>0) {
			$useridarr = array();
			$videoarr = array();

			//遍历收藏结果集
			foreach($list_getvideoshoucang as $valgp) {
				if(!in_array($valgp['userid'], $useridarr)) {
					array_push($useridarr, $valgp['userid']);
				}
				if(!in_array($valgp['dataid'], $videoarr)) {
					array_push($videoarr, $valgp['dataid']);
				}
			}

			//根据ID查询视频表数据
			$sql_getvideo_showdata = "select id,biaoti,showimg from sixty_video where id in (".implode(',',$videoarr).")";
			//echo $sql_getvideo_showdata;
			$list_getvideo_showdata = parent::__get('HyDb')->get_all($sql_getvideo_showdata);
			$newvideoarr = array();
			foreach($list_getvideo_showdata as $valgd) {
				$newvideoarr[$valgd['id']] = $valgd;
			}
			
			
			//根据id获取用户信息
			$retarr = parent::func_retsqluserdata($useridarr,50,50);
			//遍历视频收藏结果集
			foreach($list_getvideoshoucang as $keygp => $valgp) {
			    //插入标题
				$list_getvideoshoucang[$keygp]['biaoti'] = parent::func_userid_datatiqu($newvideoarr,$list_getvideoshoucang[$keygp]['dataid'],'biaoti');
				//获取视频图片
				$list_getvideoshoucang[$keygp]['showimg'] = parent::func_userid_datatiqu($newvideoarr,$list_getvideoshoucang[$keygp]['dataid'],'showimg');
				//获取七牛云图片地址
				$list_getvideoshoucang[$keygp]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$list_getvideoshoucang[$keygp]['showimg'],$this->imgwidth,$this->imgheight,true);
			}

			//准备输出数组
			$rarr = array(
					'pagemsg' => $pagemsg,
					'list' => $list_getvideoshoucang,
			);
			
		}else {
			$rarr = array();
		}



        //数据转为json，写入日志并输出
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