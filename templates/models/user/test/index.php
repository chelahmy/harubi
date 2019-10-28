<?php
// User Model Test
// By Abdullah Daud
// 27 October 2019

// NOTE: Harubi application testing framework is a work-in-progress.

// Use the PHP development server for testing:
// $ php -S localhost:8000

require '../../../../harubi/harubi.php'; 

harubi();

$test_auto_num = 0;
$tests_expected = 7;

function echo_br($str)
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

function testing($line = 0, $title, $msg = '')
{
	global $test_auto_num;
	++$test_auto_num;
	
	echo "<br/><font color='blue'>Testing #$test_auto_num</font> [line $line] - <strong>$title</strong> $msg<br/>";
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
	
	$response = as_array(request($module, $model, $action, $ctrl));
	print_pre($response);

	if (!with_status($response, $exp) || !with_results($response, $exp_results))
	{
		if (isset($failed_msg))
			die(failed(msg($failed_msg, $ctrl)));
		else
			die(failed("$model::$action"));
	}
	
	global $harubi_last_preset_invoked;
	
	if ($harubi_last_preset_invoked !== FALSE)
		notice("preset <strong>$harubi_last_preset_invoked</strong> was applied");

	global $harubi_last_toll_invoked;
	
	if ($harubi_last_toll_invoked !== FALSE)
		notice("toll <strong>$harubi_last_toll_invoked</strong> was applied");

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
* Create a table if not exists, or empty the table and reset auto-increment. 
*/
function prepare_table($dbname, $tblname, $tblsql)
{
	if (!table_exists($tblname))
	{
		$db = connect_db();

		if ($db === FALSE)
			die(failed("connecting to the database <strong>$dbname</strong>."));

		echo_br("Creating table <strong>$tblname</strong>...");
		$sql = file_get_contents($tblsql);	
		mysqli_multi_query($db, $sql);
		mysqli_close($db);
	
		if (!table_exists('user'))
			die(failed("creating table <strong>$tblname</strong>."));
		
		echo_br("Table <strong>$tblname</strong> created.");
	}
	else
	{
		delete($tblname, '`id` > 0'); // delete all records

		$db = connect_db();

		if ($db === FALSE)
			die(failed("connecting to the database <strong>$dbname</strong>."));
		
		mysqli_query($db, "ALTER TABLE `$tblname` AUTO_INCREMENT = 1");	
		mysqli_close($db);
	}

	echo_br("Table exists: <strong>$tblname</strong>");
}

//========================================================
echo "<h1>Testing User Model</h1>";

$dbname = check_db();
prepare_table($dbname, 'user', '../user.sql');

session_start();

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'signup';

testing(__LINE__, "$model::$action", "first user/super-user");

$user		= 'admin';
$password	= 'secret';
$email		= 'admin@example.com';

echo_br("Signing-up super-user <strong>$user</strong>...");
$_SESSION['last_reg'] = 0; // bypass sign-up delay
$results = as_array(request($module, $model, $action, ['name' => $user, 'password' => $password, 'email' => $email]));
print_pre($results);

if (!with_status($results, ['status' => 1]))
	die(failed("signing-up super-user <strong>$user</strong>"));

success("signed-up super-user <strong>$user</strong>");

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'signup';

testing(__LINE__, "$model::$action");

$user		= 'jamal';
$password	= 'vision';
$email		= 'jamal@example.com';

echo_br("Signing-up new user <strong>$user</strong>...");
$_SESSION['last_reg'] = 0; // bypass sign-up delay
$results = as_array(request($module, $model, $action, ['name' => $user, 'password' => $password, 'email' => $email]));
print_pre($results);

if (!with_status($results, ['status' => 1]))
	die(failed("signing-up new user <strong>$user</strong>"));

success("signed-up new user <strong>$user</strong>");

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'signin';

testing(__LINE__, "$model::$action", "non-user");

$user		= 'ali';
$password	= 'wisdom';

echo_br("Signing-in non-user <strong>$user</strong>...");
$results = as_array(request($module, $model, $action, ['name' => $user, 'password' => $password]));
print_pre($results);

if (!with_status($results, ['status' => 0, 'error_code' => 1]))
	die(failed("allowing non-user <strong>$user</strong> to sign-in"));

success("signing-in non-user <strong>$user</strong> failed as expected");


//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signin',
	'controller' => [
		'user' => 'abu',
		'password' => 'morning'
	],
	'expectation' => [
		'status' => 0,
		'error_code' => 1
	],
	'comment' => 'non-user',
	'messages' => [
		'starting' => 'Signing-in non-user <strong>%user%</strong>...',
		'success' => 'signing-in non-user <strong>%user%</strong> failed as expected',
		'failes' => 'allowing non-user <strong>%user%</strong> to sign-in'
	]
]);

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'read_own';

