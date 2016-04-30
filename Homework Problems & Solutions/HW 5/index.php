<?php

include_once('/var/www/html/hw5/hw5-lib.php');
include_once('/var/www/html/hw5/header.php');

isset($_REQUEST['s'])?$s=strip_tags($_REQUEST['s']):$s="";
isset($_REQUEST['story_id'])?$story_id=strip_tags($_REQUEST['story_id']):$story_id="";
isset($_REQUEST['book_id'])?$book_id=strip_tags($_REQUEST['book_id']):$book_id="";
isset($_REQUEST['char_id'])?$char_id=strip_tags($_REQUEST['char_id']):$char_id="";
isset($_REQUEST['char_name'])?$char_name=strip_tags($_REQUEST['char_name']):$char_name="";
isset($_REQUEST['side'])?$side=strip_tags($_REQUEST['side']):$side="";
isset($_REQUEST['race'])?$race=strip_tags($_REQUEST['race']):$race="";
isset($_REQUEST['url'])?$url=strip_tags($_REQUEST['url']):$url="";				
isset($_REQUEST['title'])?$title=strip_tags($_REQUEST['title']):$title="";

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

function add_characters_insert_form($db){
	echo "<div align=center><table><tr><td>Add Character to Books</td></tr>
		<form action=index.php method=post>
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
                <form action=index.php method=post>
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
                <form action=index.php method=post>
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
                <form action=index.php method=post>
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
        <form action=index.php method=post>	
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
		<td><a href=index.php?s=3&char_id=$char_id> Done </a></td></tr>	
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
