#!/usr/local/bin/php
<?php
ob_start();
//gets the name of the file
$x = $_GET['x'];
//location of the file
$file = "./uploads/" . $x;
header("Content-Type: application/octet-stream");
//downloads the file with the name photo.png 
header('Content-Disposition: attachment; filename= "photo.png"');
readfile ($file);
exit();
 ?>