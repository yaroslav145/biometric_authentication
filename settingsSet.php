<?php
	if(($_SERVER['REMOTE_ADDR'] == "127.0.0.1") || ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR']))
	{
		$link = mysqli_connect("localhost", "root", "", "templates") or die (mysql_error());
	
		$query = mysqli_query($link, "UPDATE settings SET max_delta=".$_POST["delta"].", template_count=".$_POST["templ"].", afk_timer=".$_POST["afk"]."");
		
		header("Location: loginPage.php");
	}
?>