<?php /**/ ?><?php

$db = new db('wp_noweapon_all', 'mysql.noweapon.org', 'noweaponorg', 'H3XX*-kX');

class db {

	public $thisDB;
	public $dbAddress;
	public $use;
	public $pass;

	public function __construct($thisDB,$dbAddress,$use,$pass) {
		$this->thisDB = $thisDB;
		$this->dbAddress = $dbAddress;
		$this->use = $use;
		$this->pass = $pass;
		$this->queryCount = 0;
		$this->connect();
	}
	public function __destruct() {
		if(is_resource(mysql_select_db($this->thisDB)))
			mysql_close();
	}
	public function connect() {
		mysql_connect($this->dbAddress,$this->use,$this->pass);
		mysql_query("SET NAMES UTF8");
		mysql_set_charset('utf8');
		mysql_select_db($this->thisDB) or die(mysql_error());
	}
	public function query($query) {
		$q = mysql_query($query) or die(mysql_error());
		$this->queryCount++;
		return $q;
	}
	public function o($q) {
		return $this->f_object($q);
	}
	public function a($q) {
		return $this->f_assoc($q);
	}
	public function f_object($query) {
		$query = mysql_fetch_object($query);
		return $query;
	}
	public function f_assoc($query) {
		$query = mysql_fetch_assoc($query);
		return $query;
	}
	public function close() {
		if(is_resource(mysql_select_db($this->thisDB)))
			mysql_close();
	}
	public function table_exists($table) {
		$tables = mysql_list_tables($this->thisDB);
		while(list($temp) = mysql_fetch_array($tables)) {
			if($temp == $table) {
				return TRUE;
			}
		}
		$this->queryCount++;
		return FALSE;
	}
	public function getOne($sql) {
		$q = $this->query($sql." LIMIT 1;");
		return $this->f_object($q);
	}
	public function getFieldType($table,$field) {
		$query = mysql_query("SELECT $field FROM $table LIMIT 1");
		$type = mysql_field_type($query,0);
		$this->queryCount++;
		return $type;
	}
	public function getFields($table) {
		$query = $this->query("SELECT * FROM $table LIMIT 1;");
		$result = $this->f_assoc($query);
		$fuckingCount = 0;
		foreach($result as $key => $value) {
			$fields[$fuckingCount] = $key;
			$fuckingCount++;
		}
		return $fields;
	}
	// public function update($table,$fields,$values,$id,$primaryKey='id') {
	// 	if(!is_array($fields))
	// 	$fields = array($fields);
	// 	if(!is_array($values))
	// 	$values = array($values);
	// 	foreach($fields as $k=>$v) {
	// 	$que[] = "`$fields[$k]` = '$values[$k]'";
	// 	}
	// 	$query = "UPDATE `$table` SET ".implode($que,', ')." WHERE `$primaryKey` = '$id'";
	// 	//echo "$query<br />\r\n";
	// 	$this->query($query);
	// 	return true;
	// }
	public function insert($table,$args) {
		if(!is_array($args))
			return 'error: $args passed to db::insert() is not an array';
		$query = "INSERT INTO `$table` (`".implode('`, `',array_keys($args))."`) VALUES ('".implode("', '",str_replace("'","''",array_values($args)))."');";
		// echo "$query\r\n";
		$this->query($query);
		return mysql_insert_id();
	}

}
