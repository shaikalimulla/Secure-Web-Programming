<?php
//Name   : Alimulla Shaik
//Purpose: A php page to handle user profile page functionalities
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/04/22

session_start();
session_regenerate_id();
include_once('/var/www/html/project/project-lib.php');

isset($_REQUEST['user'])?$user=strip_tags($_REQUEST['user']):$user="";
isset($_REQUEST['s'])?$s=strip_tags($_REQUEST['s']):$s="";
isset($_REQUEST['userid'])?$userid=strip_tags($_REQUEST['userid']):$userid="";
isset($_REQUEST['city'])?$city=strip_tags($_REQUEST['city']):$city="";
isset($_REQUEST['place_type'])?$place_type=strip_tags($_REQUEST['place_type']):$place_type="";
isset($_REQUEST['rating'])?$rating=strip_tags($_REQUEST['rating']):$rating="";
isset($_REQUEST['state'])?$state=strip_tags($_REQUEST['state']):$state="";
isset($_REQUEST['zipcode'])?$zipcode=strip_tags($_REQUEST['zipcode']):$zipcode="";
isset($_REQUEST['country'])?$country=strip_tags($_REQUEST['country']):$country="";				

connect($db);
$user =  $_SESSION['user_name'];

switch($s){
	case 10:	
		insert_user_rating($db, $user, $city, $place_type, $rating, $state, $zipcode, $country);
	break;
	case 11:
		search_for_place($db, $country, $place_type);
	break;

	case 90:	
		session_destroy();
		header("Location:/project/login.php?s=91");
	break;
}

