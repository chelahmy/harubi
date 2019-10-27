<?php

require '../../../../harubi/harubi.php'; 

harubi();

$test_auto_num = 0;

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

function not_ok($results)
{
	return $results == NULL || $results['status'] < 1;
}

function testing($line = 0)
{
	global $test_auto_num;
	++$test_auto_num;
	
	return "Testing #$test_auto_num [line $line]: ";
}

function check_db()
{
	global $harubi_mysql_settings;
	
	$dbn = $harubi_mysql_settings['database'];
	$db = connect_db();

	if ($db === FALSE)
		die("Failed to connect to the database <strong>$dbn</strong>.");

	echo_br("Database exists: <strong>$dbn</strong>");
	mysqli_close($db);
	
	return $dbn;
}

function prepare_table($dbname, $tblname, $tblsql)
{
	if (!table_exists($tblname))
	{
		$db = connect_db();

		if ($db === FALSE)
			die("Failed to connect to the database <strong>$dbname</strong>.");

		echo_br("Creating table <strong>$tblname</strong>...");
		$sql = file_get_contents($tblsql);	
		mysqli_multi_query($db, $sql);
		mysqli_close($db);
	
		if (!table_exists('user'))
			die("Failed to create table <strong>$tblname</strong>.");
		
		echo_br("Table <strong>$tblname</strong> created.");
	}
	else
	{
		delete($tblname, '`id` > 0'); // delete all records

		$db = connect_db();

		if ($db === FALSE)
			die("Failed to connect to the database <strong>$dbname</strong>.");
		
		mysqli_query($db, "ALTER TABLE `$tblname` AUTO_INCREMENT = 1");	
		mysqli_close($db);
	}

	echo_br("Table exists: <strong>$tblname</strong>");
}

//--------------------------------------------------------
echo "<h1>Testing User Model</h1>";

$dbname = check_db();
prepare_table($dbname, 'user', '../user.sql');

session_start();

//--------------------------------------------------------
echo_br(testing(__LINE__) . "user::signup #1 first user/super-user");

$user		= 'admin';
$password	= 'secret';
$email		= 'admin@example.com';

echo_br("Signing-up a super-user <strong>$user</strong>...");
$_SESSION['last_reg'] = 0; // bypass sign-up delay
$results = as_array(request('../user.php', 'user', 'signup', ['name' => $user, 'password' => $password, 'email' => $email]));

if (not_ok($results))
{
	print_pre($results);
	die("Failed to sign-up super-user <strong>$user</strong>");
}

echo_br("Signed-up super-user <strong>$user</strong>");

//--------------------------------------------------------
echo_br(testing(__LINE__) . "user::signup #2");

$user		= 'jamal';
$password	= 'vision';
$email		= 'jamal@example.com';

echo_br("Signing-up a user <strong>$user</strong>...");
$_SESSION['last_reg'] = 0; // bypass sign-up delay
$results = as_array(request('../user.php', 'user', 'signup', ['name' => $user, 'password' => $password, 'email' => $email]));

if (not_ok($results))
{
	print_pre($results);
	die("Failed to sign-up user <strong>$user</strong>");
}

echo_br("Signed-up user <strong>$user</strong>");

//--------------------------------------------------------
echo_br(testing(__LINE__) . "user::signin non-user");

$user		= 'ali';
$password	= 'wisdom';

echo_br("Signing-in non-user <strong>$user</strong>...");
$results = as_array(request('../user.php', 'user', 'signin', ['name' => $user, 'password' => $password]));

if (!not_ok($results))
{
	print_pre($results);
	die("Failed by allowing to sign-in non-user <strong>$user</strong>");
}

echo_br("Signing-in non-user <strong>$user</strong> failed as expected");

//--------------------------------------------------------
echo_br(testing(__LINE__) . "user::signin existing user");

$user		= 'jamal';
$password	= 'vision';

echo_br("Signing-in existing user <strong>$user</strong>...");
$results = as_array(request('../user.php', 'user', 'signin', ['name' => $user, 'password' => $password]));

if (not_ok($results))
{
	print_pre($results);
	die("Failed to sign-in existing user <strong>$user</strong>");
}

echo_br("Signed-in existing user <strong>$user</strong>");

//--------------------------------------------------------
echo_br(testing(__LINE__) . "user::signin super-user");

$user		= 'admin';
$password	= 'secret';

echo_br("Signing-in super-user <strong>$user</strong>...");
$results = as_array(request('../user.php', 'user', 'signin', ['name' => $user, 'password' => $password]));

if (not_ok($results))
{
	print_pre($results);
	die("Failed to sign-in super-user <strong>$user</strong>");
}

echo_br("Signed-in super-user <strong>$user</strong>");




	

?>
