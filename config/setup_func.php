<?php
	
	function connect($server, $user, $password)
	{
		try 
		{
			$conn = new PDO($server, $user, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			echo "Connecttion successful <br />";
			return ($conn);
		}
		catch(PDOException $e)
		{
			echo "Could not connect" . "<br>" . $e->getMessage();
		}
	}
	
	function init_database($server, $user, $password, $dbname)
	{
		try
		{
			$conn = connect($server, $user, $password, $dbname);
			$conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
			echo("Database successfully created <br />");
			$conn = null;
		}
		catch(PDOException $ex)
		{
			echo("ERROR: failed to create database " . $ex->getMessage());
		}
	}

	function query_database($server, $user, $password ,$dbname, $query)
	{
		try
		{
			$conn = new PDO("$server;dbname=$dbname", $user, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->exec($query);
			$conn = null;
		}
		catch(PDOException $ex)
		{
			echo("ERROR: could not perform query " . $ex->getMessage());
		}
	}

?>