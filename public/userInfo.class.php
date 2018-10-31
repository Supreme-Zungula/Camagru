<?php
	class	UserInfo
	{
		private		$name;
		private		$surname;
		private		$email;
		private		$username;
		private		$password;

		function __construct($name, $surname, $username, $email, $passwd)
		{
			$this->name = $name;
			$this->surname = $surname;
			$this->email = $email;
			$this->username = $username;
			$this->password = $passwd;
		}
		
		function set_name($name)
		{
			$this->name = $name;
		}

		function get_name()
		{
			return $this->name;
		}

		function set_surname($surname)
		{
			$this->surname = $surname;
		}
		function get_surname()
		{
			return ($this->surname);
		}
		function set_username($username)
		{
			$this->username = $username;
		}
		function get_username()
		{
			return ($this->username);
		}

		function set_email($email)
		{
			$this->email = $email;
		}

		function get_email()
		{
			return ($this->email);
		}

		function set_password($passwd)
		{
			$this->password = $passwd;
		}		
		
		function get_password()
		{
			return ($this->password);
		}
	}
?>