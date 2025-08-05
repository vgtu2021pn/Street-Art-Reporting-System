<?php 
session_start();
if(!isset($_SESSION["username"]) || (isset($_SESSION["usertype"]) && $_SESSION["usertype"] == 2) || (isset($_SESSION["usertype"]) && $_SESSION["usertype"] == 0))
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
	die("Connection failed: " .mysqli_connect_error());
}
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>
<meta charset="utf-8">
<title>My Account</title>
<link rel="icon" href="images/brush.webp" type="image/webp">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<style>
a.active {
	color: #D34143;
}

.user-cell{
	padding: 0px;
}

table.view-table{
	width: 100%;
	margin-left: auto;
	margin-right: auto;
	background-color: rgba(192, 192, 192, 0.5);
	color: black;
	margin-bottom: 20px;
}

tr.view-row{
	border-collapse: collapse;
}

td.view-cell{
	padding: 10px 10px 10px 10px; /*top right bottom left*/
}

.edit-icons{
	height: 25px;
	width: 25px;
}

.edit-buttons{
	padding: 8px;
	border: 1px solid black;
	background-color: #173457;
	border-radius: 20px;
}

.edit-buttons:hover{
	background-color: rgba(250, 250, 250, 0.5);
	border: 0px;
}

.view-foot {
	background-color: rgba(23, 52, 86, 0.88);
}

.view-sticker {
	background: linear-gradient(rgba(192, 192, 192, 0.5), orange);
	color: white;
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
				<li><a href="ReportStrArt.php">Report a Street Art and (or) Graffiti</a></li>
				<li ><a href="graph.php">Street Art and (or) Graffiti Analysis</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li class="active"><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo htmlentities($_SESSION["username"], ENT_QUOTES, 'UTF-8'); ?> </a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span>Log Out</a></li>
			</ul>
		</div>
	</nav>
	
<?php

/****************************************************************Delete**********************************************************************/

if(isset($_POST['delete']))
{
	$sa_uno = (int)$_POST['sa_uno'];
	$username = $_SESSION["username"];
	$usertype = (int)$_SESSION["usertype"];

	$selectUserDataqry = "SELECT uno, username FROM user WHERE username = ? AND usertype = ?;";
	
	$stmt = mysqli_prepare($connection, $selectUserDataqry);
	mysqli_stmt_bind_param($stmt,'si', $username, $usertype);
	mysqli_stmt_execute($stmt);
	$resultUserData = mysqli_stmt_get_result($stmt);
	
	$checkqry = "SELECT
					IF(EXISTS(SELECT 1 FROM registrar WHERE street_art_uno = ? ORDER BY created DESC LIMIT 1), 1, 0) AS one,
					IF(EXISTS(SELECT 1 FROM street_art_damage WHERE street_art_uno = ? ORDER BY created DESC LIMIT 1), 1, 0) AS two;";
				
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'ii', $sa_uno, $sa_uno);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if(mysqli_num_rows($resultUserData) > 0 && mysqli_num_rows($result2) > 0)
	{
		$rowUserData = mysqli_fetch_assoc($resultUserData);
		$rowCheckData = mysqli_fetch_assoc($result2);
		
		if($rowCheckData['one']== 1 || $rowCheckData['two']== 1)
        {
?>
	    <script> alert("Data of Street Art and (or) Graffiti can't be deleted. No 0"); </script>
<?php
        }
        else
        {
			/*delete users_street_art*/
			$delete_User_Street_Art_qry = "DELETE FROM user_street_art WHERE street_art_uno = ? AND user_uno = ?;";
	
			$stmto = mysqli_prepare($connection, $delete_User_Street_Art_qry);
			mysqli_stmt_bind_param($stmto,'ii', $sa_uno, $rowUserData['uno']);
			mysqli_stmt_execute($stmto);
	
			/*delete street_art_photo*/
			$delete_Street_Art_Photo_qry = "DELETE FROM street_art_photo WHERE street_art_uno = ?;";
	
			$stmtt = mysqli_prepare($connection, $delete_Street_Art_Photo_qry);
			mysqli_stmt_bind_param($stmtt,'i', $sa_uno);
			mysqli_stmt_execute($stmtt);
	
			/*delete street_art*/
			$delete_Street_Art_qry = "DELETE FROM street_art WHERE street_art_uno = ?;";
	
			$stmtth = mysqli_prepare($connection, $delete_Street_Art_qry);
			mysqli_stmt_bind_param($stmtth,'i', $sa_uno);
			mysqli_stmt_execute($stmtth);
	
			$sel_User_Street_Art_qry = "SELECT street_art_uno FROM user_street_art WHERE street_art_uno = ? AND user_uno = ?;";
	
			$stmtf = mysqli_prepare($connection, $sel_User_Street_Art_qry);
			mysqli_stmt_bind_param($stmtf,'ii', $sa_uno, $rowUserData['uno']);
			mysqli_stmt_execute($stmtf);
	
			$result_sel_User_Street_Art = mysqli_stmt_get_result($stmtf);
	
			if(mysqli_num_rows($result_sel_User_Street_Art) == 0)
			{
				header("Refresh: 3");
?>
			<div class="container">
				<div class="alert alert-danger alert-dismissible fade in">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					Your report with unique No <?php echo $sa_uno; ?> has been deleted successfully.
				</div>
			</div>
<?php
			}
			mysqli_free_result($result_sel_User_Street_Art);
		}
	}
	mysqli_free_result($resultUserData);
	mysqli_free_result($result2);
}
?>
	
	<!--****************************************************************Displaying user info*****************************************************************-->
 
	<div class="container" style="width: 100%; color: white; display: flex;">

