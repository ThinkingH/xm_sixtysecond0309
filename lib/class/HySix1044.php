<?php
/**
 * 大家的食谱展示图
 */

class HySix1044 extends HySix{

    private $now_page;
    private $pagesize;
    private $imgwidth;
    private $imgheight;

    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);
        $this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';    //图片宽度
        $this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:''; //图片高度
        $this->now_page = isset($input_data['page'])?$input_data['page']:'1';
        $this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10';

        //图片宽度默认值
        if(''==$this->imgwidth) {
            $this->imgwidth = 500;
        }
        //图片高度默认值
        if(''==$this->imgheight) {
            $this->imgheight = 500;
        }

    }

    private function controller_exec1(){

        $sql_where = 'where state = 1';

        //收藏数量
        $sql_count_getvideoshoucang = "select count(*) as con from sixty_cookbook ".$sql_where;
        $list_count_getvideoshoucang = parent::__get('HyDb')->get_one($sql_count_getvideoshoucang);

        //执行分页
        $pagearr = HyItems::hy_pagepage($this->now_page,$this->pagesize,$list_count_getvideoshoucang);
        $pagemsg = $pagearr['pagemsg'];
        $pagelimit = $pagearr['pagelimit'];

        $sql_cook = "select * from sixty_cookbook " . $sql_where . $pagelimit;
        $list_cook = parent::__get('HyDb')->get_all($sql_cook);

        if(count($list_cook) > 0){
            foreach($list_cook as $k_cook => $v_cook){

            }
        }

    }



    //接口入口
    public function controller_init(){
        $this->controller_exec1();
        return true;
    }
}