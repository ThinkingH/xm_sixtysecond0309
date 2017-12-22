<?php
/**
 * 推送开关

 */

class HySix1033 extends HySix{
    private $push_state;

    //数据的初始化
    function __construct($input_data){

        //数据初始化
        parent::__construct($input_data);

        $this->push_state = isset($input_data['push_state']) ? $input_data['push_state'] : '' ;

    }


    private function controller_exec1() {

        //判断推送信息
        if($this->push_state == ''){
            $echojsonstr = HyItems::echo2clientjson('101','推送信息不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //查询用户数据
        $sql_sel_ps = "select push_state from sixty_user where id = '".parent::__get('userid')."'";
        $res_ps = parent::__get('HyDb')->get_one($sql_sel_ps);

        //查询结果为空
        if(count($res_ps) <= 0){
            $echojsonstr = HyItems::echo2clientjson('101','用户信息未找到');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }


        //修改该用户推送字段信息
        $sql_up_ps = "update sixty_user set push_state = '".$this->push_state."'  where id = '".parent::__get('userid')."'";
        $res_up = parent::__get('HyDb')->execute($sql_up_ps);

        if($res_up){
            $echojsonstr = HyItems::echo2clientjson('100','修改成功');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }else{
            $echojsonstr = HyItems::echo2clientjson('101','修改失败');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }
    }




    //操作入口--头像的上传
    public function controller_init(){

//        //判断正式用户通讯校验参数
//        $r = parent::func_oneusercheck();
//        if($r===false){
//            return false;
//        }


        $this->controller_exec1();

        return true;


    }
}