<?php
// harubi.php
// Harubi - A Model-Beat/Blow-Controller (MBC) framework.
// By Abdullah Daud, chelahmy@gmail.com
// 14 November 2013
// - Start date
// 10 December 2018
// - This project has been dormant for a long time and it will find a new life.
//   The concept is not new and there are many MVC frameworks available. However,
//   harubi is focusing on model and controller only. Hence, harubi is simpler
//   minus the view baggage. However, harubi needs a layer of authorization
//   management to make it useful. Yet, there are many ways to authorize users.
//   And user management requires the viewing concern which harubi does not have.
//   Those are the reasons why the harubi project has been dormant.
// 12 December 2018
// - Introduced the *blow* routing to simplify URL rewrite.

// Literally, harubi is a keris with a golden handle, a Malay traditional hand weapon.
// Beat and blow are offensive hand movements with or without weapon against an opponent.
// There is no negative connotation on the words used when we are focusing on winning.

// This harubi is an application framework similar to the Model-View-Controller (MVC)
// framework minus the view concern. Harubi introduces routing handlers called *beat*
// and *blow*.

// Harubi is designed to be the servers for rich web client applications. All viewing
// concerns are delegated to the client side. Harubi handles the models and controllers.
// Requests are handled using the beat/blow pattern.

// A beat/blow is a request routing. A beat/blow is expecting a query to contain at least
// two parameters: a model name and an action to the model. It will invoke the controller
// for the model::action. Arguments for the controller may be passed along with the
// query. A controller is passed as a closure to the beat()/blow() function. The beat/blow
// is expecting the controller to return an associative array which will then be converted
// to JSON before being passed as the response to the request. See the comment on the
// beat()/blow() function implementation. If it is not an array then it will return as is.

// On the model side, harubi implements CRUD with object relational mapping (ORM) for MySQL.
// Every object has a unique ID mapped to the primary index of its table. The create()
// function returns the new id. The id handling is simplified in the *where* clause. See
// the where_id() function.

// Harubi is initialized with the database settings. Fields have to be mapped for PHP type
// conversion including integer, float and string. The conversion also protect the database
// from SQL injection attacks. See the harubi() function for details.

$harubi_mysql_settings = NULL;
$harubi_table_settings = NULL;
$harubi_query = NULL;

// Log variables
$harubi_logs = array();
$harubi_do_dump_log = TRUE;
$harubi_do_log_querystring = TRUE;
$harubi_respond_with_logs = FALSE;

// Injected methods
$harubi_permission_controller = NULL;
$harubi_cache_func = NULL;

function harubi_log($file, $function, $line, $type, $message)
{
	global $harubi_logs;
	
	$harubi_logs[] = array(
		'file' => $file,
		'function' => $function,
		'line' => $line,
		'type' => $type,
		'message' => $message
	);
}

function harubi_log_debug($line, $message)
{
	harubi_log('', '', $line, 'debug', $message);
}

function get_harubi_logs()
{
	global $harubi_logs;
	return $harubi_logs;
}

function print_harubi_logs()
{
	global $harubi_logs;
	
	echo '<pre>';
	print_r($harubi_logs);
	echo '</pre>';
}

function dump_harubi_logs()
{
	global $harubi_logs;
	file_put_contents('harubi.log', json_encode($harubi_logs));
}

/**
* Message enveloping functions to be used in reponding to request.
* The requester is expected to evaluate the 'status' field first
* which may have the following values:
*
* 'status'
*    0: an error occurred while processing the request.
*    1: the request had been successfully processed.
*    2: the request had been successfully processed with result.
*
* On status = 0:
* The following fields have more information about the error:
* 'error_code': a signed integer value representing the error.
* 'error_message': the textual error message.
*
* On status = 1:
* No other field to evaluate.
*
* On status = 2:
* 'results': a mixed type results of the request. 
*
*/

function respond_error($error_code, $error_message)
{
	global $harubi_respond_with_logs;
	global $harubi_logs;
	
	$respond = array(
		'status' => 0,
		'error_code' => $error_code, 
		'error_message' => $error_message
		);
		
	if ($harubi_respond_with_logs)
		$respond['logs'] = $harubi_logs;
		
	return $respond;
}

function respond_system_error($error_code = -1)
{
	return respond_error($error_code, "System error");
}

function respond_ok($results = null)
{
	global $harubi_respond_with_logs;
	global $harubi_logs;
	
	if (is_null($results))
		$respond = array(
			'status' => 1
			);
	else
		$respond = array(
			'status' => 2,
			'results' => $results
			);
					
	if ($harubi_respond_with_logs)
		$respond['logs'] = $harubi_logs;
		
	return $respond;
}

