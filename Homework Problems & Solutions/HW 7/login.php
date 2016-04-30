<?php

//Name   : Alimulla Shaik
//Purpose: A login php page to accept user name and password
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/03/14 

session_start();
#include_once('/var/www/html/hw7/header.php');
if(!isset($_SESSION['authenticated'])){

	echo "<div align=center><table>
	<form action=add.php method=post>
	<tr>
		<td>User Name: </td>
		<td><input type=\"text\" name=\"user_name\" requierd/></td>
	</tr>
	<tr>
		<td>Password: </td>
		<td><input type=\"password\" name=\"pass\" required/></td>
	</tr>
	<tr>
		<td><input type=\"submit\" name=\"submit\" value=\"Login\"/></td>
	</tr>
	<tr>
		<td><input type=\"hidden\" name=\"s\" value=\"5\"/></td>
	</tr>
	</form>
	</table>
	</div> 
	";
}
else {
	header("Location:/hw7/add.php");	
}
?>

