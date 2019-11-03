<?php
// Harubi Test Helper
// By Abdullah Daud
// 29 October 2019

$test_auto_num = 0;
$tests_expected = 0;

function echo_br($str = '')
{
	echo $str, '<br/>';
}

function print_pre($arr)
{
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

function failed($msg)
{
	return "<font color='red'>Failed:</font> $msg<br/>";
}

function success($msg)
{
	echo "<font color='green'>Success:</font> $msg<br/>";
}

function notice($msg)
{
	echo "<font color='orange'>Notice:</font> $msg<br/>";
}

function testing($line = 0, $title, $msg = '')
{
	global $test_auto_num;
	++$test_auto_num;
	
	echo "<br/><font color='blue'>Testing #$test_auto_num</font> [line $line] - <strong>$title</strong> $msg<br/>";
}

function test_total()
{
	global $test_auto_num;
	global $tests_expected;
	
	if ($test_auto_num <= 1)
		echo "<br/>Total test: <font color='blue'><strong>$test_auto_num</strong></font> of $tests_expected";
	else
		echo "<br/>Total tests: <font color='blue'><strong>$test_auto_num</strong></font> of $tests_expected";
}

function tests_expected($count)
{
	global $test_auto_num;
	global $tests_expected;

	$test_auto_num = 0;
	$tests_expected = $count;
}

function as_array($json)
{
	return json_decode($json, TRUE);
}

function with_status($response, $matching)
{
	if (!is_array($response) || !is_array($matching))
		return FALSE;
		
	if (!isset($response['status']) || !isset($matching['status']))
		return FALSE;
		
	if ($response['status'] == $matching['status'])
	{
		if (!isset($response['error_code']))
			return TRUE;
			
		if (isset($matching['error_code']) && $matching['error_code'] == $response['error_code'])
			return TRUE;
	}	
		
	return FALSE;		
}

function with_results($response, $matching)
{
	if (!is_array($response) || count($response) <= 0 || !isset($response['results']))
		return TRUE;
		
	if (!is_array($matching) || count($matching) <= 0)
		return TRUE;
	
	$results = $response['results'];
	
	foreach ($matching as $name => $val)
	{
		if (!isset($results[$name]) || $results[$name] != $val)
			return FALSE;
	}	
		
	return TRUE;		
}

function msg($msg, $ctrl)
{
	if (!is_array($ctrl) || count($ctrl) <= 0)
		return $msg;
		
	foreach ($ctrl as $name => $val)
		$msg = str_replace("%$name%", $val, $msg);
	
	return $msg;
}

/**
* Invoke a basic harubi test case.
* A parameterized request to a test module will be invoked,
* and the response will be gauged with the expected results.
* Messages will be echoed.
* Test $case arguments:
* [
*   'line' => 'line number', // use __LINE__
*   'module' => 'php module file name',
*   'model' => 'model name',
*   'action' => 'action name',
*   'controller' => [
*     ... controller arguments
*   ],
*   'expectation' => [
*      'status' => 'status code',
*      'error_code' => 'error code',
*      'results' => 'results'
*   ],
*   'comment' => 'test comment',
*   'messages' => [
*     'starting' => 'starting message prior request',
*     'success' => 'success message',
*     'failed' => 'failed message'
*   ]
* ]
*/
function test($case)
{
	if (!is_array($case) || count($case) <= 0)
		die("No test case defined");
		
	if (!isset($case['module']))
		die("Test case: no module defined");
		
	if (!isset($case['model']))
		die("Test case: no model defined");
		
	if (!isset($case['action']))
		die("Test case: no action defined");
			
	if (!isset($case['expectation']))
		die("Test case: no expectation defined");

	$exp = $case['expectation'];
	
	if (!isset($exp['status']))
		die("Test case: no status expection defined");
	
	if (isset($exp['results']))
		$exp_results = $exp['results'];
	else
		$exp_results = [];
			
	$module = $case['module'];
	$model  = $case['model'];
	$action = $case['action'];

	testing($case['line'], "$model::$action", $case['comment']);

	if (isset($case['controller']))
		$ctrl = $case['controller'];
	else
		$ctrl = [];
	
	if (isset($case['messages']))
	{
		$msgs = $case['messages'];
		
		if (isset($msgs['starting']))
			$starting_msg = $msgs['starting'];
		
		if (isset($msgs['success']))
			$success_msg = $msgs['success'];
		
		if (isset($msgs['failed']))
			$failed_msg = $msgs['failed'];
	}
	
	if (isset($starting_msg))
		echo_br(msg($starting_msg, $ctrl));
		
	$ctrl_cnt = count($ctrl);
	 	
	if ($ctrl_cnt > 0)
	{
		if ($ctrl_cnt > 1)
			echo_br("Arguments:");
		else	
			echo_br("Argument:");
		
		print_pre($ctrl);
	}
	
	$response = as_array(request($module, $model, $action, $ctrl));
	
	echo_br("Response:");
	print_pre($response);

	if (!with_status($response, $exp) || !with_results($response, $exp_results))
	{
		echo_br("Expecting:");
		print_pre($exp);
		
		if (isset($failed_msg))
			die(failed(msg($failed_msg, $ctrl)));
		else
			die(failed("$model::$action"));
	}
	
	global $harubi_last_preset_intervened;
	
	if ($harubi_last_preset_intervened !== FALSE)
		notice("preset <strong>$harubi_last_preset_intervened</strong> intervened");

	global $harubi_last_toll_intervened;
	
	if ($harubi_last_toll_intervened !== FALSE)
		notice("toll <strong>$harubi_last_toll_intervened</strong> intervened");

	if (isset($success_msg))
		success(msg($success_msg, $ctrl));
	else
		success("$model::$action");
}

/**
* Check that the database defined in the settings exists.
* Return the database name.
*/
function check_db()
{
	global $harubi_mysql_settings;
	
	$dbn = $harubi_mysql_settings['database'];
	$db = connect_db();

	if ($db === FALSE)
		die(failed("connecting to the database <strong>$dbn</strong>."));

	echo_br("Database exists: <strong>$dbn</strong>");
	mysqli_close($db);
	
	return $dbn;
}

/**
* Create a set of tables if not exists, or empty the tables and reset auto-increment. 
*/
function prepare_db($name, $sql)
{
	global $harubi_mysql_settings;
	
	$dbn = $harubi_mysql_settings['database'];
	$db = connect_db();

	if ($db === FALSE)
		die(failed("connecting to the database <strong>$dbn</strong>."));

	echo_br("Uninstalling <strong>$name</strong>...");
	$query = file_get_contents($sql . ".uninstall.sql");
	print_pre($query);
	
	if (mysqli_multi_query($db, $query))
		while (mysqli_next_result($db)); // flush		

	echo_br("Installing <strong>$name</strong>...");
	$query = file_get_contents($sql . ".install.sql");	
	print_pre($query);
	
	if (mysqli_multi_query($db, $query))
		while (mysqli_next_result($db)); // flush		

	mysqli_close($db);
}

?>
