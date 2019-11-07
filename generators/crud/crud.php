<?php
// Harubi CRUD Generator
// By Abdullah Daud
// 6 November 2019

// This is a work in progress.

/**
* Harubi implementation process:
*
* 1. A high-level model is incepted.
* 2. The model is manifested into a relational design.
*    A SQL table is considered as an atomic model.
* 3. A SQL CREATE TABLE script is implemented.
* 4. Harubi table settings are captured.
* 5. Harubi model-actions for CRUD are implemented.
* 
*/

/*
* The CRUD generator will read from a SQL CREATE TABLE script to
* extract table details such as the table name and the coloumns
* name and type. The column type will be translated into harubi's
* 'string', 'integer' or 'float' type. The extracted table details
* are kept in the following format:

	[
		'table_name' => [
			'column_name' => 'type'
		]
	]
   
* The table details will then be analysed for links where the parent-
* child relationship will be applied. See assign_links().
*
* Table convention:
*
* 1. Every table must have an 'id' column.
*
* 2. A table has a parent when one of its column name contains another table name
*    appended with '_id'. A table may have multiple parents.
*
* 3. A table has a child when any of other tables has a column name with the former
*    table name appended with '_id'. A table may have many children.
*
* 4. Column 'name' must be unique.
*   
*/

require_once 'parse_tables.php';

/**
* When $name_id string ends with '_id' then the 'name' part
* will be copied to $name, and TRUE will be returned.
* Otherwise, return FALSE.
*/
function split_name_id($name_id, &$name)
{
	$name = '';
	$pos = strrpos($name_id, '_');

	if ($pos === FALSE)
		return FALSE;
		
	$right = substr($name_id, $pos + 1);
	
	if ($right == 'id')
	{
		$name = substr($name_id, 0, $pos);
		return TRUE;
	}
	
	return FALSE;			
}

/**
* When applicable then assign each table with its parrents and/or children.
* New items '__parents__' and/or '__children__' with an array of table
* names will be added to the applicable table. These are known as table links.
* Parameter $tables contains a list of tables with an associated array of columns.
*
* Every table must have an 'id' column.
*
* A table has a parent when one of its column name contains another table name appended
* with '_id'. A table may have multiple parents.
*
* A table has a child when any of other tables has a column name with the former
* table name appended with '_id'. A table may have many children.
*
*/
function assign_links(&$tables)
{
	$links = [];
	
	foreach ($tables as $name => $cols)
	{
		foreach ($cols as $colname => $coltype)
		{
			$pname = '';
			
			if (split_name_id($colname, $pname))
			{
				if ($pname != $name && array_key_exists($pname, $tables))
				{
					if (!array_key_exists($pname, $links))
						$links[$pname] = ['__parents__' => [], '__children__' => []];
						
					$links[$pname]['__children__'][] = $name;

					if (!array_key_exists($name, $links))
						$links[$name] = ['__parents__' => [], '__children__' => []];
						
					$links[$name]['__parents__'][] = $pname;
				}
			}
		}	
	}
	
	foreach ($links as $name => $rels)
	{
		if (count($rels['__parents__']) > 0)
			$tables[$name]['__parents__'] = $rels['__parents__'];

		if (count($rels['__children__']) > 0)
			$tables[$name]['__children__'] = $rels['__children__']; 
	}
}

$tables = [];
$sql = file_get_contents("user_role.install.sql");
parse_create_tables($sql, $tables);
assign_links($tables);
echo "<pre>";
print_r($tables);
echo "</pre>";

?>
