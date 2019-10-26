<?php

// Use PHP development server
// $ php -S localhost:8000
// and browse http://localhost:8000

require 'harubi/harubi.php';

harubi();

beat('system', 'gettime', function ()
{	
	return respond_ok(['time' => time()]);
});

beat(NULL, NULL, function() {
	echo "Welcome to harubi!<br>";
	$link = "http://$_SERVER[HTTP_HOST]?model=system&action=gettime";
	echo "Try <a href=\"$link\">$link</a>";
});

?>
