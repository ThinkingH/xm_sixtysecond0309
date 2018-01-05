<?php
/**
* 首页图标获取
*/

class HySix1040 extends HySix{

    private $imgwidth;
    private $imgheight;


    //数据的初始化
    public function __construct($input_data){
        //数据初始化
        parent::__construct($input_data);
        $this->imgwidth = isset($input_data['imgwidth'])?$input_data['imgwidth']:'';    //图片宽度
        $this->imgheight = isset($input_data['imgheight'])?$input_data['imgheight']:''; //图片高度


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
        //获取图标表已开启的数据
        $sql_icon = "select word, id, showimg, keyword from sixty_icon where flag = 1 and id <> 1 and id <> 2 and id <> 3 order by ordernum desc";
        $list_icon = parent::__get('HyDb')->get_all($sql_icon);


        //判断数据是否为空
        if(count($list_icon) <= 0){
            $echojsonstr = HyItems::echo2clientjson('101', '数据获取失败');
            parent::hy_log_str_add($echojsonstr . "\n");
            echo $echojsonstr;
            return false;
        }


        //遍历结果
        $key = '';
        foreach($list_icon as $k_icon => $v_icon){
            //获取图片url地址
            $list_icon[$k_icon]['showimg'] = HyItems::hy_qiniuimgurl('sixty-jihemsg',$v_icon['showimg'],$this->imgwidth,$this->imgheight);
            //拼接分类名
            $key .= $v_icon['keyword'].',';
        }
        $key = rtrim($key,',');
//        var_dump()

        //根据分类名查询视频分类列表
        $sql_class = 'select id,name from sixty_classifymsg where flag = 1 and id in('.$key.')';
        $list_class = parent::__get('HyDb')->get_all($sql_class);

        //判断结果
        if(count($list_class) <= 0){
            $echojsonstr = HyItems::echo2clientjson('101', '数据获取失败');
            parent::hy_log_str_add($echojsonstr . "\n");
            echo $echojsonstr;
            return false;
        }


        //遍历结果集
        foreach($list_icon as $k_icon => $v_icon){
            foreach($list_class as $k_cl => $v_cl){
                //如果分类id等于视频保存的分类id
                if($v_icon['keyword'] == $v_cl['id']){
                    $list_icon[$k_icon]['keyword'] = $v_cl['name'];
                }
            }
        }


        //查询最新，特辑
        $sql_id = "select word, id, showimg, keyword from sixty_icon where id in (1,2) ORDER BY id DESC ";
        $res_id = parent::__get('HyDb')->get_all($sql_id);

        //遍历并放入输出数组
        foreach($res_id as $k_i => $v_i){
            if($v_i['id'] == 1){
                $v_i['keyword'] = '';
            }elseif($v_i['id'] == 2){
                $v_i['keyword'] = 'msgjihe';
            }
            $v_i['showimg'] = HyItems::hy_qiniuimgurl('sixty-jihemsg',$v_i['showimg'],$this->imgwidth,$this->imgheight);

            array_unshift($list_icon,$v_i);
        }


        //查询大家的投稿
        $sql_id_3 = "select word, id, showimg, keyword from sixty_icon where id = 3 limit 1";
        $res_id_3 = parent::__get('HyDb')->get_row($sql_id_3);
        $res_id_3['keyword'] = 'cookbook';
        $res_id_3['showimg'] = HyItems::hy_qiniuimgurl('sixty-jihemsg',$res_id_3['showimg'],$this->imgwidth,$this->imgheight);
        array_push($list_icon,$res_id_3);


        $echojsonstr = HyItems::echo2clientjson('100', '数据获取成功', $list_icon);
        parent::hy_log_str_add($echojsonstr . "\n");
        echo $echojsonstr;
        return true;
    }


    //接口入口
    public function controller_init(){
        $this->controller_exec1();
        return true;
    }
}