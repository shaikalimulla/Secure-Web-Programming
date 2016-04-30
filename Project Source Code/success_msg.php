<?php
//Name   : Alimulla Shaik
//Purpose: A php page to handle and display success messages with results of the user search
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/04/22

echo "
<html>
  <head>
    <title>TravelBook App</title>
    <style type=\"text/css\">
      body {font-family:sans-serif;color:#4f494f;}
      form input {border-radius: 7.5px;}
      h5 {display: inline;}
      table,th {border: 1px; }
      td {padding: 15px;}
      .label {text-align: right}
      .gap {float:left; padding-top: 40px;}
      .name:nth-child(odd){background-color:#bfbfbf;} 
      .name:nth-child(even){background-color:#f2f2f2;}
      .results {width:100%;float:left; padding:3px;}
      .wrapper { padding-left: 25px; padding-top: 20px}
    </style>
  </head>


   <body background=\"bungee.jpg\"> 
	<center>
        <h1 style=\"color:blue\"> Travel Book </h1>
        <div align=left>
        <a href=index.php style=\"background-color:yellow\"> HomePage </a>
        </div>
        <hr>
	</center>
    <div class=\"wrapper\">
         <form class=\"form\" action=user_profile.php method=post>
         <input type=\"submit\" value=\"Go back to your Profile Page\"/>
         </form>

	<div class=\"gap\">
	Result:
        <div class=\"name results\">
	<p> Thank you so much for your feedback! Successfully saved your Travel Experience. </p>
	</div>
	</div>    		
    </div>
  </body>
</html>
";
?>
