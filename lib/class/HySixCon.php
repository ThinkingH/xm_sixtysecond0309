<?php

/*
 * 馅饼接口的配置文件接口
 */

class HySixCon{
	
	protected $inputdataarr;
	
	//获取传递的get参数数组
	public function __construct($inputdataarr){
		$this->inputdataarr = $inputdataarr;
		unset($inputdataarr);
	}
	
	
	//初始化的入口
	function controller(){
		
		//获取操作类型的编号
		$thetype = isset($this->inputdataarr['thetype'])?$this->inputdataarr['thetype']:'';
		
		//判断操作类型格式是否正确
		if($thetype=='' || !is_numeric($thetype) ){
			$echojsonstr = HyItems::echo2clientjson('402','类型编号格式错误');
			echo $echojsonstr;
			return false;
			
		}else{
			
			//拼接生成要new的对应类名
			$newclassname = 'HySix'.$thetype;
			
			$classfile_path = dirname(__FILE__).DIRECTORY_SEPARATOR.$newclassname.'.php';
			
			if(!file_exists($classfile_path)) {
				$echojsonstr = HyItems::echo2clientjson('420','操作类型文件不存在');
				echo $echojsonstr;
				return false;
				
			}else {
				//new 对应类
				$initclass = new $newclassname($this->inputdataarr);
				$r = $initclass->controller_init();
				
				if($r===true){
					return true;
				}else{
					return false;
				}
				
				
			}
			
			
		
		}
		
	}
}