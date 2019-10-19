<?php
include_once('../db_handler.php');
include_once('../utility.php');

class User extends DB_HANDLER {

	private $conn;

	function __construct($conn) {
		parent::__construct($conn);
	}

	//User login session
	public function login($obj) {
		return false;
	}

	//User logout session
	public function logout() {
		return false;
	}

	//Create new user
	public function create_new_user($obj) {
		header('Content-Type: application/json');
		extract($obj);

		$query = "INSERT INTO users (username, password, email) VALUES (?,?,?)";
		$params = array("sss", $username, $password, $email);
		$result = $this->preparedStatement("add", $query, $params);
		
		if($result) {
			$rescode = $this->response_code(201);
		}
		else {
			$rescode = $this->response_code(400);
		}

		return $rescode;
	}

	public function manage_user($obj) {
		header('Content-Type: application/json');
		extract($obj);
	}

	//Delete user
	public function delete_user($obj) {
		return false;
	}

	//Check if the user exists
	public function is_user_exists($obj) {
		return false;
	}

	//Validate user credentials
	private function check_credentials($obj) {
		return false;
	}

	//Edit user information and status message
	public function manage_profile($obj) {
		return false;
	}

	//Online, not online or invisible
	public function isVisible($obj) {
		return false;
	}

}
?>