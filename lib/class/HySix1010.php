<?php
/*
 * 头像的上传
 */

class HySix1010 extends HySix{
	
	
	private $tmpimgpath; //图片临时存储
	private $imgdata;
	
	//数据的初始化
	function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
		//头像的存放位置
		$this->tmpimgpath = TMPPICPATH;
		
		$this->houzhui          = isset($input_data['houzhui']) ? $input_data['houzhui'] : '' ;
		$this->imgdata          = isset($input_data['imgdata']) ? $input_data['imgdata'] : '' ;
		
	}
	
	
	public function controller_edituserimage(){
		
		//图片保存的位置$this->imgpath
		$filepath = $this->tmpimgpath;
		
		if(!file_exists($filepath)) {
			mkdir( $filepath, 0777, true );
		}
		//图片文件名
		$filename = parent::__get('userid').'_touxiangimg.'.$this->houzhui;
		$filepathname = $filepath.$filename;
		//图片后缀重组
		$filepathname = HyItems::hy_getfiletype($filepathname);
		
		//把图片的编码解码为图片，存到对应的路径中
		file_put_contents($filepathname,base64_decode($this->imgdata));
		
		if(false===parent::func_isImage($filepathname)) {
			//解析失败
			@unlink($filepathname); //删除文件
			$echojsonstr = HyItems::echo2clientjson('100','图片解析失败，请重试');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
		}else {
			
			//上传到七牛云之前先进行图片格式转换，统一使用jpg格式
			$r = HyItems::hy_resave2jpg($filepathname);
			if($r!==false) {
				parent::hy_log_str_add($r."\n");
				$filepathname = $r;
			}
			
			
			//上传到七牛云
			$r = parent::upload_qiniu('sixty-user',$filepathname,pathinfo($filepathname,PATHINFO_BASENAME),'yes');
			
			if(false===$r) {
				@unlink($filepathname); //删除文件
				//上传失败
				$echojsonstr = HyItems::echo2clientjson('100','头像上传失败');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				
				return false;
				
			}else {
				@unlink($filepathname); //删除文件
				$filebasename  = pathinfo($filepathname, PATHINFO_BASENAME);
				$sql_touxiang  = "update sixty_user set touxiang = '".$filebasename."' where id='".parent::__get('userid')."'";
				$list_touxiang = parent::__get('HyDb')->execute($sql_touxiang);
				
				$echojsonstr = HyItems::echo2clientjson('100','头像上传成功');
				parent::hy_log_str_add($echojsonstr."\n");
				echo $echojsonstr;
				return false;
				
			}
			
			
		}
		
		
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