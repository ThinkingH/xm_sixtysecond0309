<?php
/*
 * 评论发布
 */

class HySix1041 extends HySix{


    private $tmpimgpath; //图片临时存储
    private $imgdata;
    private $houzhui;
    private $dataid;
    private $typeid;
    private $contentdata;
    private $plid;
    private $userdata;
    private $fid;

    //数据的初始化
    function __construct($input_data){

        //数据初始化
        parent::__construct($input_data);

        //头像的存放位置
        $this->tmpimgpath = TMPPICPATH;

        $this->houzhui          = isset($input_data['houzhui']) ? $input_data['houzhui'] : '' ;//上传图片后缀名
        $this->imgdata          = isset($input_data['imgdata']) ? $input_data['imgdata'] : '' ;//上传图片信息
        $this->dataid          = isset($input_data['dataid']) ? $input_data['dataid'] : '' ; //视频id字段
        $this->typeid          = isset($input_data['typeid']) ? $input_data['typeid'] : '' ; //类型id字段（1文字评论，2图片评论, 3回复）
        $this->contentdata   = isset($input_data['contentdata']) ? $input_data['contentdata'] : '' ; //文字评论内容
        $this->plid   = isset($input_data['plid']) ? $input_data['plid'] : '' ; //被回复的评论id
        $this->userdata   = isset($input_data['userdata']) ? $input_data['userdata'] : '' ; //被回复评论的用户ID
        $this->fid     = isset($input_data['fid'])?$input_data['fid']:'0';//顶层的对视频的品论的ID
    }


    public function controller_edituserimage(){

        //判断提交信息
        if(!is_numeric($this->dataid)) {
            $echojsonstr = HyItems::echo2clientjson('101','视频id字段不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }
        if(''==$this->contentdata) {
            $echojsonstr = HyItems::echo2clientjson('101','评论内容不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //根据ID查询视频信息
        $sql_videopan = "select id from sixty_video where id='".$this->dataid."'";
        $list_videopan = parent::__get('HyDb')->get_one($sql_videopan);
        if(count($list_videopan)<=0) {//查询结果为空
            $echojsonstr = HyItems::echo2clientjson('101','指定的评论视频id不存在');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //判断评论类型
        if(2==$this->typeid) {//图片评论

            if(''==$this->imgdata) {//上传图片信息为空
                $echojsonstr = HyItems::echo2clientjson('101','上传图片不能为空');
                parent::hy_log_str_add($echojsonstr."\n");
                echo $echojsonstr;
                return false;
            }
            if(''==$this->houzhui) {//上传后缀名为空
                $echojsonstr = HyItems::echo2clientjson('101','图片后缀不能为空');
                parent::hy_log_str_add($echojsonstr."\n");
                echo $echojsonstr;
                return false;
            }


            //判断临时文件夹是否存在
            if(!file_exists($this->tmpimgpath)) {
                mkdir( $filepath, 0777, true );
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



            //解析图片
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

                }else {
                    $showimg = $filename;

                }

            }

        }else if(1==$this->typeid){
            //文字评论
            $showimg = '';

        }else {
            //数据转为json，写入日志并输出
            $echojsonstr = HyItems::echo2clientjson('101','评论类型错误');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //评论内容进行64编码
        $contentdata = base64_encode($this->contentdata);


        //把数据插入评论表
        $sql_insert  = "insert into sixty_video_pinglun (type,vid,userid,content,
						showimg,create_datetime) value (
						'".$this->typeid."','".$this->dataid."','".parent::__get('userid')."',
						'".$contentdata."','".$showimg."','".date('Y-m-d H:i:s')."')";
        $list_insert = parent::__get('HyDb')->execute($sql_insert);


        //数据转为json，写入日志并输出
        $echojsonstr = HyItems::echo2clientjson('100','发布成功');
        parent::hy_log_str_add($echojsonstr."\n");
        echo $echojsonstr;
        return false;



    }

    private function controller_exec2(){
        //判断提交信息
        if(!is_numeric($this->dataid)) {
            $echojsonstr = HyItems::echo2clientjson('101','视频id字段不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }
        if(''==$this->contentdata) {
            $echojsonstr = HyItems::echo2clientjson('101','评论内容不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }
        if(''==$this->fid) {
            $echojsonstr = HyItems::echo2clientjson('101','层主id不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        if(''==$this->plid) {
            $echojsonstr = HyItems::echo2clientjson('101','被评论id不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        if(''==$this->userdata) {
            $echojsonstr = HyItems::echo2clientjson('101','被评论用户id不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //根据ID查询视频信息
        $sql_video = "select id, flag from sixty_video where id='".$this->dataid."'";
        $list_video = parent::__get('HyDb')->get_row($sql_video);
        if(count($list_video)<=0) {//查询结果为空
            $echojsonstr = HyItems::echo2clientjson('101','指定的评论视频id不存在');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        if($list_video['flag'] == 2){//判断该视频是否是启用状态
            $echojsonstr = HyItems::echo2clientjson('101','指定的视频类型不正确');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        //准备插入数据
        $this->contentdata = base64_encode($this->contentdata);
        $date = date('Y-m-d H:i:s',time());

        //插入数据库
        $sql_plb = "insert into sixty_pinglun_back (content, userid, create_datetime, plid, vid, userdata, fplid) VALUE
                    ('".$this->contentdata."','". parent::__get('userid')."','".$date."','".$this->plid."',
                    '".$this->dataid."','".$this->userdata."','".$this->fid."')";

        $insert_plback = parent::__get('HyDb')->execute($sql_plb);
        parent::hy_log_str_add($sql_plb."\n");

        if(!$insert_plback){
            //数据转为json，写入日志并输出
            $echojsonstr = HyItems::echo2clientjson('100','发布失败');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }

        $sql_news = "insert into sixty_user_news (userid, create_datetime, to_userid, vid, message, flag) VALUE
                    ('" . parent::__get('userid')."','".$date."','".$this->userdata."',
                    '".$this->dataid."','".$this->contentdata."', '2')";
        $insert_news = parent::__get('HyDb')->execute($sql_news);
        parent::hy_log_str_add($sql_news."\n");

        //根据id获取被回复的用户的极光id
        $sql_jgid = "select jiguangid from sixty_user where id = '" . $this->userdata . "'";
        $res_jgid = parent::__get('HyDb')->get_one($sql_jgid);


        //根据id获取该用户昵称
        $sql_id = "select nickname from sixty_user where id = '" . parent::__get('userid') . "'";
        $res_id = parent::__get('HyDb')->get_one($sql_id);

        if(!$res_jgid || !$res_id){
            $echojsonstr = HyItems::echo2clientjson('100','评论成功');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return true;
        }

        //发起推送
        if($res_id != ''){
            $message = $res_id.'刚刚回复了您的留言';
            $res_push = parent::func_jgpush($res_jgid,$message,'messagebox');
        }


        $echojsonstr = HyItems::echo2clientjson('100','评论成功');
        parent::hy_log_str_add($echojsonstr."\n");
        echo $echojsonstr;
        return true;
    }




    //操作入口--头像的上传
    public function controller_init(){

        //判断正式用户通讯校验参数
       $r = parent::func_oneusercheck();
       if($r===false){
           return false;
       }

        if($this->typeid == 3){
            $this->controller_exec2();
        }else{
            $this->controller_edituserimage();
        }



        return true;


    }

}