/**
* Harubi initialization function.
* @param $settings can be a filename or a JSON string with the following example
* structure. Default to filename 'settings.inc':
* {
*	"globals" : {
*		"do_dump_log" : true,
*		"do_log_querystring" : true,
*		"respond_with_logs" : false
*	},
*	
*	"mysql" : {
*		"hostname" : "localhost",
*		"username" : "root",
*		"password" : "****",
*		"database" : "board"
*	},
*	
*	"tables" : {
*		"table_1" : {
*			"user_id" : "int",
*			"message" : "string",
*			"created_utc" : "int"
*		},
*		"table_2" : {
*			"weight" : "float",
*			"created_utc" : "int"
*		}
*	}
* }
*
* The first part is the Harubi global settings.
*
* The second part is the MySQL connection settings.
*
* The third part is the tables declaration with fields mapping. Every table is
* assumed to have the id field that does not have to be declared. There are
* three types of mapping: int, float and string.
* 
* Harubi does not create the tables.
*
* @return nothing
*/
function harubi($settings = 'settings.inc')
{
	global $harubi_settings;
	global $harubi_mysql_settings;
	global $harubi_table_settings;

	if (!is_array($settings))
	{
		if (file_exists($settings))
			$settings = file_get_contents($settings);
		elseif ($settings == 'settings.inc')	
			harubi_log(__FILE__,__FUNCTION__, __LINE__, 'error', 'File settings.inc does not exist');
		
		$settings = json_decode($settings, TRUE);
	}
		
	$harubi_settings = $settings;
	
	if (isset($settings['globals']))
	{
		global $harubi_do_dump_log;
		global $harubi_do_log_querystring;
		global $harubi_respond_with_logs;
		
		$globals = $settings['globals'];
		
		if (isset($globals['do_dump_log']))
			$harubi_do_dump_log = $globals['do_dump_log'];
		
		if (isset($globals['do_log_querystring']))
			$harubi_do_log_querystring = $globals['do_log_querystring'];
		
		if (isset($globals['respond_with_logs']))
			$harubi_respond_with_logs = $globals['respond_with_logs'];
	}
	
	if (isset($settings['mysql']))
		$harubi_mysql_settings = $settings['mysql'];
	else
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'warning', 'No setting for MySQL');

	if (isset($settings['tables']))
		$harubi_table_settings = $settings['tables'];
	else
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'warning', 'No setting for tables');
}

function attach_permission_controller($controller)
{
	global $harubi_permission_controller;
	$harubi_permission_controller = $controller;
}

function connect_db()
{
	global $harubi_mysql_settings;

	if (!is_array($harubi_mysql_settings))
	{
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'error', 'MySQL settings are required');
		return FALSE;
	}
	
	$hostname = $harubi_mysql_settings['hostname']; 
	$username = $harubi_mysql_settings['username'];
	$password = $harubi_mysql_settings['password']; 
	$database = $harubi_mysql_settings['database'];
	
	for ($i = 0; $i < 3; $i++) // retry 3 times
	{
		$dbi = mysqli_connect($hostname, $username, $password, $database);

		if (!$dbi || mysqli_connect_errno() != 0)
		{
			harubi_log(__FILE__,__FUNCTION__, __LINE__, 'warning', 'Tried to connect with MySQL but failed');
			sleep(5); // wait for 5 seconds before retry
			$dbi = FALSE;
		}
	}
	
	if ($dbi === FALSE)
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'error', 'Failed to connect with MySQL');
	
	return $dbi;	
}

function esc($db, $str, $like = FALSE)
{
	$str = mysqli_real_escape_string($db, $str);
	
	// Underscore and percent have special meanings in LIKE clause
	if ($like)
		return addcslashes($str, '%_');
		
	return $str;
}

function table_exists($table)
{
	$exist = FALSE;
	$db = connect_db();
	
	if ($db === FALSE)
		return $exist;
		
	$table = esc($table);
	
	if ($result = mysqli_query($db, "SHOW TABLES LIKE '" . $table . "'"))
	{
		if (mysqli_num_rows($result) == 1)
			$exist = TRUE;
			
		mysqli_free_result($result);
	}
	
	mysqli_close($db);
	
	return $exist;
}

function sql_val($db, $table_name, $field_name, $value)
{
	global $harubi_table_settings;

	$value = esc($db, $value);
	
	if (!is_array($harubi_table_settings))
		return $value;
		
	if (!isset($harubi_table_settings[$table_name]))
		return $value;
		
	$fields = $harubi_table_settings[$table_name];
	
	if (isset($fields[$field_name]))
	{
		$field = strtolower($fields[$field_name]);
		
		if ($field == 'str' || $field == 'string')
			return "'" . $value . "'";
			
		if ($field == 'int'  || $field == 'integer' )
			return intval($value);
			
		if ($field == 'float')
			return floatval($value);
	}
	
	return $value;
}

