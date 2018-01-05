<?php
//人气分类列表展示

class HySix1043 extends HySix{



    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);

//        $this->now_page = isset($input_data['page'])?$input_data['page']:'1'; //当前页码
//        $this->pagesize = isset($input_data['pagesize'])?$input_data['pagesize']:'10'; //显示条数
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


    private function controller_exec1(){
        $sql_class = "select id, showimg, name, content from sixty_classifymsg where flag = 1 and level = 3 limit 20";
        $list_class = parent::__get('HyDb')->get_all($sql_class);

        foreach($list_class as $k_c => $v_c){
            $list_class[$k_c]['showimg'] = HyItems::hy_qiniuimgurl('sixty-jihemsg', $v_c['showimg'], $this->imgwidth, $this->imgheight);
        }
//        var_dump($list_class);die;

        $rarr = array(
//            'pagemsg' => $pagemsg,
            'list' => $list_class,
        );

        //数据转为json，写入日志并输出
        $echojsonstr = HyItems::echo2clientjson('100','数据获取成功',$rarr);
        parent::hy_log_str_add($echojsonstr."\n");
        echo $echojsonstr;
        return true;
    }

    public function controller_init(){


        //用户信息获取入口
        $this->controller_exec1();

        return true;


    }
}