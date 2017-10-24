<?php
/**
 * 视频列表分类获取
 */

class HySix1017 extends HySix{
	
	private $now_page;
	private $pagesize;
	private $classtype;
	private $searchstr;
	private $imgwidth;
	private $imgheight;
	
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->now_page = isset($input_data['page'])?$input_data['page']:'1';
		$this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';
		$this->classtype = isset($input_data['classtype'])?trim($input_data['classtype']):'';
		$this->searchstr = isset($input_data['searchstr'])?trim($input_data['searchstr']):'';
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';
		if(''==$this->imgwidth) {
			$this->imgwidth = 200;
		}
		if(''==$this->imgheight) {
			$this->imgheight = 200;
		}
	}
	
	
	protected function controller_exec1(){
		
		$echoarr = array();
		$echosearch  = '';
		$echocontent = '';
		$echoallcon  = 0;
		
		$guize = '1';
		
		$hy_classifynamearr = array();
		$sql_getcontent = "select id,name,childname,content from sixty_classifymsg";
		$list_getcontent = parent::__get('HyDb')->get_all($sql_getcontent);
		foreach($list_getcontent as $valgc) {
			$hy_classifynamearr[$valgc['name']] = $valgc;
		}
		
		
		if(''==$this->classtype || 'classify'==$this->classtype) {
			$sql_getclassify1 = "select classify1,count(*) as con from sixty_video group by classify1 order by classify1";
			$list_getclassify1  = parent::__get('HyDb')->get_all($sql_getclassify1);
			foreach($list_getclassify1 as $valgc) {
				if(''!=$valgc['classify1']) {
					$tmpchildname = isset($hy_classifynamearr[$valgc['classify1']]['childname'])?$hy_classifynamearr[$valgc['classify1']]['childname']:'';
					$tmparr = array(
							'type' => 'classify1',
							'name' => $valgc['classify1'],
							'childname' => $tmpchildname,
							'count' => $valgc['con'],
					);
					array_push($echoarr,$tmparr);
					$echoallcon += $valgc['con'];
				}
			}
		}else if('classify1'==$this->classtype) {
			$sqlwhere = '';
			if(''!==(string)$this->searchstr) {
				$echosearch = $this->searchstr;
				$sqlwhere = " where classify1='".$this->searchstr."'";
			}
			$sql_getclassify2 = "select classify2,count(*) as con from sixty_video ".$sqlwhere." group by classify2 order by classify2";
			$list_getclassify2  = parent::__get('HyDb')->get_all($sql_getclassify2);
			foreach($list_getclassify2 as $valgc) {
				if(''!=$valgc['classify2']) {
					$tmpchildname = isset($hy_classifynamearr[$valgc['classify2']]['childname'])?$hy_classifynamearr[$valgc['classify2']]['childname']:'';
					$tmparr = array(
							'type' => 'classify2',
							'name' => $valgc['classify2'],
							'childname' => $tmpchildname,
							'count' => $valgc['con'],
					);
					array_push($echoarr,$tmparr);
					$echoallcon += $valgc['con'];
				}
			}
		}else if('classify2'==$this->classtype) {
			$sqlwhere = '';
			if(''!==(string)$this->searchstr) {
				$echosearch = $this->searchstr;
				$sqlwhere = " where classify2='".$this->searchstr."'";
			}
			$sql_getclassify3 = "select classify3,count(*) as con from sixty_video ".$sqlwhere." group by classify3 order by classify3";
			$list_getclassify3  = parent::__get('HyDb')->get_all($sql_getclassify3);
			foreach($list_getclassify3 as $valgc) {
				if(''!=$valgc['classify3']) {
					$tmpchildname = isset($hy_classifynamearr[$valgc['classify3']]['childname'])?$hy_classifynamearr[$valgc['classify3']]['childname']:'';
					$tmparr = array(
							'type' => 'classify3',
							'name' => $valgc['classify3'],
							'childname' => $tmpchildname,
							'count' => $valgc['con'],
					);
					array_push($echoarr,$tmparr);
					$echoallcon += $valgc['con'];
				}
			}
		}else if('classify3'==$this->classtype) {
			$sqlwhere = '';
			if(''!==(string)$this->searchstr) {
				$echosearch = $this->searchstr;
				$sqlwhere = " where classify3='".$this->searchstr."'";
			}
			$sql_getclassify4 = "select classify4,count(*) as con from sixty_video ".$sqlwhere." group by classify4 order by classify4";
			$list_getclassify4  = parent::__get('HyDb')->get_all($sql_getclassify4);
			foreach($list_getclassify4 as $valgc) {
				if(''!=$valgc['classify4']) {
					$tmpchildname = isset($hy_classifynamearr[$valgc['classify4']]['childname'])?$hy_classifynamearr[$valgc['classify4']]['childname']:'';
					$tmparr = array(
							'type' => 'classify4',
							'name' => $valgc['classify4'],
							'childname' => $tmpchildname,
							'count' => $valgc['con'],
					);
					array_push($echoarr,$tmparr);
					$echoallcon += $valgc['con'];
				}
			}
		}else if('msgjihe'==$this->classtype) {
			if(''!==(string)$this->searchstr) {
				$guize = '2'; //指定输出规则格式，特辑单独格式
				
				$sql_getonemsg = "select * from sixty_jihemsg where id='".$this->searchstr."'";
				$list_getonemsg = parent::__get('HyDb')->get_row($sql_getonemsg);
				$echoarr = $list_getonemsg;
				$echoarr['showimg'] = HyItems::hy_qiniuimgurl('sixty-jihemsg',$echoarr['showimg'],$this->imgwidth,$this->imgheight,true);
				
			}else {
				
				$sql_count_jihedata = "select count(*) as con from sixty_jihemsg ";
				//echo $sql_count_getvideo;
				$list_count_jihedata = parent::__get('HyDb')->get_one($sql_count_jihedata);
				$pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_jihedata);
				$pagemsg = $pagearr['pagemsg'];
				$pagelimit = $pagearr['pagelimit'];
				
				
				$sql_jihedata = "select id,name,showimg,content from sixty_jihemsg order by id desc ".$pagelimit;
				$list_jihedata = parent::__get('HyDb')->get_all($sql_jihedata);
				$jihearr = array();
				$jiheidarr = array();
				foreach($list_jihedata as $valj) {
					$jihearr[$valj['id']] = $valj;
					array_push($jiheidarr,$valj['id']);
				}
				$jiheinstr = '';
				if(count($jiheidarr)>0) {
					$jiheinstr = ' and msgjihe in ('.implode(',',$jiheidarr).') ';
				}
				
				$sql_getmsgjihe = "select msgjihe,count(*) as con from sixty_video where msgjihe>0 ".$jiheinstr." group by msgjihe order by msgjihe";
				
				$list_getmsgjihe  = parent::__get('HyDb')->get_all($sql_getmsgjihe);
				foreach($list_getmsgjihe as $valgc) {
					if(''!=$valgc['msgjihe']) {
						$showimg = isset($jihearr[$valgc['msgjihe']]['showimg'])?$jihearr[$valgc['msgjihe']]['showimg']:'';
						$showimg = HyItems::hy_qiniuimgurl('sixty-jihemsg',$showimg,$this->imgwidth,$this->imgheight,true);
						$content = isset($jihearr[$valgc['msgjihe']]['content'])?$jihearr[$valgc['msgjihe']]['content']:'';
						$tmpname = isset($jihearr[$valgc['msgjihe']]['name'])?$jihearr[$valgc['msgjihe']]['name']:'';
						
						$tmparr = array(
								'type' => 'msgjihe',
								'jiheid' => $valgc['msgjihe'],
								'name' => $tmpname,
								'content' => $content,
								'showimg' => $showimg,
								'count' => $valgc['con'],
						);
						array_push($echoarr,$tmparr);
						$echoallcon += $valgc['con'];
					}
				}
			}
			
		}else {
			
			
			
		}
		
		if(''!=$echosearch) {
			$echocontent = isset($hy_classifynamearr[$echosearch]['content'])?$hy_classifynamearr[$echosearch]['content']:'';
		}
		
		if('2'==$guize) {
			$retarr = $echoarr;
			
		}else {
			$retarr = array();
			$retarr['list'] = $echoarr;
			$retarr['name'] = $echosearch;
			$retarr['content'] = $echocontent;
			$retarr['count'] = $echoallcon;
		}
		
		$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$retarr);
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