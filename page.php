<html>
	<head>
		<title>
		main page
		</title>

	</head>

	<body>
	
	<form method="post" action="exit.php">
		<input type="submit" value="Выход">
	</form>


	<canvas id="canvas"></canvas>
	
		<script>
			var canvasPosition = {
				x: canvas.offsetLeft,
				y: canvas.offsetTop
			};
		
			function randomInteger(min, max) 
			{
				var rand = min + Math.random() * (max + 1 - min);
				rand = Math.floor(rand);
				return rand;
			}
			
			function dist(x1, y1, x2, y2)
			{
				dx = x1 - x2;
				dy = y1 - y2;
				
				return Math.sqrt(dx*dx + dy*dy);
			}			
		
			canvas.width = window.innerWidth * 0.95;
            canvas.height = window.innerHeight * 0.95;
		
			var c = document.getElementById("canvas");
			var ctx = c.getContext("2d");
	
			var circX = 200, circY = 200;
			
			ctx.beginPath();
				ctx.arc(circX, circY, 30, 0, 2 * Math.PI);
			ctx.stroke();
		
			canvas.onmousemove = function mouseMove(event) //canvas поменять
			{	
				x = event.pageX - canvasPosition.x; 
				y = event.pageY - canvasPosition.y;
			
				if(dist(x, y, circX, circY) < 30)
				{
					ctx.clearRect(0, 0, canvas.width, canvas.height);
					
					circX = randomInteger(30, canvas.width - 30);
					circY = randomInteger(30, canvas.height - 30);

					ctx.beginPath();
						ctx.arc(circX, circY, 30, 0, 2 * Math.PI);
					ctx.stroke();
				}
			}			
		</script>


	
		<script>
			/*var canvasPosition = {
				x: canvas.offsetLeft,
				y: canvas.offsetTop
			};*/
		
			function randomInteger(min, max) 
			{
				var rand = min + Math.random() * (max + 1 - min);
				rand = Math.floor(rand);
				return rand;
			}
			
			function dist(x1, y1, x2, y2)
			{
				dx = x1 - x2;
				dy = y1 - y2;
				
				return Math.sqrt(dx*dx + dy*dy);
			}			
		
			//canvas.width = window.innerWidth * 0.95;
            //canvas.height = window.innerHeight * 0.95;
		
			//var c = document.getElementById("canvas");
			//var ctx = c.getContext("2d");
			
			var http = new XMLHttpRequest();
			
			var intervalTime = 50;
			var circX = 200, circY = 200;
			var x, y;
			var oldX, oldY;
			var checkX, checkY;
			var CursorMoveDist;
			var t = 0, d, oldCircX, oldCircY;
			var startX, startY, endX, endY, startStatus = false;
			var fourHarmN;
			var moveTime;
			var cMid, cMax;
			var fullLen;
			var firstSpeed;
			
			var c = [];
			var ci = 0;
			
			var j = 0;
			
			var mid=0, max=0, len=0, speed=0, time=0;
		
			var key = {};
			var timerAfterUpKey = 0;
			
			
			for(i = 0; i < 255; ++i)
			{
				key[i] = 
				{
					pushTime: 0,
					timeInDown: 0,
					clickCountWhenDown: 0,
					pauseBeforePress: 0,
					clickCountWhenBeforePress: 0
				};
			}
			
			
			document.onkeydown = function checkKeycode(event)
			{
				var keycode;
				var delta;
				var ms = new Date();
				keycode = event.which;
				
				key[keycode].pushTime = ms.getTime();
				
				delta = ms.getTime() - timerAfterUpKey;
				
				if(delta < 800)
				{
					key[keycode].pauseBeforePress += delta;
					key[keycode].clickCountWhenBeforePress++;
					//console.log(keycode+"  "+key[keycode].pauseBeforePress / key[keycode].clickCountWhenBeforePress);
				}
			}
			
			
			document.onkeyup = function checkKeycode(event)
			{
				var keycode;
				var buff;
				var ms = new Date();
				keycode = event.which;
				
				buff = ms.getTime() - key[keycode].pushTime;
				
				if(buff < 1000)
				{
					key[keycode].timeInDown += buff;
					key[keycode].clickCountWhenDown++;
					//console.log(keycode+"  "+key[keycode].timeInDown / key[keycode].clickCountWhenDown);
				}

				timerAfterUpKey = ms.getTime();
			}
			
		
			window.onmousemove = function mouseMove(event) //canvas поменять
			{	
				oldX = x;
				oldY = y;
				
				//x = event.pageX - canvasPosition.x; 
				//y = event.pageY - canvasPosition.y;
				x = event.pageX; 
				y = event.pageY;
				
				if(startStatus == true)
				{
					c[ci] = x;
					ci++;
					c[ci] = y;
					ci++;
				}
			}
			
			
			setInterval(function() 
			{
				CursorMoveDist = dist(oldX, oldY, x, y);
				
				if(startStatus == false)
				{
					if(CursorMoveDist > 8)
					{
						startStatus = true;
						moveTime = 0;
						fourHarmN = 0;
					}
				}
				else
				{
					if(((CursorMoveDist < 3) || ((checkX == x) && (checkY == y))) && (startStatus == true))
					{
						startStatus = false;	

						var url = "mainScript.php";
						var params = "";
						
						if(ci > 0)
						{
							for(i = 0; i < ci; i+=2)
							{
								params += "X"+(i/2)+"="+c[i]+"&Y"+(i/2)+"="+c[i+1]+"&";
							}
							params += "N="+(fourHarmN / 2)+"&Time="+intervalTime*4+"&fullTime="+moveTime;
							
							http.open("POST", url, true);
							http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							http.send(params);	
						}
						ci = 0;
					}
				}
				
				checkX = x;
				checkY = y;
				
				if((startStatus == true) && (fourHarmN == 0))
				{	
					if(moveTime == intervalTime*4)
					{
						fourHarmN = ci;
					}
				}
				moveTime += intervalTime;
				
			}, intervalTime);
			
				
			setInterval(function() 
			{
				var url = "mainScript.php";
				var params = "";
				var tid = 0, tbd = 0;
				var sendFlag = 0;
			
				for(i = 0; i < 255; ++i)
				{
					tid = 0;
					tbd = 0;
					
					if(key[i].clickCountWhenDown != 0)
					{
						tid = key[i].timeInDown/key[i].clickCountWhenDown;
						sendFlag = 1;
					}
					
					if(key[i].clickCountWhenBeforePress != 0)
						tbd = key[i].pauseBeforePress/key[i].clickCountWhenBeforePress;
					
					if(params != "")
						params += "&";
					
					params += "D"+i+"="+tid+"&BP"+i+"="+tbd;
					
					key[i] =
					{
						pushTime: 0,
						timeInDown: 0,
						clickCountWhenDown: 0,
						pauseBeforePress: 0,
						clickCountWhenBeforePress: 0
					};
				}
				
				if(sendFlag == 1)
				{
					http.open("POST", url, true);
					http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					http.send(params);
				}
				
			}, 6000);
			
			
			http.onreadystatechange = function() 
			{
				if(http.readyState == 4 && http.status == 200) 
				{
					if(http.responseText != "")
						console.log(http.responseText);
					
					if(http.responseText.indexOf("<html>") != -1)
						document.body.innerHTML = http.responseText;
				}
			}
		</script>	
	</body>
</html>