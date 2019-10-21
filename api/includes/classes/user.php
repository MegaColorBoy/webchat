<?php
class User extends DB_HANDLER {

	private $conn;

	function __construct($conn) {
		parent::__construct($conn);
	}

	//Wrapper function for user functionality
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
				$result = $this->user_login($obj);
				break;

			// case "logout":
			// 	$result = $this->user_logout($obj);
			// 	break;

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

	//Wrapper function for friend-related functionality
	public function manage_friends($obj) {
		header('Content-Type: application/json');
		$action = $obj['action'];

		switch($action) {
			case "send_friend_request":
				$result = $this->send_friend_request($obj['uid_a'], $obj['uid_b']);
				break;

			case "fetch_friend_requests":
				$result = $this->fetch_friend_requests($obj['uid']);
				break;

			case "fetch_friends":
				$result = $this->fetch_friends($obj['uid']);
				break;

			case "remove_friend_request":
				$result = $this->remove_friend_request($obj['uid_a'], $obj['uid_b']);
				break;

			case "add_friend":
				$result = $this->add_friend($obj['uid_a'], $obj['uid_b']);
				break;

			case "remove_friend":
				$result = $this->remove_friend($obj['uid_a'], $obj['uid_b']);
				break;

			default:
				break;
		}

		return $result;
	}

	//Add friend
	public function add_friend($uid_a, $uid_b) {
		//Check if user a is friends with user b or vice-versa
		$is_friend = json_decode($this->check_if_friends($uid_a, $uid_b), true)['http_status'];
		$is_request_sent = json_decode($this->check_friend_request_status($uid_a, $uid_b), true)['http_status'];

		if($is_friend != 200 && $is_request_sent == 200) {
			$query = "INSERT INTO friends (uid_a, uid_b) VALUES (?,?)";
			$params = array("ii", $uid_a, $uid_b);
			$result = $this->preparedStatement("add", $query, $params);

			if($result) {
				//Remove the friend request
				$remove_request = $this->remove_friend_request($uid_a, $uid_b);
				$rescode = $this->response_code(201);
			} else {
				$rescode = $this->response_code(400);
			}

		} else {
			$rescode = $this->response_code(304);
		}

		return $rescode;
	}

	//Check if user a is friends with user b or vice-versa
	private function check_if_friends($uid_a, $uid_b) {
		$query = "SELECT fr_id FROM friends WHERE (uid_a = ? AND uid_b = ?) OR (uid_b = ? AND uid_a = ?)";
		$params = array("iiii", $uid_a, $uid_b, $uid_a, $uid_b);
		$result = $this->preparedStatement("check", $query, $params);

		if($result) {
			$rescode = $this->response_code(200);
		} else {
			$rescode = $this->response_code(404);
		}

		return $rescode;
	}

	public function fetch_friends($uid) {
		$query = "
		SELECT t1.* FROM (
			SELECT 
				users.uid, 
				users.username, 
				users.email, 
				users.profile_pic, 
				users.status,
				users.created_at
			FROM 
				webchat_db.friends
			LEFT JOIN 
				webchat_db.users ON users.uid = friends.uid_a
			WHERE 
				friends.uid_b = ?
			
			UNION

			SELECT 
				users.uid, 
				users.username, 
				users.email, 
				users.profile_pic, 
				users.status,
				users.created_at
			FROM 
				webchat_db.friends
			LEFT JOIN 
				webchat_db.users ON users.uid = friends.uid_b
			WHERE friends.uid_a = ?
		) AS t1
		";
		$params = array("ii", $uid, $uid);
		$result = $this->preparedStatement("get", $query, $params);

		if($result) {
			$rescode = $this->response_code(200, $result);
		} else {
			$rescode = $this->response_code(204);
		}

		return $rescode;
	}

