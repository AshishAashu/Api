<?php
	define('host','localhost');
	define('user','root');
	define('password','1234');
	define('db','master');

	$conn = new mysqli(host,user,password,db);
	if($conn->connect_error){
		die("Conection got Error:".$conn->connect_error);
	}
?>