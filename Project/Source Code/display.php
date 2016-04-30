<?php 
//Name   : Alimulla Shaik
//Purpose: A php page to handle and display the results of the user search.
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/04/22 

session_start();
session_regenerate_id();
include_once('/var/www/html/project/project-lib.php');

isset($_REQUEST['city'])?$city=strip_tags($_REQUEST['city']):$city="";
isset($_REQUEST['place_type'])?$place_type=strip_tags($_REQUEST['place_type']):$place_type="";
isset($_REQUEST['rating'])?$rating=strip_tags($_REQUEST['rating']):$rating="";
isset($_REQUEST['country'])?$country=strip_tags($_REQUEST['country']):$country="";
isset($_REQUEST['state'])?$state=strip_tags($_REQUEST['state']):$state="";

connect($db);

echo "
	<html>
	<head>
	<title> TravelBook App </title>
	<style type=\"text/css\">
	body {font-family:sans-serif;color:#4f494f;}
	form input {border-radius: 7.5px;}
	h5 {display: inline;}
	h2 {color:chocolate}
	th {border: 5px solid chocolate; padding:80px; }
	td {padding: 70px; border: 3px solid darkcyan;}
	.label {text-align: right}
	.gap {float:left; padding-top: 40px;}
	.results {width:100%; float:left; padding:3px; }
	.wrapper { padding-left: 25px; padding-top: 20px}
	</style>	

	</head>
	<body background=\"red.jpg\">
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
	<div class=\"gap\">
	<div class=\"name results\">
	";

search_for_place($db, $country, $place_type);

function search_for_place($db, $country, $place_type)
{
	$country = mysqli_real_escape_string($db, $country);
	$place_type = mysqli_real_escape_string($db, $place_type);

	$avg_rating = 0;
	$cnt = 0;
	if($place_type == '' and $country == ''){
                echo "<h2> Opss!!! You did not enter anything. Please input either country or place type to look out for the options with ratings. </h2>";
        }
	else if($place_type == '')
	{
		search_for_country($db, $country, $place_type);
	}
	else if($country == '')
	{
		search_for_place_type($db, $country, $place_type);
	}
	else
	{
		search_for_country_place_type($db, $country, $place_type);	
	}
}

function search_for_place_type($db, $country, $place_type)
{
        $country = mysqli_real_escape_string($db, $country);
        $place_type = mysqli_real_escape_string($db, $place_type);
        $avg_rating = 0;
        $cnt = 0;

	if($query = mysqli_prepare($db, "select count(*) from user_rating where place_type =?"))
	{
		mysqli_stmt_bind_param($query, "s", $place_type);
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
		echo "Failed to prepare SQLI";
	}

	if($cnt == 0)
	{
		echo "<h2> Opss!!! No ratings available for the places belongs to the entered place_type [".$place_type."]. Please try some other place_type. </h2>";
	}
	else
	{
		echo "
		<h2> We found few places which matches your preference! Have a look at their ratings below... </h2>
		<table>
		<tr>
		<th style=\"color:green\"> Rating </th>
		<th style=\"color:green\"> City </th>
		<th style=\"color:green\"> State </th>
		<th style=\"color:green\"> Country </th>
		<th style=\"color:green\"> Place Type </th>
		</tr>";

		if($query = mysqli_prepare($db, "select o.city, o.rating, o.country, u.state, u.place_type from overall_rating o, user_rating u where u.city=o.city 
		and u.place_type=? group by o.city order by o.rating DESC"))
		{
			mysqli_stmt_bind_param($query, "s", $place_type);
			mysqli_stmt_execute($query);
			mysqli_stmt_bind_result($query, $city, $rating, $country, $state, $place_type);

			while(mysqli_stmt_fetch($query))
			{
				$city = htmlspecialchars($city);
				$state = htmlspecialchars($state);
				$rating = htmlspecialchars($rating);
				$country = htmlspecialchars($country);
				$place_type = htmlspecialchars($place_type);

				echo "
				<tr>
				<td align=\"left\" style=\"color:gold\">$rating</td>
				<td align=\"left\" style=\"color:gold\">$city</td>
				<td align=\"left\" style=\"color:gold\">$state</td>
				<td align=\"left\" style=\"color:gold\">$country</td>
				<td align=\"left\" style=\"color:gold\">$place_type</td>
				</tr>
				";
			}
		mysqli_stmt_close($query);
		}
		else
		{
			echo "Failed to prepare SQLI";
		}
	}
	echo "</table></div> </div> </div></body></html>";
}

function search_for_country($db, $country, $place_type)
{
        $country = mysqli_real_escape_string($db, $country);
        $place_type = mysqli_real_escape_string($db, $place_type);
        $avg_rating = 0;
        $cnt = 0;

                if($query = mysqli_prepare($db, "select count(*) from overall_rating where country=? order by rating DESC"))
                {
                        mysqli_stmt_bind_param($query, "s", $country);
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
                        echo "Failed to prepare SQLI";
                }

                if($cnt == 0)
                {
                        echo "<h2> Opss!!! No ratings available for the places belongs to the entered country [".$country."]. Please try some other country. </h2>";
                }
                else
                {
                        echo "
                        <h2> We found few places which matches your preference! Have a look at their ratings below... </h2>
                        <table>
                        <tr>
                        <th style=\"color:green\"> Rating </th>
                        <th style=\"color:green\"> City </th>
                        <th style=\"color:green\"> State </th>
                        <th style=\"color:green\"> Country </th>
                        <th style=\"color:green\"> Place Type </th>
                        </tr>";

                        if($query = mysqli_prepare($db, "select o.city, o.rating, o.country, u.state, u.place_type from overall_rating o, user_rating u where u.city=o.city and u.country=o.country and o.country=? group by o.city order by o.rating DESC"))
                        {
                                mysqli_stmt_bind_param($query, "s", $country);
                                mysqli_stmt_execute($query);
                                mysqli_stmt_bind_result($query, $city, $rating, $country, $state, $place_type);

                                while(mysqli_stmt_fetch($query))
                                {
                                        $city = htmlspecialchars($city);
                                        $state = htmlspecialchars($state);
                                        $rating = htmlspecialchars($rating);
                                        $country = htmlspecialchars($country);
                                        $place_type = htmlspecialchars($place_type);

                                        echo "
                                        <tr>
                                        <td align=\"left\" style=\"color:gold\">$rating</td>
                                        <td align=\"left\" style=\"color:gold\">$city</td>
                                        <td align=\"left\" style=\"color:gold\">$state</td>
                                        <td align=\"left\" style=\"color:gold\">$country</td>
                                        <td align=\"left\" style=\"color:gold\">$place_type</td>
                                        </tr>
                                        ";
                                }
                                mysqli_stmt_close($query);
                        }
                        else
                        {
                                echo "Failed to prepare SQLI";
                        }
                }
		echo "</table></div> </div> </div></body></html>";		
}

function search_for_country_place_type($db, $country, $place_type)
{
        $country = mysqli_real_escape_string($db, $country);
        $place_type = mysqli_real_escape_string($db, $place_type);

        $avg_rating = 0;
        $cnt = 0;

                if($query = mysqli_prepare($db, "select count(*) from overall_rating o, user_rating u where u.city=o.city and u.country=o.country and u.country=? and u.place_type=? group by o.city order by o.rating DESC"))
                {
                        mysqli_stmt_bind_param($query, "ss", $country, $place_type);
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
                        echo "Failed to prepare SQLI";
                }

                if($cnt == 0){
                        echo "<h2> Opss!!! No ratings available for the places belongs to the entered Country [".$country."] and Place type[".$place_type."]. Please try some other country. </h2>";
                }
                else
                {
                        echo "
                        <h2> We found few places which matches your preference! Have a look at their ratings below... </h2>
                        <table>
                        <tr>
                        <th style=\"color:green\"> Rating: </th>
                        <th style=\"color:green\"> City: </th>
                        <th style=\"color:green\"> State: </th>
                        <th style=\"color:green\"> Country: </th>
                        <th style=\"color:green\"> Place Type: </th>
                        </tr>";

			if($query = mysqli_prepare($db, "select o.rating, o.city, o.country, u.state from overall_rating o, user_rating u where u.city=o.city and u.country=o.country and u.country=? and u.place_type=? group by o.city order by o.rating DESC"))
			{
				mysqli_stmt_bind_param($query, "ss", $country, $place_type);
				mysqli_stmt_execute($query);
				mysqli_stmt_bind_result($query, $rating, $city, $country, $state);
			
				while(mysqli_stmt_fetch($query))
				{
					$city = htmlspecialchars($city);
					$rating = htmlspecialchars($rating);
					$country = htmlspecialchars($country);
					$state = htmlspecialchars($state);

					echo "
					<tr>
					<td align=\"left\" style=\"color:gold\">$rating</td>
					<td align=\"left\" style=\"color:gold\">$city</td>
					<td align=\"left\" style=\"color:gold\">$state</td>
					<td align=\"left\" style=\"color:gold\">$country</td>
					<td align=\"left\" style=\"color:gold\">$place_type</td>
					</tr>
					";
				}
				mysqli_stmt_close($query);
			}
			else 
			{
				echo "Failed to prepare SQLI";
			}			
		}
		echo "</table></div> </div> </div></body></html>";		
}

?>
