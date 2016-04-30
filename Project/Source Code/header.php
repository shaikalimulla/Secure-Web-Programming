<?php
//Name   : Alimulla Shaik
//Purpose: A php page to display header with different options of travel application
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/04/22 

echo "
	<html>
	<head>	
	<title> TravelBook App </title>
	</head>
	<body background=\"cool.jpg\">
	<style>
	table,th, td {font-family: Arial; font-size: 16pt}
	</style>
	
	<center>
	<h1 style=\"color:blue\"> Travel Book </h1>
	<hr>
	<h2>
	<table>
	    <tr>
	      <td align=\"right\" style=\"color:red\">	      
		Do not have an account? </td>
		<td> <a href=delegate.php?s=1 style=\"color:green\"> SignUP </a> <br>
		</td></tr>
	    <tr>
		<td align=\"right\" style=\"color:red\">
		Already have an account? </td>
		<td> <a href=delegate.php?s=2 style=\"color:green\"> Login </a>
		</td></tr>
	</table>	
	</h2>
";

?>
