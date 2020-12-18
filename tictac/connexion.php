<?php
function connectdb()
{
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $db = "tictac";

    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $db) or die("error");
    return $conn;
}
