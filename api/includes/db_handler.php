<?php
class DB_HANDLER
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
	function get_cust_cols($query)
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
		$response['HTTP Status'] = $code;

		switch($code)
		{
			case 200:
				$response['Message'] = "OK";
				break;

			case 201:
				$response['Message'] = "Created";
				break;

			case 204:
				$response['Message'] = "No Content";
				break;

			case 304:
				$response['Message'] = "Not Modified";
				break;

			case 400:
				$response['Message'] = "Bad Request";
				break;

			case 401:
				$response['Message'] = "Unauthorized";
				break;

			case 403:
				$response['Message'] = "Forbidden";
				break;

			case 404:
				$response['Message'] = "Not Found";
				break;

			case 409:
				$response['Message'] = "Conflict";
				break;

			case 410:
				$response['Message'] = "Gone";
				break;

			case 500:
				$response['Message'] = "Internal Server Error";
				break;

			case 503:
				$response['Message'] = "Service Unavailable";
				break;

			default:
				return "Nothing";
				break;
		}

		if($result != "") {
			$response['Result'] = $result;
		}

		return json_encode($response, JSON_PRETTY_PRINT);
	}
}
?>