<html>
	<head>
		<title>Sort</title>
	</head>
	
	<body>
		<canvas id="canvas" width="1300" height="600" style="border:1px solid #000000;"></canvas>
	</body>
	
	<script>
		var m = {};
		var n = 333;
		var i = 0;
		var buff;
		var min, minp;
		var swapp = 0;
	
		function getRandomInt(min, max) 
		{
			return Math.floor(Math.random() * (max - min)) + min;
		}
	
		for(i = 0; i < n; ++i)
			m[i] = getRandomInt(1, 500);
	
		c = document.getElementById("canvas");
		ctx = c.getContext("2d");
			
		ctx.font = "13px Arial";
		
		<?php
			$link = mysqli_connect("localhost", "root", "", "templates") or die (mysql_error());
			
			$query = mysqli_query($link, "SELECT * FROM keyboard_template WHERE user_id=4");
			$query2 = mysqli_query($link, "SELECT * FROM keyboard_template WHERE user_id=5");
			
			$rn = mysqli_num_rows($query);
			
			$i = 0;
			$j = 0;
			while($rn != $i)
			{	
				$row = mysqli_fetch_array($query);
				$row2 = mysqli_fetch_array($query2);
				
				$buff = 0;
				if($row["down_count"] != 0)
				{
					$buff = $row["push_time"]/$row["down_count"];
				}

				$buff2 = 0;
				if($row2["down_count"] != 0)
				{
					$buff2 = $row2["push_time"]/$row2["down_count"];
				}
				
				//echo "ctx.fillRect(".$i." * 3, 0, 2, ".$buff.");","\n";
				
				if(($buff !=0) && ($buff2 !=0))
				{
					$j++;
					
					echo "ctx.fillStyle = 'blue';
						  ctx.fillRect(".$j." * 30, 34, 6, ".$buff."); 
						  ctx.fillStyle = 'red';
						  ctx.fillRect(".$j." * 30 + 7, 34, 6, ".$buff2.");
						  ctx.strokeText('".chr($row["key_id"])."', ".$j." * 30, 14);
						  ctx.strokeText('".$row["key_id"]."', ".$j." * 30, 29);","\n";
					
				}
				$i++;
			}
		?>
	 </script>
</html>

