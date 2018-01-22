<?php
/**
视频全局搜索，搜索小贴士表,视频收藏显示
 */

/**
 * 视频列表获取
 */

class HySix1034 extends HySix{

    private $now_page;
    private $pagesize;
    private $imgwidth;
    private $imgheight;
    private $imgwidthd;
    private $imgheightd;
    private $searchstr;
    private $classify1;
    private $classify2;
    private $classify3;
    private $classify4;
    private $msgjihe;


    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);

        $this->now_page = isset($input_data['page'])?$input_data['page']:'1';
        $this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';
        $this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';
        $this->imgwidthd = isset($input_data['imgwidth'])?$input_data['imgwidthd']:'';
        $this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';
        $this->imgheightd = isset($input_data['imgheight'])?$input_data['imgheightd']:'';
        $this->searchstr = isset($input_data['searchstr'])?$input_data['searchstr']:'';
        $this->classify1 = isset($input_data['classify1'])?$input_data['classify1']:'';
        $this->classify2 = isset($input_data['classify2'])?$input_data['classify2']:'';
        $this->classify3 = isset($input_data['classify3'])?$input_data['classify3']:'';
        $this->classify4 = isset($input_data['classify4'])?$input_data['classify4']:'';
        $this->msgjihe = isset($input_data['msgjihe'])?$input_data['msgjihe']:'';

        if(''==$this->imgwidth) {
            $this->imgwidth = 500;
        }
        if(''==$this->imgheight) {
            $this->imgheight = 500;
        }

        if($this->pagesize == ''){
            $this->pagesize = 10;
        }

    }


    protected function controller_exec1(){

        $sql_where = " where flag='1' ";
        $sql_where_x = " where flag='1' ";

        //判断是否关键字查询
        if(''===(string)$this->searchstr) {//不是关键字查询

            if(''!==(string)$this->classify1) {
                $sql_where .= " and classify1='".$this->classify1."' ";
            }
            if(''!==(string)$this->classify2) {
                $sql_where .= " and classify2='".$this->classify2."' ";
            }
            if(''!==(string)$this->classify3) {
                $sql_where .= " and classify3='".$this->classify3."' ";
            }
            if(''!==(string)$this->classify4) {
                $sql_where .= " and classify4='".$this->classify4."' ";
            }
            if(''!==(string)$this->msgjihe) {
                $sql_where .= " and msgjihe='".$this->msgjihe."' ";
            }

            //获取数据总数
            $sql_count_getvideo = "select count(*) as con from sixty_video ".$sql_where;
            //echo $sql_count_getvideo;
            //$list_count_getvideoarr = parent::__get('HyDb')->get_all($sql_count_getvideo);
            $list_count_getvideoarr = parent::func_runtime_sql_data($sql_count_getvideo);
            $list_count_getvideo = isset($list_count_getvideoarr[0]['con'])?$list_count_getvideoarr[0]['con']:'0';

            //分页
            $pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideo);
            $pagemsg = $pagearr['pagemsg'];
            $pagelimit = $pagearr['pagelimit'];


            //查询视频表数据
            $sql_getvideo = "select id,id as vid,classify1,classify2,classify3,classify4,showimg,biaoti, jieshao, 
                        maketime, biaotichild ,shicaititle,create_datetime 
						from sixty_video
						".$sql_where." order by id desc ".$pagelimit;

            $list_getvideo = parent::func_runtime_sql_data($sql_getvideo);



            //判断用户是否登录
            if(1!=parent::__get('usertype')) {
                $r = false;
            }else {
                //查询用户表，看该手机号用户是否存在
                $sql_getuserdata = "select * from sixty_user where id='".parent::__get('userid')."' order by id desc limit 1";
                $this->userlistdata = $this->HyDb->get_row($sql_getuserdata);
                if(count($this->userlistdata)<=0) {
                    $r = false;
                }else {
                    //判断userkey是否正确
                    $ser_tokenkey = parent::__get('userlistdata');
                    if(''==parent::__get('userkey') || $ser_tokenkey['tokenkey']!=parent::__get('userkey')) {
                        $r = false;
                    }else {
                        $r = true;
                    }
                }
            }



            if($r) {//用户已登录

                //获取用户已收藏的视频
                $sql_sc = "select id, dataid from sixty_video_shoucang where userid = '" . parent::__get('userid') . "' and type = 1";
//                $sql_sc = "select id, dataid from sixty_video_shoucang where userid = '" . 1 . "' and type = 1";
                $list_sc = parent::__get('HyDb')->get_all($sql_sc);
                if(count($list_sc) > 0) {
                    foreach($list_getvideo as $keygv => $valgv) {
                        //$list_getvideo[$keygv]['create_date'] = substr($list_getvideo[$keygv]['create_datetime'],0,10);
                        foreach($list_sc as $k_sc => $v_sc) {

                            if($valgv['id'] == $v_sc['dataid']) {
                                $list_getvideo[$keygv]['coll'] = '1';//视频已收藏
                                break;
                            } else {
                                $list_getvideo[$keygv]['coll'] = '2';//视频未收藏
                            }
                        }
                        $list_getvideo[$keygv]['vtype'] = '1';
                        $list_getvideo[$keygv]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage', $list_getvideo[$keygv]['showimg'], $this->imgwidth, $this->imgheight);
                    }
                }else{
                    foreach($list_getvideo as $keygv => $valgv) {
                        $list_getvideo[$keygv]['vtype'] = '1';
                        $list_getvideo[$keygv]['coll'] = '2';//视频未收藏
                        $list_getvideo[$keygv]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage', $valgv['showimg'], $this->imgwidth, $this->imgheight);

                    }
                }

            } else {
                foreach($list_getvideo as $keygv => $valgv) {
                    $list_getvideo[$keygv]['vtype'] = '1';
                    $list_getvideo[$keygv]['coll'] = '2';//视频未收藏
                    $list_getvideo[$keygv]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage', $valgv['showimg'], $this->imgwidth, $this->imgheight);

                }
            }


        }else {//是关键字查询

            //分页查询,视频表和小贴士表数据
            $sql_where .= " and ( biaoti like '%" . $this->searchstr . "%' or biaotichild like '%" . $this->searchstr . "%')";
            $sql_where_x .= " and ( biaoti like '%" . $this->searchstr . "%' )";
            //$sql_where2 = "where sixty_video.flag = 1 and (sixty_video.biaoti like '%" . $this->searchstr . "%' or sixty_video.biaotichild like '%" . $this->searchstr . "%') and sixty_video_shoucang.userid = '" . parent::__get('userid') . "'";


            //获取符合条件的2两张表的数据总条数
            $sql_count_getvideo = "select count(*) as con from sixty_video " . $sql_where;
            $sql_count_xiaotieshi = "select count(*) as con from sixty_tieshi_video " . $sql_where_x;
            $count_getvideo = parent::__get('HyDb')->get_one($sql_count_getvideo);
            $count_xiaotieshi = parent::__get('HyDb')->get_one($sql_count_xiaotieshi);
            $sum_count = $count_getvideo + $count_xiaotieshi;  //总条数

            if($sum_count == 0) {

                //显示给前端的分页信息
                $pagearr = HyItems::hy_pagepage($this->now_page, $this->pagesize, $sum_count);
                $pagemsg = $pagearr['pagemsg'];
                $rarr = array(
                    'pagemsg' => $pagemsg,
                    'list' => array(),
                );
                $echojsonstr = HyItems::echo2clientjson('100', '数据获取成功', $rarr);
                parent::hy_log_str_add($echojsonstr . "\n");
                echo $echojsonstr;
                return true;
            }

            //获取2张表每张表的分页显示条数
//            $pages_getvideo = floor($count_getvideo / $sum_count * $this->pagesize);
//            $pages_xiaotieshi = $sum_count - $pages_getvideo;


            //显示给前端的分页信息
            $pagearr = HyItems::hy_pagepage($this->now_page, $this->pagesize, $sum_count);
            $pagemsg = $pagearr['pagemsg'];


            //视频表分页信息
            $pagearr_v = HyItems::hy_pagepage($this->now_page, $this->pagesize, $count_getvideo);
            //$pagemsg = $pagearr['pagemsg'];
            $pagelimit_v = $pagearr_v['pagelimit'];


            //视频表数据
            $sql_get_v = "select id,id as vid,classify1,classify2,classify3,classify4,showimg,biaoti,biaotichild,
                        shicaititle,maketime,jieshao,create_datetime 
						from sixty_video
						" . $sql_where . " order by id desc " . $pagelimit_v;


            $list_v = parent::__get('HyDb')->get_all($sql_get_v);
            $coun_v = count($list_v);

            if($coun_v > 0) {
                //遍历视频表结果集
                foreach($list_v as $k_v => $v_v) {
                    $list_v[$k_v]['vtype'] = '1';
                    $list_v[$k_v]['coll'] = '2';
                    $list_v[$k_v]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage', $v_v['showimg'], $this->imgwidth, $this->imgheight);

                }
                $list_getvideo = $list_v;
            }


            if($coun_v > 0 && $coun_v < 10) {

                //得出补充出去的个数
                $duo_chu_ye_shu = 10 - $coun_v;
                //获取小贴士表数据
                $sql_get_x = "select id,id as vid,abstract as jieshao,showimg,biaoti,create_datetime,class 
						from sixty_tieshi_video
						" . $sql_where_x . " order by id desc limit " . $duo_chu_ye_shu;

                $list_x = parent::__get('HyDb')->get_all($sql_get_x);

                //判断结果数是否大于0
                if(count($list_x) > 0) {
                    //遍历结果集，取出所有分类id
                    $class_id = array();
                    foreach($list_x as $k_l => $v_l) {
                        $class_id[] = $v_l['class'];
                    }
                    $class_id = array_unique($class_id);
                    $class_str = implode(',', $class_id);

                    //根据id查询分类表数据
                    $sql_tieshi_class = "select id, name from sixty_tieshi_class where id in(" . $class_str . ")";
                    $list_tieshi_class = parent::__get('HyDb')->get_all($sql_tieshi_class);

                    //遍历小贴士表结果集
                    foreach($list_x as $k_x => $v_x) {
                        $list_x[$k_x]['vtype'] = '2';
                        $list_x[$k_x]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage', $v_x['showimg'], $this->imgwidth, $this->imgheight);
                        //遍历贴士表分类结果集
                        foreach($list_tieshi_class as $k_class => $v_class) {
                            if($v_class['id'] == $v_x['class']) {
                                $list_x[$k_x]['classname'] = $v_class['name'];
                            }
                        }
                    }

                    //判断是否取出视频
                    if($coun_v > 0) {//取出视频
                        //合并数组
                        $list_getvideo = array_merge($list_v, $list_x);

                    }
                }
            }


            if($coun_v <= 0) {


                //得出补充出去的个数
                $duo_chu_ye_shu = abs($count_getvideo - $this->pagesize * ceil($count_getvideo / $this->pagesize));


                //limit 起始位置
                $dang_qian_ye = $this->now_page - ceil($count_getvideo / $this->pagesize) + $duo_chu_ye_shu - 1;
                $pagesize = $this->pagesize + $duo_chu_ye_shu;


                //获取小贴士表数据
                $sql_get_x = "select id,id as vid,showimg,abstract as jieshao,biaoti,create_datetime,class 
                    from sixty_tieshi_video
                    " . $sql_where_x . " order by id desc limit " . $dang_qian_ye . ',' . $pagesize;

                $list_x = parent::__get('HyDb')->get_all($sql_get_x);


                if(count($list_x) > 0) {
                    //遍历结果集，取出所有分类id
                    $class_id = array();
                    foreach($list_x as $k_l => $v_l) {
                        $class_id[] = $v_l['class'];
                    }
                    $class_id = array_unique($class_id);
                    $class_str = implode(',', $class_id);

                    //根据id查询分类表数据
                    $sql_tieshi_class = "select id, name from sixty_tieshi_class where id in(" . $class_str . ")";
                    $list_tieshi_class = parent::__get('HyDb')->get_all($sql_tieshi_class);

                    //遍历小贴士表结果集
                    foreach($list_x as $k_x => $v_x) {
                        $list_x[$k_x]['vtype'] = '2';
                        $list_x[$k_x]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage', $v_x['showimg'], $this->imgwidth, $this->imgheight);
                        //遍历贴士表分类结果集
                        foreach($list_tieshi_class as $k_class => $v_class) {
                            if($v_class['id'] == $v_x['class']) {
                                $list_x[$k_x]['classname'] = $v_class['name'];
                            }
                        }
                    }

                    $list_getvideo = $list_x;

                }else{
                    $list_getvideo = array();
                }
            }

        }

        $rarr = array(
            'pagemsg' => $pagemsg,
            'list' => $list_getvideo,
        );

        $echojsonstr = HyItems::echo2clientjson('100', '数据获取成功', $rarr);
        parent::hy_log_str_add($echojsonstr . "\n");
        echo $echojsonstr;
        return true;


    }


    //用户信息--操作入口
    public function controller_init(){


        //用户信息获取入口
        $this->controller_exec1();

        return true;


    }




}