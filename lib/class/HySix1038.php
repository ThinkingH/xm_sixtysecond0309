<?php
/**
 * 用户的信息获取
 */

class HySix1038 extends HySix{



    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);

    }


    protected function controller_getuserinfo(){

        //获取用户信息
        $userlistdata = parent::__get('userlistdata');

        //准备数据校验数组
        $retarr = array(
            'id',
            'phone',
            'sex',
            'birthday',
            'nickname',
            'touxiang',
            'describes',
            'create_datetime',
            'address',
        );

        //新数据接收数组
        $newuserlist = array();

        //遍历用户信息数组
        foreach($userlistdata as $keyu => $valu) {
            //匹配用户信息数组键名是否在校验数组中
            if(in_array($keyu, $retarr)) {//匹配成功
                //键值存入新数组
                $newuserlist[$keyu] = (string)$valu;
            }
        }


        //判断用户头像是否为空
        if($newuserlist['touxiang']!='') {//头像不为空

            if(substr($newuserlist['touxiang'],0,4)!='http') {

                //拼接七牛云头像链接
                $newuserlist['touxiang'] = HyItems::hy_qiniuimgurl('sixty-user',$newuserlist['touxiang'],100,100,true);
            }else {
                //链接为微信的，不做处理
            }
        }

        //数据转为json，写入日志并输出
        $echojsonstr = HyItems::echo2clientjson('100','信息获取成功',$newuserlist);
        parent::hy_log_str_add($echojsonstr."\n");
        echo $echojsonstr;
        return true;


    }


    //用户信息--操作入口
    public function controller_init(){

        //判断正式用户通讯校验参数
        $r = parent::func_oneusercheck();
        if($r===false){
            return false;
        }

        //用户信息获取入口
        $this->controller_getuserinfo();

        return true;


    }




}