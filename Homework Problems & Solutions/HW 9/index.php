<?php
//Name   : Alimulla Shaik
//Purpose: A php page to parse tolkien database with different functionalities
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

connect($db);

num_check($s); 
num_check($story_id);
num_check($book_id);
num_check($char_id);

switch($s){
	case 1:
		display_books($db, $story_id);
		break;

	case 2:
		get_characters($db, $book_id);
		break;
	
	case 3:
		display_characters($db, $char_id);
		break;

	case 4:
		display_characters_list($db);		
		break;
	
	default :
		view_stories($db);
		break;
}

function display_books($db, $story_id){
	echo "<div align=center><table><tr><td><u><b>Books</b></u></td></tr>";
	$story_id = mysqli_real_escape_string($db, $story_id);
	if($query = mysqli_prepare($db, "SELECT bookid,title FROM books WHERE storyid = ?"))
        {
               	mysqli_stmt_bind_param($query, "s", $story_id);
		mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $book_id, $title);
                while(mysqli_stmt_fetch($query))
		{
			$book_id = htmlspecialchars($book_id);
                	$title = htmlspecialchars($title);
                	echo "<tr><td><a href=index.php?s=2&book_id=$book_id>$title</a><br></td></tr>";
		}
		mysqli_stmt_close($query);
        }	
	echo "</table></div>";	
}

function get_characters($db, $book_id){
	echo "<div align=center><table><tr><td><u><b>Characters</b></u></td></tr>";
	$book_id = mysqli_real_escape_string($db, $book_id);
	if($query = mysqli_prepare($db, "SELECT ch.characterid,ch.name FROM characters as ch,appears as ar WHERE ar.characterid=ch.characterid and ar.bookid= ?"))
        {
               	mysqli_stmt_bind_param($query, "i", $book_id);
		mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $char_id, $name);                
		
		while(mysqli_stmt_fetch($query))
		{
			$char_id = htmlspecialchars($char_id);
                	$name = htmlspecialchars($name);
                	echo "<tr><td><a href=index.php?s=3&char_id=$char_id>$name</a><br></td></tr>";
		}
		mysqli_stmt_close($query);
        }	
}

function display_characters($db, $char_id){
	echo "<div align=center><table><tr><td><u><b>Appearances</b></u></td></tr>
	<tr><td>Character</td><td>Book</td><td>Story</td></tr>"; 
	$char_id = mysqli_real_escape_string($db, $char_id);
	if($query = mysqli_prepare($db, "SELECT b1.title,c1.name,s1.story FROM appears as a1, books as b1,characters as c1, stories as s1 WHERE 
	c1.characterid=a1.characterid and a1.bookid=b1.bookid and b1.storyid=s1.storyid and c1.characterid = ?"))
        {
               	mysqli_stmt_bind_param($query, "i", $char_id);
		mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $title, $name, $story);
                while(mysqli_stmt_fetch($query))
		{
			$title = htmlspecialchars($title);
                	$name = htmlspecialchars($name);
			$story = htmlspecialchars($story);
			echo "<tr> <td> <a href=index.php>$name</a></td>
                		<td><a href=index.php>$title</a></td>
                		<td> <a href=index.php>$story</a></td></tr>";
                }
		mysqli_stmt_close($query);
        }	
	echo "</table></div>";
}

function display_characters_list($db){
	echo "<div align=center><table><tr><td>Characters</td></tr>";
	if($query = mysqli_prepare($db, "SELECT ch.characterid,ch.name,pi.url FROM characters as ch, pictures as pi WHERE ch.characterid=pi.characterid "))
        {
		mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $char_id, $name, $url);
                while(mysqli_stmt_fetch($query))
		{
			$char_id = htmlspecialchars($char_id);
			$name = htmlspecialchars($name);
                	$url = htmlspecialchars($url);
                	echo "<tr><td><a href=index.php?s=3&char_id=$char_id>$name</a></td>";
			echo "<td><img src=\"".$url."\"></td></tr>";
		}
		mysqli_stmt_close($query);
        }
	echo "</table></div>";
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
