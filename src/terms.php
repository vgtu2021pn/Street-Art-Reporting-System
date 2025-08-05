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
	die("Connection failed: " .mysqli_connect_error());
}
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>
<meta charset="utf-8">
<title>Terms</title>
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
if($_SESSION["usertype"] == 0)
{
?>			
				<li><a href="sagi.php">List of a Street Art and (or) Graffiti</a></li>
<?php
}
elseif($_SESSION["usertype"] == 1)
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

/****************************************************************Update Terms**********************************************************************/

if(isset($_POST['btnUpdateTerms']) && ($_SESSION["usertype"] == 1 || $_SESSION["usertype"] == 2))
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
	    <script> alert("Please Enter required Field. No 6"); </script>
<?php
    }
    else
    {
		if(mysqli_num_rows($result) > 0)    
		{
			while($row = mysqli_fetch_array($result))  
			{
				if(empty($row["adulthood"])) {
					$adult = ($_POST['adult'] == 'on')? 1 : 0;
				}
				if(empty($row["termsofservice"])) {
					$termsofservice = ($_POST['termsofservice'] == 'on')? 1 : 0;
				}
				
				if(password_verify($oldpwUser, $row["password"]))  
				{
					if(empty($row["adulthood"])) {
						$update = "UPDATE 
										user
									SET
										adulthood = ?
									WHERE
										uno = ?;";
			
						$updateprepare = mysqli_prepare($connection, $update);
						mysqli_stmt_bind_param($updateprepare, 'ii', $adult, $row["uno"]);
						mysqli_stmt_execute($updateprepare);
					}
					if(empty($row["termsofservice"])) {
						$update2 = "UPDATE 
										user
									SET
										termsofservice = ?
									WHERE
										uno = ?;";
			
						$updateprepare2 = mysqli_prepare($connection, $update2);
						mysqli_stmt_bind_param($updateprepare2, 'ii', $termsofservice, $row["uno"]);
						mysqli_stmt_execute($updateprepare2);
					}
					
					echo '<script> alert("Process was completed."); </script>';
				}
				else
				{
					echo '<script>alert("Wrong User Details 1. No 6");</script>';
				}				
			}
		}
		else  
		{
			echo '<script>alert("Wrong User Details 2. No 6");</script>';  
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
if($_SESSION["usertype"] == 0)
{
?>					
					<td class="user-cell">Spectator</td>
<?php
}
elseif($_SESSION["usertype"] == 1)
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
						<a class="active" href="terms.php"><span class="glyphicon glyphicon<?php echo (empty($rowUserData['termsofservice']) && !empty($usertype))? "-warning-sign" : "-list-alt"; ?>"></span> Actual Terms</a>
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
		
		
		<!--****************************************************************Terms*****************************************************************-->
		
		<div style="width: 55%; margin-left: 100px;">
			<table class="view-table" border=0>
				<tr class="view-row">
					<th class="view-cell">
						No.
					</th>	
					<th class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							Official public documents of the Site 
						</font>
					</th>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						1.
					</td>									
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							HTTP Cookie technology is mandatory for this Site. Site does not deploy special HTTP Cookies for advertisement or user tracking.
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						2.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							The Site's security representative, data protection officer, computer security incident response team accept any Security, Privacy and Cybersecurity breach Notification(s) according the <b>Resposible Disclosure Terms</b>. 
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						3.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							Information about storage & management & disposal of User data are available in the <b>Privacy Policy Terms</b>. 
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						4.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							 Agreement between service provider and service consumer, copyright compliance or services's License Terms offer to service consumers are found in the <b>Terms of Service</b>. 
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						5.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							 When service offer purchases of Goods (e.g. Colorful Photo Prints) and Services (e.g. subscription of newsletter & newspaper), then plausible disputes regarding communicated Quality of Goods and Services are found in the <b>Return and Refund Policy Terms</b>. 
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						6.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							 When service offer delivery of Goods (e.g. Colorful Photo Prints) and Services (e.g. payment of subscription, tracking of purchased Goods), then delivery rules, descriptions, terms and definitions are found in the <b>Shipping Policy Terms</b>. 
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						7.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							 Consumers must receive current Contact Information of Service provider, that are found in the <b>Contact Information Terms</b>. 
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						8.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							 Service provider include short information about jurisdictional entity who is offering this Service, that are available in the <b>Legal notice</b>.
						</font>
					</td>
				</tr>
			</table>			
<?php
if(!empty($usertype))
{
?>
			<form name="updateTermsForm" id="updateTermsForm" action="terms.php" method="post">
			<table class="view-table" border=0>
				<tr class="view-row">
					<th class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							Following approval in force
						</font>
					</th>
				</tr>
<?php
if(empty($rowUserData['adulthood']) || empty($rowUserData['termsofservice']))
{
?>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="oldpwUser" type="password" class="form-control" data-type="password" placeholder="Your password*" autocomplete="off">
						</font>
					</td>
				</tr>
<?php
}
if(empty($rowUserData['adulthood']))
{
?>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="adult" id="approveAdultUser" type="checkbox">
							<label for="approveAdultUser">I'm approving, that I'm or have been 18 Years old.</label>
						</font>
					</td>
				</tr>
<?php
}
if(empty($rowUserData['termsofservice']))
{
?>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="termsofservice" id="approveTermsOfServiceUser" type="checkbox">
							<label for="approveTermsOfServiceUser">I'm accepting Terms of Service of this Site.</label>
						</font>
					</td>
				</tr>
<?php
}
else
{
?>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							You have accepted Terms of Service of this Site.
						</font>
					</td>
				</tr>
<?php
}
if(empty($rowUserData['adulthood']) || empty($rowUserData['termsofservice']))
{
?>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="btnUpdateTerms" type="submit" class="button" value="Accept It" style="color:white; font-weight:bold; background-color: #173457;" id="btnUpdateTerms" onclick="JavaScript:return validateUpdateTermsForm();" />
						</font>
					</td>
				</tr>
<?php
}
?>
			</table>
			</form>
<?php
}
?>
		</div>
	</div>
<?php
if(!empty($usertype))
{
?>
	<script type="text/javascript">
	function validateUpdateTermsForm() {
		const userResponse = confirm("Do You want to proceed it further?");
		
	    if (userResponse) {
			document.getElementById('updateTermsForm').name='btnUpdateTerms';
			document.getElementById('updateTermsForm').action='terms.php';
			document.getElementById('updateTermsForm').submit();
	    }
	    else {
			document.getElementById('updateTermsForm').addEventListener('submit',
			function(event) {
				event.preventDefault();
			});
		}
	}
	</script>
<?php
}
?>	
</body>  
</html>
