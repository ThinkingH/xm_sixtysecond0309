<?php
/**
 *食谱主表数据提交-带图
 */

class HySix1030 extends HySix {
    private $tmpimgpath; //图片临时存储
    private $imgdata;
    private $houzhui;
    private $biaoti;
    private $abstract;
    private $maketime;
    private $money;
    private $keypoint;
    private $user_id;
    private $state;
    private $update;
    private $create_datetime;
    private $cook_cailiao;

    //数据的初始化
    function __construct($input_data){

        //数据初始化
        parent::__construct($input_data);

        $this->tmpimgpath = TMPPICPATH;

        $this->houzhui = isset($input_data['houzhui']) ? $input_data['houzhui'] : 'jpg' ;
        $this->imgdata = isset($input_data['imgdata']) ? $input_data['imgdata'] : '' ;
        $this->biaoti = isset($input_data['biaoti']) ? $input_data['biaoti'] : '' ;//标题
        $this->abstract = isset($input_data['abstract']) ? $input_data['abstract'] : '' ;//介绍
        $this->maketime = isset($input_data['maketime']) ? $input_data['maketime'] : '' ;//制作时间
        $this->money = isset($input_data['money']) ? $input_data['money'] : '' ;//金额花费
        $this->keypoint = isset($input_data['keypoint']) ? $input_data['keypoint'] : '' ;//关键提示
        $this->user_id = isset($input_data['user_id']) ? $input_data['user_id'] : '' ;//用户ID
        $this->cook_cailiao = isset($input_data['cook_cailiao']) ? $input_data['cook_cailiao'] : '' ;//食材数组
        $this->state = 2;//食谱状态，’2‘,审核中
        $this->create_datetime = date('Y-m-d H:i:s', time());//创建时间
        $this->update = isset($input_data['update']) ? $input_data['update'] : '' ;//类型，1插入，2更新

    }

    private function controller_exec1() {
        //上传食谱图片是否为空
        if($this->imgdata == '') {
            $echojsonstr = HyItems::echo2clientjson('101','食谱封面图不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //标题是否为空
        if($this->biaoti == '') {
            $echojsonstr = HyItems::echo2clientjson('101','食谱标题不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //简介是否为空
        if($this->abstract == '') {
            $echojsonstr = HyItems::echo2clientjson('101','食谱简介不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //食材是否为空
        if($this->cook_cailiao == '') {
            $echojsonstr = HyItems::echo2clientjson('101','食谱食材不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //料理时间是否为空
        if($this->maketime == '') {
            $echojsonstr = HyItems::echo2clientjson('101','料理时间不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //根据提交标题查询食谱数据库，查看该标题是否存在
        $sql_biaoti = "select id from sixty_cookbook where biaoti = '" . $this->biaoti . "'";
        $res_biaoti = parent::__get('HyDb')->get_one($sql_biaoti);
        if($res_biaoti != '') {
            $echojsonstr = HyItems::echo2clientjson('101','此标题已存在，请换一个标题');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }


        //图片临时保存文件夹是否存在
        if(!file_exists($this->tmpimgpath)) {
            //不存在，创建文件夹
            mkdir($filepath, 0777, true );
        }

        //图片文件名
        $filename = parent::__get('userid').'_'.date('ymdHis').mt_rand(100,999).'.'.$this->houzhui;
        $filepathname = $this->tmpimgpath.$filename;

        //把图片的编码解码为图片，存到对应的路径中
        file_put_contents($filepathname,base64_decode($this->imgdata));

        //图片后缀重组
        $cz_filepathname = HyItems::hy_getfiletype($filepathname);
        //对文件进行重命名，修改后缀
        rename($filepathname,$cz_filepathname);


        if(false===parent::func_isImage($cz_filepathname)) {
            //解析失败
            @unlink($cz_filepathname); //删除文件
            $echojsonstr = HyItems::echo2clientjson('101','封面图解析失败，请重试');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }else {

            //上传到七牛云之前先进行图片格式转换，统一使用jpg格式
            $r = HyItems::hy_resave2jpg($cz_filepathname);
            if($r!==false) {
                parent::hy_log_str_add($r."\n");
                $cz_filepathname = $r;
            }


            //上传到七牛云
            $r = parent::upload_qiniu('sixty-imgpinglun',$cz_filepathname,pathinfo($cz_filepathname,PATHINFO_BASENAME),'yes');

            if(false===$r) {
                @unlink($cz_filepathname); //删除文件
                //上传失败
                $echojsonstr = HyItems::echo2clientjson('101','封面图上传失败');
                parent::hy_log_str_add($echojsonstr."\n");
                echo $echojsonstr;

                return false;
            }


            //对象转为数组
            $cook_cailiao_arr = json_decode(json_encode($this->cook_cailiao),true);
            $array_cailiao = array();
            //遍历数组，整理数据
            foreach($cook_cailiao_arr as $k_cl => $v_cl) {
                $array_cailiao[]['id'] = $v_cl['id'];
                $array_cailiao[]['rmvalue'] = $v_cl['rmvalue'];
                $array_cailiao[]['rcvalue'] = $v_cl['rcvalue'];
            }

            $cailiao = json_encode($array_cailiao);

            $onlyidflag = date('ymdHis').mt_rand(1000,9999);

            //拼接sql语句
            $sql_insert = "insert into sixty_cookbook (showimg, biaoti, abstract, maketime,
                            money, keypoint, user_id, state, 
                            create_datetime,onlyidflag,food)
                          value ('".$filename."','".$this->biaoti."','".$this->abstract."','".$this->maketime."','".
                $this->money."','".$this->keypoint."','".parent::__get('userid')."','".$this->state."','".
                $this->create_datetime."','".$onlyidflag."','".$cailiao."')";

            parent::hy_log_str_add(HyItems::hy_trn2space($sql_insert)."\n");

            //执行添加
            $res_insert = parent::__get('HyDb')->execute($sql_insert);

            if(!$res_insert){
                $echojsonstr = HyItems::echo2clientjson('101','食谱上传失败');
                parent::hy_log_str_add($echojsonstr."\n");
                echo $echojsonstr;
                return false;
            }


            //取出刚插入的数据
            $sql_id = "select id from sixty_cookbook where onlyidflag = '".$onlyidflag."' order by onlyidflag desc";
            $cook_id = parent::__get('HyDb')->get_one($sql_id);


            //改变步骤表cook_id
            $old_cook_id = -1 * parent::__get('userid');
            $sql_update_cook_id = "update sixty_cookbook_buzhou set cookid_id = '".$cook_id."' where cookid_id = '".$old_cook_id."'";


            if(!$sql_update_cook_id){
                $echojsonstr = HyItems::echo2clientjson('101','步骤修改');
                parent::hy_log_str_add($echojsonstr."\n");
                echo $echojsonstr;
                return false;
            }


            $echojsonstr = HyItems::echo2clientjson('100','食谱上传成功');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return true;


        }

    }



    //操作入口--食谱提交
    public function controller_init(){

        //判断正式用户通讯校验参数
        $r = parent::func_oneusercheck();
        if($r===false){
            return false;
        }

        $this->controller_exec1();

        return true;


    }
}