function clean($value, $type = 'int', $like = FALSE)
{
	if ($type == 'int')
		return intval($value); 
	elseif ($type == 'float')
		return floatval($value); 
	
	$db = connect_db();

	if ($db === FALSE)
		return FALSE;

	return esc($db, $value, $like);
}

function equ($name, $value, $type = 'int', $op = '=')
{
	$db = connect_db();

	if ($db === FALSE)
		return FALSE;

	$lwrop = strtolower($op);
	$name = esc($db, $name);
	
	if ($type == 'int')
		$value = intval($value); 
	elseif ($type == 'float')
		$value = floatval($value); 
	else
	{
		if ($lwrop == 'like')
			$value = esc($db, $value, TRUE);
		else
			$value = esc($db, $value);
	}
	
	if ($type == 'string' || $lwrop == 'like')
		return "`$name` $op '$value'";
		
	return "`$name` $op $value"; 
}

function create($table, $fields)
{
	$db = connect_db();

	if ($db === FALSE)
		return -1;

	if (!is_array($fields))
		$fields = json_decode($fields, TRUE);
		
	$table = esc($db, $table);

	$index = 0;
	$colnames = '';
	$colvals = '';
	
	foreach ($fields as $colname => $colval)
	{
		if ($index > 0)
		{
			$colnames .= ',';
			$colvals .= ',';
		}
			
		$colname = esc($db, $colname);
		$colnames .= "`$colname`"; 
		$colvals .= sql_val($db, $table, $colname, $colval);
			
		++$index;
	}
	
	$query = "INSERT INTO `$table` ($colnames) " . 
		"VALUES ($colvals);";

	global $harubi_do_log_querystring;
	
	if (isset($harubi_do_log_querystring) && $harubi_do_log_querystring)
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'notice', 'Inserting a record into MySQL using query: ' . $query);
		
	$wait = 0;
	$id = -2;
	
	for ($t = 0; $t < 5; $t++) // With retrials
	{
		if ($wait > 0)
			sleep($wait); // delay
			
		++$wait; // incremental delay for the next retrial
		
		if (mysqli_query($db, $query) === TRUE)
		{
			$id = mysqli_insert_id($db);
			break;
		}
		else
			harubi_log(__FILE__,__FUNCTION__, __LINE__, 'warning', 'Tried to insert a record into MySQL but failed using query: ' . $query);
	}

	mysqli_close($db);

	if ( $id == -2)
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'error', 'Failed to insert a record into MySQL using query: ' . $query);

	return intval($id);	
}

/**
* Expands a short form 'where' with sugar coated 'id' field:
* - Numeric $where will be prepended with 'id='.
* - $where started with an operator will be prepended with 'id'. 
* For both cases the value after the operator will be casted to integer.
* 
* Otherwise, return $where as is.
* 
* @param mixed $where
* 
* @return proper 'where' or FALSE
*/
function where_id($where)
{
	$len = strlen($where);
	
	if ($where === FALSE || $len <= 0)
		return FALSE;
		
	if (in_array($where[0], array('=', '>', '<', '!')))
	{
		$eq = '';
		$val = '';
		
		for ($i = 0; $i < $len; $i++)
		{
			$chr = $where[$i];
			
			if (in_array($chr, array('=', '>', '<', '!')))
			{
				if (strlen($val) > 0)
					break;
					
				$eq .= $chr;				
			}
			else
				$val .= $chr;
		}
		
		if (strlen($val) <= 0 || strlen($eq) <= 0 
			|| !in_array($eq, array('=', '>', '<', '>=', '<=', '!=', '<>', '<=>')))
			return FALSE;
			
		$where = 'id' . $eq . intval($val);
	}
	elseif (is_numeric($where))
		$where = 'id=' . intval($where);
	
	return $where;
}

