<?php
//Name   : Alimulla Shaik
//Purpose: A php page to handle add character functionality
//Author : shal5122@colorado.edu
//Version: 1.0
//Date   : 2016/04/01 

session_start();
session_regenerate_id();
include_once('/var/www/html/hw9/hw9-lib.php');
include_once('/var/www/html/hw9/header.php');

isset($_REQUEST['s'])?$s=strip_tags($_REQUEST['s']):$s="";
isset($_REQUEST['story_id'])?$story_id=strip_tags($_REQUEST['story_id']):$story_id="";
isset($_REQUEST['book_id'])?$book_id=strip_tags($_REQUEST['book_id']):$book_id="";
isset($_REQUEST['char_id'])?$char_id=strip_tags($_REQUEST['char_id']):$char_id="";
isset($_REQUEST['char_name'])?$char_name=strip_tags($_REQUEST['char_name']):$char_name="";
isset($_REQUEST['side'])?$side=strip_tags($_REQUEST['side']):$side="";
isset($_REQUEST['race'])?$race=strip_tags($_REQUEST['race']):$race="";
isset($_REQUEST['url'])?$url=strip_tags($_REQUEST['url']):$url="";				
isset($_REQUEST['title'])?$title=strip_tags($_REQUEST['title']):$title="";
isset($_REQUEST['user_name'])?$user_name=strip_tags($_REQUEST['user_name']):$user_name="";
isset($_REQUEST['pass'])?$pass=strip_tags($_REQUEST['pass']):$pass="";
isset($_REQUEST['redirect'])?$redirect=strip_tags($_REQUEST['redirect']):$redirect="";
isset($_REQUEST['new_username'])?$new_username=strip_tags($_REQUEST['new_username']):$new_username="";
isset($_REQUEST['new_pass'])?$new_pass=strip_tags($_REQUEST['new_pass']):$new_pass="";
isset($_REQUEST['email'])?$email=strip_tags($_REQUEST['email']):$email="";
$ip_address ='';

connect($db);

num_check($s); 
num_check($story_id);
num_check($book_id);
num_check($char_id);

if(isset($_SESSION['authenticated']) && ($_SESSION['authenticated']="yes" ))
{	
		
}
else {
	//authenticate($db, $user_name, $pass);
	if($user_name == null)
	{	
		header("Location:/hw9/login.php");
	}
        $ip_address = $_SERVER['REMOTE_ADDR'];
        #echo "ip add:= " .$ip_address;

        $white_listed_ip_addresses = white_list();
	$attempt_count = incorrect_attempts_count($db,$ip_address);
	if((!($is_ip_white_listed = in_array($ip_address, $white_listed_ip_addresses))) && ($attempt_count >= 5))
        {
        	update_current_login_in_db($db, $user_name, "failure");
		header("Location:/hw9/login.php");        	
        }
        else {
		authenticate($db, $user_name, $pass);        		
		checkAuth();		
	}
}

switch($s){
	case 5:	
		add_characters_insert_form($db);
	break;
	case 6:
		add_characters($db, $char_name, $side, $race);
	break;

	case 7:	
		add_picture_to_character($db, $char_name, $char_id, $url);
	break;

	case 8:
		add_char_to_books($db, $char_id, $char_name);
	break;
	
	case 9:
		add_book_finish($db, $char_id, $char_name, $title);
	break;
	
	case 90:
		if(isset($_SESSION['userid']) && $_SESSION['userid'] == 1){
			add_user();
		} else
			echo "Unauthorized user!!! Do not have access to use this functionality";
	break;
	
	case 91:
		if(isset($_SESSION['userid']) && $_SESSION['userid'] == 1){
               	        update_add_user_in_db($db, $new_username, $new_pass, $email);			
                } else
       	                echo "Unauthorized user!!! Do not have access to use this functionality";
	break;

	case 92:
		if(isset($_SESSION['userid']) && $_SESSION['userid'] == 1){
               	        display_all_users($db);
                } else
       	                echo "Unauthorized user!!! Do not have access to use this functionality";
	break;
	
	case 93:
		if(isset($_SESSION['userid']) && $_SESSION['userid'] == 1){
               	        update_user_password();
                } else
       	                echo "Unauthorized user!!! Do not have access to use this functionality";
	break;
		
	case 94:
		if(isset($_SESSION['userid']) && $_SESSION['userid'] == 1){
                        update_password_in_db($db, $new_username, $new_pass);
                 } else
    			echo "Unauthorized user!!! Do not have access to use this functionality";
	break;

	case 95:
		session_destroy();
		header("Location:/hw9/login.php");
	break;
	
	case 96:
		if(isset($_SESSION['userid']) && $_SESSION['userid'] == 1){
                        display_all_incorrect_login_attempts($db);
                 } else
                        echo "Unauthorized user!!! Do not have access to use this functionality";
	break;
		
	default :
		add_characters_insert_form($db);
	break;
}

