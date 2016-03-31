<?php
	$host = "localhost";
	$username = "root";
	$password = "";
	$db_name = "dblogistics";
	$table_cars = "cars";

	$connect_cars = mysqli_connect("$host", "$username", "$password") 
		or die ("Could not connect to sql.");
	mysqli_select_db($connect_cars, $db_name) 
		or die ("Could not select database");
?>