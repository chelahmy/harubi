<?php
// User Model Test
// By Abdullah Daud
// 27 October 2019

// Use the PHP development server for testing:
// $ php -S localhost:8000

require '../../../../harubi/harubi.php';
require '../../../../test/test_helper.php';

harubi();

//========================================================
echo "<h1>Testing User Model</h1>";

tests_expected(32);

$dbname = check_db();
prepare_table($dbname, 'user', '../user.sql');

session_start(); // This user model uses PHP session
unset($_SESSION['user']); // Reset user session

//--------------------------------------------------------
$_SESSION['last_reg'] = 0; // bypass sign-up delay
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signup',
	'controller' => [
		'name' => 'admin',
		'password' => 'secret',
		'email' => 'admin@example.com'
	],
	'comment' => 'first user/super-user',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Signing-up super-user <strong>%name%</strong>...',
		'success' => 'signed-up super-user <strong>%name%</strong> ',
		'failed' => 'signing-up super-user <strong>%name%</strong>'
	]
]);

//--------------------------------------------------------
$_SESSION['last_reg'] = 0; // bypass sign-up delay
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signup',
	'controller' => [
		'name' => 'jamal',
		'password' => 'vision',
		'email' => 'jamal@example.com'
	],
	'comment' => '',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Signing-up new user <strong>%name%</strong>...',
		'success' => 'signed-up new user <strong>%name%</strong> ',
		'failed' => 'signing-up new user <strong>%name%</strong>'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signin',
	'controller' => [
		'name' => 'ali',
		'password' => 'wisdom'
	],
	'expectation' => [
		'status' => 0,
		'error_code' => 1
	],
	'comment' => 'non-user',
	'messages' => [
		'starting' => 'Signing-in non-user <strong>%name%</strong>...',
		'success' => 'signing-in non-user <strong>%name%</strong> failed as expected',
		'failed' => 'allowing non-user <strong>%name%</strong> to sign-in'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read_own',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'without signing-in',
	'expectation' => [
		'status' => 0,
		'error_code' => 1000
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> own record without signing-in...',
		'success' => 'reading user <strong>%name%</strong> own record without signing-in failed as expected',
		'failed' => 'allowing to read user <strong>%name%</strong> own record without signing-in'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'update_own',
	'controller' => [
		'name' => 'jamal',
		'password' => 'vision1',
		'email' => 'jamal_one@example.com'
	],
	'comment' => 'without signing-in',
	'expectation' => [
		'status' => 0,
		'error_code' => 1000
	],
	'messages' => [
		'starting' => 'Updating user <strong>%name%</strong> own record without signing-in...',
		'success' => 'updating user <strong>%name%</strong> own record without signing-in failed as expected',
		'failed' => 'allowing to update user <strong>%name%</strong> own record without signing-in'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'without signing-in',
	'expectation' => [
		'status' => 0,
		'error_code' => 1000
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> record without signing-in...',
		'success' => 'reading user <strong>%name%</strong> record without signing-in failed as expected',
		'failed' => 'allowing to read user <strong>%name%</strong> record without signing-in'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'update',
	'controller' => [
		'name' => 'jamal',
		'password' => 'vision1',
		'email' => 'jamal_one@example.com'
	],
	'comment' => 'without signing-in',
	'expectation' => [
		'status' => 0,
		'error_code' => 1000
	],
	'messages' => [
		'starting' => 'Updating user <strong>%name%</strong> record without signing-in...',
		'success' => 'updating user <strong>%name%</strong> record without signing-in failed as expected',
		'failed' => 'allowing to update user <strong>%name%</strong> record without signing-in'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'delete',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'without signing-in',
	'expectation' => [
		'status' => 0,
		'error_code' => 1000
	],
	'messages' => [
		'starting' => 'Deleting user <strong>%name%</strong> record without signing-in...',
		'success' => 'deleting user <strong>%name%</strong> record without signing-in failed as expected',
		'failed' => 'allowing to delete user <strong>%name%</strong> record without signing-in'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signin',
	'controller' => [
		'name' => 'jamal',
		'password' => 'vision',
	],
	'comment' => 'existing user',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Signing-in existing user <strong>%name%</strong>...',
		'success' => 'signed-in existing user <strong>%name%</strong>',
		'failed' => 'signing-in existing user <strong>%name%</strong>'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read_own',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => '',
	'expectation' => [
		'status' => 2,
		'results' => [
			'name' => 'jamal',
			'email' => 'jamal@example.com'
		]
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> own record...',
		'success' => 'read user <strong>%name%</strong> own record',
		'failed' => 'reading user <strong>%name%</strong> own record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'update_own',
	'controller' => [
		'name' => 'jamal',
		'password' => 'vision1',
		'email' => 'jamal_one@example.com'
	],
	'comment' => '',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Updating user <strong>%name%</strong> own record...',
		'success' => 'updated user <strong>%name%</strong> own record',
		'failed' => 'updating user <strong>%name%</strong> own record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signout',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => '',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Signing-out user <strong>%name%</strong>...',
		'success' => 'signed-out user <strong>%name%</strong>',
		'failed' => 'signing-out user <strong>%name%</strong>'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read_own',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'after signed-out',
	'expectation' => [
		'status' => 0,
		'error_code' => 1000,
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> own record after signed-out...',
		'success' => 'reading user <strong>%name%</strong> own record after signed-out failed as expected',
		'failed' => 'allowing to read user <strong>%name%</strong> own record after signed-out'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signin',
	'controller' => [
		'name' => 'jamal',
		'password' => 'vision1',
	],
	'comment' => 'with updated credentials',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Signing-in user <strong>%name%</strong>...',
		'success' => 'signed-in user <strong>%name%</strong>',
		'failed' => 'signing-in user <strong>%name%</strong>'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read_own',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'after update',
	'expectation' => [
		'status' => 2,
		'results' => [
			'name' => 'jamal',
			'email' => 'jamal_one@example.com'
		]
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> own record after update...',
		'success' => 'read user <strong>%name%</strong> own record after update',
		'failed' => 'reading user <strong>%name%</strong> own record after update'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'restricted',
	'expectation' => [
		'status' => 0,
		'error_code' => 1001
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> record with restricted action...',
		'success' => 'reading user <strong>%name%</strong> record with restricted action failed as expected',
		'failed' => 'allowing to read user <strong>%name%</strong> record with restricted action'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'update',
	'controller' => [
		'name' => 'jamal',
		'password' => 'vision2',
		'email' => 'jamal_two@example.com'
	],
	'comment' => 'restricted',
	'expectation' => [
		'status' => 0,
		'error_code' => 1001
	],
	'messages' => [
		'starting' => 'Updating user <strong>%name%</strong> record with restricted action...',
		'success' => 'updating user <strong>%name%</strong> record with restricted action failed as expected',
		'failed' => 'allowing to update user <strong>%name%</strong> record with restricted action'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'delete',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'restricted',
	'expectation' => [
		'status' => 0,
		'error_code' => 1001
	],
	'messages' => [
		'starting' => 'Deleting user <strong>%name%</strong> record with restricted action...',
		'success' => 'deleting user <strong>%name%</strong> record with restricted action failed as expected',
		'failed' => 'allowing to delete user <strong>%name%</strong> record with restricted action'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signin',
	'controller' => [
		'name' => 'admin',
		'password' => 'secret',
	],
	'comment' => 'super-user',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Signing-in super-user <strong>%name%</strong>...',
		'success' => 'signed-in super-user <strong>%name%</strong>',
		'failed' => 'signing-in super-user <strong>%name%</strong>'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read_own',
	'controller' => [
		'name' => 'admin'
	],
	'comment' => 'super-user',
	'expectation' => [
		'status' => 2,
		'results' => [
			'name' => 'admin',
			'email' => 'admin@example.com'
		]
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> own record...',
		'success' => 'read user <strong>%name%</strong> own record',
		'failed' => 'reading user <strong>%name%</strong> own record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'update_own',
	'controller' => [
		'name' => 'admin',
		'password' => 'secret1',
		'email' => 'admin_one@example.com'
	],
	'comment' => 'super-user',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Updating user <strong>%name%</strong> own record...',
		'success' => 'updated user <strong>%name%</strong> own record',
		'failed' => 'updating user <strong>%name%</strong> own record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signout',
	'controller' => [
		'name' => 'admin'
	],
	'comment' => 'super-user',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Signing-out user <strong>%name%</strong>...',
		'success' => 'signed-out user <strong>%name%</strong>',
		'failed' => 'signing-out user <strong>%name%</strong>'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read_own',
	'controller' => [
		'name' => 'admin'
	],
	'comment' => 'super-user after signed-out',
	'expectation' => [
		'status' => 0,
		'error_code' => 1000,
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> own record after signed-out...',
		'success' => 'reading user <strong>%name%</strong> own record after signed-out failed as expected',
		'failed' => 'allowing to read user <strong>%name%</strong> own record after signed-out'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'signin',
	'controller' => [
		'name' => 'admin',
		'password' => 'secret1',
	],
	'comment' => 'super-user with updated credentials',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Signing-in user <strong>%name%</strong>...',
		'success' => 'signed-in user <strong>%name%</strong>',
		'failed' => 'signing-in user <strong>%name%</strong>'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read_own',
	'controller' => [
		'name' => 'admin'
	],
	'comment' => 'super-user after update',
	'expectation' => [
		'status' => 2,
		'results' => [
			'name' => 'admin',
			'email' => 'admin_one@example.com'
		]
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> own record after update...',
		'success' => 'read user <strong>%name%</strong> own record after update',
		'failed' => 'reading user <strong>%name%</strong> own record after update'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'super-user',
	'expectation' => [
		'status' => 2,
		'results' => [
			'name' => 'jamal',
			'email' => 'jamal_one@example.com'
		]
	],
	'messages' => [
		'starting' => 'Reading user <strong>%name%</strong> record...',
		'success' => 'read user <strong>%name%</strong> record',
		'failed' => 'reading user <strong>%name%</strong> record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'update',
	'controller' => [
		'name' => 'jamal',
		'password' => 'vision2',
		'email' => 'jamal_two@example.com'
	],
	'comment' => 'super-user',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Updating user <strong>%name%</strong> record...',
		'success' => 'updated user <strong>%name%</strong> record',
		'failed' => 'updating user <strong>%name%</strong> record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read',
	'controller' => [
		'name' => 'ali'
	],
	'comment' => 'super-user on non-user',
	'expectation' => [
		'status' => 0,
		'error_code' => 1
	],
	'messages' => [
		'starting' => 'Reading non-user <strong>%name%</strong> record...',
		'success' => 'reading non-user <strong>%name%</strong> record failed as expected',
		'failed' => 'allowing to read non-user <strong>%name%</strong> record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'update',
	'controller' => [
		'name' => 'ali',
		'password' => 'wisdom1',
		'email' => 'ali_one@example.com'
	],
	'comment' => 'super-user on non-user',
	'expectation' => [
		'status' => 0,
		'error_code' => 1003
	],
	'messages' => [
		'starting' => 'Updating non-user <strong>%name%</strong> record...',
		'success' => 'updating non-user <strong>%name%</strong> record failed as expected',
		'failed' => 'allowing to update non-user <strong>%name%</strong> record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'delete',
	'controller' => [
		'name' => 'admin'
	],
	'comment' => 'super-user delete own record',
	'expectation' => [
		'status' => 0,
		'error_code' => 1002
	],
	'messages' => [
		'starting' => 'Deleting super-user <strong>%name%</strong> record...',
		'success' => 'deleting super-user <strong>%name%</strong> record failed as expected',
		'failed' => 'allowing to delete super-user <strong>%name%</strong> record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'delete',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'super-user',
	'expectation' => [
		'status' => 1
	],
	'messages' => [
		'starting' => 'Deleting user <strong>%name%</strong> record...',
		'success' => 'deleted user <strong>%name%</strong> record',
		'failed' => 'deleting user <strong>%name%</strong> record'
	]
]);

//--------------------------------------------------------
test([
	'line' => __LINE__,
	'module' => '../user.php',
	'model' => 'user',
	'action' => 'read',
	'controller' => [
		'name' => 'jamal'
	],
	'comment' => 'super-user on deleted user',
	'expectation' => [
		'status' => 0,
		'error_code' => 1
	],
	'messages' => [
		'starting' => 'Reading deleted user <strong>%name%</strong> record...',
		'success' => 'reading deleted user <strong>%name%</strong> record failed as expected',
		'failed' => 'allowing to read deleted user <strong>%name%</strong> record'
	]
]);

//========================================================
test_total();

?>
