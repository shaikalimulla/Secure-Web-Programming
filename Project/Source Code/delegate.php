<?php
//Name   : Alimulla Shaik
//Purpose: A php page to handle signup and login functionalities including redirecting to different pages
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/04/22

session_start();
session_regenerate_id();
include_once('/var/www/html/project/project-lib.php');

isset($_REQUEST['s'])?$s=strip_tags($_REQUEST['s']):$s="";
isset($_REQUEST['user_name'])?$user_name=strip_tags($_REQUEST['user_name']):$user_name="";
isset($_REQUEST['pass'])?$pass=strip_tags($_REQUEST['pass']):$pass="";
isset($_REQUEST['redirect'])?$redirect=strip_tags($_REQUEST['redirect']):$redirect="";
isset($_REQUEST['new_username'])?$new_username=strip_tags($_REQUEST['new_username']):$new_username="";
isset($_REQUEST['new_pass'])?$new_pass=strip_tags($_REQUEST['new_pass']):$new_pass="";
isset($_REQUEST['email'])?$email=strip_tags($_REQUEST['email']):$email="";
$ip_address ='';

connect($db);

num_check($s); 

switch($s){
	case 1:	
		route_to_signup();
	break;

	case 2:
		route_to_login($db, $user_name, $pass);
	break;

	case 92:
		if(isset($_SESSION['userid']) && $_SESSION['userid'] == 9){
               	        display_all_users($db);
                } else
       	                echo "Unauthorized user!!! Do not have access to use this functionality";
	break;

	case 96:
		if(isset($_SESSION['userid']) && $_SESSION['userid'] == 9){
                        display_all_incorrect_login_attempts($db);
                 } else
                        echo "Unauthorized user!!! Do not have access to use this functionality";
	break;

	case 93:
        	update_user_password();
	break;
		
	case 94:
		update_password_in_db($db, $new_username, $new_pass);
	break;

	default:
		route_to_login($db, $user_name, $pass);
	break;
}

function route_to_signup(){
        if(isset($_SESSION['authenticated']) && ($_SESSION['authenticated']="yes" ))
        {
		echo "<h2> You are already logged in and session is in progress!!!";
		header("Location:/project/error.php");	
	}
	else {
		header("Location:/project/signup.php?s=1");
	}
}

function route_to_login($db, $user_name, $pass){
	if(isset($_SESSION['authenticated']) && ($_SESSION['authenticated']="yes" ))
	{
		#echo "<h2> Session Expired... Please login to continue</h2>";	
		header("Location:/project/user_profile.php");		
	}
	else {
		if($user_name == null)
		{	
			header("Location:/project/login.php");
		}
	        $ip_address = $_SERVER['REMOTE_ADDR'];

        	$white_listed_ip_addresses = white_list();
		$attempt_count = incorrect_attempts_count($db,$ip_address);
		if((!($is_ip_white_listed = in_array($ip_address, $white_listed_ip_addresses))) && ($attempt_count >= 5))
        	{
        		update_current_login_in_db($db, $user_name, "failure");
			header("Location:/project/login.php");        	
        	}
	        else {
			authenticate($db, $user_name, $pass);        		
			checkAuth();		
		}
	}
}

function incorrect_attempts_count($db, $ip_address)
{	
	$cnt = 0;
	if($query = mysqli_prepare($db, "select count(*) from login where action='failure' and date > DATE_SUB(NOW(),INTERVAL 1 HOUR) and ip=?"))
	{
		mysqli_stmt_bind_param($query, "s", $ip_address);
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $cnt);
		while(mysqli_stmt_fetch($query))
		{
			$cnt = htmlspecialchars($cnt);
		}
		mysqli_stmt_close($query);  		
	}
	else
	{		
		echo "Error!!! Can not find incorrect attempts in database"; 
	 	exit;
	}

	return $cnt;
}

function update_current_login_in_db($db, $user_name, $action)
{
	$ip_address = $_SERVER['REMOTE_ADDR'];
	
	if($query = mysqli_prepare($db, "insert into login set loginid='', date=NOW(), ip=?, user=?, action=?"))
	{
		mysqli_stmt_bind_param($query, "sss", $ip_address, $user_name, $action);
		mysqli_stmt_execute($query);
		mysqli_stmt_close($query);		
	}	
	else
	{
		echo "Error!!! Can not update database";	 	
	}
}

function display_all_incorrect_login_attempts($db)
{	
	create_header();
	if($query = mysqli_prepare($db,"select ip, count(*) from login where action='failure' GROUP BY ip"))
	{				
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $ip_address, $cnt);	
		echo "
		<div align=center>
		<h2> Incorrect Login Attempts Details are listed below...</h2>
		<table>
		<th>IP Address </th> 
		<th>Number of Incorrect Login Attempts </th> ";
		
		while(mysqli_stmt_fetch($query))
		{
			$ip_address = htmlspecialchars($ip_address);
			$count = htmlspecialchars($cnt);
			echo "
			<tr>
			<td style=\"color:maroon\">$ip_address </td>
			<td style=\"color:maroon\">$cnt </td>
			</tr>";
		}
		echo "</table>";
		mysqli_stmt_close($query);
	}
	else
	{
		echo "Error!!! Can not find incorrect login attempts in database"; 
		exit;
	}
}

