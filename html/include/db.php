<?php



class SQLBuilder
{

	public function multiple_parent_where($parent, $items)
	{
		$s = '';
		if (count($items) > 0) {
			$s .= '(';
			$counter = 0;
			foreach ($items as $item) {
				if ($counter > 0) $s .= ' OR ';
				$s .= $parent . "='" . $item . "'";
				$counter++;
			}
			$s .= ')';
		} else {
			$s .= "1";
		}
		return $s;
	}
}

class dbc extends SQLBuilder
{
	protected $conn = null;
	protected $debugMode = true;

	private $username = "root";
	private $password = "";
	private $dbname = "";
	private $server = "localhost";

	function __construct($config = null)
	{
		if (defined('DB_SERVER')) $this->server = DB_SERVER;
		if (defined('DB_USER')) $this->username = DB_USER;
		if (defined('DB_PASS')) $this->password = DB_PASS;
		if (defined('DB_NAME')) $this->dbname = DB_NAME;



		if ($config != null) {
			if (isset($config['server'])) $this->server = $config['server'];
			if (isset($config['username'])) $this->username = $config['username'];
			if (isset($config['password'])) $this->password = $config['password'];
			if (isset($config['dbname'])) $this->dbname = $config['dbname'];
		}
	}

	function Connect()
	{
		$server = $this->server;
		$username = $this->username;
		$password = $this->password;
		$dbname = $this->dbname;

		try {
			$this->conn = new mysqli(
				$server,
				$username,
				$password,
				$dbname
			);

			if ($this->conn->connect_errno) {
				return false;
				//printf("Connect failed: %s\n", $mysqli->connect_error);
			} else {
				$this->conn->query("SET NAMES 'UTF8'");
				return true;
			}
		} catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
			$this->conn = mysqli_connect($server, $username, $password, $dbname);
			$this->conn->query("SET NAMES 'UTF8'");
			return true;
		}
	}

	function SelectDB($dbname)
	{
		return mysqli_select_db($this->conn, $dbname);
	}

	function Close()
	{
		$this->conn->close();
		//mysql_close();
	}

	function __destruct()
	{
		//$this->Close();
	}

	function activatedDebugMode()
	{
		$this->debugMode = true;
	}

	function MultiQuery($sql)
	{
		mysqli_multi_query($this->conn, $sql);
	}

	function Query($sql)
	{
		if ($this->debugMode) { //$this->debugMode = true;
			$rst = mysqli_query($this->conn, $sql) or die(mysqli_error($this->conn) . "\r\n" . $sql . "\r\n");
			if ($this->conn->connect_errno) {
				printf("Connect failed: %s\n", $this->conn->connect_error);
			}
			return $rst;
		} else { //$this->debugMode = false;
			$rst = mysqli_query($this->conn, $sql);
			if ($this->conn->connect_errno) {
				printf("Connect failed: %s\n", $this->conn->connect_error);
			}
			return $rst;
		}
	}

	function Escape_String($data)
	{
		return mysqli_escape_string($this->conn, $data);
	}

	function Clean($data)
	{
		mysqli_free_result($data);
	}

	function Fetch($rst)
	{
		return mysqli_fetch_array($rst);
	}



	function Insert($table_name, $list_variable)
	{
		$sql = "INSERT INTO $table_name";
		$s_column = "(";
		$s_value = " VALUES(";
		$count = 0;
		foreach ($list_variable as $name => $value) {
			if ($count > 0) {
				$s_column .= ",";
				$s_value .= ",";
			}
			$s_column .= str_replace("#", "", $name);
			if (preg_match("/#/", $name)) {
				$s_value .= "$value";
			} else {
				$s_value .= "'$value'";
			}
			$count++;
		}
		$s_column .= ")";
		$s_value .= ")";
		$sql .= $s_column . $s_value;
		//echo $sql."\n";
		$this->Query($sql);
		if (mysqli_affected_rows($this->conn) > 0) {
			return true;
		} else {
			return false;
		}
	}

	function GetID()
	{
		return mysqli_insert_id($this->conn);
	}

	function Update($table_name, $list_variable, $condition = "1")
	{
		$sql = "UPDATE $table_name SET ";
		$count = 0;
		foreach ($list_variable as $name => $value) {
			if ($count > 0) $sql .= ",";

			$sql .= str_replace("#", "", $name);
			if (preg_match("/#/", $name)) {
				$sql .= "=$value";
			} else {
				$sql .= "='$value'";
			}
			$count++;
		}
		$sql .= " WHERE $condition";
		$this->Query($sql);
		if (mysqli_affected_rows($this->conn) > 0) {
			return true;
		} else {
			return false;
		}
	}

	function UpdateNotCheckAffected($table_name, $list_variable, $condition = "1")
	{
		$sql = "UPDATE $table_name SET ";
		$count = 0;
		foreach ($list_variable as $name => $value) {
			if ($count > 0) $sql .= ",";

			$sql .= str_replace("#", "", $name);
			if (preg_match("/#/", $name)) {
				$sql .= "=$value";
			} else {
				$sql .= "='$value'";
			}
			$count++;
		}
		$sql .= " WHERE $condition";

		if ($this->Query($sql)) {
			return true;
		} else {
			return false;
		}
	}

	function Delete($table, $condition = "1")
	{
		$sql = "DELETE FROM $table WHERE $condition";
		$this->Query($sql);
		if (mysqli_affected_rows($this->conn) > 0) {
			return true;
		} else {
			return false;
		}
	}

	function HasRecord($table, $condition = "1")
	{
		$sql = "SELECT * FROM $table WHERE $condition";

		$rst = mysqli_query($this->conn, $sql) or die(mysqli_error($this->conn) . "\r\n" . $sql . "\r\n");
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", $this->conn->connect_error);
			return false;
		} else if (mysqli_num_rows($rst) > 0) {
			return true;
		} else {
			return false;
		}
	}

	function GetRecord($table, $feild, $condition = "1")
	{
		$sql = "SELECT $feild FROM $table WHERE $condition";

		$rst = mysqli_query($this->conn, $sql) or die(mysqli_error($this->conn) . "\r\n" . $sql . "\r\n");
		if ($rst == false) {
			printf("Query failed: %s\n", mysqli_error($this->conn));
			return false;
		} else {
			if (mysqli_num_rows($rst) > 0) {
				return mysqli_fetch_array($rst);
			} else {
				return 0;
			}
		}
	}

	function Real_Escape_String($string)
	{
		return mysqli_real_escape_string($this->conn, $string);
	}

	function GetCount($table, $condition = "1")
	{
		$sql = "SELECT COUNT(*) AS NumberOfAll FROM $table WHERE $condition";

		$rst = mysqli_query($this->conn, $sql);
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", $this->conn->connect_error);
			return false;
		} else {
			if (mysqli_num_rows($rst) > 0) {
				while ($row = mysqli_fetch_array($rst)) {
					$count = $row['NumberOfAll'];
				}
				return $count;
			} else {
				return 0;
			}
		}
	}

	function GetSum($field, $table, $condition = "1")
	{
		$sql = "SELECT SUM($field) AS sum FROM $table WHERE $condition";

		$rst = mysqli_query($this->conn, $sql);
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", $this->conn->connect_error);
			return false;
		} else {
			if (mysqli_num_rows($rst) > 0) {
				while ($row = mysqli_fetch_array($rst)) {
					$sum = $row['sum'];
				}
				return ($sum != NULL ? $sum : 0);
			} else {
				return 0;
			}
		}
	}

	function Total($rst)
	{
		return mysqli_num_rows($rst);
	}

	function transaction_begin()
	{
		$this->Query("BEGIN");
	}

	function transaction_commit()
	{
		$this->Query("COMMIT");
	}

	function transaction_rollback()
	{
		$this->Query("ROLLBACK");
	}

	function QueryAndFetch($sql)
	{
		//**This Method look alike GetRecord() , you can use your SQL that unlike [SELECT FROM WHERE] such as LEFT JOIN, RIGHT JOIN etc.
		$rst = mysqli_query($this->conn, $sql);
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", $this->conn->connect_error);
			return false;
		} else {
			if (mysqli_num_rows($rst) > 0) {
				return mysqli_fetch_array($rst);
			} else {
				return 0;
			}
		}
	}
}
