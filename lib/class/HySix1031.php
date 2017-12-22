<?php
/**
 * 食谱步骤提交-带图

 */

class HySix1031 extends HySix{
    private $tmpimgpath; //图片临时存储
    private $imgdata;
    private $houzhui;
    private $sort;
    private $word;
    private $update;


    //数据的初始化
    function __construct($input_data){

        //数据初始化
        parent::__construct($input_data);

        //头像的存放位置
        $this->tmpimgpath = TMPPICPATH;

        $this->houzhui          = isset($input_data['houzhui']) ? $input_data['houzhui'] : '' ;
        $this->imgdata          = isset($input_data['imgdata']) ? $input_data['imgdata'] : '' ;
        $this->sort          = isset($input_data['sort']) ? $input_data['sort'] : '' ; //视频id字段
        $this->word          = isset($input_data['word']) ? $input_data['word'] : '' ; //类型id字段（1文字评论，2图片评论）
        $this->update          = isset($input_data['update']) ? $input_data['update'] : '' ; //类型id字段（1文字评论，2图片评论）

    }


    public function controller_exec1(){

        //上传图片是否为空
        if($this->imgdata == '') {
            $echojsonstr = HyItems::echo2clientjson('101', '食谱步骤图不能为空');
            parent::hy_log_str_add($echojsonstr . "\n");
            echo $echojsonstr;
            return false;
        }

        //步骤文字是否为空
        if($this->word == '') {
            $echojsonstr = HyItems::echo2clientjson('101', '食谱步骤文字不能为空');
            parent::hy_log_str_add($echojsonstr . "\n");
            echo $echojsonstr;
            return false;
        }

//        if($this->sort == '') {
//            $echojsonstr = HyItems::echo2clientjson('101', '食谱顺序不能为空');
//            parent::hy_log_str_add($echojsonstr . "\n");
//            echo $echojsonstr;
//            return false;
//        }

        //判断临时文件夹是否存在
        if(!file_exists($this->tmpimgpath)) {
            mkdir( $this->tmpimgpath, 0777, true );
        }

        //图片文件名
        $filename = parent::__get('userid').'_'.date('ymdHis').mt_rand(100,999).'.'.$this->houzhui;
        //文件的路径
        $filepathname = $this->tmpimgpath.$filename;

        //把图片的编码解码为图片，存到对应的路径中
        file_put_contents($filepathname,base64_decode($this->imgdata));

        //图片后缀重组
        $cz_filepathname = HyItems::hy_getfiletype($filepathname);
        //对文件进行重命名，修改后缀
        rename($filepathname,$cz_filepathname);


        if(false===parent::func_isImage($cz_filepathname)) {
            //解析失败
            $echojsonstr = HyItems::echo2clientjson('101','图片解析失败，请重试');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }else {

            //上传到七牛云
            $r = parent::upload_qiniu('sixty-imgpinglun',$cz_filepathname,$filename);
            unlink($cz_filepathname); //删除文件

            if(false===$r) {
                //上传失败
                $echojsonstr = HyItems::echo2clientjson('101','图片上传失败');
                parent::hy_log_str_add($echojsonstr."\n");
                echo $echojsonstr;
                return false;

            }else{
                $showimg = $filename;

            }
        }

        $cook_id = -1 * parent::__get('userid');
        //拼接sql语句
        $sql = "insert into sixty_cookbook_buzhou (picture, word, sort, create_datetime, cook_id) value
          ('".$filename."','".$this->word."','".$this->sort."','".$this->create_datetime."','".$cook_id."')";


        parent::hy_log_str_add(HyItems::hy_trn2space($sql)."\n");

        //执行
        $res = parent::__get('HyDb')->execute($sql);

        if($res){
            $echojsonstr = HyItems::echo2clientjson('100','提交成功');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return true;
        }else{
            $echojsonstr = HyItems::echo2clientjson('101','提交失败');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

    }



    public function controller_exec2(){

        if($this->sort == '') {
            $echojsonstr = HyItems::echo2clientjson('101', '食谱顺序不能为空');
            parent::hy_log_str_add($echojsonstr . "\n");
            echo $echojsonstr;
            return false;
        }


        $cook_id = -1 * parent::__get('userid');
        $num = $this->sort + 1;

        $sql_data = "select id, picture from sixty_cookbook_buzhou where cook_id = '". $cook_id ."' order by id asc limit ".$num;
        $res_data = parent::__get('HyDb')->get_all($sql_data);

        $id = isset($res_data[$this->sort]['id']) ? $res_data[$this->sort]['id'] : '';
        $pic = isset($res_data[$this->sort]['picture']) ? $res_data[$this->sort]['picture'] : '';
//        var_dump($id);die;
        if($id == '' || $pic == ''){
            $echojsonstr = HyItems::echo2clientjson('101','该步骤未找到');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }


        $update_str = '';
        if($this->imgdata != '') {
            if(!file_exists($this->tmpimgpath)) {
                mkdir( $this->tmpimgpath, 0777, true );
            }

            //图片文件名
            $filename = parent::__get('userid').'_'.date('ymdHis').mt_rand(100,999).'.'.$this->houzhui;
            //文件的路径
            $filepathname = $this->tmpimgpath.$filename;

            //把图片的编码解码为图片，存到对应的路径中
            file_put_contents($filepathname,base64_decode($this->imgdata));

            //图片后缀重组
            $cz_filepathname = HyItems::hy_getfiletype($filepathname);
            //对文件进行重命名，修改后缀
            rename($filepathname,$cz_filepathname);


            if(false===parent::func_isImage($cz_filepathname)) {
                //解析失败
                $echojsonstr = HyItems::echo2clientjson('101','图片解析失败，请重试');
                parent::hy_log_str_add($echojsonstr."\n");
                echo $echojsonstr;
                return false;
            }else {

                //上传到七牛云
                $r = parent::upload_qiniu('sixty-imgpinglun',$cz_filepathname,$filename);
                unlink($cz_filepathname); //删除文件

                if(false===$r) {
                    //上传失败
                    $echojsonstr = HyItems::echo2clientjson('101','图片上传失败');
                    parent::hy_log_str_add($echojsonstr."\n");
                    echo $echojsonstr;
                    return false;

                }else{
                    $update_str .= "picture = '" . $filename . "',";

                    //删除七牛旧图片
                    parent::delete_qiniu('sixty-imgpinglun', $pic);
                }
            }
        }

        if($this->word != '') {
            $update_str .= "word = '" . $this->word . "',";
        }

        $update_str = substr($update_str, 0, -1);

        //拼接sql语句
        $sql = "update sixty_cookbook_buzhou set ". $update_str . " where id = " . $id;

        parent::hy_log_str_add(HyItems::hy_trn2space($sql)."\n");

        //执行
        $res = parent::__get('HyDb')->execute($sql);

        if($res){
            $echojsonstr = HyItems::echo2clientjson('100','修改成功');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return true;
        }else{
            $echojsonstr = HyItems::echo2clientjson('101','修改失败');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

    }



    //操作入口
    public function controller_init(){

        //判断正式用户通讯校验参数
        $r = parent::func_oneusercheck();
        if($r===false){
            return false;
        }

        if($this->update == ''){
            $echojsonstr = HyItems::echo2clientjson('101','插入类型不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }else if($this->update == '1'){
            $this->controller_exec1();
        }else if($this->update == '2'){
            $this->controller_exec2();
        }




        return true;


    }
}