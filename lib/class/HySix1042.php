<?php
/**
 * 评论数据获取-视频文字评论
 */

class HySix1042 extends HySix{

    private $now_page;
    private $pagesize;
    private $imgwidth;
    private $imgheight;
    private $nowid;


    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);

        $this->now_page = isset($input_data['page'])?$input_data['page']:'1';//当前页数
        $this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';//单页显示数目
        $this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';//图片宽度
        $this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:'';//图片高度
        $this->nowid     = isset($input_data['nowid'])?$input_data['nowid']:'0';//视频id
        //图片默认宽度
        if(''==$this->imgwidth) {
            $this->imgwidth = 300;
        }
        //图片默认高度
        if(''==$this->imgheight) {
            $this->imgheight = 300;
        }
        //视频id是否为空
        if(!is_numeric($this->nowid)) {
            $this->nowid = 0;
        }
    }


    protected function controller_exec1(){

        //准备sql where语句
        $sql_where = " where vid='".$this->nowid."' and type='1' ";

        //进行分页信息查询
        $sql_count_getvideopinglun = "select count(*) as con from sixty_video_pinglun ".$sql_where;
        $list_count_getvideopinglun = parent::__get('HyDb')->get_one($sql_count_getvideopinglun);
        $pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideopinglun);
        $pagemsg = $pagearr['pagemsg'];
        $pagelimit = $pagearr['pagelimit'];


        //查询评论表文字评论
        $sql_getvideopinglun = "select id,userid,content,create_datetime
								from sixty_video_pinglun
								".$sql_where." order by dianzan desc,id desc ".$pagelimit;

        $list_getvideopinglun =  parent::__get('HyDb')->get_all($sql_getvideopinglun);


        //判断搜索结果
        if(count($list_getvideopinglun) > 0){
            $plstr = '';
            $useridarr = array();
            foreach($list_getvideopinglun as $k_p => $v_p){
                //把评论id放入数组
                $plstr .= $v_p['id'] . ',';
                if(!in_array($v_p['userid'], $useridarr)) {
                    array_push($useridarr, $v_p['userid']);
                }

            }
            $plstr = rtrim($plstr,',');

            //搜索评论回复表
            $sql_where_back = " where vid='".$this->nowid."' and plid in (" . $plstr .")";
            $sql_pl_back = "select id,userid,content,create_datetime, plid, userdata
								from sixty_pinglun_back
								".$sql_where_back." order by id desc ";

            $list_pl_back =  parent::__get('HyDb')->get_all($sql_pl_back);


            //判断搜索结果
            if(count($list_pl_back) > 0){//搜索结果为空

                //遍历评论表结果集
                foreach($list_pl_back as $valgp) {
                    //把用户id放入数组
                    if(!in_array($valgp['userid'], $useridarr)) {
                        array_push($useridarr, $valgp['userid']);
                    }
                }
                //根据用户id获取用户信息
                $retarr = parent::func_retsqluserdata($useridarr,50,50);


                //遍历评论结果集
                foreach($list_getvideopinglun as $keygp => $valgp) {
                    //存入年月日
                    $list_getvideopinglun[$keygp]['create_date'] = date('Y年m月d日', strtotime($list_getvideopinglun[$keygp]['create_datetime']));
                    //存入用户昵称
                    $list_getvideopinglun[$keygp]['nickname'] = parent::func_userid_datatiqu($retarr, $list_getvideopinglun[$keygp]['userid'], 'nickname');
                    //存入用户头像
                    $list_getvideopinglun[$keygp]['touxiang'] = parent::func_userid_datatiqu($retarr, $list_getvideopinglun[$keygp]['userid'], 'touxiang');
                    //存入评论内容
                    $list_getvideopinglun[$keygp]['content'] = base64_decode($list_getvideopinglun[$keygp]['content']);
                    $list_getvideopinglun[$keygp]['back'] = array();
                    foreach($list_pl_back as $k_plb => $v_plb) {
                        //存入年月日
                        $list_pl_back[$k_plb]['create_date'] = date('Y年m月d日', strtotime($v_plb['create_datetime']));
                        //存入用户昵称
                        $list_pl_back[$k_plb]['nickname'] = parent::func_userid_datatiqu($retarr, $v_plb['userid'], 'nickname');
                        //存入用户头像
                        $list_pl_back[$k_plb]['touxiang'] = parent::func_userid_datatiqu($retarr, $v_plb['userid'], 'touxiang');
                        //存入评论内容
                        $list_pl_back[$k_plb]['content'] = base64_decode($k_plb['content']);

                        if($valgp['id'] == $v_plb['plid']) {
                            array_push($list_getvideopinglun[$keygp]['back'], $list_pl_back[$k_plb]);

                        }
                    }
                }
            }else{
                //遍历评论结果集
                foreach($list_getvideopinglun as $keygp => $valgp) {
                    //存入年月日
                    $list_getvideopinglun[$keygp]['create_date'] = date('Y年m月d日', strtotime($list_getvideopinglun[$keygp]['create_datetime']));
                    //存入用户昵称
                    $list_getvideopinglun[$keygp]['nickname'] = parent::func_userid_datatiqu($retarr, $list_getvideopinglun[$keygp]['userid'], 'nickname');
                    //存入用户头像
                    $list_getvideopinglun[$keygp]['touxiang'] = parent::func_userid_datatiqu($retarr, $list_getvideopinglun[$keygp]['userid'], 'touxiang');
                    //存入评论内容
                    $list_getvideopinglun[$keygp]['content'] = base64_decode($list_getvideopinglun[$keygp]['content']);
                    $list_getvideopinglun[$keygp]['back'] = array();
                }
            }
        }


        //准备输出数组
        $rarr = array(
            'pagemsg' => $pagemsg,
            'list' => $list_getvideopinglun,
        );

        //数据转为json，写入日志并输出
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