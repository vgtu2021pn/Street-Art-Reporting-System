<?php
session_start();  
if(!isset($_SESSION["username"])) {
	header("Location: Login.php?action=login");
}

$servername = "localhost";
$user = "sardb";
$pw = "mypassword";
$db = "streetartreportingdb";

$connection = mysqli_connect($servername, $user, $pw, $db);

if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
}

if(isset($_GET['uno'])) {
	
	$uno = (int)$_GET['uno'];
	
	$selectqry = "SELECT photo_name,photo_size,photo_type,photo_data FROM street_art_photo WHERE uno = $uno;";
	$result = mysqli_query($connection, $selectqry);
	
	if(mysqli_num_rows($result)>0) {
		$row=mysqli_fetch_assoc($result);
		
		header('Content-Type: '.$row['photo_type']);
		header('Content-Length: '.$row['photo_size']);
		header('Content-Disposition: inline;filename="'.$row['photo_name'].'"');
		echo base64_decode($row['photo_data']);
		exit;
	}
}
else {
		header('HTTP/1.0 404 Not Found');
		echo "Image not found.";
		exit;
}
?>
