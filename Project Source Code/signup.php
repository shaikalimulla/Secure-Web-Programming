<?php
//Name   : Alimulla Shaik
//Purpose: A php page to handle signup functionalities
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/04/22

session_start();
session_regenerate_id();

include_once('/var/www/html/project/project-lib.php');

isset($_REQUEST['s'])?$s=strip_tags($_REQUEST['s']):$s="";
isset($_REQUEST['new_username'])?$new_username=strip_tags($_REQUEST['new_username']):$new_username="";
isset($_REQUEST['new_pass'])?$new_pass=strip_tags($_REQUEST['new_pass']):$new_pass="";
isset($_REQUEST['email'])?$email=strip_tags($_REQUEST['email']):$email="";
$ip_address ='';

connect($db);

num_check($s); 

switch($s){
	case 1:	
		signup($db);
	break;
	case 3:
		update_user_in_db($db, $new_username, $new_pass, $email);
	break;
}

function signup(){

echo "
 	<html>
        <head>
        <title> TravelBook App </title>
        <style>
        th {border: 3px solid chocolate; padding:5px; }
        td {padding: 5px; border: 3px solid darkcyan;}
        </style>

        </head>
        <body background=\"turkey.jpg\">
        <center>
        <h1 style=\"color:blue\"> Travel Book </h1>
        <div align=left>
        <a href=index.php style=\"background-color:yellow\"> HomePage </a>
        </div>
        <hr>

	<form action=signup.php method=post>
	<table>
	<tr>
		<th>User Name </th>
		<td><input type=\"text\" name=\"new_username\" requierd/></td>
	</tr>
	<tr>
		<th>Email ID </th>
		<td><input type=\"text\" name=\"email\" requierd/></td>
	</tr>
	<tr>
		<th>Password </th>
		<td><input type=\"password\" name=\"new_pass\" required/></td>
	</tr>
	</table>
		<input type=\"submit\" name=\"submit\" value=\"SignUp\"/>
		<input type=\"hidden\" name=\"s\" value=\"3\"/>
	</form>
	</div> 
	";
}

function update_user_in_db($db, $new_username, $new_pass, $email){
	$new_username=mysqli_real_escape_string($db,$new_username);
	$new_pass=mysqli_real_escape_string($db,$new_pass);
	$email=mysqli_real_escape_string($db,$email);
	$salt = rand(10,10000);
	$hash_salt=hash('sha256',$salt);
	$hash_pass=hash('sha256',$new_pass.$hash_salt);

	$cnt = 0;		
	$query="select count(*) from users where email=?";
	if($stmt=mysqli_prepare($db,$query))
	{
		mysqli_stmt_bind_param($stmt,"s",$email);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $cnt);
		while(mysqli_stmt_fetch($stmt))
		{
			$cnt = htmlspecialchars($cnt);
		}
		mysqli_stmt_close($stmt);	
	}	

	if($cnt >= 1)
	{	
		header("Location:/project/login.php?s=5");				
		exit;
	} else
	{ 
		/*if($query = mysqli_prepare($db, "SET foreign_key_checks = 0")){
			mysqli_stmt_execute($query);
			mysqli_stmt_close($query);
		}
		*/
		if($query = mysqli_prepare($db, "insert into users set userid='', username=?, password=?, email=?, salt=?"))
    		{
			mysqli_stmt_bind_param($query, "ssss", $new_username,$hash_pass, $email, $hash_salt);
	            	mysqli_stmt_execute($query);
			mysqli_stmt_close($query);
			header("Location:/project/login.php?s=4");
  		}
	  	else {
	  		echo "Error!!! Can not update new user details in database";
			header("Location:/project/index.php");
		}
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
