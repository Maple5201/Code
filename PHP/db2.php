<?php
session_start(); 

function getDbConnection() {
    $servername = "sql101.infinityfree.com";
    $username = "if0_36150369";
    $password = "zhangIFDB159876";
    $dbname = "if0_36150369_Group5";

    $link = mysqli_connect($servername, $username, $password, $dbname);

    if ($link === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    return $link;
}
?>
