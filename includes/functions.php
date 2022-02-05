<?php
function isBanned()
{
    $user=$_SESSION["usern"];
    $chatId=$_GET["user"];
    $banQuery = mysql_query("SELECT * FROM chatroom_user WHERE chatroom='$chatId' AND user='$user' AND is_banned=1");
    $banCheck = mysql_fetch_array($banQuery);
    if(!empty($banCheck))
    {
        return true;
    }else{
        return false;
    }
}
function isModerator()
{
    $user=$_SESSION["usern"];
    $chatId=$_GET["user"];
    $modQuery= mysql_query("SELECT * FROM chatroom_user WHERE chatroom='$chatId' AND user='$user' AND type='mods'");
    $modCheck = mysql_fetch_array($modQuery);
    if(!empty($modCheck))
    {
        return true;
    }else{
        return false;
    }
}
?>