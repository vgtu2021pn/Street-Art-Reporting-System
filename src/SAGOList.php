<?php
session_start();
if(!isset($_SESSION["username"]) && !isset($_SESSION["usertype"]) && $_SESSION["usertype"] != 2)
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

.view-head {
	background-color: rgba(23, 52, 86, 0.88);
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
				<li class="active"><a href="#">Management of Street Art and (or) Graffiti</a></li>
				<li ><a href="graph.php">Street Art and (or) Graffiti Analysis</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo isset($_SESSION["username"])? $_SESSION["username"] : ''; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Log Out</a></li>
			</ul>
		</div>
	</nav>

<!--****************************************************brief view to expanded view***************************************************-->
<table class="view-table">
	
<?php
    $selectqry = "SELECT * FROM street_art WHERE EXISTS (SELECT * FROM user_street_art WHERE user_street_art.street_art_uno = street_art.street_art_uno);";
	$result = mysqli_query($connection, $selectqry);
	
	$total_rows = mysqli_num_rows($result);
	
	$limit = 5;
    // get the required number of pages
    $total_pages = ceil ($total_rows / $limit);
	
    // update the active page number
    if (!isset ($_GET['page']) ) { 
        $page_number = 1;  
    } else {
        $page_number = (int)$_GET['page'];
    }

    // get the initial page number
    $initial_page = ($page_number-1) * $limit;

    // get data of selected rows per page
    $getQuery = "SELECT * FROM street_art WHERE EXISTS (SELECT * FROM user_street_art WHERE user_street_art.street_art_uno = street_art.street_art_uno) LIMIT " . $initial_page . ',' . $limit;
    $resultLimit = mysqli_query($connection, $getQuery);

    //display the retrieved result on the webpage 
	if(mysqli_num_rows($result)>0)
	{
		$i = 1;
		while ($row = mysqli_fetch_array($resultLimit)) 
		{
			$sa_uno = (int)$row["street_art_uno"];
			
			$selectDamageArtDataqry = "	SELECT 
								* 
							FROM 
								street_art_damage
							WHERE 
								street_art_uno = ?
							ORDER BY 
								created DESC
							LIMIT 1;";
			
			$stmtg = mysqli_prepare($connection, $selectDamageArtDataqry);
			mysqli_stmt_bind_param($stmtg,'i', $sa_uno);
			mysqli_stmt_execute($stmtg);
			$resultDamageArtData = mysqli_stmt_get_result($stmtg);
			
			$rowDamageArtData = mysqli_fetch_assoc($resultDamageArtData);
			mysqli_free_result($resultDamageArtData);
			
			$selectRegistrarDataqry = "	SELECT 
								registrar_status
							FROM 
								registrar
							WHERE 
								street_art_uno = ?
							ORDER BY 
								created DESC
							LIMIT 1;";
			
			$stmtb = mysqli_prepare($connection, $selectRegistrarDataqry);
			mysqli_stmt_bind_param($stmtb,'i', $sa_uno);
			mysqli_stmt_execute($stmtb);
			$resultRegistrarData = mysqli_stmt_get_result($stmtb);
			
			$rowRegistrarData = mysqli_fetch_assoc($resultRegistrarData);
			mysqli_free_result($resultRegistrarData);
			
			$selectqry2 = "SELECT uno, photo_name FROM street_art_photo WHERE street_art_uno = {$sa_uno} LIMIT 1";
			$result2 = mysqli_query($connection, $selectqry2);
			
			if(mysqli_num_rows($result2)>0)
			{
				while($row2=mysqli_fetch_assoc($result2))
				{
?>
					<!--************************************************************brief view***********************************************************************-->
					<tr class="view-row">
						<td class="view-cell view-head">
							<a href="SAGORegistrar.php?street_art_uno=<?php echo $sa_uno; ?>"><span class="glyphicon glyphicon-duplicate"<?php if(!empty($rowRegistrarData['registrar_status'])) {?> style="color:green;"<?php } ?>></span> Registration Process of this Street Art</a>
						</td>
						<td class="view-head">
							<a href="SAGODamage.php?street_art_uno=<?php echo $sa_uno; ?>"><?php if(empty($rowDamageArtData['sticker_status'])) { ?><span class="glyphicon glyphicon-chevron-up" style="color:black;"></span> To place e.Sticker "Damage"<?php } else { ?><span class="glyphicon glyphicon-chevron-down" style="color:red;"></span> To remove e.Sticker "Damage"<?php } ?></a>
						</td>
					</tr>					
					
					<tr class="view-row" onclick="openModal(<?php echo $i; ?>); currentSlide(1, <?php echo $i; ?>)">
						<td class="view-cell">																					<!--author-->
							<font style="font-weight: bold; font-size: 20px; font-family: Tahoma, sans-serif;">
								<?php echo htmlentities($row["author"], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
						
						<td rowspan=6 style="padding: 15px;">																		<!--photo-->
							<img src="img.php?uno=<?php echo $row2['uno']; ?>" alt="<?php echo $row2['photo_name']; ?>" onclick="openModal(<?php echo $i; ?>); currentSlide(1, <?php echo $i; ?>)" class="hover-shadow cursor" style="height: 200px; width: 200px; margin-right: 0px;">
						</td>
					</tr>
					
					<tr class="view-row" onclick="openModal(<?php echo $i; ?>); currentSlide(1, <?php echo $i; ?>)">
						<td class="view-cell">																						<!--category-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Category:</b> <?php echo htmlentities($row["category"], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row" onclick="openModal(<?php echo $i; ?>); currentSlide(1, <?php echo $i; ?>)">
						<td class="view-cell">																						<!--message-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Message:</b> <?php echo htmlentities($row["message"], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row" onclick="openModal(<?php echo $i; ?>); currentSlide(1, <?php echo $i; ?>)">
						<td class="view-cell">																						<!--location-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Location:</b> <?php echo htmlentities($row["location"], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row" onclick="openModal(<?php echo $i; ?>); currentSlide(1, <?php echo $i; ?>)">
						<td class="view-cell">																						<!--date-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Date:</b> <?php echo htmlentities($row["date"], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row" onclick="openModal(<?php echo $i; ?>); currentSlide(1, <?php echo $i; ?>)">
						<td class="view-cell">																						<!--time-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Time:</b> <?php echo htmlentities($row["time"], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row" style="border-bottom: 10px solid #173457;">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					
					<!--*****************************************************************expanded view********************************************************************-->
			
					<div id="myModal<?php echo $i; ?>" class="modal">
		
						<span class="close cursor" onclick="closeModal(<?php echo $i; ?>)">&times;</span>	<!--close icon-->
	
						<div class="modal-content">
<?php				
							$selectqry3 = "SELECT uno, photo_name FROM street_art_photo WHERE street_art_uno = $sa_uno;";
							$result3 = mysqli_query($connection, $selectqry3);
							
							if(mysqli_num_rows($result3)>0)
							{
								$j = 1;
								
								while($row3=mysqli_fetch_assoc($result3))
								{
?>
									<div class="mySlides<?php echo $i; ?>" >
										<div class="numbertext"><?php echo $j; ?> / 4</div>
										<img src="img.php?uno=<?php echo $row3['uno']; ?>" alt="<?php echo $row3['photo_name']; ?>" style="width:100%">
									</div>
<?php
									$j++;
								}
							}
?>

							<a class="prev" onclick="plusSlides(-1,<?php echo $i; ?>)">&#10094;</a>	<!--navigate images forward and backward-->
							<a class="next" onclick="plusSlides(1,<?php echo $i; ?>)">&#10095;</a>

							<div class="caption-container">
								<p id="caption"><hr></p>
							</div>
							
<?php	
							$selectqry4 = "SELECT uno, photo_name FROM street_art_photo WHERE street_art_uno = $sa_uno;";
							$result4 = mysqli_query($connection, $selectqry4);

							if(mysqli_num_rows($result4)>0)
							{
								$x = 1;
								
								while($row4=mysqli_fetch_assoc($result4))
								{
?>
									<div class="column" >	<!--hovering small images-->
										<img class="demo cursor" src="img.php?uno=<?php echo $row4['uno']; ?>" alt="<?php echo $row4['photo_name']; ?>" style="width:100%" onclick="currentSlide(<?php echo $x; ?>, <?php echo $i; ?>)" >
									</div>
<?php
									$x++;
								}
							}
?>
						</div>
<?php
						$selectqry5 = "	SELECT 
											user.uno,
											CONCAT(user.fname, ' ', user.lname) AS fullname,
											user.usertype,
											user.created,
											IF(EXISTS(SELECT 1 FROM user_contact WHERE user_uno = ?), GROUP_CONCAT(CONCAT_WS(': ',user_contact.contact_type,user_contact.contact_data) ORDER BY user_contact.contact_type DESC), '') AS contact
										FROM 
											user
										JOIN 
											user_contact ON user.uno = user_contact.user_uno
										WHERE 
											user.uno = ?;";
						
						$stmtn = mysqli_prepare($connection, $selectqry5);
						mysqli_stmt_bind_param($stmtn,'ii', $row3['uno'], $row3['uno']);
						mysqli_stmt_execute($stmtn);
						$result5 = mysqli_stmt_get_result($stmtn);
						
						if(mysqli_num_rows($result5)>0)
						{
							$row5=mysqli_fetch_assoc($result5)				
?>
								<div style="margin-top: 15%; z-index: 0;">
									
									<div style="display: flex; margin-bottom: 30px; margin-left: 350px;">
										<div style="">
											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Author:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row["author"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>
											
											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Category:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row["category"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>

											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Message:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row["message"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>

											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Description:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row["description"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>

											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Location:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row["location"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>

											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Date:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row["date"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>

											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Time:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row["time"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>
										</div>

										<div>
											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Created:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row5["created"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>

											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Name:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row5["fullname"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>

											<div class="head-div">
												<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b>Contact:</b></div>
												<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($row5["contact"], ENT_QUOTES, 'UTF-8'); ?></div>
											</div>
										</div>

									</div>
									
								</div>
<?php
						}
?>	
					</div>
<?php
					$j++;
				}
			}
			
			$i++;
		}
    }
?>
	<tr class="view-row" style="border-bottom: 20px solid #173457;">
		<td></td>
		<td></td>
	<tr>
	
	<tr style="background: #173457;">
		<td colspan=2 style="">
			<div style="text-align: center;">
<?php
				for($page_number = 1; $page_number<= $total_pages; $page_number++)
				{  
?>
					<a href = "SAGOList.php?page=<?php echo $page_number; ?>" class="pagenumbers"><?php echo $page_number; ?></a> 	
<?php
				}
?>
			</div>
		</td>
	</tr>

</table>
<script>

	function plusSlides(n,z) {
		showSlides(slideIndex += n,z);	//navigate images forward and backward
	}

	function openModal(x) {
		var id = "myModal".concat(x);
		document.getElementById(id).style.display = "block";
	}

	function closeModal(y) {
		var id = "myModal".concat(y);
		document.getElementById(id).style.display = "none";
	}

	var slideIndex = 1;
	showSlides(slideIndex);

	function currentSlide(n, x) {
		showSlides(slideIndex = n, x);
	}

	function showSlides(n, x) {
		var i;
		var className = "mySlides".concat(x);
		var slides = document.getElementsByClassName(className);
		var dots = document.getElementsByClassName("demo");
		var captionText = document.getElementById("caption");
		if (n > slides.length) {slideIndex = 1}
		if (n < 1) {slideIndex = slides.length}
		for (i = 0; i < slides.length; i++) {
			slides[i].style.display = "none";
		}
		for (i = 0; i < dots.length; i++) {
			dots[i].className = dots[i].className.replace(" active", "");
		}
		slides[slideIndex-1].style.display = "block";
		dots[slideIndex-1].className += " active";
		captionText.innerHTML = dots[slideIndex-1].alt;
	}

</script>
    
</body>
</html>