function display_all_users($db)
{
	create_header();	
	if($query = mysqli_prepare($db, "select username from users"))
        {
                mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $username);
		echo "
		<div align=center>
		<h2> All users are listed below...</h2>
		<table>
		<th>User Name </th>
		";
                
		while(mysqli_stmt_fetch($query))
		{
			$username = htmlspecialchars($username);
                	echo "<tr><td style=\"color:maroon\">$username</td></tr>";
		}
		echo "</table>";
		mysqli_stmt_close($query);
        }
}

function update_user_password(){
	create_header();
	echo "<div align=center>
		<h2 style=\"color:maroon\"> Update your password here... </h2>
		<form action=delegate.php method=post>
		<table>
		<tr>
			<th>Username</th>
			<td><input type=\"text\" name=\"new_username\" required/></td>
		</tr>
		<tr>
			<th>New Password</th>
			<td><input type=\"password\" name=\"new_pass\" required/></td>
		</tr>
		</table>
		<input type=\"submit\" name=\"submit\" value=\"submit\"/>
		<input type=\"hidden\" name=\"s\" value=\"94\"/>
		</form></div> 
	";
}

function update_password_in_db($db, $new_username, $new_pass){
	create_header();
	$new_username=mysqli_real_escape_string($db,$new_username);
	$new_pass=mysqli_real_escape_string($db,$new_pass);
				
	$salt = rand(10,10000);
	$hash_salt=hash('sha256',$salt);
	$hash_pass=hash('sha256',$new_pass.$hash_salt);
	
	if($query = mysqli_prepare($db, "update users set salt =?, password=? where username=?"))
    	{
		mysqli_stmt_bind_param($query, "sss", $hash_salt ,$hash_pass, $new_username);
            	mysqli_stmt_execute($query);
	        mysqli_stmt_close($query);
        	echo "<h2 align=center style=\"background-color:grey\">Password updation is completed for user:[ " . $new_username."] </h2>";
  	}
  	else
  		echo "Error!!! can not update new password in database";		
}

function create_header(){
	echo "
        <html>
        <head>
        <title> TravelBook App </title>
        <style>
        th {border: 3px solid chocolate; padding:5px; }
        td {padding: 5px; border: 3px solid darkcyan;}
        input[type=\"text\"] {width: 300px; height:20px;}
	input[type=\"password\"] {width: 300px; height:20px;}

	form input {border-radius: 7.5px;}
	.wrapper { padding-left: 25px; padding-top: 20px}

        </style>

        </head>
        <body background=\"san.jpg\">

        <center>
        <h1 style=\"color:blue\"> Travel Book </h1>
        <div align=left>
        <a href=index.php style=\"background-color:yellow\"> HomePage </a>
        </div>
        <hr>
	</center>
        <div align=right>
        <a href=user_profile.php?s=90 style=\"background-color:gold\"> Logout </a>
        </div>
	<div class=\"wrapper\">
	<form class=\"form\" action=user_profile.php method=post>
	<input type=\"submit\" value=\"Go back to your Profile Page\"/>
	</form>
	</div>		
	</body></html>
	";
}

function authenticate($db,$user_name,$pass)
{
	$query="select userid, email, password, salt from users where username=?";
	if($stmt=mysqli_prepare($db,$query))
	{
		mysqli_stmt_bind_param($stmt,"s",$user_name);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt,$userid,$email,$password,$salt);
		while(mysqli_stmt_fetch($stmt))
		{
			$userid=$userid;
			$password=$password;
			$salt=$salt;
			$email=$email;
		}
		mysqli_stmt_close($stmt);
		$epass=hash('sha256',$pass.$salt);
		if($epass==$password)
		{
			session_regenerate_id();
			$_SESSION['userid']=$userid;
			$_SESSION['user_name']=$user_name;
			$_SESSION['email']=$email;
			$_SESSION['authenticated']="yes";
			$_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
			$_SESSION['HTTP_USER_AGENT']=md5($_SERVER['SERVER_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
			$_SESSION['created']=time();
			$_SESSION['created']=time();
			update_current_login_in_db($db, $user_name, "success");
			header("Location:/project/user_profile.php");
		}
		else
		{
			echo "Failed to Login";
			update_current_login_in_db($db, $user_name, "failure");
			error_log("Error: Travel app has failed login from " . $_SERVER['REMOTE_ADDRESS'], 0);
	
			header("Location:/project/login.php");
			exit;
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
