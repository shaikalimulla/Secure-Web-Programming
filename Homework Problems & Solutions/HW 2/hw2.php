<?php

//Name   : Alimulla Shaik
//Purpose: A php page that parses 4 files in hw2 or the days of the week and displays content with alternate color.
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/01/25 

isset ($_REQUEST['choice']) ? $choice=$_REQUEST['choice']:$choice="";

echo "
<html>
<head>
<title> TLEN5839 HW2: Alimulla Shaik
</title>
</head>
<body>
";

echo"
<form method=post action=hw2.php>

<select name=\"choice\">
	<option value=\"\"> Select.. </option>
	<option value=\"0\"> 1 </option>
	<option value=\"1\"> 2 </option>
	<option value=\"2\"> 3 </option>
	<option value=\"3\"> 4 </option>
	<option value=\"4\"> Days of the Week </option>
	<option value=\"5\"> List Content of /etc dir </option>
</select>

<input type=\"submit\" value=\"Submit\">
</form>
";

if($choice!=null && !is_numeric($choice)){
	echo "Error!!! Post variable is not numeric <br> \n";
	goto End;
}

function displayDays(){
	$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	$temp = 2;
	for($i=0; $i<count($days);$i++) {
		if($temp > 7)
			$temp = $temp - 7;

		echo "Day $temp of the week is $days[$i] <br> \n";
		$temp++;
	} 
}

function parseFile($id){
	$id = $id+1;
	$filePath = "/var/www/html/hw2";
	$fileName = "$filePath/file$id.txt";
	
	$lines = file($fileName);
	$count = 0;
	
	foreach($lines as $line) {
		if($count >= 100)
			break;

		if(substr($line, 0,1) == "#")
			continue;
		else {
			if($count%2 ==0)
				echo "<div style=\"background-color:red;\"/>";
			else
				echo "<div style=\"background-color:yellow;\"/>";
			echo "$line <br> \n";
			$count++;
		}
	}
}

function listContent() {
	$dirPath = "/etc/";
	if(is_dir($dirPath)) {		
		if($openDir = opendir($dirPath)) {			
			while(($file = readdir($openDir))!=false) {	
				$dirArray[] = $file;			
			}
			closedir($dirName);			
		}
 	}
	sort($dirArray);
		
	echo "<table border=1 cellpadding=5 cellspacing=0>";
	echo "<tr>
		<th> File Name </th> 
		<th> File Type </th> 
	      </tr>";			
	echo "<style>
	th {
		color:grey;
	}
	tr {
		color:blue;
	}	
	</style>";			
				
	for ($index=0; $index < count($dirArray); $index++) {
		$fileName = $dirArray[$index];
		$type = filetype($dirPath . $dirArray[$index]);
		
		echo "<tr>
			<td> $fileName  </td>";
		if($type == "dir")
			$type="directory";
		else if($type == "link")	
			$type="sym link";			
		echo "  <td> $type </td> </tr>";					
	}		
	
	echo "</table>";
}

switch($choice){
	default:
		echo "Please select an option <br> \n";
	break;

	case "0":
	case "1":
	case "2":
	case "3":
		parseFile($choice);
	break;
	case "4":
		displayDays();
	break;
	case "5":
		listContent();
	break;
}

End:
echo "
</body>
</html>
";
?>