echo 	"
        <html>
        <head>
        <title> TravelBook App </title>
	<style>
	th {border: 3px solid chocolate; padding:5px; }
        td {padding: 5px; border: 3px solid darkcyan;}
	input[type=\"text\"] {width: 300px; height:20px;}
	</style>
        
	</head>
        <body background=\"san.jpg\">

        <center>
        <h1 style=\"color:blue\"> Travel Book </h1>
        <div align=left>
        <a href=index.php style=\"background-color:yellow\"> HomePage </a>
	</div>
	<hr>
	<div align=right>
	";

if(isset($_SESSION['userid']) && $_SESSION['userid'] == 9)
{	
	echo "
        <a href=delegate.php?s=92 style=\"background-color:gold\"> Show All Users </a> |
	<a href=delegate.php?s=96 style=\"background-color:gold\"> Show All Incorrect Login Attempts </a> |	
        ";
}

echo "	
	<a href=delegate.php?s=93 style=\"background-color:gold\"> Update Password </a> |	
        <a href=user_profile.php?s=90 style=\"background-color:gold\"> Logout </a>
        </div>
	<h2>Welcome ".$user."!!!</h2>
";

echo "

      <form action=\"user_profile.php\" method=\"post\" autocomplete=\"off\">
	<h3 style=\"color:yellow\">Rate places here...</h3> 
		
		<table>
		    <tr>
		      <th align=\"right\" style=\"color:maroon\">Place Name: </th>
		      <td align=\"left\"><input type=\"text\" name=\"city\" required/></td>
		    </tr>
		    <tr>
		      <th align=\"right\" style=\"color:maroon\">Place type: </th>
		      <td align=\"left\"><input type=\"text\" name=\"place_type\" required/></td>
		    </tr>
		    <tr>
		      <th align=\"right\" style=\"color:maroon\">Rating: </th>
		      <td align=\"left\"><input type=\"text\" name=\"rating\" required/></td>
		    </tr>
		    
		    <tr>
                      <th align=\"right\" style=\"color:maroon\">State: </th>
                      <td align=\"left\"><input type=\"text\" name=\"state\"/></td>
                    </tr>
                    <tr>
                      <th align=\"right\" style=\"color:maroon\">Zip Code: </th>
                      <td align=\"left\"><input type=\"text\" name=\"zipcode\"/></td>
                    </tr>
                    <tr>
                      <th align=\"right\" style=\"color:maroon\">Country: </th>
                      <td align=\"left\"><input type=\"text\" name=\"country\" required/></td>
                    </tr>
		   </table>
		    <input type=\"hidden\" name=\"s\" value=\"10\"/>	
		    <input type=\"submit\" value=\"Insert Place Details\" style=\"background-color:grey\"/>
      </form>
	<br>

      <form action=\"user_profile.php\" method=\"post\" autocomplete=\"off\">
        <h3 style=\"color:yellow\">Search for the Places here...</h3>

                <table>
                    <tr>
                      <th align=\"right\" style=\"color:maroon\">Country: </th>
                      <td align=\"left\"><input type=\"text\" value=\"\" name=\"country\"/></td>
                    </tr>
                    <tr>
                      <th align=\"right\" style=\"color:maroon\">Place type: </th>
                      <td align=\"left\"><input type=\"text\" name=\"place_type\"/></td>
                    </tr>
		 </table>
		 <td><input type=\"hidden\" name=\"s\" value=\"11\"/></td>
		 <input type=\"submit\" value=\"Search for the Places\" style=\"background-color:grey\"/>
      </form>

";


function insert_user_rating($db, $user, $city, $place_type, $rating, $state, $zipcode, $country){
	$user = mysqli_real_escape_string($db, $user);
	$city = mysqli_real_escape_string($db, $city);
	$place_type = mysqli_real_escape_string($db, $place_type);
	$rating = mysqli_real_escape_string($db, $rating);
	$state = mysqli_real_escape_string($db, $state);
	$zipcode = mysqli_real_escape_string($db, $zipcode);
	$country = mysqli_real_escape_string($db, $country);
	
	/*if($query = mysqli_prepare($db, "select userid from users where username=?"))
	{
		mysqli_stmt_bind_param($query, "s", $user);
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $userid);
		while(mysqli_stmt_fetch($query))
		{
			$userid = htmlspecialchars($userid);
		}
		mysqli_stmt_close($query);
	}
	*/	
	if($query = mysqli_prepare($db, "SET foreign_key_checks = 0")){
		mysqli_stmt_execute($query);
		mysqli_stmt_close($query);
	}
	#echo "city".$userid.$user.$place_type.$rating.$city.$state.$country.$zipcode;
	if($stmt = mysqli_prepare($db, "INSERT into user_rating SET id='', username=?, place_type=?, rating=?, city=?, state=?, country=?, zip=?"))
	{
		mysqli_stmt_bind_param($stmt, "ssisssi",$user, $place_type, $rating, $city, $state, $country, $zipcode);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		update_rating_for_place($db, $rating, $city, $country);
		header("Location:/project/success_msg.php");
	}
	else {
        	echo "Error!!! Can not update new user details in database";
	}

}

function update_rating_for_place($db, $rating, $city, $country){
        $city = mysqli_real_escape_string($db, $city);
        $country = mysqli_real_escape_string($db, $country);
        $rating = mysqli_real_escape_string($db, $rating);

        $avg_rating = 0;
        $cnt = 0;

        if($query = mysqli_prepare($db, "select avg(rating) from user_rating where city=?"))
        {
                mysqli_stmt_bind_param($query, "s", $city);
                mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $avg_rating);
                while(mysqli_stmt_fetch($query))
                {
                        $avg_rating = htmlspecialchars($avg_rating);
                }
                mysqli_stmt_close($query);
        }

        if($query = mysqli_prepare($db, "select count(*) from overall_rating where city=?"))
        {
                mysqli_stmt_bind_param($query, "s", $city);
                mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $cnt);
                while(mysqli_stmt_fetch($query))
                {
                        $cnt = htmlspecialchars($cnt);
                }
                mysqli_stmt_close($query);
        }
		
        if($cnt >=1){
                if($query = mysqli_prepare($db, "update overall_rating set rating=? where city=?"))
                {
                        mysqli_stmt_bind_param($query, "is", $avg_rating ,$city);
                        mysqli_stmt_execute($query);
                        mysqli_stmt_close($query);
                }
                else
                        echo "Error!!! can not update average rating in database";
        } else{

                if($stmt = mysqli_prepare($db, "INSERT into overall_rating SET rating=?, city=?, country=?"))
                {
                        mysqli_stmt_bind_param($stmt, "iss",$avg_rating, $city, $country);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                }
                else {
                        echo "Error!!! Can not update rating in database";
                }
        }
}

function search_for_place($db, $country, $place_type){
	if($place_type ==''){
		header("Location:/project/display.php?country=$country");
	}
	else {
		header("Location:/project/display.php?country=$country&place_type=$place_type");
	}
}

?>