	//Fetch friend requests based on the current user's ID
	public function fetch_friend_requests($uid) {
		$query = "
		SELECT t1.* FROM (
			SELECT 
				users.uid, 
				users.username, 
				users.email, 
				users.profile_pic, 
				users.status,
				users.created_at
			FROM 
				webchat_db.friend_requests
			LEFT JOIN 
				webchat_db.users ON users.uid = friend_requests.uid_a
			WHERE 
				friend_requests.uid_b = ?
			
			UNION

			SELECT 
				users.uid, 
				users.username, 
				users.email, 
				users.profile_pic, 
				users.status,
				users.created_at
			FROM 
				webchat_db.friend_requests
			LEFT JOIN 
				webchat_db.users ON users.uid = friend_requests.uid_b
			WHERE friend_requests.uid_a = ?
		) AS t1
		";
		$params = array("ii", $uid, $uid);
		$result = $this->preparedStatement("get", $query, $params);

		if($result) {
			$rescode = $this->response_code(200, $result);
		} else {
			$rescode = $this->response_code(204);
		}

		return $rescode;
	}

	//Remove friend request
	public function remove_friend_request($uid_a, $uid_b) {
		$query = "DELETE FROM friend_requests WHERE (uid_a = ? AND uid_b = ?) OR (uid_b = ? AND uid_a = ?)";
		$params = array('iiii', $uid_a, $uid_b, $uid_a, $uid_b);
		$result = $this->preparedStatement("edit/delete", $query, $params);

		if($result) {
			$rescode = $this->response_code(200);
		} else {
			$rescode = $this->response_code(304);
		}

		return $rescode;
	}

	//Remove friend
	public function remove_friend($uid_a, $uid_b) {
		$query = "DELETE FROM friends WHERE (uid_a = ? AND uid_b = ?) OR (uid_b = ? AND uid_a = ?)";
		$params = array('iiii', $uid_a, $uid_b, $uid_a, $uid_b);
		$result = $this->preparedStatement("edit/delete", $query, $params);

		if($result) {
			$rescode = $this->response_code(200);
		} else {
			$rescode = $this->response_code(304);
		}

		return $rescode;
	}

	//User login session
	public function user_login($obj) {
		extract($obj);
		$data = $obj['data'];
		extract($data);

		//Check if the user exists
		$is_exists = json_decode($this->is_user_exists($email), true);
		$http_status_1 = $is_exists["http_status"];

		//Check if the user credentials are correct
		$is_valid = json_decode($this->check_credentials($data), true);
		$http_status_2 = $is_valid['http_status'];

		if($http_status_1 == 200) {
			if($http_status_2 != 200) {
				$rescode = $this->response_code(204);
			} else {
				$query = "SELECT * FROM users WHERE email = ? AND password = ?";
				$params = array("ss", $email, $password);
				$result = $this->preparedStatement("get", $query, $params);
				$rescode = $this->response_code(200, $result);
			}	
		} else {
			$rescode = $this->response_code(404);
		}
		
		return $rescode;
	}

	//User logout
	// public function user_logout($obj) {
		
	// }

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
		extract($obj);

		$query = "SELECT uid FROM users WHERE email = ? AND password = ?";
		$params = array("ss", $email, $password);
		$result = $this->preparedStatement("get", $query, $params);

		if($result) {
			$rescode = $this->response_code(200);
		} else {
			$rescode = $this->response_code(204);
		}

		return $rescode;		
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

	//Send friend request
	public function send_friend_request($uid_a, $uid_b) {
		//Check if friend request has been sent
		$is_request_sent = json_decode($this->check_friend_request_status($uid_a, $uid_b), true)['http_status'];

		//If a request is present, then display a conflict
		if($is_request_sent == 200) {
			$rescode = $this->response_code(409);
		} else { // Otherwise, send request
			$query = "INSERT INTO friend_requests (uid_a, uid_b) VALUES (?,?)";
			$params = array("ii", $uid_a, $uid_b);
			$result = $this->preparedStatement("add", $query, $params);

			if($result) {
				$rescode = $this->response_code(201);
			} else {
				$rescode = $this->response_code(400);
			}
		}

		return $rescode;
	}

	//Check if friend request status
	private function check_friend_request_status($uid_a, $uid_b) {
		$query = "SELECT * FROM friend_requests WHERE (uid_a = ? AND uid_b = ?) OR (uid_b = ? AND uid_a = ?)";
		$params = array("iiii", $uid_a, $uid_b, $uid_a, $uid_b);
		$result = $this->preparedStatement("check", $query, $params);

		if($result) {
			$rescode = $this->response_code(200);
		} else {
			$rescode = $this->response_code(404);
		}

		return $rescode;
	}

}
?>