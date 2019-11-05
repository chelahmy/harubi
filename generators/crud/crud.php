<?php
// Harubi CRUD Generator
// By Abdullah Daud
// 6 November 2019

// This is a work in progress.

require_once 'parse_tables.php';

$tables = [];
$sql = file_get_contents("user_role.install.sql");
parse_create_tables($sql, $tables);
echo "<pre>";
print_r($tables);
echo "</pre>";

?>