<?php  
$username = $_SESSION["username"];
$usertype = (int)$_SESSION["usertype"];

$selectUserDataqry = "SELECT 
							uno,
							CONCAT(fname, ' ', lname) AS fullname,
							usertype,
							adulthood,
							termsofservice,
							created
						FROM
							user
						WHERE
							username = ?
						AND
							usertype = ?;";

$stmt = mysqli_prepare($connection, $selectUserDataqry);
mysqli_stmt_bind_param($stmt,'si', $username, $usertype);
mysqli_stmt_execute($stmt);
$resultUserData = mysqli_stmt_get_result($stmt);

$rowUserData = mysqli_fetch_assoc($resultUserData);
?>
		
		<!--****************************************************************User data*****************************************************************-->
		
		<div style="width: 30%; padding: 0px 50px 50px 50px;">
			<table border=0 style="width: 100%; ">
				<tr>
					<td align="center" colspan=2>
						<img src="images/user.jpg" style="height: 100px; width: 100px; border-radius: 50px;">
					</td>
				</tr>
				<tr>
					<td class="user-cell" align="center" colspan=2>
						<h1>Welcome - <?php echo htmlentities($username, ENT_QUOTES, 'UTF-8'); ?></h1>
					</td>
				</tr>
<?php
if(!empty($rowUserData['fullname']))
{
?>
				<tr>
					<td class="user-cell"style="width: 50%"><b>Name:</b></td>
					<td class="user-cell">
						<?php echo htmlentities($rowUserData['fullname'], ENT_QUOTES, 'UTF-8'); ?>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
<?php
}
?>
				<tr>
					<td class="user-cell"><b>Type:</b></td>
					<td class="user-cell">Artist</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell"><b>Created:</b></td>
					<td class="user-cell">
						<?php echo htmlentities($rowUserData['created'], ENT_QUOTES, 'UTF-8'); ?>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell" rowspan=9><b>Initiate:</b></td>
					<td class="user-cell">
						<a class="active" href="useraccount.php"><span class="glyphicon glyphicon-home"></span> Home</a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a href="userpasswd.php"><span class="glyphicon glyphicon-lock"></span> Password change</a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a href="terms.php"><span class="glyphicon glyphicon<?php echo empty($rowUserData['termsofservice'])? "-warning-sign" : "-list-alt"; ?>"></span> Actual Terms</a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a href="userfamily.php"><span class="glyphicon glyphicon-apple"></span> New Family Account</a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a href="userwipe.php"><span class="glyphicon glyphicon-erase"></span> Remove Account</a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>			
			</table>
		</div>
		
		
		<!--****************************************************************User Reports*****************************************************************-->
		
		<div style="width: 55%; margin-left: 100px;">
			<table class="view-table" border=0>
<?php
$selectHistoryqry = "	SELECT 
							rt.*,
							(SELECT re.sticker_status FROM street_art_damage AS re WHERE re.street_art_uno = rt.street_art_uno ORDER BY created DESC LIMIT 1) AS sticker_status
						FROM 
							street_art AS rt
						WHERE 
							rt.street_art_uno IN (	SELECT 
													street_art_uno 
												FROM 
													user_street_art 
												WHERE 
													user_uno IN (	SELECT 
																		uno 
																	FROM 
																		user 
																	WHERE 
																		username = ? 
																	AND 
																		usertype = ?)
												);";

