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
<title>My Account's Removal</title>
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

/****************************************************************Delete**********************************************************************/

if(isset($_POST['btnRemoveUser']))
{
	$oldpwUser = $_POST['oldpwUser'];
	
	$username = $_SESSION['username'];
	$usertype = (int)$_SESSION["usertype"];
	
	$selectqry = "SELECT * FROM user WHERE username=? AND usertype=?;";
	$stmt = mysqli_prepare($connection, $selectqry);
	mysqli_stmt_bind_param($stmt,'si', $username, $usertype);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if($oldpwUser=="")
	{
?>
	    <script> alert("Please Enter required Field. No 5"); </script>
<?php
    }
    else
    {
		if(mysqli_num_rows($result) > 0)    
		{
			while($row = mysqli_fetch_array($result))  
			{
				if(password_verify($oldpwUser, $row["password"]))  
				{
					/*delete user_street_art*/
					$delete_User_Street_Art_qry = "DELETE FROM user_street_art WHERE user_uno = ?;";
					
					$stmto = mysqli_prepare($connection, $delete_User_Street_Art_qry);
					mysqli_stmt_bind_param($stmto,'i', $row['uno']);
					mysqli_stmt_execute($stmto);
					
					/*delete user_contact*/
					$delete_User_Contact_qry = "DELETE FROM user_contact WHERE user_uno = ?;";
					
					$stmtt = mysqli_prepare($connection, $delete_User_Contact_qry);
					mysqli_stmt_bind_param($stmtt,'i', $row['uno']);
					mysqli_stmt_execute($stmtt);
					
					/*delete user_family*/
					$delete_User_Family_qry = "DELETE FROM user_family WHERE user_uno = ? OR user_family_uno = ?;";
					
					$stmtth = mysqli_prepare($connection, $delete_User_Family_qry);
					mysqli_stmt_bind_param($stmtth,'ii', $row['uno'], $row['uno']);
					mysqli_stmt_execute($stmtth);
					
					/*delete user*/
					$delete_User_qry = "DELETE FROM user WHERE uno = ?;";
					
					$stmtf = mysqli_prepare($connection, $delete_User_qry);
					mysqli_stmt_bind_param($stmtf,'i', $row['uno']);
					mysqli_stmt_execute($stmtf);
					
					session_destroy();
					header("Location: Login.php?action=login"); 
				}
				else
				{
					echo '<script>alert("Wrong User Details 1. No 5");</script>';
				}				
			}
		}
		else  
		{
			echo '<script>alert("Wrong User Details 2. No 5");</script>';  
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
						<a class="active" href="userwipe.php"><span class="glyphicon glyphicon-erase"></span> Remove Account</a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>				
			</table>
		</div>
		
		
		<!--****************************************************************Account's removal*****************************************************************-->
		
		<div style="width: 55%; margin-left: 100px;">
			<form name="removeAccountForm" id="removeAccountForm" action="userwipe.php" method="post">
			<table class="view-table" border=0>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							This Form gonna delete "<?php echo htmlentities($rowUserData['fullname'], ENT_QUOTES, 'UTF-8'); ?>'s" Account completely.
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="oldpwUser" type="password" class="form-control" data-type="password" placeholder="Your password*" autocomplete="off">
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="btnRemoveUser" type="submit" class="button" value="Remove It" style="color:white; font-weight:bold; background-color: #173457;" id="btnRemoveAccountUser" onclick="JavaScript:return validateRemoveAccountForm();" />
						</font>
					</td>
				</tr>
			</table>
			</form>
		</div>
	</div>
	<script type="text/javascript">
	function validateRemoveAccountForm() {
		const userResponse = confirm("Do You want to proceed it further?");
		
	    if (userResponse) {
			document.getElementById('removeAccountForm').name='btnRemoveUser';
			document.getElementById('removeAccountForm').action='userwipe.php';
			document.getElementById('removeAccountForm').submit();
	    }
	    else {
			document.getElementById('removeAccountForm').addEventListener('submit',
			function(event) {
				event.preventDefault();
			});
		}
	}
	</script>
</body>  
</html>
