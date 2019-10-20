<?php
// include_once('../db_handler.php');
// include_once('../utility.php');

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

	public function manage_users($obj) {
		header('Content-Type: application/json');
		$action = $obj['action'];

		switch($action) {
			case "create_user":
				$result = $this->create_user($obj['data']);
				break;

			case "get_user":
				$result = $this->get_user($obj['uid']);
				break;

			case "get_all_users":
				$result = $this->get_all_users();
				break;

			case "delete_user":
				$result = $this->delete_user($obj['uid']);
				break;

			case "login":
				break;

			case "logout":
				break;

			case "update_profile":
				$result = $this->update_profile($obj['uid'], $obj['data']);
				break;

			case "isVisible":
				$result = $this->isVisible($obj['uid'], $obj['val']);
				break;

			default:
				$result = NULL;
				break;
		}

		return $result;
	}

	//Create new user
	public function create_user($obj) {
		extract($obj);

		$is_exists = json_decode($this->is_user_exists($email), true);
		$http_status = $is_exists["http_status"];

		if($http_status != 200) {
			$query = "INSERT INTO users (username, password, email) VALUES (?,?,?)";
			$params = array("sss", $username, $password, $email);
			$result = $this->preparedStatement("add", $query, $params);
			
			if($result) {
				$rescode = $this->response_code(201);
			}
			else {
				$rescode = $this->response_code(400);
			}

		} else {
			$rescode = $this->response_code(304);
		}
		
		return $rescode;
	}

	//Get user by ID
	public function get_user($uid) {
		$query = "SELECT * FROM users WHERE uid = ?";
		$params = array("i", $uid);
		$result = $this->preparedStatement("get", $query, $params);

		if($result) {
			$rescode = $this->response_code(200, $result);
		} else {
			$rescode = $this->response_code(404);
		}

		return $rescode;
	}

	//Get all users
	public function get_all_users() {
		$query = "SELECT username, email FROM users ORDER BY created_at DESC";
		$result = $this->response_code(200, $this->custom_query($query));
		return $result;
	}

	//Delete user
	public function delete_user($uid) {
		$query = "DELETE FROM users WHERE uid = ?";
		$params = array("i", $uid);
		$result = $this->preparedStatement("edit/delete", $query, $params);

		if($result) {
			$rescode = $this->response_code(200);
		} else {
			$rescode = $this->response_code(400);
		}

		return $rescode;
	}

	//Check if the user exists
	private function is_user_exists($email) {
		$query = "SELECT uid FROM users WHERE email = ?";
		$params = array("s", $email);
		$result = $this->preparedStatement("check", $query, $params);

		if($result) {
			$rescode = $this->response_code(200);
		} else {
			$rescode = $this->response_code(204);
		}

		return $rescode;
	}

	//Validate user credentials
	private function check_credentials($obj) {
		return false;
	}

	//Edit user information and status message
	public function update_profile($uid, $obj) {
		extract($obj);
		$query = "UPDATE users SET username = ?, password = ?, email = ?,  status = ?, profile_pic = ?, isVisible = ? WHERE uid = ?";
		$params = array("sssssii", $username, $password, $email, $status, $profile_pic, $isVisible, $uid);
		$result = $this->preparedStatement("edit/delete", $query, $params);

		if($result) {
			$rescode = $this->response_code(200);
		} else {
			$rescode = $this->response_code(400);
		}

		return $rescode;
	}

	//Online, not online or invisible
	public function isVisible($uid, $val) {
		$query = "UPDATE users SET status = ? WHERE uid = ?";
		$params = array('ii', $val, $uid);
		$result = $this->preparedStatement("edit/delete", $query, $params);

		if($result) {
			$rescode = $this->response_code(200);
		} else {
			$rescode = $this->response_code(400);
		}

		return $rescode;
	}

}
?>