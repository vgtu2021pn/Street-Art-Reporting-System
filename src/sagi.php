<?php
session_start();
if(!isset($_SESSION["username"]))
{
	header("Location: Login.php?action=login");  
}

$servername = "localhost";
$user = "sardb";
$pw = "mypassword";
$db = "streetartreportingdb";

$connection = mysqli_connect($servername, $user, $pw, $db);

if(!$connection)
{
	die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View Street Art and (or) Graffiti</title>
<link rel="icon" href="images/brush.webp" type="image/webp">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style>
body {
	font-family: Verdana, sans-serif;
	color: #FFFFFF;
	margin: 0;
}

* {
	box-sizing: border-box;
}

.row > .column {
	padding: 0 8px;
}

.row:after {
	content: "";
	display: table;
	clear: both;
}

.column {
	float: left;
	width: 25%;
}

/* The Modal (background) */
.modal {
	display: none;
	position: fixed;
	z-index: 1;
	padding-top: 100px;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	overflow: auto;
	background-color: black;
}

/* Modal Content */
.modal-content {
	position: relative;
	background-color: #fefefe;
	margin: auto;
	padding: 0;
	width: 90%;
	max-width: 1200px;
}

/* The Close Button */
.close {
	color: white;
	position: absolute;
	top: 10px;
	right: 25px;
	font-size: 35px;
	font-weight: bold;
}

.close:hover,
.close:focus {
	color: #999;
	text-decoration: none;
	cursor: pointer;
}

.mySlides {
	display: none;
}

.cursor {
	cursor: pointer;
}

/* Next & previous buttons */
.prev,
.next {
	cursor: pointer;
	position: absolute;
	top: 50%;
	width: auto;
	padding: 16px;
	margin-top: -50px;
	color: white;
	background-color: rgba(0, 0, 0, 0.5);
	font-weight: bold;
	font-size: 20px;
	transition: 0.6s ease;
	border-radius: 0 3px 3px 0;
	user-select: none;
	-webkit-user-select: none;
}

/* Position the "next button" to the right */
.next {
	right: 0;
	border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover,
.next:hover {
	background-color: rgba(0, 0, 0, 0.8);
}

/* Number text (1/3 etc) */
.numbertext {
	color: #f2f2f2;
	font-size: 12px;
	padding: 8px 12px;
	position: absolute;
	top: 0;
	font-family: Helvetica, sans-serif;
}

.view-head {
	background-color: rgba(23, 52, 86, 0.88);
}

img {
	display: block;
	margin-right: auto;
	margin-left: auto;
	margin-bottom: -4px;
}

.caption-container {
	text-align: center;
	background-color: black;
	padding: 2px 16px;
	color: white;
}

.demo {
	opacity: 0.6;
}

.active,
.demo:hover {
	opacity: 1;
}

img.hover-shadow {
	transition: 0.3s;
}

.hover-shadow:hover {
	box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}

.vertical-menu {
	width:300px;
}

.vertical-menu a {
	background-color: #eee;
	color: black;
	display: block;
	padding: 12px;
	text-decoration: none;
}

.vertical-menu a:hover {
	background-color: #ccc;
}

.vertical-menu a.active {
	background-color: #04AA6D;
	color: white;
}
		
table.view-table {
	width: 60%;
	margin-left: auto;
	margin-right: auto;
	background-color: rgba(192, 192, 192, 0.5);
	color: black;
	margin-bottom: 20px;
}
		
tr.view-row {
	border-collapse: collapse;
}

tr.view-row:hover {
	cursor: pointer;
}
		
td.view-cell {
	padding: 5px 0px 5px 15px; /*top right bottom left*/
}
		
.numbertext {
	background-color: rgba(0, 0, 0, 0.5);
}
	
.head-div {
	display: flex;
	background-color: rgba(255, 255, 255, 0.5);
	border: 1px solid black;
	border-radius: 20px;
	margin-top: 10px;
	margin-left: 5px;
	margin-right: 5px;
}
		
.cell {
	width: 200px;
	text-align: justify;
	font-family: Helvetica, sans-serif;
	color: black;
	background-color: rgba(255, 255, 255, 0.5);
	padding: 10px;
}
		
.pagenumbers {
	font-size: 15px;
	color: white;
	text-decoration: none;
	padding: 8px;
	border-radius: 8px;
}
		
.pagenumbers:hover {
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}
		
.validate-buttons {
	width: 80px;
	color: white;
	font-family: Helvetica, sans-serif;
	cursor: pointer;
	border: 0px;
	border-radius: 20px;
	padding: 8px;
	font-weight: bold;
}
</style>
</head>
<body style="background: #173457;">

	<!--****************************************************************Navigation Bar*****************************************************************-->
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">Street Art and (or) Graffiti Reporting System</a>
			</div>
    
			<ul class="nav navbar-nav">
				<li class="active"><a href="#">View Street Art and (or) Graffiti</a></li>
				<li ><a href="graph.php">Street Art and (or) Graffiti Analysis</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php if(isset($_SESSION["username"]) && $_SESSION["username"] == 'none') { echo 'Spectator'; } ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Log Out</a></li>
			</ul>
		</div>
	</nav>


<!--****************************************************brief view to expanded view***************************************************-->
<table class="view-table">
	
<?php
    $selectqry = "	SELECT 
						DISTINCT r1.street_art_uno,
						r1.registrar_status,
						r1.created 
					FROM 
						registrar AS r1
					INNER JOIN
						(SELECT 
							rt.street_art_uno, 
							(SELECT re.created FROM registrar AS re WHERE re.street_art_uno = rt.street_art_uno ORDER BY created DESC LIMIT 1) AS maxcreated
						FROM 
							registrar AS rt
						GROUP BY
							rt.street_art_uno
						ORDER BY 
							maxcreated DESC) AS r2
					ON
						(r1.created = r2.maxcreated)
					WHERE
						r1.registrar_status = 1
					ORDER BY
						created DESC;";
	$result = mysqli_query($connection, $selectqry);
	
	$total_rows = mysqli_num_rows($result);
	
	$limit = 10;
    // get the required number of pages
    $total_pages = ceil ($total_rows / $limit);
	
    // update the active page number
    if (!isset ($_GET['page']) ) { 
        $page_number = 1;  
    } else {
        $page_number = $_GET['page'];
    }

    // get the initial page number
    $initial_page = ($page_number-1) * $limit;

    // get data of selected rows per page
    $getQuery = "	SELECT 
						DISTINCT r1.street_art_uno,
						r1.registrar_status,
						r1.created 
					FROM 
						registrar AS r1
					INNER JOIN
						(SELECT 
							rt.street_art_uno, 
							(SELECT re.created FROM registrar AS re WHERE re.street_art_uno = rt.street_art_uno ORDER BY created DESC LIMIT 1) AS maxcreated
						FROM 
							registrar AS rt
						GROUP BY
							rt.street_art_uno
						ORDER BY 
							maxcreated DESC) AS r2
					ON
						(r1.created = r2.maxcreated)
					WHERE
						r1.registrar_status = 1
					ORDER BY
						created DESC
					LIMIT " . $initial_page . ',' . $limit;
    $resultLimit = mysqli_query($connection, $getQuery);

    //display the retrieved result on the webpage 
	if(mysqli_num_rows($result)>0)
	{
		while ($row = mysqli_fetch_array($resultLimit)) 
		{
			$sa_uno = (int)$row["street_art_uno"];
			
			$selectqry2 = 	"SELECT 
								* 
							FROM 
								street_art 
							WHERE 
								street_art_uno = $sa_uno;";
			$result2 = mysqli_query($connection, $selectqry2);
			
			$selectqry3 = 	"SELECT 
								uno,
								photo_name
							FROM 
								street_art_photo 
							WHERE 
								street_art_uno = $sa_uno;";
			$result3 = mysqli_query($connection, $selectqry3);
			
			if(mysqli_num_rows($result2)>0 && mysqli_num_rows($result3)>0)
			{
				while($row2=mysqli_fetch_assoc($result2))
				{
?>
					<!--************************************************************brief view***********************************************************************-->
					
					<tr class="view-row">
						<td colspan=3 class="view-cell">															<!--artist-->
							<font style="font-weight: bold; font-size: 20px; font-family: Tahoma, sans-serif;">
								<?php echo htmlentities($row2['author'], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
<?php
					while($row3=mysqli_fetch_assoc($result3))
					{
?>
						<td style="padding: 15px;">																	<!--photo-->
							<a href="img.php?uno=<?php echo $row3['uno']; ?>"><img src="img.php?uno=<?php echo $row3['uno']; ?>" alt="<?php echo htmlentities($row3['photo_name'], ENT_QUOTES, 'UTF-8'); ?>" style="width: 320px; height: 240px; margin-right: 0px;"></a>
						</td>
<?php
					}
?>
					</tr>
					<tr class="view-row">
						<td colspan=3 class="view-cell">															<!--category-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Category:</b> <?php echo htmlentities($row2['category'], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td colspan=3 class="view-cell">															<!--message-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Message:</b> <?php echo htmlentities($row2['message'], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td colspan=3 class="view-cell">																		<!--location-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Location:</b> <?php echo htmlentities($row2['location'], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td colspan=3 class="view-cell">																		<!--date-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Date:</b> <?php echo htmlentities($row2['date'], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td colspan=3 class="view-cell">																		<!--time-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Time:</b> <?php echo htmlentities($row2['time'], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td colspan=3 class="view-cell">																		<!--latitude-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Latitude:</b> <?php echo htmlentities($row2['lat'], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td colspan=3 class="view-cell">																		<!--longitude-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Longitude:</b> <?php echo htmlentities($row2['lng'], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					<tr class="view-row" style="border-bottom: 10px solid #173457;">
						<td colspan=3>&nbsp;</td>
					</tr>
<?php
				}
			}
		}
    }
?>
	<tr class="view-row" style="border-bottom: 20px solid #173457;">
		<td colspan=3>&nbsp;</td>
	<tr>
	
	<tr style="background: #173457;">
		<td colspan=3 style="">
			<div style="text-align: center;">
<?php
				for($page_number = 1; $page_number<= $total_pages; $page_number++)
				{  
?>
					<a href = "sagi.php?page=<?php echo $page_number; ?>" class="pagenumbers"><?php echo $page_number; ?></a> 	
<?php
				}
?>
			</div>
		</td>
	</tr>

</table>
</body>
</html>
