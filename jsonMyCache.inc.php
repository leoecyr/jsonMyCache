<?php

class jsonMyCache
{
	// begin public methods
	public function __construct($host,$user,$password,$database,$namespace)
	{
		$this->joc_table = 'jsonmycache_' . $namespace;

		$this->con = new mysqli($host,$user,$password,$database);
		if (mysqli_connect_errno())
		{echo "Failed to connect to MySQL: " . mysqli_connect_error();}
		$sql = "CREATE TABLE ".$this->joc_table." (
	    		okey VARCHAR(50) PRIMARY KEY,
	    		value TEXT,
	    		ivalue int,
	    		last_set DATETIME DEFAULT NULL
			);";
	
		if ($this->con->query($sql) === TRUE)
	    	{echo("Table .$this->joc_table. successfully created.<br/>\n");}
		//else
		//{echo "Failed to create ".$this->joc_table." table with error " . $this->con->error . "<Br/>";}
		//echo "Opened connection to the database.<br/>";
	}

	public function __destruct()
	{
		mysqli_close($this->con);
		//echo "Closed connection to the database.<br/>";
	}

	// begin public methods
	public function set($key,$value)
	{
		$dbready_value = $this->con->real_escape_string($value);

		$sql = "INSERT INTO `".$this->joc_table."` (`okey`,`value`,`last_set`)" .
	    		" VALUES ('$key', '$dbready_value', NOW())" .
			" ON DUPLICATE KEY UPDATE value='$dbready_value', last_set=NOW();";
		$result = $this->con->query($sql);
		//if ($result == TRUE)
		//{echo "New Record has id " . $this->con->insert_id;}
		//else
		//{echo "Error: " .$this->con->error . "<BR/>";}
	}

	public function get($key,$fresh = false)
	{

		// Check the cache unless we're asked for a fresh object
		if ($fresh == false)
		{
			$sql = "SELECT value FROM " . $this->joc_table .
				" WHERE okey='$key'" . 
				" AND `last_set` > TIMESTAMPADD(HOUR,-1,NOW());";
			$result = $this->con->query($sql);
		}

		if ($result == true)
		{
			$row = $result->fetch_assoc();
	    		//echo "Select returned " .  $result->num_rows . " rows";
	    		/* free result set */
	    		$result->close();
		
			//echo "Cache hit!  Returned " . $key . " from cache: " . var_dump($row['value']);
			return $row['value'];
		}
		else
		{return false;}
		//{echo "Errors: " .$this->con->error . "<Br/><br />";}
	}

	// Planned features
	public function inc($key)
	{
	}

	public function dec($key)
	{
	}

	public function delete($key)
	{
	}

}

?>