function read($table, $fields = FALSE, $where = FALSE, $order_by = FALSE, $sort = FALSE, $limit = FALSE, $offset = FALSE, $count = FALSE)
{
	$db = connect_db();

	if ($db === FALSE)
		return FALSE;

	$table = esc($db, $table);
	
	if ($count)
		$query = "SELECT COUNT(*) AS `count` FROM ";
	else
	{
		if ($fields === FALSE)
			$cols = "*";
		else
		{
			if (is_array($fields))
			{
				$cols = "";
			
				foreach ($fields as $field)
				{
					if (strlen($cols) > 0)
						$cols .= ", ";
					
					$cols .= esc($db, $field);
				}
			}
			else
				$cols = "`" . esc($db, $fields) . "`";
		}
		
		$query = "SELECT " . $cols . " FROM ";
	}
	
	$query .= "`$table`";
	
	if ($where !== FALSE)
	{
		$where = where_id($where);
		$query .= " WHERE " . $where;
	}
	
	if ($order_by !== FALSE)
	{
		$order_by = esc($db, $order_by);
		$query .= " ORDER BY `$order_by`";
	}
	
	if ($sort !== FALSE && strlen($sort) > 0)
	{
		$sort = strtoupper($sort);
		
		if ($sort == "ASC" || $sort == "DESC")
			$query .= " " . $sort;
	}

	if ($limit !== FALSE)
	{
		$limit = intval($limit);
		
		if ($offset !== FALSE)
		{
			$offset = intval($offset);
			$query .= " LIMIT " . $offset . "," . $limit;
		}
		else
			$query .= " LIMIT " . $limit;
	}
	elseif ($offset !== FALSE)
	{
		$offset = intval($offset);
		$query .= " LIMIT " . $offset . ",18446744073709551615";
	}
		
	$query .= ";";

	global $harubi_do_log_querystring;
	
	if (isset($harubi_do_log_querystring) && $harubi_do_log_querystring)
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'notice', 'Selecting a record from MySQL using query: ' . $query);
		
	$records = array();
	
	if ($result = mysqli_query($db, $query))
	{
		while ($row = mysqli_fetch_assoc($result))
		{
			$records[] = $row;
		}
			
		mysqli_free_result($result);
	}
	else
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'error', 'Failed to select a record from MySQL using query: ' . $query);
		
	mysqli_close($db);
	
	return $records;
}

function update($table, $fields, $where)
{
	$db = connect_db();
	
	if ($db === FALSE)
		return FALSE;
		
	if (!is_array($fields))
		$fields = json_decode($fields, TRUE);
		
	$table = esc($db, $table);
	$where = where_id($where);

	$set = "";

	foreach ($fields as $colname => $colval)
	{
		if (strlen($set) > 0)
			$set .= ', ';
			
		$colname = esc($db, $colname);
		$set .= "`$colname` = " . sql_val($db, $table, $colname, $colval);
	}
	
	$query = "UPDATE `$table` SET $set WHERE $where;";
	
	global $harubi_do_log_querystring;
	
	if (isset($harubi_do_log_querystring) && $harubi_do_log_querystring)
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'notice', 'Updating a record into MySQL using query: ' . $query);
		
	$status = FALSE;
	$wait = 0;
	
	for ($t = 0; $t < 5; $t++) // With retrials
	{
		if ($wait > 0)
			sleep($wait); // delay
			
		++$wait; // incremental delay for the next retrial
		
		if (mysqli_query($db, $query) === TRUE)
		{
			$status = TRUE;
			break;
		}
		else
			harubi_log(__FILE__,__FUNCTION__, __LINE__, 'warning', 'Tried to update a record in MySQL but failed using query: ' . $query);
	}

	mysqli_close($db);

	if (!$status)
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'error', 'Failed to update a record in MySQL using query: ' . $query);

	return $status;	
}

function delete($table, $where)
{
	$db = connect_db();
	
	if ($db === FALSE)
		return FALSE;
		
	$table = esc($db, $table);
	
	if ($where !== FALSE)
	{
		$where = where_id($where);	
		$query = "DELETE FROM `$table` WHERE $where;";
	}
	else
		$query = "DELETE FROM `$table`;";
	
	global $harubi_do_log_querystring;
	
	if (isset($harubi_do_log_querystring) && $harubi_do_log_querystring)
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'notice', 'Deleting a record from MySQL using query: ' . $query);
		
	$status = FALSE;
	$wait = 0;
	
	for ($t = 0; $t < 5; $t++) // With retrials
	{
		if ($wait > 0)
			sleep($wait); // delay
			
		++$wait; // incremental delay for the next retrial
		
		if (mysqli_query($db, $query) === TRUE)
		{
			$status = TRUE;
			break;
		}
		else
			harubi_log(__FILE__,__FUNCTION__, __LINE__, 'warning', 'Tried to delete a record from MySQL but failed using query: ' . $query);
	}

	mysqli_close($db);

	if (!$status)
		harubi_log(__FILE__,__FUNCTION__, __LINE__, 'error', 'Failed to delete a record from MySQL using query: ' . $query);

	return $status;	
}