$stmth = mysqli_prepare($connection, $selectHistoryqry);
mysqli_stmt_bind_param($stmth,'si', $username, $usertype);
mysqli_stmt_execute($stmth);

$resultHistory = mysqli_stmt_get_result($stmth);

if(mysqli_num_rows($resultHistory)>0)
{
	while($rowHistory = mysqli_fetch_assoc($resultHistory))
	{
		$sa_uno = (int)$rowHistory['street_art_uno'];
		
		$selectHistoryImageqry = "SELECT uno, photo_name FROM street_art_photo WHERE street_art_uno = ? LIMIT 1";
		
		$stmtf = mysqli_prepare($connection, $selectHistoryImageqry);
		mysqli_stmt_bind_param($stmtf,'i', $sa_uno);
		mysqli_stmt_execute($stmtf);
		
		$resultHistoryImg = mysqli_stmt_get_result($stmtf);
		$rowHistoryImg = mysqli_fetch_assoc($resultHistoryImg);
?>
				<tr class="view-row">
					<td class="view-cell">																											<!--author-->
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<b>Author:</b> <?php echo htmlentities($rowHistory['author'], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
					<td class="view-cell" rowspan=7 align="right">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">															<!--photo-->
							<b>Photo:</b> <img src="img.php?uno=<?php if(!empty($rowHistoryImg['uno'])) { echo $rowHistoryImg['uno']; } ?>" class="hover-shadow cursor" style="height: 230px; width: 230px; margin-right: 0px;" alt="<?php if(!empty($rowHistoryImg['photo_name'])) { echo htmlentities($rowHistoryImg['photo_name'], ENT_QUOTES, 'UTF-8'); } ?>">
						</font>
					</td>
					<td class="view-cell" style="background-color: #173457; padding-left: 20px;" rowspan=6>
						<form method="POST" action="updatestrart.php">
							<input type="hidden" name="sa_uno" value="<?php echo $sa_uno; ?>">
							<button type="submit" name="update" class="edit-buttons"><img src="images/edit.png" class="edit-icons"></button>		<!--update-->
						</form>
						
						<form method="POST" action="useraccount.php">
							<input type="hidden" name="sa_uno" value="<?php echo $sa_uno; ?>">	
							<button type="submit" name="delete" class="edit-buttons"><img src="images/delete.png" class="edit-icons"></button>		<!--delete-->
						</form>
					</td>	
				</tr>
<?php
		if(!empty($rowHistory["category"]))
		{
?>
				<tr class="view-row">
					<td class="view-cell">																						<!--category-->
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<b>Category:</b> <?php echo htmlentities($rowHistory["category"], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>
<?php
		}
		if(!empty($rowHistory["message"]))
		{
?>
				<tr class="view-row">
					<td class="view-cell">																						<!--message-->
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<b>Message:</b> <?php echo htmlentities($rowHistory["message"], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>
<?php
		}
		if(!empty($rowHistory["description"]))
		{
?>
				<tr class="view-row">
					<td class="view-cell">																						<!--description-->
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<b>Description:</b> <?php echo htmlentities($rowHistory["description"], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>
<?php
		}
		if(!empty($rowHistory["location"]))
		{
?>
				<tr class="view-row">
					<td class="view-cell">																						<!--location-->
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<b>Location:</b> <?php echo htmlentities($rowHistory["location"], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>
<?php
		}
		if(!empty($rowHistory["date"]))
		{
?>
				<tr class="view-row">
					<td class="view-cell">																						<!--date-->
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<b>Date:</b> <?php echo htmlentities($rowHistory['date'], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>
<?php
		}
		if(!empty($rowHistory["time"]))
		{
?>
				<tr class="view-row">
					<td colspan=3 class="view-cell">																						<!--time-->
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<b>Time:</b> <?php echo htmlentities($rowHistory['time'], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>
<?php
		}
		if(!empty($rowHistory['sticker_status'])) 
		{
?>
				<tr class="view-row">
					<td colspan=3 class="view-cell view-sticker">																											<!--sticker-->
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<i><center>DAMAGE<center></i>
						</font>
					</td>
				</tr>
<?php
		}
?>
				<tr class="view-row">
					<td colspan=3 class="view-cell">																						<!--time-->
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<b>Created:</b> <?php echo htmlentities($rowHistory['created'], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>
				<tr class="view-row view-foot">
					<td colspan=3>																						<!--time-->
						&nbsp;
					</td>
				</tr>
<?php
	}
}
?>
			</table>
		</div>
	</div>
</body>  
</html>
