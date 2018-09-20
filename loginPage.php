<style>
	#okno {
        width: 300px;
        height: 150px;
        text-align: center;
        padding: 15px;
        border: 3px solid #0000cc;
        border-radius: 10px;
        color: #0000cc;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        margin: auto;
        background: #fff;
      }
</style>


<html>
	<head>
		<title>Login page</title>
	</head>
	
	<body>
		<div id="okno">
			Логин<br>
			<form method="post" action="auth.php">
				<input href="#" type="text" size="30" name="login">
				<br>
				Пароль<br>
				<input href="#" type="password" size="30" name="pass">
				<br><br>
				<input type="submit" value="Вход">
			</form>
			
			<?php
				if(($_SERVER['REMOTE_ADDR'] == "127.0.0.1") || ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR']))
				{
					echo '
					<form method="post" action="settings.php">
						<input type="submit" value="Настройки">
					</form>';
				}
			?>
		</div>
	</body>
</html>