testing(__LINE__, "$model::$action", "without signing-in");

$user		= 'jamal';

echo_br("Reading user <strong>$user</strong> own record without signing-in...");
$results = as_array(request($module, $model, $action));
print_pre($results);

if (!with_status($results, ['status' => 0, 'error_code' => 1000]))
	die(failed("allowing to read user <strong>$user</strong> own record without signing-in"));

if ($harubi_last_preset_invoked !== FALSE)
	notice("preset <strong>$harubi_last_preset_invoked</strong> was applied");

success("reading user <strong>$user</strong> own record without signing-in failed as expected");

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'update_own';

testing(__LINE__, "$model::$action", "without signing-in");

$user		= 'jamal';
$password	= 'vision1';
$email		= 'jamal_one@example.com';

echo_br("Updating user <strong>$user</strong> own record without signing-in...");
$results = as_array(request($module, $model, $action, ['password' => $password, 'email' => $email]));
print_pre($results);

if (!with_status($results, ['status' => 0, 'error_code' => 1000]))
	die(failed("allowing to update user <strong>$user</strong> own record without signing-in"));

if ($harubi_last_preset_invoked !== FALSE)
	notice("preset <strong>$harubi_last_preset_invoked</strong> was applied");

success("updating user <strong>$user</strong> own record without signing-in failed as expected");

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'read';

testing(__LINE__, "$model::$action", "without signing-in");

$user		= 'jamal';

echo_br("Reading user <strong>$user</strong> record without signing-in...");
$results = as_array(request($module, $model, $action, ['user' => $user]));
print_pre($results);

if (!with_status($results, ['status' => 0, 'error_code' => 1000]))
	die(failed("allowing to read user <strong>$user</strong> record without signing-in"));

if ($harubi_last_preset_invoked !== FALSE)
	notice("preset <strong>$harubi_last_preset_invoked</strong> was applied");

success("reading user <strong>$user</strong> record without signing-in failed as expected");

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'update';

testing(__LINE__, "$model::$action", "without signing-in");

$user		= 'jamal';
$password	= 'vision1';
$email		= 'jamal_one@example.com';

echo_br("Updating user <strong>$user</strong> record without signing-in...");
$results = as_array(request($module, $model, $action, ['user' => $user, 'password' => $password, 'email' => $email]));
print_pre($results);

if (!with_status($results, ['status' => 0, 'error_code' => 1000]))
	die(failed("allowing to update user <strong>$user</strong> record without signing-in"));

if ($harubi_last_preset_invoked !== FALSE)
	notice("preset <strong>$harubi_last_preset_invoked</strong> was applied");

success("updating user <strong>$user</strong> record without signing-in failed as expected");

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'delete';

testing(__LINE__, "$model::$action", "without signing-in");

$user		= 'jamal';

echo_br("Deleting user <strong>$user</strong> record without signing-in...");
$results = as_array(request($module, $model, $action, ['user' => $user]));
print_pre($results);

if (!with_status($results, ['status' => 0, 'error_code' => 1000]))
	die(failed("allowing to delete user <strong>$user</strong> record without signing-in"));

if ($harubi_last_preset_invoked !== FALSE)
	notice("preset <strong>$harubi_last_preset_invoked</strong> was applied");

success("deleting user <strong>$user</strong> record without signing-in failed as expected");

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'signin';

testing(__LINE__, "$model::$action", "existing user");

$user		= 'jamal';
$password	= 'vision';

echo_br("Signing-in existing user <strong>$user</strong>...");
$results = as_array(request($module, $model, $action, ['name' => $user, 'password' => $password]));
print_pre($results);

if (!with_status($results, ['status' => 1]))
	die(failed("signing-in existing user <strong>$user</strong>"));

success("signed-in existing user <strong>$user</strong>");

//--------------------------------------------------------
$module = '../user.php';
$model  = 'user';
$action = 'signin';

testing(__LINE__, "$model::$action", "super-user");

$user		= 'admin';
$password	= 'secret';

echo_br("Signing-in super-user <strong>$user</strong>...");
$results = as_array(request($module, $model, $action, ['name' => $user, 'password' => $password]));
print_pre($results);

if (!with_status($results, ['status' => 1]))
	die(failed("signing-in super-user <strong>$user</strong>"));

success("signed-in super-user <strong>$user</strong>");


//========================================================
echo "<br/>Tests total: <font color='blue'><strong>$test_auto_num</strong></font> of $tests_expected";


	

?>
