<?php
/**
 * 评论数据获取-带图片-图片投稿
 */

class HySix1019 extends HySix{
	
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
		
		$this->now_page = isset($input_data['page'])?$input_data['page']:'1';//当前页码
		$this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';//单页显示条数
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';//图片宽度
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';//图片高度
		$this->nowid     = isset($input_data['nowid'])?$input_data['nowid']:'0';//视频ID
		$this->typeid     = isset($input_data['typeid'])?$input_data['typeid']:'1';//查询类型
        //id是否为空
		if(!is_numeric($this->nowid)) {
			$this->nowid = 0;
		}
		//查询类型是否为空
		if(!is_numeric($this->typeid)) {
			$this->typeid = 1;
		}
		//图片默认宽度
		if(''==$this->imgwidth) {
			$this->imgwidth = 300;
		}
		//图片默认高度
		if(''==$this->imgheight) {
			$this->imgheight = 300;
		}
	}
	
	
	protected function controller_exec1(){

	    //准备sql where语句
		$sql_where = " where vid='".$this->nowid."' and type='2' ";

		//查询分页数据
		$sql_count_getvideopinglun = "select count(*) as con from sixty_video_pinglun ".$sql_where;
		$list_count_getvideopinglun = parent::__get('HyDb')->get_one($sql_count_getvideopinglun);
		$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideopinglun);
		$pagemsg = $pagearr['pagemsg'];
		$pagelimit = $pagearr['pagelimit'];


		//查询评论表 带图评论
		$sql_getvideopinglun = "select id,userid,content,showimg,create_datetime 
								from sixty_video_pinglun
								".$sql_where." order by dianzan desc,id desc ".$pagelimit;
		
		$list_getvideopinglun =  parent::__get('HyDb')->get_all($sql_getvideopinglun);

		$useridarr = array();
		//遍历评论表结果集
		foreach($list_getvideopinglun as $valgp) {
		    //取出用户ID
			if(!in_array($valgp['userid'], $useridarr)) {
				array_push($useridarr, $valgp['userid']);
			}
		}
		//根据用户ID获取用户信息
		$retarr = parent::func_retsqluserdata($useridarr,50,50);

		//遍历评论结果集
		foreach($list_getvideopinglun as $keygp => $valgp) {
		    //评论时间
			$list_getvideopinglun[$keygp]['create_date'] = date('Y年m月d日',strtotime($list_getvideopinglun[$keygp]['create_datetime']));
			//评论图片地址
			$list_getvideopinglun[$keygp]['showimg'] = HyItems::hy_qiniuimgurl('sixty-imgpinglun',$list_getvideopinglun[$keygp]['showimg'],$this->imgwidth,$this->imgheight);
			//用户昵称
			$list_getvideopinglun[$keygp]['nickname'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun[$keygp]['userid'],'nickname');
			//用户头像
			$list_getvideopinglun[$keygp]['touxiang'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun[$keygp]['userid'],'touxiang');
            //评论文字内容
            $list_getvideopinglun[$keygp]['content'] = base64_decode($valgp['content']);

		}

		//准备输出数组
		$rarr = array(
				'pagemsg' => $pagemsg,
				'list' => $list_getvideopinglun,
		);

        //数据转为json，写入日志并输出
		$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$rarr);
		parent::hy_log_str_add($echojsonstr."\n");
		echo $echojsonstr;
		return true;
		
		
	}
	
	protected function controller_exec2(){
	    //准备sql where 语句
		$sql_where = " where userid='".parent::__get('userid')."' and type='2' ";


		//查询分页信息
		$sql_count_getvideopinglun = "select count(*) as con from sixty_video_pinglun ".$sql_where;
		$list_count_getvideopinglun = parent::__get('HyDb')->get_one($sql_count_getvideopinglun);
		$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideopinglun);
		$pagemsg = $pagearr['pagemsg'];
		$pagelimit = $pagearr['pagelimit'];


		//查询评论表数据
		$sql_getvideopinglun = "select id,userid,content,showimg,create_datetime 
								from sixty_video_pinglun
								".$sql_where." order by dianzan desc,id desc ".$pagelimit;
		
		$list_getvideopinglun =  parent::__get('HyDb')->get_all($sql_getvideopinglun);


		$useridarr = array();
		//遍历评论结果集
		foreach($list_getvideopinglun as $valgp) {
		    //取出用户ID
			if(!in_array($valgp['userid'], $useridarr)) {
				array_push($useridarr, $valgp['userid']);
			}
		}

		//根据用户ID获取用户信息
		$retarr = parent::func_retsqluserdata($useridarr,50,50);

		//遍历评论结果集
		foreach($list_getvideopinglun as $keygp => $valgp) {
		    //评论时间
			$list_getvideopinglun[$keygp]['create_date'] = date('Y年m月d日',strtotime($list_getvideopinglun[$keygp]['create_datetime']));
			//评论图片
			$list_getvideopinglun[$keygp]['showimg'] = HyItems::hy_qiniuimgurl('sixty-imgpinglun',$list_getvideopinglun[$keygp]['showimg'],$this->imgwidth,$this->imgheight);
			//用户昵称
			$list_getvideopinglun[$keygp]['nickname'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun[$keygp]['userid'],'nickname');
			//用户头像
			$list_getvideopinglun[$keygp]['touxiang'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun[$keygp]['userid'],'touxiang');
            //评论文字内容
            $list_getvideopinglun[$keygp]['content'] = base64_decode($list_getvideopinglun[$keygp]['content']);

		}


		//准备输出数组
		$rarr = array(
				'pagemsg' => $pagemsg,
				'list' => $list_getvideopinglun,
		);


        //数据转为json，写入日志并输出
		$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$rarr);
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
			$this->controller_exec1();
		}
		
	
		return true;
	
	
	}
	
	
	
	
}