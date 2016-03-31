<?php
	$host = "localhost";
	$username = "root";
	$password = "";
	$db_name = "dblogistics";
	$table_people = "people";

	$connect_people = mysqli_connect("$host", "$username", "$password") 
		or die ("Could not connect to sql.");
	mysqli_select_db($connect_people, $db_name) 
		or die ("Could not select database");
?>