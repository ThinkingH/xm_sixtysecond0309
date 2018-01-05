<?php
/**
 * 视频列表分类获取，添加合集详情页图片
 */

class HySix1039 extends HySix{

    private $now_page;
    private $pagesize;
    private $classtype;
    private $searchstr;
    private $imgwidth;
    private $imgheight;


    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);

        $this->now_page = isset($input_data['page'])?$input_data['page']:'1'; //当前页码
        $this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10'; //显示条数
        $this->classtype = isset($input_data['classtype'])?trim($input_data['classtype']):'';//分类类型
        $this->searchstr = isset($input_data['searchstr'])?trim($input_data['searchstr']):'';//搜索关键字
        $this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';//图片宽度
        $this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';//图片高度
        //默认图片宽度
        if(''==$this->imgwidth) {
            $this->imgwidth = 300;
        }
        //默认图片高度
        if(''==$this->imgheight) {
            $this->imgheight = 300;
        }
    }


    protected function controller_exec1(){

        $echoarr = array();
        $echosearch  = '';
        $echocontent = '';
        $echoallcon  = 0;
        $echofenlei  = array();

        $guize = '1';

        $hy_classifynamearr = array();
        //查询分类信息表
        $sql_getcontent = "select id,name,childname,content from sixty_classifymsg where flag = 1";
        $list_getcontent = parent::__get('HyDb')->get_all($sql_getcontent);
        //遍历结果集
        foreach($list_getcontent as $valgc) {
            $hy_classifynamearr[$valgc['name']] = $valgc;
        }


        //判读查询类型是否为空或查询全部分类
        if(''==$this->classtype || 'classify'==$this->classtype) {
            //执行查询
            $sql_getclassify1 = "select classify1,count(*) as con from sixty_video where flag='1' group by classify1 order by classify1";
            $list_getclassify1 = parent::func_runtime_sql_data($sql_getclassify1);

            //遍历结果集
            foreach($list_getclassify1 as $valgc) {
                //把分类1视频数目存入输出数组
                $echoallcon += $valgc['con'];
                if(''!=$valgc['classify1']) {//分类1中不为空
                    //判断分类1中子名称是否存在，不存在给一个空
                    $tmpchildname = isset($hy_classifynamearr[$valgc['classify1']]['childname'])?$hy_classifynamearr[$valgc['classify1']]['childname']:'';
                    //拼接分类1数组
                    $tmparr = array(
                        'type' => 'classify1',
                        'name' => $valgc['classify1'],
                        'childname' => $tmpchildname,
                        'count' => $valgc['con'],
                    );
                    //把分类数组放入输出数组
                    array_push($echoarr,$tmparr);
                }
            }

        }else if('classify1'==$this->classtype) {
            $sqlwhere = " where flag='1' ";
            if(''!==(string)$this->searchstr) {
                $echosearch = $this->searchstr;
                $sqlwhere .= " and classify1='".$this->searchstr."' ";
            }
            $sql_getclassify2 = "select classify2,count(*) as con from sixty_video ".$sqlwhere." group by classify2 order by field(classify2,'网红菜','甜品','西式','其他','小贴士') ";
            $list_getclassify2 = parent::func_runtime_sql_data($sql_getclassify2);

            foreach($list_getclassify2 as $valgc) {
                $echoallcon += $valgc['con'];
                if(''!=$valgc['classify2']) {
                    $tmpchildname = isset($hy_classifynamearr[$valgc['classify2']]['childname'])?$hy_classifynamearr[$valgc['classify2']]['childname']:'';
                    $tmparr = array(
                        'type' => 'classify2',
                        'name' => $valgc['classify2'],
                        'childname' => $tmpchildname,
                        'count' => $valgc['con'],
                    );
                    array_push($echoarr,$tmparr);
                }
            }



            $sql_fenlei = "select classify1 from sixty_video where flag='1' and classify1='".$this->searchstr."' order by id desc limit 1";
            $list_fenlei = parent::__get('HyDb')->get_row($sql_fenlei);
            if(count($list_fenlei)>0) {
                $echofenlei['classifys'] = array($list_fenlei['classify1']);
            }

        }else if('classify2'==$this->classtype) {
            $sqlwhere = " where flag='1' ";
            if(''!==(string)$this->searchstr) {
                $echosearch = $this->searchstr;
                $sqlwhere .= " and classify2='".$this->searchstr."' ";
            }

            $sql_getclassify3 = "select classify3,count(*) as con from sixty_video ".$sqlwhere." group by classify3 order by classify3";
            $list_getclassify3 = parent::func_runtime_sql_data($sql_getclassify3);


            foreach($list_getclassify3 as $valgc) {
                $echoallcon += $valgc['con'];
                if(''!=$valgc['classify3']) {
                    $tmpchildname = isset($hy_classifynamearr[$valgc['classify3']]['childname'])?$hy_classifynamearr[$valgc['classify3']]['childname']:'';
                    $tmparr = array(
                        'type' => 'classify3',
                        'name' => $valgc['classify3'],
                        'childname' => $tmpchildname,
                        'count' => $valgc['con'],
                    );
                    array_push($echoarr,$tmparr);
                }
            }

            $sql_fenlei = "select classify1,classify2 from sixty_video where flag='1' and classify2='".$this->searchstr."' order by id desc limit 1";
            $list_fenlei = parent::__get('HyDb')->get_row($sql_fenlei);
            if(count($list_fenlei)>0) {
                $echofenlei['classifys'] = array($list_fenlei['classify1'],$list_fenlei['classify2']);
            }

        }else if('classify3'==$this->classtype) {
            $sqlwhere = " where flag='1' ";
            if(''!==(string)$this->searchstr) {
                $echosearch = $this->searchstr;
                $sqlwhere .= " and classify3='".$this->searchstr."'";
            }
            $sql_getclassify4 = "select classify4,count(*) as con from sixty_video ".$sqlwhere." group by classify4 order by classify4";
            $list_getclassify4 = parent::func_runtime_sql_data($sql_getclassify4);

            foreach($list_getclassify4 as $valgc) {
                $echoallcon += $valgc['con'];
                if(''!=$valgc['classify4']) {
                    $tmpchildname = isset($hy_classifynamearr[$valgc['classify4']]['childname'])?$hy_classifynamearr[$valgc['classify4']]['childname']:'';
                    $tmparr = array(
                        'type' => 'classify4',
                        'name' => $valgc['classify4'],
                        'childname' => $tmpchildname,
                        'count' => $valgc['con'],
                    );
                    array_push($echoarr,$tmparr);

                }
            }

            $sql_fenlei = "select classify1,classify2,classify3 from sixty_video where flag='1' and classify3='".$this->searchstr."' order by id desc limit 1";
            $list_fenlei = parent::__get('HyDb')->get_row($sql_fenlei);
            if(count($list_fenlei)>0) {
                $echofenlei['classifys'] = array($list_fenlei['classify1'],$list_fenlei['classify2'],$list_fenlei['classify3']);
            }


        }else if('msgjihe'==$this->classtype) {
            if(''!==(string)$this->searchstr) {
                $guize = '2'; //指定输出规则格式，特辑单独格式
                $sql_getonemsg = "select * from sixty_jihemsg where id='".$this->searchstr."' and flag='1' ";
                $list_getonemsg = parent::__get('HyDb')->get_row($sql_getonemsg);
                $echoarr = $list_getonemsg;
                $echoarr['showimg'] = HyItems::hy_qiniuimgurl('sixty-jihemsg',$echoarr['showimg'],$this->imgwidth,$this->imgheight,true);
                $echoarr['detailimg'] = HyItems::hy_qiniuimgurl('sixty-jihemsg',$echoarr['detailimg'],$this->imgwidth,$this->imgheight,true);

            }else {

                $sql_count_jihedata = "select count(*) as con from sixty_jihemsg where flag='1' ";
                //echo $sql_count_getvideo;
                //$list_count_jihedataarr = parent::__get('HyDb')->get_all($sql_count_jihedata);
                $list_count_jihedataarr = parent::func_runtime_sql_data($sql_count_jihedata);
                $list_count_jihedata = isset($list_count_jihedataarr[0]['con'])?$list_count_jihedataarr[0]['con']:'0';
                $pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_jihedata);
                $pagemsg = $pagearr['pagemsg'];
                $pagelimit = $pagearr['pagelimit'];


                $sql_jihedata = "select id,name,detailimg, showimg,content,create_datetime from sixty_jihemsg where flag='1' order by orderby desc,id desc ".$pagelimit;
//                $list_jihedata = parent::__get('HyDb')->get_all($sql_jihedata);
                $list_jihedata = parent::func_runtime_sql_data($sql_jihedata);
                $jihearr = array();
                $jiheidarr = array();
                foreach($list_jihedata as $valj) {
                    $jihearr[$valj['id']] = $valj;
                    array_push($jiheidarr,$valj['id']);
                }
                $jiheinstr = '';
                if(count($jiheidarr)>0) {
                    $jiheinstr = ' and msgjihe in ('.implode(',',$jiheidarr).') ';
                }

                $sql_getmsgjihe = "select msgjihe,count(*) as con from sixty_video where flag='1' and msgjihe>0 ".$jiheinstr." group by msgjihe order by msgjihe";
                $list_getmsgjihe = parent::func_runtime_sql_data($sql_getmsgjihe);

                foreach($list_getmsgjihe as $valgc) {
                    if(''!=$valgc['msgjihe']) {
                        $showimg = isset($jihearr[$valgc['msgjihe']]['showimg'])?$jihearr[$valgc['msgjihe']]['showimg']:'';
                        $showimg = HyItems::hy_qiniuimgurl('sixty-jihemsg',$showimg,$this->imgwidth,$this->imgheight,true);
                        $content = isset($jihearr[$valgc['msgjihe']]['content'])?$jihearr[$valgc['msgjihe']]['content']:'';
                        $tmpname = isset($jihearr[$valgc['msgjihe']]['name'])?$jihearr[$valgc['msgjihe']]['name']:'';
                        $tmpcreate_datetime = isset($jihearr[$valgc['msgjihe']]['create_datetime'])?$jihearr[$valgc['msgjihe']]['create_datetime']:'';

                        $tmparr = array(
                            'type' => 'msgjihe',
                            'jiheid' => $valgc['msgjihe'],
                            'name' => $tmpname,
                            'content' => $content,
                            'showimg' => $showimg,
                            'create_datetime' => $tmpcreate_datetime,
                            'create_date' => date('Y年m月d日',strtotime($tmpcreate_datetime)),
                            'count' => $valgc['con'],
                        );
                        array_push($echoarr,$tmparr);
                        $echoallcon += $valgc['con'];
                    }
                }
            }

        }else {



        }

        if(''!=$echosearch) {
            $echocontent = isset($hy_classifynamearr[$echosearch]['content'])?$hy_classifynamearr[$echosearch]['content']:'';
        }

        if('2'==$guize) {
            $retarr = $echoarr;

        }else {
            $retarr = array();
            $retarr['list'] = $echoarr;
            $retarr['name'] = $echosearch;
            $retarr['content'] = $echocontent;
            $retarr['count'] = $echoallcon;
            $retarr['fenlei'] = $echofenlei;
            if(isset($pagemsg)) {
                $retarr['pagemsg'] = $pagemsg;
            }
        }

        $echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$retarr);
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