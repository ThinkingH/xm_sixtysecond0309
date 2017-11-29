<?php
/**
 * 视频列表对应详细内容获取
 */

class HySix1016 extends HySix{
	
	private $nowid;
	private $imgwidth;
	private $imgheight;
	
	//数据的初始化
	public function __construct($input_data){
		//数据初始化
		parent::__construct($input_data);
		
		$this->nowid = isset($input_data['nowid'])?$input_data['nowid']:'0';
		$this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';
		$this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';
		if(''==$this->imgwidth) {
			$this->imgwidth = 300;
		}
		if(''==$this->imgheight) {
			$this->imgheight = 300;
		}
		if(!is_numeric($this->nowid)) {
			$this->nowid = 0;
		}
		
	}
	
	
	protected function controller_exec1(){
		
		$sql_getvideo = "select id,classify1,classify2,classify3,classify4,
						showimg,videosavename,biaoti,biaotichild,jieshao,fenshu,
						maketime,huafeimoney,tishishuoming,create_datetime 
						from sixty_video 
						where id='".$this->nowid."' 
						and flag='1'
						order by id desc limit 1";
// 		echo $sql_getvideo;
		$list_getvideo =  parent::__get('HyDb')->get_row($sql_getvideo);
		
		if(count($list_getvideo)<=0) {
			$echojsonstr = HyItems::echo2clientjson('101','数据获取失败');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
		}else {
			$list_getvideo['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$list_getvideo['showimg'],$this->imgwidth,$this->imgheight);
			$list_getvideo['videourl'] = HyItems::hy_qiniubucketurl('sixty-video',$list_getvideo['videosavename']);
			
			$sql_getbuzhou = "select buzhouid,buzhoucontent from sixty_video_buzhou where vid='".$this->nowid."' order by id asc";
			//$list_getbuzhou = parent::__get('HyDb')->get_all($sql_getbuzhou);
			$list_getbuzhou = parent::func_runtime_sql_data($sql_getbuzhou);
			if(count($list_getbuzhou)<=0) {
				$list_getvideo['buzhouarr'] = array();
			}else {
				$list_getvideo['buzhouarr'] = $list_getbuzhou;
			}
			
			
			$sql_getcailiao = "select name,yongliang from sixty_video_cailiao where vid='".$this->nowid."' order by id asc";
			//$list_getcailiao = parent::__get('HyDb')->get_all($sql_getcailiao);
			$list_getcailiao = parent::func_runtime_sql_data($sql_getcailiao);
			if(count($list_getcailiao)<=0) {
				$list_getvideo['cailiaoarr'] = array();
			}else {
				$list_getvideo['cailiaoarr'] = $list_getcailiao;
			}
			
			
			//评论个数
			$sql_count_getvideopinglun = "select count(*) as con from sixty_video_pinglun where vid='".$this->nowid."' and type='1' ";
			$list_count_getvideopinglun = parent::__get('HyDb')->get_one($sql_count_getvideopinglun);
			$list_getvideo['pingluncount'] = $list_count_getvideopinglun;
			
			
			//单图片评论个数
			$sql_count_getvideopinglunpic = "select count(*) as con from sixty_video_pinglun where vid='".$this->nowid."' and type='2' ";
			$list_count_getvideopinglunpic = parent::__get('HyDb')->get_one($sql_count_getvideopinglunpic);
			$list_getvideo['picpingluncount'] = $list_count_getvideopinglunpic;
			
			
			
			//单图片评论-5条
			$sql_getvideopinglun = "select id,userid,content,showimg,create_datetime
									from sixty_video_pinglun
									where vid='".$this->nowid."' and type='2' 
									order by dianzan desc,id desc limit 5";
			$list_getvideopinglun =  parent::__get('HyDb')->get_all($sql_getvideopinglun);
			
			$useridarr = array();
			foreach($list_getvideopinglun as $valgp) {
				if(!in_array($valgp['userid'], $useridarr)) {
					array_push($useridarr, $valgp['userid']);
				}
			}
			$retarr = parent::func_retsqluserdata($useridarr,50,50);
			// 		print_r($retarr);
			foreach($list_getvideopinglun as $keygp => $valgp) {
				//$list_getvideopinglun[$keygp]['create_date'] = substr($list_getvideopinglun[$keygp]['create_datetime'],0,10);
				$list_getvideopinglun[$keygp]['showimg']  = HyItems::hy_qiniuimgurl('sixty-imgpinglun',$list_getvideopinglun[$keygp]['showimg'],$this->imgwidth,$this->imgheight);
				$list_getvideopinglun[$keygp]['nickname'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun[$keygp]['userid'],'nickname');
				$list_getvideopinglun[$keygp]['touxiang'] = parent::func_userid_datatiqu($retarr,$list_getvideopinglun[$keygp]['userid'],'touxiang');
			}
			
			$list_getvideo['picpinglunlist'] = $list_getvideopinglun;
			
			
			//判断该用户是否收藏了该条数据
			$sql_pan = "select id from sixty_video_shoucang where dataid='".$this->nowid."' and userid='".parent::__get('userid')."' and type='1' order by id desc limit 1";
			$list_pan = parent::__get('HyDb')->get_one($sql_pan);
			if($list_pan>0) {
				$list_getvideo['shoucangflag'] = '1';
			}else {
				$list_getvideo['shoucangflag'] = '2';
			}
			
			
			
			$echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$list_getvideo);
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return true;
		}
		
	}
	
	
	//用户信息--操作入口
	public function controller_init(){
		
		
		//用户信息获取入口
		$this->controller_exec1();
	
		return true;
	
	
	}
	
	
	
	
}