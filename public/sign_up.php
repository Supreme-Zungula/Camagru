<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
function clean_input($data)
{
	echo("clean input <br />");
	$data = trim($data);
	$data = stripslashes($data);
	$data = strip_tags($data);
	return $data;
}

function add_user_to_db($new_user, $db_name)
{
	require "../config/database.php";
	
	$firstName = $new_user->get_name();
	$lastName = $new_user->get_surname();
	$username = $new_user->get_username();
	$email = $new_user->get_email();
	$password = $new_user->get_password();

	try
	{
		$conn = new PDO("$DB_DSN;dbname=$db_name", $DB_USER, $DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stm = $conn->prepare("INSERT INTO users(firstname, lastname, username, email, `password`)
							VALUES(?, ?, ?, ?, ?)");
		$stm->execute([$firstName, $lastName, $username, $email, $password]);
		echo " user inserted\n";
		$conn = null;
	}
	catch(PDOException $ex)
	{
		echo("Your shit was not added " . $ex->getMessage());
	}
}

function valid_name($name)
{
	if (!preg_match("/^[a-zA-Z-]+/",$name))
	{
		echo("This field can only letters");
		return;
	}
}
function valid_username($username)
{
	if (preg_match("/\w+/" ,$username))
		return (true);
	else
		return (false);
}

function valid_email($email)
{
	if (filter_var($email, FILTER_VALIDATE_EMAIL))
		return true;
	else
		return false;
}

function is_taken($field, $value)
{
	require "../config/database.php";
	
	try
	{
		$conn = new PDO("$DB_DSN;dbname=$DB_NAME", $DB_USER, $DB_PASSWORD);
		$stmt = $conn->prepare("SELECT $field FROM users WHERE $field=?");
		$stmt->execute([$value]); 
		$user =$stmt->fetch();
		if ($user)
			return (1);
		else
			return (0);
	}
	catch(PDOException $ex)
	{
		echo("ERROR: connection failed" . $ex->getMessage());
	}
}

function validate_input()
{	
	require "userInfo.class.php";
	require "mail.php";

	$db_name = "Camagru_DB";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$firstName = strtolower(clean_input($_POST["firstname"]));
		if (!valid_name($firstName))
			echo ("Name can only have letters");
		$lastName = strtolower(clean_input($_POST["lastname"]));
		if (!valid_name($lastName))
			echo("This field can only have letters");
		$username = strtolower(clean_input($_POST["username"]));
		if (!valid_username($username))
			echo("Username can only have letters, number and underscores");
		$email = strtolower(clean_input($_POST["email"]));
		if (!valid_email($email))
			echo ("Invalid email address");
		$password = clean_input($_POST["password"]); 
		$confirmPassword = clean_input($_POST["confirmPassword"]);
		if (is_taken("email", $email))
		{
			echo ("Email has already been used.");
			return;
		}
		if (is_taken("username", $username))
		{
			echo("This username has been taken");
			return;
		}
		if ($password != $confirmPassword)
		{
			echo("Passwords do not match");
			return;
		}
		else
			{
				$password = hash("whirlpool", $password);
				$new_user = new  UserInfo($firstName, $lastName, $username,$email, $password);
			}
		add_user_to_db($new_user, $db_name);
		send_mail($email);
	}
}
validate_input();
?>