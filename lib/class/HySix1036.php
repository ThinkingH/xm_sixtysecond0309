<?php
/**
 * 小贴士列表
 */

class HySix1036 extends HySix{

    private $now_page;
    private $pagesize;
    private $imgwidth;
    private $imgheight;
    private $searchstr;
    private $typex;
    private $dataid;



    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);

        $this->now_page = isset($input_data['page'])?$input_data['page']:'1';   //当前页数
        $this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';  //显示个数
        $this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';    //图片宽度
        $this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:''; //图片高度
        $this->searchstr = isset($input_data['searchstr'])?$input_data['searchstr']:''; //搜索分类id
        $this->typex = isset($input_data['typex'])?$input_data['typex']:''; //显示类型
        $this->dataid = isset($input_data['dataid'])?$input_data['dataid']:''; //视频id


        //图片宽度默认值
        if(''==$this->imgwidth) {
            $this->imgwidth = 500;
        }
        //图片高度默认值
        if(''==$this->imgheight) {
            $this->imgheight = 500;
        }

    }

    protected function controller_exec1(){

        //查询小贴士分类表数据
        $sql_class = "select id, name from sixty_tieshi_class order by id desc";
//        $list_class = parent::func_runtime_sql_data($sql_class);
        $list_class = parent::__get('HyDb')->get_all($sql_class);

        //遍历结果集
        $con = 0;
        $list = array();
        foreach($list_class as $k_c => $v_c) {

            $sql_v = "select id as vid, biaoti, showimg, videosavename, abstract as jieshao from sixty_tieshi_video where flag=1 and class='".$v_c['id']."' order by create_datetime desc limit 8";
            $list_v = parent::__get('HyDb')->get_all($sql_v);

            foreach($list_v as $k_v => $v_v){
                $list_v[$k_v]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage', $v_v['showimg'], $this->imgwidth, $this->imgheight);
                $list_v[$k_v]['videourl'] = HyItems::hy_qiniubucketurl('sixty-video',$v_v['videosavename']);
                $list_v[$k_v]['vtype'] = '1';
                $list_v[$k_v]['class'] = $v_c['id'];
                $list_v[$k_v]['classname'] = $v_c['name'];
            }
            $list[$con]['class'] = $v_c['id'];
            $list[$con]['classname'] = $v_c['name'];
            $list[$con]['listvideo'] = $list_v;
            $con++;
        }

        $echoarr = array(
            'list' => $list
        );

        $echojsonstr = HyItems::echo2clientjson('100', '数据获取成功', $echoarr);
        parent::hy_log_str_add($echojsonstr . "\n");
        echo $echojsonstr;
        return true;

    }

    protected function controller_exec2(){

        if($this->searchstr === (string)''){
            $echojsonstr = HyItems::echo2clientjson('101', '分类名为空');
            parent::hy_log_str_add($echojsonstr . "\n");
            echo $echojsonstr;
            return false;
        }


        //sql where语句开头
        $sql_where = " where flag = '1' and class = '".$this->searchstr."'";

        //分页
        $sql_count = "select count(*) as con from sixty_tieshi_video".$sql_where;
//        var_dump($sql_count);die;
        $count_video = parent::__get('HyDb')->get_all($sql_count);

        //如果查询结果为空，则给默认值0
        $count_video = isset($count_video[0]['con'])?$count_video[0]['con']:'0';

        //调用分页函数
        $pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$count_video);
        $pagemsg = $pagearr['pagemsg'];
        $pagelimit = $pagearr['pagelimit'];

        //
        $sql_xvideo = "select abstract as jieshao, biaoti, videosavename, showimg, id, id as vid, class from sixty_tieshi_video".$sql_where." order by id desc".$pagelimit;
        $list_video = parent::__get('HyDb')->get_all($sql_xvideo);


        if(count($list_video) > 0){
            $sql_tieshi_class = "select name from sixty_tieshi_class where id = '".$list_video['0']['class']."'";
            $res_class = parent::__get('HyDb')->get_one($sql_tieshi_class);
            //遍历查询结果集
            foreach($list_video as $keygv => $valgv) {
                //$list_getvideo[$keygv]['create_date'] = substr($list_getvideo[$keygv]['create_datetime'],0,10);
                //查询获取七牛云图片地址
                $list_video[$keygv]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$valgv['showimg'],$this->imgwidth,$this->imgheight);
                //获取七牛云视频地址
                $list_video[$keygv]['videourl'] = HyItems::hy_qiniubucketurl('sixty-video',$valgv['videosavename']);
                $list_video[$keygv]['vtype'] = '1';
                $list_video[$keygv]['classname'] = $res_class;
            }
        }else{
            $list_video = array();
        }



        //准备输出数组
        $rarr = array(
            'pagemsg' => $pagemsg,
            'list' => $list_video,
        );

        //数据转为json，写入日志并输出
        $echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$rarr);
        parent::hy_log_str_add($echojsonstr."\n");
        echo $echojsonstr;
        return true;


    }



    private function controller_exec3(){
        //判断数据是否为空
        if($this->searchstr == ''){
            $echojsonstr = HyItems::echo2clientjson('101', '视频分类为空');
            parent::hy_log_str_add($echojsonstr . "\n");
            echo $echojsonstr;
            return false;
        }

        if($this->dataid == ''){
            $echojsonstr = HyItems::echo2clientjson('101', '视频id为空');
            parent::hy_log_str_add($echojsonstr . "\n");
            echo $echojsonstr;
            return false;
        }


        //根据分类名查询贴士视频表数据
        $sql_video = "select biaoti, videosavename, showimg, id, id as vid ,abstract as jieshao from sixty_tieshi_video 
                      where flag = 1 and class = '" . $this->searchstr ."' and id <> ".$this->dataid." order by create_datetime desc limit 20";

        $res_video = parent::__get('HyDb')->get_all($sql_video);

        if(count($res_video) > 0){
            foreach($res_video as $k_v => $v_v){
                //获取七牛云视频地址
                $res_video[$k_v]['videourl'] = HyItems::hy_qiniubucketurl('sixty-video',$v_v['videosavename']);
                //查询获取七牛云图片地址
                $res_video[$k_v]['showimg'] = HyItems::hy_qiniuimgurl('sixty-videoimage',$v_v['showimg'],$this->imgwidth,$this->imgheight);
                $list_video[$k_v]['vtype'] = '1';
            }
        }else{
            $res_video = array();
        }


        //数据转为json，写入日志并输出
        $echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$res_video);
        parent::hy_log_str_add($echojsonstr."\n");
        echo $echojsonstr;
        return true;
    }

    //用户信息--操作入口
    public function controller_init(){

        if($this->typex == 1){
            //小贴士简单列表展示
            $this->controller_exec1();
        }else if($this->typex == 2){
            //小贴士按系列展示
            $this->controller_exec2();
        }else if($this->typex == 3){
            //小贴士展示20条数据
            $this->controller_exec3();
        }else{
            $echojsonstr = HyItems::echo2clientjson('101','查询类型不正确');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        return true;


    }




}