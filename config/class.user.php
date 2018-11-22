<?php
require_once("database.php");

class USER
{	

	private $conn;
	
	/* Connect to the database */
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
    }
	
	/* Runs a given query on the database */
	public function run_query($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}
	
	/* Registers a user to the database */
	public function register($firstname, $lastname, $username, $email, $password)
	{
		try
		{
			$new_password = password_hash($password, PASSWORD_DEFAULT);
			$stmt = $this->conn->prepare("INSERT INTO users(firstname, lastname, username, email, `password`) 
		            VALUES(:firstname, :lastname, :username, :email, :password)");
													  
			$stmt->bindParam(":firstname", $firstname, PDO::PARAM_STR);
			$stmt->bindParam(":lastname", $lastname, PDO::PARAM_STR);									  
			$stmt->bindParam(":username", $username, PDO::PARAM_STR);
			$stmt->bindParam(":email", $email, PDO::PARAM_STR);
			$stmt->bindParam(":password", $new_password, PDO::PARAM_STR);

			$stmt->execute();	
			
			return $stmt;	
		}
		catch(PDOException $ex)
		{
			echo "ERROR: " . $ex->getMessage();
		}				
	}
	
	/* Tries to login a user to the site using the given information */
	public function login($username, $password)
	{
		try
		{
			$stmt = $this->conn->prepare("SELECT `user_id`, firstname, lastname, username, email, `password` FROM users WHERE username=:username");
			$stmt->execute(array(':username'=>$username));
			if($stmt->rowCount() == 1)
			{
				$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
				if (password_verify($password, $userRow['password']))
				{
					$_SESSION['user_session'] = $userRow['username'];
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		catch(PDOException $e)
		{
			echo "ERROR:" . $e->getMessage();
		}
	}
	
	/* confirms user's email address */
	public function verify($username, $email)
	{
		try{
			$stmt = $this->conn->prepare("UPDATE users SET confirmed = 'Yes' WHERE username=:username");
			$stmt->execute(array(':username'=>$username));	
			return true;
		}
		catch(PDOException $ex){
			return false;
		}
	}
	/* Changes user's old password to the new password give */
	public function change_password($username, $new_password)
	{
		try{
			$password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $query = $this->run_query("UPDATE users
                    SET `password`=:new_password
					WHERE username=:username");
            $query->bindParam(":new_password", $password_hash, PDO::PARAM_STR);
            $query->bindParam(":username", $username, PDO::PARAM_STR);
            $query->execute();
			return true;
		}
		catch(PDOException $ex){
			return false;
		}
	}

	/* Updates user's details with the ones give */
	public function update_details($old_uname, $firstname, $lastname, $username, $email, $notifications)
	{
		try{
			/* update users table */
			$query = $this->run_query("UPDATE users
					SET firstname =:firstname, lastname=:lastname, username=:username,
						email=:email, notifications=:notifications
					WHERE username=:old_uname");
			$query->bindParam(":old_uname", $old_uname, PDO::PARAM_STR);
			$query->bindParam(":firstname", $firstname, PDO::PARAM_STR);
			$query->bindParam(":lastname", $lastname, PDO::PARAM_STR);
			$query->bindParam(":username", $username, PDO::PARAM_STR);
			$query->bindParam(":email", $email, PDO::PARAM_STR);
			$query->bindParam(":notifications", $notifications, PDO::PARAM_STR);
			$query->execute();

			/* update media table */
			$query = $this->run_query("UPDATE media SET username=:username
					WHERE username=:old_uname");
			$query->bindParam(":old_uname", $old_uname, PDO::PARAM_STR);
			$query->bindParam(":username", $username, PDO::PARAM_STR);
			$query->execute();

			/* update comments table */
			$query = $this->run_query("UPDATE comments SET username=:uname WHERE username=:old_uname");
			$query->bindParam(":old_uname", $old_uname, PDO::PARAM_STR);
			$query->bindParam(":uname", $username, PDO::PARAM_STR);
			$query->execute();

			/* update likes table */
			$query = $this->run_query("UPDATE likes SET username=:uname WHERE username=:old_uname");
			$query->bindParam(":uname", $username, PDO::PARAM_STR);
			$query->bindParam(":old_uname", $old_uname, PDO::PARAM_STR);
			$query->execute();
			return true;
		}
		catch(PDOException $ex){
			echo "ERROR: " . $ex->getMessage();
			return false;
		}
	}

	/* Upload a give file to the database */
	public function upload_file($username, $filename)
	{
		try{
			 $query = $this->run_query("INSERT INTO media (username, media_name)
					VALUES(:username, :media_name)");
			$query->bindParam(":username", $username, PDO::PARAM_STR);
			$query->bindParam(":media_name", $filename, PDO::PARAM_STR);
			$query->execute();
			return true;
		}
		catch(PDOException $ex){
			return false;
		}
	}

	/* Deletes a file from the database for the given username*/
	public function delete_file($file_id, $filename, $username)
	{
		try{
			$query = $this->run_query("DELETE FROM media WHERE media_name=:fname AND username=:username AND media_id=:file_id");
			$query->bindParam(":fname", $filename, PDO::PARAM_STR);
			$query->bindParam(":username", $username, PDO::PARAM_STR);
			$query->bindParam(":file_id", $file_id, PDO::PARAM_STR);
			$query->execute();
			
			$query = $this->run_query("DELETE FROM comments WHERE media_id=:file_id");
			$query->bindParam(":file_id", $file_id, PDO::PARAM_STR);
			$query->execute();

			$query = $this->run_query("DELETE FROM likes WHERE media_id=:file_id");
			$query->bindParam(":file_id", $file_id, PDO::PARAM_STR);
			$query->execute();
			return true;
		}
		catch(PDOException $ex){
			return false;
		}
	}
	public function is_registered($username)
	{	
		try
		{
			$stmt = $this->conn->prepare("SELECT 1 FROM users WHERE username=:username");
			$stmt->bindParam(":username", $username, PDO::PARAM_STR);
			$stmt->execute();
			if ($stmt->rowCount() >= 1)
			{
				return true;
			}
			else{
				return (false);
			}
		}
		catch(PDOException $ex)
		{
			echo("ERROR: " . $ex->getMessage());
		}
	}

	/* checks if user's email has been varified */
	public function is_verified($username)
	{
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE username=:username");
		$stmt->bindParam(":username", $username, PDO::PARAM_STR);
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount() == 1)
		{
			if ($user['confirmed'] == "Yes")
				 return (true);
			else
				return (false);
		}
	}

	/* Check if the user is logged in */
	public function is_loggedin()
	{
		if(isset($_SESSION['user_session']))
		{
			return true;
		}
		else
			return false;
	}
	
	/* redirect to the given url */
	public function redirect($url)
	{
		header("location: $url");
	}
	
	/* logs out the user in the current session */
	public function logout()
	{
		unset($_SESSION['user_session']);
		session_destroy();
		return true;
	}
}
?>