echo "<center>";
if(isset($_SESSION['userid']) && $_SESSION['userid'] == 1)
{
	echo "<a href=add.php?s=90> Add New User </a> |
        <a href=add.php?s=92> Show All Users </a> |
	<a href=add.php?s=93> Update Password </a> |
	<a href=add.php?s=96> Show All Incorrect Login Attempts </a> |
        ";
}
echo "<a href=add.php?s=95> Logout </a>";

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
	if($query = mysqli_prepare($db,"select ip, count(*) from login where action='failure' GROUP BY ip"))
	{				
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $ip_address, $cnt);
		echo "<table><tr><b> Incorrect Login Attempts Details: </b></tr>
		<tr><td>IP Address  | </td> 
		<td>Number of Incorrect Login Attempts | </td></tr> ";
		
		while(mysqli_stmt_fetch($query))
		{
			$ip_address = htmlspecialchars($ip_address);
			$count = htmlspecialchars($cnt);
			echo "<tr><td>$ip_address | </td>
				  <td>$cnt | </td></tr> <br>";
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
			$_SESSION['email']=$email;
			$_SESSION['authenticated']="yes";
			$_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
			$_SESSION['HTTP_USER_AGENT']=md5($_SERVER['SERVER_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
			$_SESSION['created']=time();
			$_SESSION['created']=time();
			update_current_login_in_db($db, $user_name, "success");
		}
		else
		{
			echo "Failed to Login";
			update_current_login_in_db($db, $user_name, "failure");
			error_log("Error: Tolkien app has failed login from " . $_SERVER['REMOTE_ADDRESS'], 0);
	
			header("Location:/hw9/login.php");
			exit;
		}
	}
}

function add_user(){
	echo "
	<div align=center><table><tr><td>Add New User to tolkien App </td></tr>
	<form action=add.php method=post>
	<tr>
		<td>User Name:</td><td><input type=\"text\" name=\"new_username\" required/>
		</td>
	</tr>
	<tr>
		<td>Password:</td><td><input type=\"password\" name=\"new_pass\" required/>
		</td>
	</tr>
	<tr>
		<td>Email:</td><td><input type=\"email\" name=\"email\" required/>
		</td>
	</tr>
	<tr>
		<td><input type=\"submit\" name=\"submit\" value=\"submit\"/>
		</td>
	</tr>
	<tr>
		<td><input type=\"hidden\" name=\"s\" value=\"91\"/>
		</td>
	</tr>
	</form>
	</table>
	</div> 
	";	
}

function update_add_user_in_db($db, $new_username, $new_pass, $email){
	$new_username=mysqli_real_escape_string($db,$new_username);
	$new_pass=mysqli_real_escape_string($db,$new_pass);
	$email=mysqli_real_escape_string($db,$email);
	$salt = rand(10,10000);
	$hash_salt=hash('sha256',$salt);
	$hash_pass=hash('sha256',$new_pass.$hash_salt);
	
	if($query = mysqli_prepare($db, "insert into users set userid='', username=?, password=?, email=?, salt=?"))
    	{
		mysqli_stmt_bind_param($query, "ssss", $new_username,$hash_pass, $email, $hash_salt);
            	mysqli_stmt_execute($query);
		mysqli_stmt_close($query);
            	echo "Added new user: " . $new_username;
  	}
  	else
  		echo "Error!!! Can not update new user details in database";
}

function display_all_users($db){	
	if($query = mysqli_prepare($db, "select username from users"))
        {
                mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $username);
                while(mysqli_stmt_fetch($query))
		{
			$username = htmlspecialchars($username);
                	echo "<tr><td>$username<br></td></tr>";
		}
		mysqli_stmt_close($query);
        }
}

