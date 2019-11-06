<?php
// Harubi CRUD Generator
// By Abdullah Daud
// 6 November 2019

// This is a work in progress.

require_once 'parse_tables.php';

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