/**
* route() is called by beat() and blow().
* See descriptions on those functions.
*/
function route($model, $action, $controller, $use_q = FALSE)
{
	if (!is_callable($controller))
		return;
	
	global $harubi_cache_func;
	
	if ($harubi_cache_func != NULL &&
		is_callable($harubi_cache_func))
	{
		$acfunc = new ReflectionFunction($harubi_cache_func);
		$acfunc->invokeArgs(array($model, $action)); // The cache will decide whether to exit().
	}
	
	global $harubi_permission_controller;
	$has_permission = TRUE;
	$result = NULL;
	
	if ($harubi_permission_controller != NULL &&
		is_callable($harubi_permission_controller))
	{
		$pctrl = new ReflectionFunction($harubi_permission_controller);
		$status = $pctrl->invokeArgs(array($model, $action));
		
		if ($status !== TRUE)
		{
			$has_permission = FALSE;

			if ($status !== FALSE)
			{
				if (is_array($status))
					$result = json_encode($status);
				else
					$result = $status;
			}
		}
	}

	global $harubi_query;
	
	if ($has_permission)
	{
		$ctrl = new ReflectionFunction($controller);
		$params = array();
		$i = 2; // after model and action
		
		foreach ($ctrl->getParameters() as $param)
		{
			$has_val = FALSE;
			
			if ($use_q)
			{
				if (is_array($harubi_query) && isset($harubi_query[$i]))
				{
					$params[] = $harubi_query[$i];
					$has_val = TRUE;
				}
			}
			elseif (isset($_REQUEST[$param->name]))
			{
				$params[] = $_REQUEST[$param->name];
				$has_val = TRUE;
			}
			
			if (!$has_val) {
				if ($param->isDefaultValueAvailable())
					$params[] = $param->getDefaultValue();
				else
					$params[] = NULL;
			}
			
			++$i;
		}

		$ret = $ctrl->invokeArgs($params);
		
		if (is_array($ret))
			$result = json_encode($ret);
		else
			$result = $ret;
	}
	else
	{
		if ($result == NULL)
			$result = json_encode(array(respond_error(-1000, "No permission to access $model::$action")));
	}
	
	if (isset($harubi_do_dump_log) && $harubi_do_dump_log)
		dump_harubi_logs();

	exit($result);
}

/**
* beat() passes a request to the $controller and calls exit() to exit
* the entire script with a response to the request. It will do nothing
* if the $model and $action do not match. If $model is NULL then it
* takes any, so does $action.
*  
* Expecting request arguments 'model', 'action' and those
* matching with the $controller parameters.
* 
* The $controller will be invoked if both $model and $action
* matched with the request. Matching request arguments will
* also be passed to the $controller. 
* 
* The $controller is expected to return with an assoc array
* which will then be converted to a json string as the response
* to the request. Or as is if the return is not an array.
*
* Prior to all above, permission to invoke the action will be consulted if
* $harubi_permission_controller is implemented. The permission controller
* is expected to take 2 arguments: model and action. And the permission
* controller is expected to return either TRUE or FALSE. Otherwise, the
* return value will be taken as is as the response.
* 
* @param string $model
* @param string $action
* @param closure $controller
* 
* @return nothing or response with json data
*/
function beat($model, $action, $controller)  
{
	if (!isset($_REQUEST['model']) || !isset($_REQUEST['action']))
		return;

	if ($model == NULL)
		$model = $_REQUEST['model'];
		
	if ($action == NULL)
		$action = $_REQUEST['action'];
	
	if ($model != $_REQUEST['model'] || $action != $_REQUEST['action'])
		return;
	
	route($model, $action, $controller);
}

/**
* blow() is similar to beat() except that it takes $_REQUEST['q']
* instead of $_REQUEST['model'] and $_REQUEST['action'].
* The 'q' argument is expected to be a string with the following syntax:
* 'model/action/controller-param1/...'.
*/
function blow($model, $action, $controller)
{
	global $harubi_query;
	
	if ($harubi_query == NULL)
	{
		if (!isset($_REQUEST['q']))
			return;
	
		$harubi_query = explode("/", $_REQUEST['q']);
	}

	if (isset($harubi_query[0]))
		$m = $harubi_query[0];
	else
		$m = NULL;

	if (isset($harubi_query[1]))
		$a = $harubi_query[1];
	else
		$a = NULL;
		
	if ($model == NULL && $m != NULL)
		$model = $m;	
		
	if ($action == NULL && $a != NULL)
		$action = $a;	
	
	if ($model != $m || $action != $a)
		return;
	
	route($model, $action, $controller, TRUE);
}

?>
