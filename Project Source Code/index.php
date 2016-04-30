<?php
//Name   : Alimulla Shaik
//Purpose: A php page to handle and display home page functionalities
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/04/22

session_start();
session_regenerate_id();
#include_once('/var/www/html/project/project-lib.php');
include_once('/var/www/html/project/header.php');

isset($_REQUEST['s'])?$s=strip_tags($_REQUEST['s']):$s="";
#connect($db);

num_check($s); 

echo "
<html>
<head>
<title> TravelBook App </title>
</head>
<body background=\"homepage.jpg\">
</body>
</html>
";



function num_check($var) { 
	if ($var != null) {
		if(!is_numeric($var)) {
			print "<b> [ERROR]: Invalid Syntax. </b> <br>";
			exit;
		}
	}
}

?>
