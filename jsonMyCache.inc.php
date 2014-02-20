<?php


class jsonMyCache
{
	// Constructor
	public function __construct($host,$user,$password,$database,$namespace)
	{
		$this->joc_table = 'jsonmycache_' . $namespace;

		$this->con = new mysqli($host,$user,$password,$database);

		// To show handle properties
		//var_dump($this->con);

		if (mysqli_connect_errno())
		{echo "Failed to connect to MySQL: " . mysqli_connect_error();}
		$sql = "CREATE TABLE ".$this->joc_table." (
	    		okey VARCHAR(50) PRIMARY KEY,
	    		value TEXT,
	    		ivalue int,
	    		etag TEXT,
	    		last_set DATETIME DEFAULT NULL
			);";
	
		if ($this->con->query($sql) === TRUE)
	    	{echo("Table .$this->joc_table. successfully created.<br/>\n");}
		//else
		//{echo "Failed to create ".$this->joc_table." table with error " . $this->con->error . "<Br/>";}
		//echo "Opened connection to the database.<br/>";
	}


	// Destructor
	public function __destruct()
	{
		mysqli_close($this->con);
		//echo "Closed connection to the database.<br/>";
	}


	// begin public methods
	public function set($key,$value,$etag="")
	{
		$dbready_value = $this->con->real_escape_string($value);

		$sql = "INSERT INTO `".$this->joc_table."` (`okey`,`value`,`etag`,`last_set`)" .
	    		" VALUES ('$key', '$dbready_value', '$etag', NOW())" .
			" ON DUPLICATE KEY UPDATE value='$dbready_value', last_set=NOW();";
		$result = $this->con->query($sql);
		//if ($result)
		//{echo "New Record has id " . $this->con->insert_id;}
		//else
		//{echo "Error: " .$this->con->error . "<BR/>";}
	}

	public function last_set($key)
	{

		$sql = "UPDATE `".$this->joc_table."` SET last_set=NOW() WHERE okey='$key';";
		$result = $this->con->query($sql);
		//if ($result)
		//{echo "New Record has id " . $this->con->insert_id;}
		//else
		//{echo "Error: " .$this->con->error . "<BR/>";}
	}


	public function get($key,$complete=false)
	{
		// Check the cache unless we're asked for a fresh object
		//if ($fresh == true)
		//{return false;}

		$sql = "SELECT * FROM " . $this->joc_table ." WHERE okey='$key';";
				//" AND `last_set` > TIMESTAMPADD(HOUR,-1,NOW());";
		$result = $this->con->query($sql);
		if ($result == true)
		{
			$row = $result->fetch_assoc();
	    		$result->close();

			//echo "Cache hit!  Returned " . $key . " from cache: " . var_dump($row['value']);

			if($complete == true)
			{return $row;}
			else
			{return $row['value'];}
		}
		else
		{return false;}
		//{echo "Errors: " .$this->con->error . "<Br/><br />";}
	}


	public function flush()
	{
		$sql = "DELETE FROM `".$this->joc_table."`;";
		$result = $this->con->query($sql);
		//echo $this->con->error;
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
