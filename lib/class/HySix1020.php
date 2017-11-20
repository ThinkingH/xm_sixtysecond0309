<?php
/*
 * 评论发布
 */

class HySix1020 extends HySix{
	
	
	private $tmpimgpath; //图片临时存储
	private $imgdata;
	private $houzhui;
	private $dataid;
	private $typeid;
	private $contentdata;
	
	//数据的初始化
	function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
		//头像的存放位置
		$this->tmpimgpath = TMPPICPATH;
		
		$this->houzhui          = isset($input_data['houzhui']) ? $input_data['houzhui'] : '' ;
		$this->imgdata          = isset($input_data['imgdata']) ? $input_data['imgdata'] : '' ;
		$this->dataid          = isset($input_data['dataid']) ? $input_data['dataid'] : '' ; //视频id字段
		$this->typeid          = isset($input_data['typeid']) ? $input_data['typeid'] : '' ; //类型id字段（1文字评论，2图片评论）
		$this->contentdata   = isset($input_data['contentdata']) ? $input_data['contentdata'] : '' ;
		
	}
	
	
	public function controller_edituserimage(){
		
		if(!is_numeric($this->dataid)) {
			$echojsonstr = HyItems::echo2clientjson('101','视频id字段不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		if(''==$this->contentdata) {
			$echojsonstr = HyItems::echo2clientjson('101','评论内容不能为空');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		//查询视频id确认是否存在
		$sql_videopan = "select id from sixty_video where id='".$this->dataid."'";
		$list_videopan = parent::__get('HyDb')->get_one($sql_videopan);
		if($list_videopan<=0) {
			$echojsonstr = HyItems::echo2clientjson('101','指定的评论视频id不存在');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
		if(2==$this->typeid) {
			//图片评论
			if(''==$this->imgdata) {
				$echojsonstr = HyItems::echo2clientjson('101','上传图片不能为空');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
			}
			if(''==$this->houzhui) {
				$echojsonstr = HyItems::echo2clientjson('101','图片后缀不能为空');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
			}
			
			
			if(!file_exists($this->tmpimgpath)) {
				mkdir( $filepath, 0777, true );
			}
			//图片文件名
			$filename = parent::__get('userid').'_'.date('ymdHis').mt_rand(100,999).'.'.$this->houzhui;
			//文件的路径
			$filepathname = $this->tmpimgpath.$filename;
			
			//把图片的编码解码为图片，存到对应的路径中
			file_put_contents($filepathname,base64_decode($this->imgdata));
			
			//图片后缀重组
			$cz_filepathname = HyItems::hy_getfiletype($filepathname);
			//对文件进行重命名，修改后缀
			rename($filepathname,$cz_filepathname);
			
			
			
			
			if(false===parent::func_isImage($cz_filepathname)) {
				//解析失败
				$echojsonstr = HyItems::echo2clientjson('101','图片解析失败，请重试');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
			}else {
				
				//上传到七牛云
				$r = parent::upload_qiniu('sixty-imgpinglun',$cz_filepathname,$filename);
				unlink($cz_filepathname); //删除文件
				
				if(false===$r) {
					//上传失败
					$echojsonstr = HyItems::echo2clientjson('101','图片上传失败');
					parent::hy_log_str_add($echojsonstr."\n");
					echo $echojsonstr;
					return false;
					
				}else {
					$showimg = $filename;
					
				}
				
			}
			
		}else if(1==$this->typeid){
			//文字评论
			$showimg = '';
			
		}else {
			$echojsonstr = HyItems::echo2clientjson('101','评论类型错误');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}
		
		$sql_insert  = "insert into sixty_video_pinglun (type,vid,userid,content,
						showimg,create_datetime) value (
						'".$this->typeid."','".$this->dataid."','".parent::__get('userid')."',
						'".$this->contentdata."','".$showimg."','".date('Y-m-d H:i:s')."')";
		$list_insert = parent::__get('HyDb')->execute($sql_insert);
		
		$echojsonstr = HyItems::echo2clientjson('100','发布成功');
		parent::hy_log_str_add($echojsonstr."\n");
		echo $echojsonstr;
		return false;
		
		
		
	}
	
	
	
	
	//操作入口--头像的上传
	public function controller_init(){
		
		//判断正式用户通讯校验参数
		$r = parent::func_oneusercheck();
		if($r===false){
			return false;
		}
		
		
		//头像的上传
		$this->controller_edituserimage();
	
		return true;
	
	
	}
	
}