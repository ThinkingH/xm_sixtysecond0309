<?php
/*
 * 关于版本公司等信息获取
 * 
 */
class HySix1008 extends HySix{
	
	
	//数据的初始化
	function __construct($input_data){
	
		//数据初始化
		parent::__construct($input_data);
	
	
	}
	
	
	//进行操作
	protected function controller_getcontent(){
		
		//准备查询sql语句
		$sql_config = "select id,name,key1,val1 from sixty_config where name='banbenhao' or name='kaipingimg' or name='companyname'";
		//$list_config = parent::__get('HyDb')->get_all($sql_config);
        //执行查询
		$list_config = parent::func_runtime_sql_data($sql_config);

		//判断查询结果是否为空
		if(count($list_config)<=0) {//查询结果为空

            //数据转为json，写入日志并输出
			$echojsonstr = HyItems::echo2clientjson('101','信息获取失败');
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return false;
			
		}else {//查询结果不为空

            //准备空数组
			$configarr = array();

			//遍历查询结果集
			foreach($list_config as $valc) {
			    //把结果集数据赋给新数组
				$configarr[$valc['name']] = $valc['val1'];
// 				$configarr[$valc['name']] = array(
// 						'key' => $valc['key1'],
// 						'val' => $valc['val1'],
						
// 				);
			}

            //数据转为json，写入日志并输出
			$echojsonstr = HyItems::echo2clientjson('100','信息获取成功',$configarr);
			parent::hy_log_str_add($echojsonstr."\n");
			echo $echojsonstr;
			return true;
		}
		
		
	}
	
	
	
	//操作入口
	public function controller_init(){
		$this->controller_getcontent();
	
		return true;
	
	}
}

