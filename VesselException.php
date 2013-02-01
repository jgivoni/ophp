<?php

namespace Ophp;

class VesselException extends Exception 
{ 
	var $pars = array();

	function __construct($msg = '', $code = 0, Exception $previous = null, $pars = array()) { 
		parent::__construct($msg, $code, $previous); 
		$this->pars = $pars;
	}
} 

// How I want error/debugging to be displayed:
/* When a recordset couldn't be retrieved because the connection to the database couldn't be established:

Exception: Couldn't load user list (index.php, line 13)

Exception: Couldn't load record set (class.User.php, line 143)
	- resource type: "user"
	- offset: 0
	- limit 50
	- keyword: "bent"
	
Exception: Couldn't execute SQL statement (class.DatabaseInterface.php, line 75)
	- sql: "SELECT * FROM users WHERE name LIKE "%bent%" LIMIT 0, 50"
	
Exception: Couldn't open connection to database (class.DatabaseInterface.php, line 24)
	- host: "localhost"
	- user: "root"
	- pwd: "<undisclosed>"
	- mysql_error: "Access denied for user 'root'@'localhost' (using password: YES)"
*/

?>