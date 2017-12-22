<?php
/**
视频全局搜索，搜索小贴士表
 */

/**
 * 视频列表获取
 */

class HySix1034 extends HySix{

    private $now_page;
    private $pagesize;
    private $imgwidth;
    private $imgheight;
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
        $this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';
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

    }


    protected function controller_exec1(){

        $sql_where = " where flag='1' ";

        if(''===(string)$this->searchstr) {
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

            $sql_count_getvideo = "select count(*) as con from sixty_video ".$sql_where;
            //echo $sql_count_getvideo;
            //$list_count_getvideoarr = parent::__get('HyDb')->get_all($sql_count_getvideo);
            $list_count_getvideoarr = parent::func_runtime_sql_data($sql_count_getvideo);
            $list_count_getvideo = isset($list_count_getvideoarr[0]['con'])?$list_count_getvideoarr[0]['con']:'0';

            $pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideo);
            $pagemsg = $pagearr['pagemsg'];
            $pagelimit = $pagearr['pagelimit'];

            $sql_getvideo = "select id,id as vid,classify1,classify2,classify3,classify4,showimg,biaoti,biaotichild,shicaititle,jieshao,create_datetime 
						from sixty_video
						".$sql_where." order by id desc ".$pagelimit;
        }else {

            $sql_where .= " and ( biaoti like '%".$this->searchstr."%' or biaotichild like '%".$this->searchstr."%')";
            $sql_count_getvideo = "SELECT COUNT(*) FROM((SELECT id FROM sixty_video ".$sql_where.") UNION ALL (SELECT id FROM sixty_tieshi_video where biaoti like '%".$this->searchstr."%')) as a";

            //echo $sql_count_getvideo;
            //$list_count_getvideoarr = parent::__get('HyDb')->get_all($sql_count_getvideo);
            $list_count_getvideoarr = parent::func_runtime_sql_data($sql_count_getvideo);
            $list_count_getvideo = isset($list_count_getvideoarr[0]['con'])?$list_count_getvideoarr[0]['con']:'0';

            $pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideo);
            $pagemsg = $pagearr['pagemsg'];
            $pagelimit = $pagearr['pagelimit'];

            $sql_getvideo = "(select id,id as vid,showimg,biaoti,jieshao,create_datetime 
						from sixty_video
						".$sql_where.")union all (select biaoti, showimg, id as vid, id, create_datetime, abstract from sixty_tieshi_video where biaoti like '%".$this->searchstr."%') order by id desc ".$pagelimit;
            $list_video =  parent::__get('HyDb')->get_all($sql_getvideo);

            $sql_video = "select id,id as vid,classify1,classify2,classify3,classify4,showimg,biaoti,biaotichild,shicaititle,jieshao from sixty_video " . $sql_where;
            $list_getvideo =  parent::__get('HyDb')->get_all($sql_video);

            foreach($list_video as $k_l => $v_l){
                foreach($list_getvideo as $k_g => $v_g){
                    if($v_l['biaoti'] == $v_g['biaoti'] && $v_l['id'] == $v_g['id'] && $v_l['showimg'] == $v_g['showimg']){
                        $list_video[$k_l]['classify1'] = $v_g['classify1'];
                        $list_video[$k_l]['classify2'] = $v_g['classify2'];
                        $list_video[$k_l]['classify3'] = $v_g['classify3'];
                        $list_video[$k_l]['classify4'] = $v_g['classify4'];
                        $list_video[$k_l]['biaotichild'] = $v_g['biaotichild'];
                        $list_video[$k_l]['shicaititle'] = $v_g['shicaititle'];
                        $list_video[$k_l]['jieshao'] = $v_g['jieshao'];
                    }else {
                        $list_video[$k_l]['classify1'] = '';
                        $list_video[$k_l]['classify2'] = '';
                        $list_video[$k_l]['classify3'] = '';
                        $list_video[$k_l]['classify4'] = '';
                        $list_video[$k_l]['biaotichild'] = '';
                        $list_video[$k_l]['shicaititle'] = '';
                        $list_video[$k_l]['jieshao'] = '';
                    }
                }
            }
        }var_dump($list_video);die;



//
//        $sql_count_getvideo = "select count(*) as con from sixty_video ".$sql_where;
//        //echo $sql_count_getvideo;
//        //$list_count_getvideoarr = parent::__get('HyDb')->get_all($sql_count_getvideo);
//        $list_count_getvideoarr = parent::func_runtime_sql_data($sql_count_getvideo);
//        $list_count_getvideo = isset($list_count_getvideoarr[0]['con'])?$list_count_getvideoarr[0]['con']:'0';
//
//        $pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideo);
//        $pagemsg = $pagearr['pagemsg'];
//        $pagelimit = $pagearr['pagelimit'];
//
//        $sql_getvideo = "select id,id as vid,classify1,classify2,classify3,classify4,showimg,biaoti,biaotichild,shicaititle,jieshao,create_datetime
//						from sixty_video
//						".$sql_where." order by id desc ".$pagelimit;
//
//        $sql_getvideo = "select a.id,a.id as vid,a.classify1,a.classify2,a.classify3,a.classify4,a.showimg,a.biaoti,a.biaotichild,a.shicaititle,a.jieshao,a.create_datetime,b.biaoti,b.showimg,b.id,b.class
//						from sixty_video as a,sixty_tieshi_video as b
//						".$sql_where." order by id desc ".$pagelimit;
//        var_dump($sql_getvideo);die;
// 		echo $sql_getvideo;
// 		$list_getvideo =  parent::__get('HyDb')->get_all($sql_getvideo);
        $list_getvideo = parent::func_runtime_sql_data($sql_getvideo);

        foreach($list_getvideo as $keygv => $valgv) {
            //$list_getvideo[$keygv]['create_date'] = substr($list_getvideo[$keygv]['create_datetime'],0,10);
            $list_getvideo[$keygv]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$list_getvideo[$keygv]['showimg'],$this->imgwidth,$this->imgheight);
        }

        $rarr = array(
            'pagemsg' => $pagemsg,
            'list' => $list_getvideo,
        );

        $echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$rarr);
        parent::hy_log_str_add($echojsonstr."\n");
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