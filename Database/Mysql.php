<?php

namespace Setsuna\Database;

class Mysql
{
	private static $_instance = null;
	private $conn = null;


	private function factory($host, $username, $password, $databaseName) {

		$this->conn = mysql_connect($host, $username, $password);

		if(!$this->conn) die(mysql_error());

		mysql_select_db($databaseName, $this->conn);
        mysql_query('set names utf8' , $this->conn);
		return $this;
	}



	/**
	 * 链接数据库
	 * @param $host         数据库地址
	 * @param $username     数据库用户名
	 * @param $password     数据库用户密码
	 * @param $databaseName 数据库名
	 * @return db|null
	 */
	public function get_instance($host, $username, $password, $databaseName)
	{
		if(!(self::$_instance instanceof self))
		{
			self::$_instance = $this->factory($host, $username, $password, $databaseName);
		}

		return self::$_instance;
	}

	/**
	 * 根据条件获取记录
	 * @param        $tableName
	 * @param string $condition
	 * @param string $fields
	 * @param string $order
	 * @param string $limit
	 * @return array
	 */
	public function fetch_all_by_condition($tableName, $condition = '1=1', $fields = '*', $order = '', $limit = '')
	{
		if($limit != '') {
			$limit = "LIMIT {$limit}";
		}

		if($order != '') {
			$order = "ORDER BY {$order}";
		}

		$sql = "SELECT {$fields} FROM `{$tableName}` WHERE {$condition} {$order} {$limit}";

		return $this->query($sql);
	}

	/**
	 * 插入 1条 或 多条 数据
	 * @param $tableName
	 * @param $data
	 * @return int
	 */
	public function insert_data($tableName, $data)
	{
	
        $sql = '';
		foreach($data as $field => $value) {
			$field = mysql_real_escape_string(trim($field), $this->conn);
			$value = mysql_real_escape_string(trim($value), $this->conn);
			if($sql) {
				$sql = $sql . ", ";
			}
			$sql = $sql . "`".$field."` = '".$value."'";
		}
		$sql = "INSERT INTO " .$tableName ." set " . $sql;

		mysql_query($sql, $this->conn);
		return mysql_affected_rows($this->conn);
	}

	/**
	 * 根据条件更新一条记录
	 * @param $tableName
	 * @param $data
	 * @param $condition
	 * @return int
	 */
	public function update_data($tableName, $data, $condition)
	{
		$tmp = array();
		foreach($data as $key => $value)
		{
			$value = $this->dateEscape($value);
			$tmp[] = "{$key}='{$value}'";
		}

		$set = implode(',', $tmp);
		$sql = "UPDATE `{$tableName}` SET {$set} WHERE {$condition}";
		mysql_query($sql, $this->conn);

		return mysql_affected_rows($this->conn);
	}

	/**
	 * 根据条件删除数据
	 * @param $tableName
	 * @param $condition
	 * @return int
	 */
	public function delete_data($tableName, $condition)
	{
		$sql = "DELETE FROM `{$tableName}` WHERE {$condition}";

		mysql_query($sql, $this->conn);

		return mysql_affected_rows($this->conn);
	}

	/**
	 * query 执行 SQL语句
	 * @param $sql
	 * @return array
	 */
	public function query($sql)
	{
		$query = mysql_query($sql, $this->conn);
		$tmp   = array();
		while(@$row = mysql_fetch_assoc($query))
		{
			$tmp[] = $row;
		}

		return $tmp;
	}

	/**
	 * 数据转义
	 * @param      $str
	 * @param bool $like
	 * @return array|mixed|string
	 */
	public function dateEscape($str, $like = false)
	{
		if(get_magic_quotes_gpc())
		{
			$str = stripslashes($str);
		}

		if(is_array($str))
		{
			foreach($str as $key => $val)
			{
				$str[$key] = $this->dateEscape($val, $like);
			}

			return $str;
		}

		if(function_exists('mysql_real_escape_string'))
		{
			$str = addslashes($str);
		}
		elseif(function_exists('mysql_escape_string'))
		{
			$str = mysql_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}

		if($like === true)
		{
			$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
		}

		return $str;
	}
}
