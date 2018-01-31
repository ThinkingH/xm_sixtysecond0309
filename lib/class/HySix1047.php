<?php
/**
 * 控制开关
 */

class HySix1047 extends HySix{

//    private $version;



    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);
//        $this->version = isset($input_data['version'])?$input_data['version']:'0';   //当前页数

    }


    protected function controller_exec1(){

        if($this->version === ''){
            //数据转为json，写入日志并输出
            $echojsonstr = HyItems::echo2clientjson('100','版本号错误');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return true;
        }

        //获取开关控制表数据
        $sql_onoff = "select id,name,flag from sixty_on_off where version = '".parent::__get('version')."'";

        $list_onoff = parent::__get('HyDb')->get_all($sql_onoff);


        //准备输出数组
        $rarr['list'] = $list_onoff;
var_dump($rarr);die;
        //数据转为json，写入日志并输出
        $echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$rarr);
        parent::hy_log_str_add($echojsonstr."\n");
        echo $echojsonstr;
        return true;


    }


    //用户信息--操作入口
    public function controller_init(){


        //入口
        $this->controller_exec1();

        return true;


    }




}