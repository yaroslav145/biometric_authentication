<?php
	ignore_user_abort(true);
	session_start();
	
	$link = mysqli_connect("localhost", "root", "", "templates") or die (mysql_error());
	
	
	$query = mysqli_query($link, "SELECT * FROM settings");
			
	if(mysqli_num_rows($query) > 0)
	{	
		$row = mysqli_fetch_array($query);
		 
		$deviation = $row["max_delta"];
		$deviation = (100 - $deviation)/100;
		$templ_count = $row["template_count"];
		$afk_timer = $row["afk_timer"];
	}

	
	if(isset($_SESSION['last_time']))
	{
		if(microtime(true) - $_SESSION['last_time'] > $afk_timer)
		{
			$_SESSION['n_e'] = 0;
			$_SESSION['mid'] = 0;
			$_SESSION['max'] = 0;
			$_SESSION['len'] = 0;
			$_SESSION['spd'] = 0;
			$_SESSION['time'] = 0;
			
			for($i = 0; $i < 255; $i++)
			{
				if(!isset($_SESSION['numDown'.$i.'']))
				{
					$_SESSION['numDown'.$i.''] = 0;
					$_SESSION['downTimer'.$i.''] = 0;
					$_SESSION['numBefore'.$i.''] = 0;
					$_SESSION['beforeTimer'.$i.''] = 0;
				}
			}
		}
	}
	$_SESSION['last_time'] = microtime(true);
	
	function dist($x1, $y1, $x2, $y2)
	{
		$dx = $x1 - $x2;
		$dy = $y1 - $y2;
		
		if(($dx == 0) && ($dy == 0))
			return 1;
		
		return sqrt($dx*$dx + $dy*$dy);
	}		

	$query = mysqli_query($link, "SELECT * FROM keyboard_template WHERE user_id = '".$_SESSION['ID']."'");
	
	if(mysqli_num_rows($query) == 0)
	{
		for($i = 0; $i < 255; $i++)
		{
			mysqli_query($link, "INSERT INTO keyboard_template (user_id, key_id, push_time, pause_time, down_count, pause_count) VALUES ('".$_SESSION['ID']."', '".$i."', 0, 0, 0, 0)");
		}
	}	
	
	
	$sessionSummD = 0;
	$sessionSummB = 0;
	$countD = 0;
	$countB = 0;
	for($i = 0; $i < 255; $i++)
	{
		if(isset($_POST['D'.$i.'']))
		{
			$keyDownTime[$i] = $_POST['D'.$i.''];
			$keyBeforePressTime[$i] = $_POST['BP'.$i.''];

			if(!isset($_SESSION['numDown'.$i.'']))
			{
				$_SESSION['numDown'.$i.''] = 0;
				$_SESSION['downTimer'.$i.''] = 0;
				$_SESSION['numBefore'.$i.''] = 0;
				$_SESSION['beforeTimer'.$i.''] = 0;
			}
			
			if($keyDownTime[$i] != 0)
			{
				$_SESSION['numDown'.$i.'']++;
				$_SESSION['downTimer'.$i.''] += $keyDownTime[$i];
				
				if($_SESSION['numDown'.$i.''] >= $templ_count * 3)
				{
					echo "TTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTemplate",$_SESSION['numDown'.$i.''],"    ",$_SESSION['downTimer'.$i.''] ,"\n";
					$_SESSION['numDown'.$i.''] /= 2;
					$_SESSION['downTimer'.$i.''] /= 2;
				}
			}
			
			if($keyBeforePressTime[$i] != 0)
			{
				$_SESSION['numBefore'.$i.'']++;
				$_SESSION['beforeTimer'.$i.''] += $keyBeforePressTime[$i];
				
				if($_SESSION['numBefore'.$i.''] >= $templ_count * 3)
				{
					$_SESSION['numBefore'.$i.''] /= 2;
					$_SESSION['beforeTimer'.$i.''] /= 2;
				}
			}
			
			mysqli_query($link, "UPDATE keyboard_template SET down_count=down_count*0.5, pause_count=pause_count*0.5, push_time=push_time*0.5, pause_time=pause_time*0.5 WHERE down_count >= 40");
			
			//echo $i, "   ", $_SESSION['downTimer'.$i.'']/$_SESSION['numDown'.$i.''], "   ", $_SESSION['beforeTimer'.$i.'']/$_SESSION['numBefore'.$i.''], "\n";
			
			mysqli_query($link, "SELECT * FROM keyboard_template WHERE key_id=".$i." AND user_id=".$_SESSION['ID']."");
			
			if(mysqli_num_rows($query) > 0)
			{	
				$row = mysqli_fetch_array($query);
				
				if(($_SESSION['numDown'.$i.''] != 0) && ($row["down_count"] != 0) && ($row["push_time"] != 0) && ($_SESSION['downTimer'.$i.''] != 0))
				{
					$sessionBuffD = ($_SESSION['downTimer'.$i.''] / $_SESSION['numDown'.$i.'']);
					$sessionBuffD2 = ($row["push_time"] / $row["down_count"]);

					if($sessionBuffD >= $sessionBuffD2)
					{
						$sessionSummD += $sessionBuffD2 / $sessionBuffD;
					}
					else
					{
						$sessionSummD += $sessionBuffD / $sessionBuffD2;
					}
					$countD++;
					
					
					//echo $i, "   ",(($_SESSION['downTimer'.$i.''] / $_SESSION['numDown'.$i.'']) / ($row["push_time"] / $row["down_count"])), "\n";
				}
				
				if(($_SESSION['beforeTimer'.$i.''] != 0) && ($_SESSION['numBefore'.$i.''] != 0) && ($row["pause_time"] != 0) && ($row["pause_count"] != 0))
				{
					$sessionBuffB = ($_SESSION['beforeTimer'.$i.''] / $_SESSION['numBefore'.$i.'']);
					$sessionBuffB2 = ($row["pause_time"] / $row["pause_count"]);

					if($sessionBuffB >= $sessionBuffB2)
					{
						$sessionSummB += $sessionBuffB2 / $sessionBuffB;
					}
					else
					{
						$sessionSummB += $sessionBuffB / $sessionBuffB2;
					}
					$countB++;
					//$sessionBuffB = ($_SESSION['beforeTimer'.$i.''] / $_SESSION['numBefore'.$i.'']) - ($row["pause_time"] / $row["pause_count"]);
					//$sessionBuffB *= $sessionBuffB;
					//$sessionSummB += $sessionBuffB;
					//$countB++;
					
					
					//$checker += (($_SESSION['beforeTimer'.$i.''] / $_SESSION['numBefore'.$i.'']) / ($row["pause_time"] / $row["pause_count"]));
					//$checkerCount++;
				}
				
				//echo $i," %%%  ",$sessionBuffD,"  %%%  ", $sessionBuffB, "\n";
			}
		}
	}
	
	/*if($countB != 0)
		$sessionSummB = sqrt($sessionSummB) / $countB;
	
	if($countD != 0)
		$sessionSummD = sqrt($sessionSummD) / $countD;*/
	
	if($countB != 0)
		$sessionSummB = $sessionSummB / $countB;
	
	if($countD != 0)
		$sessionSummD = $sessionSummD / $countD;
	
	$trueFlag = 1;
	// 14 60    18 70   22 80
	
	if(($countD > 14) && (($sessionSummB < $deviation - 0.23) || ($sessionSummD < $deviation - 0.23)))
	{
		$trueFlag = 0;
	}
	
	if(($countD > 18) && (($sessionSummB < $deviation - 0.12) || ($sessionSummD < $deviation - 0.12)))
	{
		$trueFlag = 0;
	}
	
	if(($countD > 22) && (($sessionSummB < $deviation - 0.07) || ($sessionSummD < $deviation - 0.07)))
	{
		$trueFlag = 0;
	}
	
	if(($countD > 30) && (($sessionSummB < $deviation) || ($sessionSummD < $deviation)))
	{
		$trueFlag = 0;
	}
	
	//echo " # ",$countB, "     ",$countD , " # \n";
	//echo " # ",$sessionSummB, "     ",$sessionSummD , " # \n";
	
	echo $sessionSummB,"\n";
	
	if($trueFlag == 1)
	{
			for($i = 0; $i < 255; $i++)
			{		
				if(isset($_POST['D'.$i.'']))
				{
					if($keyDownTime[$i] > 0)
						mysqli_query($link, "UPDATE keyboard_template SET  push_time=push_time+".$keyDownTime[$i].", down_count=down_count+1 WHERE key_id=".$i." AND user_id=".$_SESSION['ID']."");
						
					if($keyBeforePressTime[$i] > 0)
						mysqli_query($link, "UPDATE keyboard_template SET pause_time=pause_time+".$keyBeforePressTime[$i].", pause_count=pause_count+1 WHERE key_id=".$i." AND user_id=".$_SESSION['ID']."");
				}
			}
	}
	else
	{
		if($_SESSION['fails'] == 3)
		{
			session_destroy();
			header("Location: LoginPage.php");
		}
		
		$_SESSION['fails']++;
	}
	
	//mysqli_query($link, "INSERT INTO keyboard_template (user_id, key, push_time, pause_time, next_key) VALUES ('".$_SESSION['ID']."', '".$i."', '".$_SESSION['downTimer'.$i.'']."', '".$_SESSION['beforeTimer'.$i.'']."', 0)");
	
	
	$j = 0;
	while(true)
	{
		if(!isset($_POST['X'.$j.'']))
			break;
		
		$x[$j] = $_POST['X'.$j.''];
		$y[$j] = $_POST['Y'.$j.''];
	
		$j++;
	}
	$j--;
	
	if($j != -1)
	{
		if(isset($_POST['N']))
		{
			$N = $_POST['N'];
			$timeHarm = $_POST['Time'];
			$fullTime = $_POST['fullTime'];
		}
		
		$d = dist($x[0], $y[0], $x[$j], $y[$j]);
		
		$cMax = 0;
		$cMid = 0;
		$fullLen = 0;
		$firstSpeed = 0;
		for($i = 0; $i < $j; $i++)
		{
			$buff = abs(($y[0] - $y[$j])*$x[$i] - ($x[0] - $x[$j])*$y[$i] + $x[0]*$y[$j] - $y[0]*$x[$j]) / $d;
			
			$cMid += $buff;
			
			if($buff > $cMax)
			{
				$cMax = $buff;
			}
			
			if($i >= 1)
			{
				$fullLen += dist($x[$i-1], $y[$i-1], $x[$i], $y[$i]);
									
				if(($i == $N) && ($timeHarm != 0) && ($N != 0))
				{
					$firstSpeed = ($fullLen / $d) / $timeHarm;
				}
			}
		}
		
		$moveTime = $fullTime / log($d + 1, 2);
		
		if($N == 0)
		{
			$firstSpeed = ($fullLen / $d) / $fullTime;
		}
		
		
		$cMid /= ($j + 1);
		$cMid /= $d;
		$cMax /= $d; 
		$fullLen /= $d;
		
		
		if(!isset($_SESSION['mid']))
		{
			$_SESSION['n_e'] = 1;
			$_SESSION['mid'] = $cMid;
			$_SESSION['max'] = $cMax;
			$_SESSION['len'] = $fullLen;
			$_SESSION['spd'] = $firstSpeed;
			$_SESSION['time'] = $moveTime;
		}
		else
		{
			$_SESSION['n_e']++;
			$_SESSION['mid'] += $cMid;
			$_SESSION['max'] += $cMax;
			$_SESSION['len'] += $fullLen;
			$_SESSION['spd'] += $firstSpeed;
			$_SESSION['time'] += $moveTime;
		}
		
		$m1 = $_SESSION['mid']/$_SESSION['n_e'];
		$m2 = $_SESSION['max']/$_SESSION['n_e'];
		$m3 = $_SESSION['len']/$_SESSION['n_e'];
		$m4 = $_SESSION['spd']/$_SESSION['n_e'];
		$m5 = $_SESSION['time']/$_SESSION['n_e'];
		$m6 = $_SESSION['n_e'];
		
		//echo "\n\n\n";
		//echo $cMid, "  ", $cMax, "  ", $fullLen, "  ", $firstSpeed, "  ", $moveTime. "\n --  \n";
		//echo $m1, "  ", $m2, "  ", $m3, "  ", $m4, "  ", $m5. "  ", $m6, "\n";
		
		$delta = 0;
		
		$query = mysqli_query($link, "SELECT avg(move_time), avg(mid_deviation), avg(max_deviation), avg(full_len), avg(first_speed) FROM mouse_template WHERE user_id = '".$_SESSION['ID']."' group by user_id");
		
		if(mysqli_num_rows($query) > 0)
		{	
			$row = mysqli_fetch_array($query);
			
			$d1 = $row["avg(mid_deviation)"];
			$d2 = $row["avg(max_deviation)"];
			$d3 = $row["avg(full_len)"];
			$d4 = $row["avg(first_speed)"];
			$d5 = $row["avg(move_time)"];
			
			if($m1 != 0)
			{
				if($m1 >= $d1)
					$delta += $d1/$m1;
				else
					$delta += $m1/$d1;
				
				if($m2 >= $d2)
					$delta += $d2/$m2;
				else
					$delta += $m2/$d2;
				
				if($m3 >= $d3)
					$delta += $d3/$m3;
				else
					$delta += $m3/$d3;
				
				if($m4 >= $d4)
					$delta += $d4/$m4;
				else
					$delta += $m4/$d4;
				
				if($m5 >= $d5)
					$delta += $d5/$m5;
				else
					$delta += $m5/$d5;
				
				$delta /= 5;
				
				echo "   ##  ", $delta;
			}
		}
		
		
		if($_SESSION['n_e'] >= 15)
		{
			if(($delta > $deviation) || ($delta == 0))
			{
				$_SESSION['failsMouse'] = 0;
				
				mysqli_query($link, "INSERT INTO mouse_template(user_id, move_time, mid_deviation, max_deviation, full_len, first_speed) VALUES ('".$_SESSION['ID']."', '".$m5."', '".$m1."', '".$m2."', '".$m3."', '".$m4."')");
			}
			else
			{
				$_SESSION['failsMouse']++;
					
				if($_SESSION['failsMouse'] > 1)
				{
					session_destroy();
					header("Location: LoginPage.php");
				}
			}
			
			$_SESSION['n_e'] = 0;
			$_SESSION['mid'] = 0;
			$_SESSION['max'] = 0;
			$_SESSION['len'] = 0;
			$_SESSION['spd'] = 0;
			$_SESSION['time'] = 0;
			
			$query = mysqli_query($link, "SELECT * FROM mouse_template WHERE user_id = '".$_SESSION['ID']."'");
			
			if(mysqli_num_rows($query) > $templ_count*2)
			{	
				$query = mysqli_query($link, "DELETE FROM mouse_template WHERE user_id = '".$_SESSION['ID']."' ORDER BY id LIMIT 1");		
			}
		}
	}
?>