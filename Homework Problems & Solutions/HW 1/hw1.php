<?php

isset ($_REQUEST['i']) ? $i=$_REQUEST['i']:$i="";
isset ($_POST['x']) ? $x=$_POST['x']:$x="";
isset ($_POST['cnt']) ? $cnt=$_POST['cnt']:$cnt="";

echo "
<html> 
<head>
<title> HW 1 Solution </title>
</head>
<body>
";

if($i == null || $cnt == 0) 
{
	$x = rand(0,20);
}

if($x!=$i){
echo "
<form method=post action=hw1.php>
Guess Number:
<input type=\"number\" id=\"i\" name=\"i\" pattern=\"[0-9]\">
<input type=\"hidden\" id=\"x\" name=\"x\" value=\"$x\">
<input type=\"submit\" value=\"Submit\">
<br>
<br>
";
}

while(true) 
{
	if($i == null) 
	{
		echo "Please enter value in text field available <br>";
		break;
	}

	$cnt++;
	
	if($i<0 || $i>20)
	{
		echo "Error!!! You entered:[$i] and value entered should be in range (0,20) <br>";
		
		echo "
		<input type=\"hidden\" name=\"cnt\" value=\"$cnt\">
		";

		break;
	}
	
	if($x == $i)
	{
		echo "<br><br>Bingo!!! You entered:[$i] and it is same number as random number. You guessed number in count:[$cnt] <br>";
		$cnt = 0;
	}
	else if($x > $i)
	{
		echo "Oopss!!! You entered:[$i] and it is less than random number <br>";
	}
	else
	{
		echo "Oopss!!! You entered:[$i] and it is  greater than random number <br>";
	}

	echo "
	<input type=\"hidden\" name=\"cnt\" value=\"$cnt\">
	";

	break;
}

echo "</form></body></html>";
?>
