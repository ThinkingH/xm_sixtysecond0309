<?php


/**
 * 基于mysqli的数据库操作类
 * 
 * @author yu
 *
 */
class HyDb {
	
	
	protected $mysqli; //mysqli实例对象  
	protected $rs; //结果集存放变量  
	protected $fetch_mode = MYSQLI_ASSOC; //获取模式  
	
	
	
	
	//构造函数：主要用来返回一个mysqli对象  
	public function  __construct() {
		
		$this->mysqli = new mysqli( DBHOST, DBUSER, DBPASS, DBNAME, DBPORT );
		
		if(mysqli_connect_errno()) {
			$this->mysqli = false;
			//echo '<h2>'.mysqli_connect_error().'</h2>';
			exit;
		} else {
			$this->mysqli->set_charset("utf8");
		}
	}
	
	
	
	
	//析构函数：主要用来释放结果集和关闭数据库连接  
	public function __destruct() {
		$this->free();
		$this->close();
	}
	
	
	
	
	//释放结果集所占资源  
	protected function free() {
		if(isset($this->rs)) {
			@$this->rs->free();
		}
	}

	//关闭数据库连接  
	protected function close() {
		$this->mysqli->close();
	}
	
	
	
	
	//获取结果集  
	protected function fetch() {
		return $this->rs->fetch_array($this->fetch_mode);
	}
	
	
	//获取查询的sql语句---insert和update语句同样可以使用此函数处理
	protected function get_query_sql($sql, $limit = null) {
		if (@preg_match("/[0-9]+(,[ ]?[0-9]+)?/is", $limit) && !preg_match("/ LIMIT [0-9]+(,[ ]?[0-9]+)?$/is", $sql)) {
			$sql .= " limit " . $limit;
		}
		return $sql;
	}
	
	
	
	
	//执行sql语句查询---核心查询操作函数
	public function query($sql, $limit = null) {
		
		$sql = $this->get_query_sql($sql, $limit);
		
		$this->rs = $this->mysqli->query($sql);
		if(!$this->rs) {
			echo "<p>error: ".$this->mysqli->error."</p>";
// 			echo $sql;
			return false;
		}else {
			return $this->rs;
		}
	}
	
	
	
	
	//返回单条记录的单个字段值  
	public function get_one($sql) {
		$this->query($sql, 1);
		$this->fetch_mode = MYSQLI_NUM;
		$row = $this->fetch();
		$this->free();
		return $row[0];
	}
	
	
	
	
	//获取单条记录  
	public function get_row($sql, $fetch_mode = MYSQLI_ASSOC ) {
		$this->query($sql, 1);
		$this->fetch_mode = $fetch_mode;
		$row = $this->fetch();
		$this->free();
		return $row;
	}
	
	
	
	
	//返回所有的结果集  
	public function get_all($sql, $limit = null, $fetch_mode = MYSQLI_ASSOC ) {  
		$this->query($sql, $limit);
		$all_rows = array();
		$this->fetch_mode = $fetch_mode;
		while($rows = $this->fetch()) {
			$all_rows[] = $rows;
		}
		$this->free();
		return $all_rows;
	}
	
	
	
	
	//数据插入更新sql语句执行函数
	public function execute($sql) {
		if(trim($sql) == '') {
			//如果提交的语句为空，直接返回false
			return false;
		}else {
			//执行sql语句
			if( $this->mysqli->query($sql) ) {
				return true;
			}else {
				echo "<p>error: ".$this->mysqli->error."</p>";
				return false;
			}
			
		}
		
	}
	
	
	
	
}
