<?php
abstract class DB_HANDLER
{
	//Connection
	private $conn;

	//Constructor
	function __construct($conn)
	{
		$this->conn = $conn;
	}

	//Reference values
	function refValues($arr)
	{
		if(strnatcmp(phpversion(),'5.3') >= 0)
		{
			$refs = array();
			foreach($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;
		}
		return $arr;
	}

	//Prepared statement
	function preparedStatement($type, $query, $params)
	{
		//Add
		if($type == "add")
		{
			$stmt = $this->conn->prepare($query);
			call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));
			$result = $stmt->execute();
			$stmt->close();
			return $result;
		}

		//Edit/Delete
		if($type == "edit/delete")
		{
			$stmt = $this->conn->prepare($query);
			call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));
			$rc = $stmt->execute();
			if ( false===$rc ) {
			  die('execute() failed: ' . htmlspecialchars($stmt->error));
			}
			$num_affected_rows = $stmt->affected_rows;
			$stmt->close();
			return $num_affected_rows > 0;
		}

		//Check
		if($type == "check")
		{
			$stmt = $this->conn->prepare($query);
			call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));
			$rc = $stmt->execute();
			if ( false===$rc ) {
			  die('execute() failed: ' . htmlspecialchars($stmt->error));
			}
			$stmt->store_result();
			$num_rows = $stmt->num_rows;
			$stmt->close();
			return $num_rows > 0;
		}

		//Get
		if($type == "get")
		{
			include_once('utility.php');
			$utility_handler = new UTILITY();

			$stmt = $this->conn->prepare($query);
			call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));

			if($stmt->execute())
			{
				$arr = array();
				$row = $utility_handler->bind_result_array($stmt);

				if(!$stmt->error)
				{
					$counter = 0;
					while($stmt->fetch())
					{
						$arr[$counter] = $utility_handler->getCopy($row);
						$counter++;
					}
				}
				$stmt->close();
				return $arr;
			}
			else
			{
				return NULL;
			}
		}
	}

	//Get Custom columns
	function custom_query($query)
	{
		include_once('utility.php');
		$utility_handler = new UTILITY();

		$arr = array();
		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		$row = $utility_handler->bind_result_array($stmt);

		if(!$stmt->error)
		{
			$counter = 0;
			while($stmt->fetch())
			{
				$arr[$counter] = $utility_handler->getCopy($row);
				$counter++;
			}
		}
		$stmt->close();
		return $arr;
	}

	//HTTP Error messages
	public function response_code($code, $result="")
	{
		$response['http_status'] = $code;

		switch($code)
		{
			case 200:
				$response['message'] = "OK";
				break;

			case 201:
				$response['message'] = "Created";
				break;

			case 204:
				$response['message'] = "No Content";
				break;

			case 304:
				$response['message'] = "Not Modified";
				break;

			case 400:
				$response['message'] = "Bad Request";
				break;

			case 401:
				$response['message'] = "Unauthorized";
				break;

			case 403:
				$response['message'] = "Forbidden";
				break;

			case 404:
				$response['message'] = "Not Found";
				break;

			case 409:
				$response['message'] = "Conflict";
				break;

			case 410:
				$response['message'] = "Gone";
				break;

			case 500:
				$response['message'] = "Internal Server Error";
				break;

			case 503:
				$response['message'] = "Service Unavailable";
				break;

			default:
				return "Nothing";
				break;
		}

		if($result != "") {
			$response['result'] = $result;
		}

		return json_encode($response, JSON_PRETTY_PRINT);
	}
}
?>