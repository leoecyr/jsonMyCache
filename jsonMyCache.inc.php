<?php

class ConnectionFailed extends Exception { }


class jsonMyCache
{
	// Constructor
	public function __construct($host,$user,$password,$database,$namespace)
	{
		$this->debug = false;

		if($namespace == "")
		{throw new ConnectionFailed("No namespace provided.  Connection failed.");}
		//{return false;}

		$this->namespace = $namespace;
		$this->joc_table = 'jsonmycache_' . $namespace;

		$this->con = new mysqli($host,$user,$password,$database);
		if (mysqli_connect_errno())
		{error_log("ERROR: Failed to connect to MySQL: " . mysqli_connect_error());}

		$sql = "CREATE TABLE IF NOT EXISTS ".$this->joc_table." (
	    		okey VARCHAR(50) PRIMARY KEY,
	    		value LONGTEXT,
	    		etag TEXT,
	    		last_set DATETIME DEFAULT NULL
			);";

		if ($this->debug)
		{error_log("DEBUG: jsonMyCache running SQL: " . $sql);}

		$result = $this->con->query($sql);
		if ( ($result == TRUE) && $this->debug)
	    	{error_log("DEBUG: Table .$this->joc_table. exists or was successfully created.");}

		if($this->con->error)
		{error_log("ERROR: Failed to create ".$this->joc_table." table with error " . $this->con->error );}

		if ($this->debug)
		{error_log("DEBUG: Opened connection to the database using namespace: " . $namespace);}
	}


	// Destructor
	public function __destruct()
	{
		mysqli_close($this->con);
		if($this->debug)
		{error_log("Closed connection to the database using namespace: " . $this->namespace);}
	}


	// begin public methods
	public function set($key,$value,$etag="")
	{
		$dbready_value = $this->con->real_escape_string($value);

		$sql = "INSERT INTO `".$this->joc_table."` (`okey`,`value`,`etag`,`last_set`)" .
	    		" VALUES ('$key', '$dbready_value', '$etag', NOW())" .
			" ON DUPLICATE KEY UPDATE value='$dbready_value', last_set=NOW();";
		$result = $this->con->query($sql);
		if ($result && $this->debug)
		{error_log("DEBUG: New Record has id " . $this->con->insert_id);}

		if($this->con->error)
		{error_log("ERROR: " .$this->con->error);}
	}

	public function last_set($key)
	{

		$sql = "UPDATE `".$this->joc_table."` SET last_set=NOW() WHERE okey='$key';";
		$result = $this->con->query($sql);
		if ($result && $this->debug)
		{error_log("DEBUG: New Record has id " . $this->con->insert_id);}

		if($this->con->error)
		{error_log("ERROR: " .$this->con->error);}
	}


	public function get($key,$complete=false)
	{
		// Check the cache unless we're asked for a fresh object
		//if ($fresh == true)
		//{return false;}

		$sql = "SELECT * FROM " . $this->joc_table ." WHERE okey='$key';";
				//" AND `last_set` > TIMESTAMPADD(HOUR,-1,NOW());";

		if ($this->debug)
		{error_log("DEBUG: jsonMyCache::get() running SQL: " . $sql);}

		$result = $this->con->query($sql);
		if($this->con->error)
		{error_log("ERROR: " .$this->con->error);}

		if ($result == true)
		{
			$row = $result->fetch_assoc();
	    		$result->close();

			if($this->debug)
			{"Cache hit!  Returned " . $key . " from cache: " . var_dump($row['value']);}

			if($complete == true)
			{return $row;}
			else
			{return $row['value'];}
		}
		else
		{return false;}
	}


	public function flush()
	{
		$sql = "DELETE FROM `".$this->joc_table."`;";
		$result = $this->con->query($sql);
		if($this->con->error)
		{error_log("Errors: " .$this->con->error);}
	}


	public function delete($key)
	{
		$sql = "DELETE FROM `".$this->joc_table."` WHERE `okey`=".$key.";";
		$result = $this->con->query($sql);
		if($this->con->error)
		{
			error_log("Ran: " . $sql);
			error_log("Errors: " .$this->con->error);
		}
	}
}

?>