function update_user_password(){
	echo "<div align=center><table><tr><td>Update users password</td></tr>
		<form action=add.php method=post>
		<tr>
			<td>Username:</td><td><input type=\"text\" name=\"new_username\" required/>
			</td>
		</tr>
		<tr>
			<td>New Password:</td><td><input type=\"password\" name=\"new_pass\" required/>
			</td>
		</tr>
		<tr>
			<td><input type=\"submit\" name=\"submit\" value=\"submit\"/>
			</td>
		</tr>
		<tr>
			<td><input type=\"hidden\" name=\"s\" value=\"94\"/>
			</td>
		</tr></form></table></div> 
	";
}

function update_password_in_db($db, $new_username, $new_pass){
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
        	echo "Password updation is completed for user: " . $new_username;
  	}
  	else
  		echo "Error!!! can not update new password in database";		
}

function add_characters_insert_form($db){
	echo "<div align=center><table><tr><td>Add Character to Books</td></tr>
		<form action=add.php method=post>
		<tr>
			<td>Character Name </td>
			<td><input type=\"text\" name=\"char_name\"/></td>
		</tr>
		<tr>
			<td>Race</td>
			<td><input type=\"text\" name=\"race\"/></td>
		</tr>
		<tr>
			<td>Side</td>
			<td><input type=\"radio\" name=\"side\" value=\"good\"/>Good
			<input type=\"radio\" name=\"side\" value=\"evil\"/>Evil</td>
		</tr>
		<tr>
			<td><input type=\"submit\" name=\"submit\" value=\"submit\"/></td>
		</tr>
		<tr>
			<td><input type=\"hidden\" name=\"s\" value=\"6\"/></td>
		</tr>
		</form>
		</table>
		</div> ";
}

function add_characters($db, $char_name, $side, $race){
	$char_name = mysqli_real_escape_string($db, $char_name);
	$side = mysqli_real_escape_string($db, $side);
	$race = mysqli_real_escape_string($db, $race);

        if($query = mysqli_prepare($db, "INSERT into characters SET characterid='', name=?, race=?, side=?"))
        {
                mysqli_stmt_bind_param($query, "sss", $char_name,$race,$side);
                mysqli_stmt_execute($query);
            	mysqli_stmt_close($query);
	}
	if($query = mysqli_prepare($db, "SELECT characterid FROM characters WHERE name=? and race=? and side=? order by characterid desc limit 1"))	
	{
		mysqli_stmt_bind_param($query, "sss", $char_name,$race,$side);
                mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $char_id);
                while(mysqli_stmt_fetch($query))
                {
			$char_id = htmlspecialchars($char_id);			
                }
        	mysqli_stmt_close($query);
        }
	else
		echo "Error in query";

	echo "<div align=center><table><tr><td>Add Picture to Character ".$char_name." </td></tr>
                <form action=add.php method=post>
                <tr><td>Character Picture URL</td><td><input type=\"text\" name=\"url\" size=\"35\"/></td></tr>
                <tr><td><input type=\"submit\" name=\"submit\" value=\"submit\"/></td></tr>
		<tr><td><input type=\"hidden\" name=\"char_name\" value=\"$char_name\">
		<tr><td><input type=\"hidden\" name=\"char_id\" value=\"$char_id\">

                <tr><td><input type=\"hidden\" name=\"s\" value=\"7\"/></td></tr>
                </form>
		</table>
                </div> ";

}

function add_picture_to_character($db, $char_name, $char_id, $url){
	$char_name = mysqli_real_escape_string($db, $char_name);
	$url = mysqli_real_escape_string($db, $url);
	$char_id = mysqli_real_escape_string($db, $char_id);
	
	if($query = mysqli_prepare($db, "INSERT into pictures SET pictureid='', url=?, characterid=?"))
        {
                mysqli_stmt_bind_param($query, "si", $url, $char_id);
                mysqli_stmt_execute($query);
                mysqli_stmt_close($query);
        }

	echo "<div align=center><table><tr><td>Added Picture for ".$char_name." </td></tr>
                <form action=add.php method=post>
                <tr><td><input type=\"submit\" name=\"submit\" value=\"Add Character to Books \"/></td></tr>
		<tr><td><input type=\"hidden\" name=\"char_name\" value=\"$char_name\">
                <tr><td><input type=\"hidden\" name=\"char_id\" value=\"$char_id\">
                <tr><td><input type=\"hidden\" name=\"s\" value=\"8\"/></td></tr>
                </form>
                </table>
                </div> ";
}

