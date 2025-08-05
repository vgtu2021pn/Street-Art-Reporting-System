<?php 
session_start();
if(!isset($_SESSION["username"]) || (isset($_SESSION["usertype"]) && $_SESSION["usertype"] == 0))
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
<title>My Account's Password</title>
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
<?php
if($_SESSION["usertype"] == 1)
{
?>			
				<li><a href="ReportStrArt.php">Report a Street Art and (or) Graffiti</a></li>
<?php
}
elseif($_SESSION["usertype"] == 2)
{
?>
				<li><a href="SAGOList.php">Management of Street Art and (or) Graffiti</a></li>
<?php
}
?>
				<li ><a href="graph.php">Street Art and (or) Graffiti Analysis</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li class="active"><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo htmlentities($_SESSION["username"], ENT_QUOTES, 'UTF-8'); ?> </a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span>Log Out</a></li>
			</ul>
		</div>
	</nav>
	
<?php

/****************************************************************Change Password**********************************************************************/

if(isset($_POST['btnChangePasswdArtist']))
{
	$oldpwArtist = $_POST['oldpwArtist'];
	$newpwArtist = $_POST['newpwArtist'];
	$repnewpwArtist = $_POST['repnewpwArtist'];
	$hashednewpwArtist = password_hash($newpwArtist,PASSWORD_DEFAULT);
	
	$username = $_SESSION['username'];
	$usertype = (int)$_SESSION["usertype"];
	
	$selectqry = "SELECT * FROM user WHERE username=? AND usertype=?;";
	$stmt = mysqli_prepare($connection, $selectqry);
	mysqli_stmt_bind_param($stmt,'ss', $username, $usertype);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if($oldpwArtist=="" || $newpwArtist=="" || $repnewpwArtist=="")
	{
?>
	    <script> alert("Please Enter required Fields. No 3"); </script>
<?php
    }
	elseif($newpwArtist!=$repnewpwArtist)
	{
?>
            <script> alert("Password Fields are not matched. No 3"); </script>
<?php
	}
    else
    {
		if(mysqli_num_rows($result) > 0) 
		{
			while($row = mysqli_fetch_array($result))
			{
				if(password_verify($oldpwArtist, $row["password"]))
				{
					$updateArtist = "UPDATE 
										user
									SET
										password = ?
									WHERE
										username = ? 
									AND 
										usertype = ?;";
		
					$updateprepare = mysqli_prepare($connection, $updateArtist);
					mysqli_stmt_bind_param($updateprepare, 'sss', $hashednewpwArtist, $username, $usertype);
					mysqli_stmt_execute($updateprepare);
					
					if($usertype == 1)
					{
						header("Location: useraccount.php");
					}
					elseif($usertype == 2)
					{
						header("Location: SAGOList.php");
					}
				}
				else
				{
					echo '<script>alert("Wrong User Details 1. No 3");</script>';
				}
			}
		}
		else  
		{
			echo '<script>alert("Wrong User Details 2. No 3");</script>';  
		}
	}
	mysqli_free_result($result);
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
<?php
if($_SESSION["usertype"] == 1)
{
?>					
					<td class="user-cell">Artist</td>
<?php
}
elseif($_SESSION["usertype"] == 2)
{
?>
					<td class="user-cell">Evaluator</td>
<?php
}
?>		
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
<?php
if($_SESSION["usertype"] == 1)
{
?>	
					<td class="user-cell" rowspan=9><b>Initiate:</b></td>
<?php
}
elseif($_SESSION["usertype"] == 2)
{
?>	
					<td class="user-cell" rowspan=7><b>Initiate:</b></td>
<?php
}
else
{
?>
					<td class="user-cell" rowspan=7><b>Initiate:</b></td>
<?php
}
?>
					<td class="user-cell">
						<a href="useraccount.php"><span class="glyphicon glyphicon-home"></span> Home</a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a class="active" href="userpasswd.php"><span class="glyphicon glyphicon-lock"></span> Password change</a>
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
<?php
if($_SESSION["usertype"] == 1)
{
?>
				<tr>
					<td class="user-cell">
						<a href="userfamily.php"><span class="glyphicon glyphicon-apple"></span> New Family Account</a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
<?php
}
?>
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
		
		<div style="width: 55%; margin-left: 100px;">
			<form name="changePasswdForm" id="changePasswdForm" action="userpasswd.php" method="post">
			<table class="view-table" border=0>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="oldpwArtist" type="password" class="form-control" data-type="password" placeholder="Your password*" autocomplete="off">
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="newpwArtist" type="password" class="form-control" data-type="password" placeholder="New password*" autocomplete="off">
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="repnewpwArtist" type="password" class="form-control" data-type="password" placeholder="Repeat new password*" autocomplete="off">
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="btnChangePasswdArtist" type="submit" class="button" value="Change It" style="color:white; font-weight:bold; background-color: #173457;" id="btnChangePasswdArtist" onclick="JavaScript:return validateChangePasswdForm();" />
						</font>
					</td>
				</tr>
			</table>
			</form>
		</div>
	</div>
	<script type="text/javascript">
	function validateChangePasswdForm() {
		
	}
	</script>
</body>  
</html>
