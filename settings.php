<html>
  <head>
    <title>Settings</title>
    <style>
      
    </style>
  </head>
 
  <body>
		<h1 align="center">Settings</h1>
   
		<form method="post" action="settingsSet.php">
			<?php
				$link = mysqli_connect("localhost", "root", "", "templates") or die (mysql_error());
				
				$query = mysqli_query($link, "SELECT * FROM settings");
					
				if(mysqli_num_rows($query) > 0)
				{	
					$row = mysqli_fetch_array($query);
					 
					echo '
						Максималное отклонение в процентах:</br>
						<input name="delta" type="text" size="30" value='.$row["max_delta"].'></br></br>
						Количество сохраняемых биометрических шалонов:</br>
						<input name="templ" type="text" size="30" value='.$row["template_count"].'></br></br>
						Время бездействия(в секундах):</br>
						<input name="afk" type="text" size="30" value='.$row["afk_timer"].'></br></br>
					';
				}
			?>
			<input type="submit" size="30"></br></br>
		</form>
  </body>
</html>