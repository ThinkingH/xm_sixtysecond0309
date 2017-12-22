<?php
/**
 * 食谱步骤删除
 */

class HySix1032 extends HySix {

    private $sort;
    private $typec;

    function __construct($input_data)
    {
        parent::__construct($input_data);

        $this->sort = isset($input_data['sort']) ? $input_data['sort'] : '' ;//食谱顺序id
        $this->typec = isset($input_data['typec']) ? $input_data['typec'] : '' ;//删除类型
    }

    private function controller_exec1() {//删除单条数据

        //判断食材不为空
        if($this->sort == '') {
            $echojsonstr = HyItems::echo2clientjson('101','顺序不能为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }


        //顺序从0开始加一后为获取条数
        $num = $this->sort + 1;
        //获取用户id并取反
        $cook_id = -1 * parent::__get('userid');
        //查询食谱步骤表，查询对应数据
        $sql_data = "select id, picture from sixty_cookbook_buzhou where cook_id = '".$cook_id."' order by sort asc LIMIT ".$num;
        $res_data = parent::__get('HyDb')->get_all($sql_data);

        //数据未找到
        if(count($res_data,0) != $num){
            $echojsonstr = HyItems::echo2clientjson('101','该步骤未找到');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }


        //取出评论ID，图片名称
        $id = $res_data[$this->sort]['id'];
        $pic = $res_data[$this->sort]['picture'];

        //删除七牛云上对应图片
        parent::delete_qiniu('sixty-imgpinglun',$pic);


        //删除数据库对应数据
        $sql_del  = "delete from sixty_cookbook_buzhou where id = '".$id."'";
        parent::hy_log_str_add(HyItems::hy_trn2space($sql_del)."\n");
        $res_del = parent::__get('HyDb')->execute($sql_del);

        if($res_del){

            $echojsonstr = HyItems::echo2clientjson('100','删除成功');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return true;
        }else{
            $echojsonstr = HyItems::echo2clientjson('101','删除失败');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }


    }


    //删除全部步骤
    private function controller_exec2() {

        //获取用户ID并取反
        $cook_id = -1 * parent::__get('userid');

        //根据cook_id字段查询所有该用户为正式提交的步骤条数
        $sql_data = "select id, picture from sixty_cookbook_buzhou where cook_id = '".$cook_id."' LIMIT 1";
        $res_data = parent::__get('HyDb')->get_all($sql_data);

        //判断查询结果是否为空
        if(count($res_data) > 0){
            foreach($res_data as $k_res => $v_res){
                parent::delete_qiniu('sixty-imgpinglun',$v_res['picture']);
            }


            $sql_del  = "delete from sixty_cookbook_buzhou where cook_id = '".$cook_id."'";
            parent::hy_log_str_add(HyItems::hy_trn2space($sql_del)."\n");
            $res_del = parent::__get('HyDb')->execute($sql_del);

            if($res_del){
                $echojsonstr = HyItems::echo2clientjson('100','删除成功');
                parent::hy_log_str_add($echojsonstr."\n");
                echo $echojsonstr;
                return true;
            }else{
                $echojsonstr = HyItems::echo2clientjson('101','删除失败');
                parent::hy_log_str_add($echojsonstr."\n");
                echo $echojsonstr;
                return false;
            }
        }else {
            $echojsonstr = HyItems::echo2clientjson('100','删除成功');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return true;
        }


    }

    //操作入口--食谱食材提交
    public function controller_init(){

        //判断正式用户通讯校验参数
        $r = parent::func_oneusercheck();
        if($r===false){
            return false;
        }

        if($this->typec == ''){
            $echojsonstr = HyItems::echo2clientjson('101','删除类型为空');
            parent::hy_log_str_add($echojsonstr."\n");
            echo $echojsonstr;
            return false;
        }else if($this->typec == 1){//删除单条步骤
            $this->controller_exec1();
            return true;
        }else if($this->typec == 2){//删除所有步骤
            $this->controller_exec2();
            return true;
        }





    }
}