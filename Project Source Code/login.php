<?php

//Name   : Alimulla Shaik
//Purpose: A login php page to accept user name and password
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/04/22 

session_start();
session_regenerate_id();
#include_once('/var/www/html/project/header.php');

isset($_REQUEST['s'])?$s=strip_tags($_REQUEST['s']):$s="";
num_check($s);

echo "
	<html>
        <head>
        <title> TravelBook App </title>
        <style>
        th {border: 3px solid chocolate; padding:5px; }
        td {padding: 5px; border: 3px solid darkcyan;}
       
        </style>

        </head>
        <body background=\"homepage.jpg\">
        <center>
        <h1 style=\"color:blue\"> Travel Book </h1>
        <div align=left>
        <a href=index.php style=\"background-color:yellow\"> HomePage </a>
        </div>
        <hr>
";

switch($s)
{
	case 2:
		create_login_form();
	break;

	case 4:
	        echo "
		<h2 style=\"background-color:grey\">
	        Thank you so much for your interest!!! Please login to continue... </h2>
        	</center></body></html>
	        ";
		create_login_form();
	break;

	case 5:
		echo "
		<h2 style=\"background-color:grey\">
		Opss!!! Seems like you have already created an account with this email id. Please login to continue... </h2>
		";
		create_login_form();
	break;
	
	case 91:
                echo "
                <h2 style=\"background-color:grey\">
                Thank you so much for trying out our application! Please visit again! </h2>
                </center></body></html>
                ";
                create_login_form();
	break;
	
	default:
		create_login_form();
	break;
}

function create_login_form()
{
	if(!isset($_SESSION['authenticated']))
	{
		echo "
		<div align=center>
		<h2 style=\"color:maroon\">Please login here...</h2>
		<form action=delegate.php method=post>
		<table>
		<tr>
			<th>User Name </th>
			<td><input type=\"text\" name=\"user_name\" requierd/></td>
		</tr>
		<tr>
			<th>Password </th>
			<td><input type=\"password\" name=\"pass\" required/></td>
		</tr>
		</table>

		<input type=\"submit\" name=\"submit\" value=\"Login\"/>
		<td><input type=\"hidden\" name=\"s\" value=\"2\"/>	
		</form>
		</table>
		</div>
		</body>
		</html> 
		";
	}
	else {
		header("Location:/project/delegate.php");	
	}
}
	
function num_check($var) { 
	if ($var != null) {
		if(!is_numeric($var)) {
			print "<b> [ERROR]: Invalid Syntax. </b> <br>";
			exit;
		}
	}
}

?>

