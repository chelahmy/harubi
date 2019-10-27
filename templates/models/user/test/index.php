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
$test_expected = 5;

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

function as_array($json)
{
	return json_decode($json, TRUE);
}

function with_status($results, $matching)
{
	if (!is_array($results) || !is_array($matching))
		return FALSE;
		
	if (!isset($results['status']) || !isset($matching['status']))
		return FALSE;
		
	if ($results['status'] == $matching['status'])
	{
		if (!isset($results['error_code']))
			return TRUE;
			
		if (isset($matching['error_code']) && $matching['error_code'] == $results['error_code'])
			return TRUE;
	}	
		
	return FALSE;		
}

function testing($line = 0, $msg = '')
{
	global $test_auto_num;
	++$test_auto_num;
	
	echo "<br/><font color='blue'>Testing #$test_auto_num</font> [line $line]: $msg<br/>";
}

function failed($msg)
{
	return "<font color='red'>Failed:</font> $msg";
}

function success($msg)
{
	echo "<font color='green'>Success:</font> $msg<br/>";
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
testing(__LINE__, "user::signup #1 first user/super-user");

$user		= 'admin';
$password	= 'secret';
$email		= 'admin@example.com';

echo_br("Signing-up super-user <strong>$user</strong>...");
$_SESSION['last_reg'] = 0; // bypass sign-up delay
$results = as_array(request('../user.php', 'user', 'signup', ['name' => $user, 'password' => $password, 'email' => $email]));
print_pre($results);

if (!with_status($results, ['status' => 1]))
	die(failed("signing-up super-user <strong>$user</strong>"));

success("signed-up super-user <strong>$user</strong>");

//--------------------------------------------------------
testing(__LINE__, "user::signup #2");

$user		= 'jamal';
$password	= 'vision';
$email		= 'jamal@example.com';

echo_br("Signing-up new user <strong>$user</strong>...");
$_SESSION['last_reg'] = 0; // bypass sign-up delay
$results = as_array(request('../user.php', 'user', 'signup', ['name' => $user, 'password' => $password, 'email' => $email]));
print_pre($results);

if (!with_status($results, ['status' => 1]))
	die(failed("signing-up new user <strong>$user</strong>"));

success("signed-up new user <strong>$user</strong>");

//--------------------------------------------------------
testing(__LINE__, "user::signin non-user");

$user		= 'ali';
$password	= 'wisdom';

echo_br("Signing-in non-user <strong>$user</strong>...");
$results = as_array(request('../user.php', 'user', 'signin', ['name' => $user, 'password' => $password]));
print_pre($results);

if (!with_status($results, ['status' => 0, 'error_code' => 1]))
	die(failed("allowing non-user <strong>$user</strong> to sign-in"));

success("signing-in non-user <strong>$user</strong> failed as expected");

//--------------------------------------------------------
testing(__LINE__, "user::signin existing user");

$user		= 'jamal';
$password	= 'vision';

echo_br("Signing-in existing user <strong>$user</strong>...");
$results = as_array(request('../user.php', 'user', 'signin', ['name' => $user, 'password' => $password]));
print_pre($results);

if (!with_status($results, ['status' => 1]))
	die(failed("signing-in existing user <strong>$user</strong>"));

success("signed-in existing user <strong>$user</strong>");

//--------------------------------------------------------
testing(__LINE__, "user::signin super-user");

$user		= 'admin';
$password	= 'secret';

echo_br("Signing-in super-user <strong>$user</strong>...");
$results = as_array(request('../user.php', 'user', 'signin', ['name' => $user, 'password' => $password]));
print_pre($results);

if (!with_status($results, ['status' => 1]))
	die(failed("signing-in super-user <strong>$user</strong>"));

success("signed-in super-user <strong>$user</strong>");


//========================================================
echo "<br/>Tests total: <font color='blue'><strong>$test_auto_num</strong></font> of $test_expected";


	

?>
