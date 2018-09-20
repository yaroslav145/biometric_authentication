<?php
	$login = $_POST["login"];
	$pass = $_POST["pass"];
	
	$link = mysqli_connect("localhost", "root", "", "templates") or die (mysql_error());
	
	$query = mysqli_query($link, "SELECT * FROM users_table WHERE log = '".$login."' AND pass = '".$pass."'");
	
	if(mysqli_num_rows($query) > 0)
	{	
		$row = mysqli_fetch_array($query);
		
		$buff = $row["id"];
		
		$query = mysqli_query($link, "SELECT * FROM users WHERE external_identifer = '".$buff."'");
		
		if(mysqli_num_rows($query) == 0)
		{
			mysqli_query($link, "INSERT INTO users (external_identifer) VALUES ('".$buff."')");
			
			$query = mysqli_query($link, "SELECT * FROM users WHERE external_identifer = '".$buff."'");
		}
		
		if(mysqli_num_rows($query) > 0)
		{	
			$row = mysqli_fetch_array($query);
			
			session_start();
			$_SESSION['ID'] = $row["id"];
			$_SESSION['fails'] = 0;
			$_SESSION['failsMouse'] = 0;
			
			mysqli_query($link, "INSERT INTO log (date, user_id) VALUES (now(), ".$row["id"].")");
			
			mysqli_close($link);
			
			header("Location: page.php");
		}
	}
?>