function add_char_to_books($db, $char_id, $char_name){
	$char_id = mysqli_real_escape_string($db, $char_id);
	$char_name = mysqli_real_escape_string($db, $char_name);

	echo "<div align=center><table><tr><td>Add ".$char_name." to Books </td></tr>
                <form action=add.php method=post>
		<tr>
			<td>Select Book </td>
			<td> <select name=\"title\"> ";
	
	if($query = mysqli_prepare($db, "SELECT distinct(a.bookid), b.title FROM books b, appears a WHERE a.bookid NOT IN 
		(SELECT bookid FROM appears WHERE characterid=?) AND b.bookid=a.bookid"))
        {
		mysqli_stmt_bind_param($query, "i", $char_id);
                mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $book_id, $title);		
                while(mysqli_stmt_fetch($query))
                {
                        $book_id = htmlspecialchars($book_id);
                        $title = htmlspecialchars($title);
                        echo "<option value =\"$title\">$title</option> ";
                }
		echo "</select></td></tr>";
                mysqli_stmt_close($query);
        }	
	echo "<tr><td><input type=\"submit\" name=\"submit\" value=\"Add to Book \"/></td></tr>
		<tr><td><input type=\"hidden\" name=\"char_name\" value=\"$char_name\">
                <tr><td><input type=\"hidden\" name=\"char_id\" value=\"$char_id\">
                <tr><td><input type=\"hidden\" name=\"s\" value=\"9\"/></td></tr>";
	echo "</form></table></div>";
}

function add_book_finish($db, $char_id, $char_name, $title){
        $char_id = mysqli_real_escape_string($db, $char_id);
        $char_name = mysqli_real_escape_string($db, $char_name);
	$title = mysqli_real_escape_string($db, $title);
	
	if($query = mysqli_prepare($db, "select bookid from books where title=?"))
	{
		mysqli_stmt_bind_param($query, "s", $title);
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $book_id);
        	while(mysqli_stmt_fetch($query))
        	{
            		$book_id = htmlspecialchars($book_id);
		}
		mysqli_stmt_close($query);
	}
	if($query = mysqli_prepare($db, "INSERT into appears SET appearsid='', bookid=?, characterid=?"))
	{
		mysqli_stmt_bind_param($query, "ii", $book_id, $char_id);
		mysqli_stmt_execute($query);
		mysqli_stmt_close($query);
	}
        
	echo "<div align=center><table><tr><td>Added ".$char_name." to book ".$book_id." </td></tr>
	<div align=center><table><tr><td>Add ".$char_name." to Books </td></tr>
        <form action=add.php method=post>	
	<tr>
        	<td>Select Book </td>
                <td><select name=\"title\"> ";

        if($query = mysqli_prepare($db, "SELECT distinct(a.bookid), b.title FROM books b, appears a WHERE a.bookid NOT IN
                (SELECT bookid FROM appears WHERE characterid=?) AND b.bookid=a.bookid"))
        {
                mysqli_stmt_bind_param($query, "i", $char_id);
                mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $book_id, $title);
                while(mysqli_stmt_fetch($query))
                {
                        $book_id = htmlspecialchars($book_id);
                        $title = htmlspecialchars($title);
                        echo "<option value =\"$title\">$title</option>";
                }
                echo "</select></td></tr>";
                mysqli_stmt_close($query);
        }
        echo "<tr><td><input type=\"submit\" name=\"submit\" value=\"Add to Book \"/></td>
		<td><a href=add.php?s=3&char_id=$char_id> Done </a></td></tr>	
		<tr><td><input type=\"hidden\" name=\"char_name\" value=\"$char_name\">
                <tr><td><input type=\"hidden\" name=\"char_id\" value=\"$char_id\">
                <tr><td><input type=\"hidden\" name=\"s\" value=\"9\"/></td></tr>";
        echo "</form></table></div>";
	
	
}



function view_stories($db){
	echo "<div align=center><table><tr><td><u><b>Stories</b></u><br></td></tr>";
        if($query = mysqli_prepare($db, "SELECT storyid,story FROM stories"))
        {
        	mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $story_id, $story);
                while(mysqli_stmt_fetch($query))
                {
                	$story_id = htmlspecialchars($story_id);
                        $story = htmlspecialchars($story);
                        echo "<tr><td><a href=index.php?s=1&story_id=$story_id>$story</a><br></td></tr>";
                }
                mysqli_stmt_close($query);
        }
        echo "</table></div>";
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
