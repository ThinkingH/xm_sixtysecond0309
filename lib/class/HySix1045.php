<?php
/**
 * 用户消息获取
 */

class HySix1045 extends HySix{

    private $now_page;
    private $pagesize;
    private $imgwidth;
    private $imgheight;


    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);

        $this->now_page = isset($input_data['page'])?$input_data['page']:'1';//当前页数
        $this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';//单页显示条数
        $this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';//图片宽度
        $this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';//图片高度
        //默认图片宽度
        if(''==$this->imgwidth) {
            $this->imgwidth = 500;
        }
        //默认图片高度
        if(''==$this->imgheight) {
            $this->imgheight = 500;
        }


    }


    protected function controller_exec1(){
//var_dump(parent::__get('userid'));die;
        //获取分页数据
        $sql_count_news = "select count(*) as con from sixty_user_news where to_userid ='".parent::__get('userid')."'";
//        $sql_count_news = "select count(*) as con from sixty_user_news where to_userid ='44'";
        $list_count_news = parent::__get('HyDb')->get_one($sql_count_news);
        $pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_news);
        $pagemsg = $pagearr['pagemsg'];
        $pagelimit = $pagearr['pagelimit'];

        //查询通知表
        $sql_news  = "select id,message,create_datetime, userid, vid, to_userid
							from sixty_user_news where to_userid = '".parent::__get('userid')."'
							order by id desc ".$pagelimit;
//        $sql_news  = "select id,message,create_datetime, userid, vid, to_userid
//							from sixty_user_news where to_userid = '44'
//							order by id desc ".$pagelimit;
        $list_news =  parent::__get('HyDb')->get_all($sql_news);

        //判断是否取出数据
        if(count($list_news) <= 0){
            $list_news = array();
            //准备输出数字
            $rarr = array(
                'pagemsg' => $pagemsg,
                'list' => $list_news,
            );
        }else{
            $user_id_all = array();
            //遍历结果集
            foreach($list_news as $k_news => $v_news) {
                //转换日期格式并存入结果集
                $list_news[$k_news]['create_datetime'] = date('Y年m月d日',strtotime($v_news['create_datetime']));
                //取出用户id
                $user_id_all[] = $v_news['userid'];

            }

            //获取用户自身ID
            $user_id_all[] = parent::__get('userid');
//            $user_id_all[] = 44;
//            var_dump($user_id_all);
            //获取用户数据
            $user_msg = parent::func_retsqluserdata($user_id_all,$this->imgwidth,$this->imgheight);


            //遍历结果集
            foreach($user_msg as $k_msg => $v_msg){
                foreach($list_news as $k_news => $v_news){

//                    if($v_news['to_userid'] == $k_msg){
//                        $list_news[$k_news]['touxiang'] = HyItems::hy_qiniuimgurl('sixty-user',$v_msg['touxiang'],$this->imgwidth,$this->imgheight,true);
//                        $list_news[$k_news]['nickname'] =  $v_msg['nickname'];
//                        $list_news[$k_news]['message'] =  base64_decode($v_news['message']);
//                    }

                    if($v_news['userid'] == $k_msg){
                        $list_news[$k_news]['touxiang'] =  HyItems::hy_qiniuimgurl('sixty-user',$v_msg['touxiang'],$this->imgwidth,$this->imgheight,true);
                        $list_news[$k_news]['nickname'] =  $v_msg['nickname'];
                        $list_news[$k_news]['message'] =  base64_decode($v_news['message']);
                    }
                }
            }

            //准备输出数字
            $rarr = array(
                'pagemsg' => $pagemsg,
                'list' => $list_news,
            );
        }



        //数据转为json，写入日志并输出
        $echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$rarr);
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
        $this->controller_exec1();

        return true;


    }




}