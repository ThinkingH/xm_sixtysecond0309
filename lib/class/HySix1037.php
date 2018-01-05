<?php
/*
 * 用户信息修改
 */

class HySix1037 extends HySix{

    private $sex;
    private $birthday;
    private $nickname;
    private $describes;
    private $address;

    //数据的初始化
    function __construct($input_data){

        //数据初始化
        parent::__construct($input_data);

        $this->sex      = isset($input_data['sex'])? $input_data['sex']:'';
        $this->birthday = isset($input_data['birthday'])?$input_data['birthday']:'';
        $this->nickname = isset($input_data['nickname'])?$input_data['nickname']:'';
        $this->describes = isset($input_data['describes'])?$input_data['describes']:'';
        $this->address = isset($input_data['address'])?$input_data['address']:'';

    }


    protected function controller_edituserinfo(){

//        if($this->address == ''){
//            //数据转为json，写入日志并输出
//            $echojsonstr = HyItems::echo2clientjson('101','地址是空');
//            parent::hy_log_str_add($echojsonstr."\n");
//            echo $echojsonstr;
//            return false;
//        }

        //判断性别，生日，昵称， 描述是否为空
        if($this->sex!='' || $this->birthday!='' || $this->nickname!='' || $this->describes!='' || $this->address!=''){

            //sql语句开头
            $useredit_sql = "update sixty_user set ";

            if($this->sex!=''){//性别不为空
                $useredit_sql .= " sex='".$this->sex."', ";
            }
            if($this->birthday!=''){//生日不为空
                $useredit_sql .= " birthday='".$this->birthday."', ";
            }
            if($this->nickname!=''){//昵称不为空
                $useredit_sql .= " nickname='".$this->nickname."', ";
            }
            if($this->describes!=''){//描述不为空
                $useredit_sql .= " describes='".$this->describes."', ";
            }
            if($this->address!=''){//地址不为空
                $useredit_sql .= " address='".$this->address."', ";
            }

            //去掉sql语句结尾处 ,
            $useredit_sql = rtrim($useredit_sql,', ');

            //拼接sql语句条件
            $useredit_sql .= " where id='".parent::__get('userid')."' and tokenkey='".parent::__get('userkey')."' ";

            //执行sql语句
            $useredit_list = parent::__get('HyDb')->execute($useredit_sql);
            parent::hy_log_str_add($useredit_sql."\n");

            //数据转为json，写入日志并输出
            $echojsonstr = HyItems::echo2clientjson('100','信息修改成功');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return true;


        }else{

            //数据转为json，写入日志并输出
            $echojsonstr = HyItems::echo2clientjson('101','修改参数为空，无法执行修改');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return true;

        }

    }




    //操作入口--用户信息修改，正常用户功能
    public function controller_init(){

        //判断正式用户通讯校验参数
        $r = parent::func_oneusercheck();
        if($r===false){
            return false;
        }


        //用户信息修改入口
        $this->controller_edituserinfo();

        return true;


    }




}