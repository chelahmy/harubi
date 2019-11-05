<?php
// Parse SQL CREATE TABLE
// By Abdullah Daud
// 6 November 2019


/**
* Skip CREATE TABLE [IF NOT EXISTS]
*/
function skip_create_table($sql)
{
	$sql = trim($sql);
	$pos = stripos($sql, 'create'); // find 'create'
	
	if ($pos === FALSE)
		return FALSE;

	$sql = trim(substr($sql, $pos + 6)); // skip 'create'	
	$word = substr($sql, 0, 5); // expecting 'table'
	
	if (strcasecmp($word, 'table') != 0)
		return skip_create_table($sql);
		

	//echo "<br/>";
	//print_r($sql);
	//echo "<br/>";
		
	$sql = trim(substr($sql, 5)); // skip 'table'
	$word = substr($sql, 0, 2); // testing for 'if'
	
	if (strcasecmp($word, 'if') == 0)
	{
		$sql = trim(substr($sql, 2)); // skip 'if'
		$word = substr($sql, 0, 3); // expecting 'not'

		if (strcasecmp($word, 'not') != 0)
			return FALSE;
			
		$sql = trim(substr($sql, 3)); // skip 'not'
		$word = substr($sql, 0, 6); // expecting 'exists'

		if (strcasecmp($word, 'exists') != 0)
			return FALSE;
			
		$sql = trim(substr($sql, 6)); // skip 'exists'
	}
	
	return $sql;	
}

/**
* Skip word
*/
function skip_word($sql, &$word)
{
	$word = '';
	$sql = trim($sql);
	$len = strlen($sql);
	
	for ($i = 0; $i < $len; $i++)
	{
		$c = $sql[$i];
		
		if (ctype_space($c) || $c == '`' || $c == ',' || $c == ';' || $c == '=' || $c == '(' || $c == ')')
			return trim(substr($sql, $i)); // skip word
		
		$word .= $c;
	}
	
	return $str;	
}

/**
* Skip name or quoted name
*/
function skip_name($sql, &$name)
{
	$name = '';
	$sql = trim($sql);

	if ($sql[0] == '`')
	{
		$sql = substr($sql, 1); // skip '`'
		$pos = stripos($sql, '`'); // find the other '`'
		
		if ($pos === FALSE)
			return FALSE;
			
		$name = substr($sql, 0, $pos);

		return trim(substr($sql, $pos + 1)); // skip quoted name
	}
	
	return skip_word($sql, $name);
}

/**
* Parse table column definition
*/
function parse_column($sql, &$name, &$type)
{
	$sql = skip_name($sql, $name);
	
	if ($sql === FALSE)
		return FALSE;
		
	$sql = skip_word($sql, $type);
	
	if ($sql === FALSE)
		return FALSE;
	
	// skip to the end of column definition
	$open_bracket = FALSE;
	$len = strlen($sql);
	
	for ($i = 0; $i < $len; $i++)
	{
		$c = $sql[$i];
		
		if ($c == '(')
			$open_bracket = TRUE;
		elseif ($c == ')')
		{
			if ($open_bracket)
			{
				$open_bracket = FALSE;
				continue;
			}
			else
				break;
		}
		elseif ($c == ',')
		{
			++$i; // skip ','
			break;
		}
	}
		
	return trim(substr($sql, $i));
}

/**
* Parse all create table definition
*/
function parse_create_tables($sql, &$tables)
{
	$name = '';
	$sql = skip_create_table($sql);
	
	if ($sql === FALSE)
		return FALSE;

	//echo "<br/>";
	//print_r($sql);
	//echo "<br/>";
		
	$sql = skip_name($sql, $name);
	
	if ($sql === FALSE)
		return FALSE;

	if (strlen($name) <= 0)
		return FALSE;
		
	// parse columns
	$pos = stripos($sql, '('); // find '('

	if ($pos === FALSE)
		return FALSE;
		
	$sql = trim(substr($sql, $pos + 1)); // skip '('
	$cols = [];
		
	while (strlen($sql) > 0)
	{
		$colname = '';
		$coltype = '';
		$sql = parse_column($sql, $colname, $coltype);
		
		if ($sql === FALSE)
			return FALSE;
		
		if (strlen($colname) <= 0 || strlen($coltype) <= 0)
			return FALSE;
			
		$cols[$colname] = $coltype;
			
		if ($sql[0] == ')')
		{
			$sql = trim(substr($sql, $pos + 1)); // skip ')'
			break;
		}
	}

	$tables[$name] = $cols;

	// skip to the end of create table definition
	$len = strlen($sql);
	
	for ($i = 0; $i < $len; $i++)
	{
		$c = $sql[$i];
		
		if ($c == ';')
		{
			++$i; // skip ';'
			break;
		}
	}

	$sql = trim(substr($sql, $i));
		
	if (strlen($sql) <= 0)
		return TRUE;
		
	return parse_create_tables($sql, $tables);
}


?>
