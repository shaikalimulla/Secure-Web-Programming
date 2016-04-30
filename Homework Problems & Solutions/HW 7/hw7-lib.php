<?php
//Name   : Alimulla Shaik
//Purpose: Library file to connect to database
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/03/14 

function connect(&$db){
	$mycnf = "/etc/hw5-mysql.conf";
	if(!file_exists($mycnf)){
		echo "ERROR: DB Config file not found: $mycnf";
		exit;
	}
	
	$mysql_ini_array = parse_ini_file($mycnf);
	$db_host = $mysql_ini_array["host"];
	$db_user = $mysql_ini_array["user"];
	$db_pass = $mysql_ini_array["pass"];
	$db_port = $mysql_ini_array["port"];
	$db_name = $mysql_ini_array["dbName"];
	#$db=mysqli_init();
	#$db_sslkey='/etc/mysql-ssl/server-key.pem';
	#$db_sslcert='/etc/mysql-ssl/server-cert.pem';
	#mysqli_ssl_set($db, $db_sslkey , $db_sslcert, NULL, NULL, NULL);
	#mysqli_real_connect($db, $db_host, $db_user, $db_pass, $db_name, $db_port);
	
	#if(mysqli_connect_errno()){
	#	print "DB Error:".mysqli_connect_errno();
	#	exit;
	#}

	$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
	
	if(!$db) {
		print "Error connecting DB: " . mysqli_connect_error();
		exit;
	}
}

function white_list(){
	$ip_address_list = array();
	array_push($ip_address_list, '198.18.2.66');
	return $ip_address_list;
}

?>


