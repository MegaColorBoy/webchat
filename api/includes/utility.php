<?php
CLASS UTILITY
{
	function __construct(){}

	//Bind result array
	function bind_result_array($stmt)
	{
		$meta = $stmt->result_metadata();
		$result = array();
		while($field = $meta->fetch_field())
		{
			$result[$field->name] = NULL;
			$params[] = &$result[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $params);
		return $result;
	}

	//Get copy of array references
	function getCopy($row)
	{
		return array_map(create_function('$a', 'return $a;'), $row);
	}

	//Random password generator
	function rand_pass_gen($length)
	{
		$chars = "abcdefghijklmnopqrstuvwzxyz\$_?-0123456789";
		$charArr = str_split($chars);
		$result = "";
		for($i=0; $i<$length; $i++)
		{
			//returns the index of the random char
			$rand_char = array_rand($charArr);
			//concatenate the char into new string
			$result .= "" . $charArr[$rand_char];
		}
		return $result;
	}

	//Prints error message and redirects to a webpage
	function print_msg($message, $redirect)
	{
		echo "<script type='text/javascript'>
				alert('".$message."');
				window.location.replace('".$redirect."');
			</script>";
	}

	//Create slug for URLs
	//Rewrite in .htaccess to display SEO URLs instead
	function gen_slug($url)
	{
		//prepare string with basic normalization
		$url = strtolower($url);
		$url = strip_tags($url);
		$url = stripslashes($url);
		$url = html_entity_decode($url);
		//Remove any quotes
		$url = str_replace('\"','',$url);
		//Replace non-alpha chars with '-'
		$match = '/[^a-z0-9]+/';
		$replace = '-';
		$url = preg_replace($match, $replace, $url);
		$url = trim($url, '-');
		return $url;
	}

	//Returns unique keywords from a string
	function gen_keywords($string)
	{
		$keywords = "";
		$string = trim(preg_replace('/\s+/', ' ', strtolower($string)));
		$tokens = explode(" ",$string);
		$tokens = array_unique($tokens);
		sort($tokens);
		foreach($tokens as $key=> $value)
		{
			$keywords .= $value." ";
		}
		return $keywords;
	}

    function shiftValuesToArray($arr, $keyParam)
    {
        $newArr = array();
        foreach($arr as $key => $value)
        {
            foreach($arr[$key] as $key2 => $value)
            {
            	if($key2 == $keyParam)
            	{
            		$newArr[] = $value;
            	}
            }
        }
        //Return as a string
        return $newArr;
    }

    function genRandNumString($length)
	{
		$token = "";
		$alphaNumStr = "0123456789";
		$maxLength = strlen($alphaNumStr);
		for($i=0;$i<$length;$i++)
		{
			$token .= $alphaNumStr[rand(0, $maxLength-1)];
		}
		return $token;
	}

	function genRandAlphaString($length)
	{
		$token = "";
		$alphaNumStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$maxLength = strlen($alphaNumStr);
		for($i=0;$i<$length;$i++)
		{
			$token .= $alphaNumStr[rand(0, $maxLength-1)];
		}
		return $token;
	}

	function genReferenceNumber()
	{
		return $this->genRandAlphaString(4).'-'.$this->genRandNumString(4).'-'.$this->genRandAlphaString(4);
	}
}
?>