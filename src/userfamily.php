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
	<title>My Account's Family</title>
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

	/****************************************************************Create Account's Family Account**********************************************************************/

	if(isset($_POST['btnCreateFamilyArtist']))
	{
		$fnameArtist = $_POST['fnameArtist'];
		$lnameArtist = $_POST['lnameArtist'];
		$pseudonyme = $_POST['pseudonyme'];
		$oldpwArtist = $_POST['oldpwArtist'];
		$newpwArtist = $_POST['newpwArtist'];
		$repnewpwArtist = $_POST['repnewpwArtist'];
		$hashednewpwArtist = password_hash($newpwArtist,PASSWORD_DEFAULT);
		$adult = 0;
		$termsofservice = 0;
		
		$username = $_SESSION['username'];
		$usertype = (int)$_SESSION['usertype'];
		
		$selectqry = "SELECT * FROM user WHERE username=? AND usertype=?;";
		$stmt = mysqli_prepare($connection, $selectqry);
		mysqli_stmt_bind_param($stmt,'si', $username, $usertype);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		$checkqry = "SELECT uno, username FROM user WHERE username=? AND usertype=?;";
		$chstmt = mysqli_prepare($connection, $checkqry);
		mysqli_stmt_bind_param($chstmt,'si', $pseudonyme, $usertype);
		mysqli_stmt_execute($chstmt);
		$result2 = mysqli_stmt_get_result($chstmt);
		
		if($username=="" || $newpwArtist=="" || $repnewpwArtist=="" || $oldpwArtist=="")
		{
	?>
			<script> alert("Please Enter required Fields. No 4"); </script>
	<?php
		}
		elseif(mysqli_num_rows($result2) > 0)
		{
	?>
			<script> alert("Please Enter different Pseudonyme. No 4"); </script>
	<?php	    
		}
		elseif($newpwArtist!=$repnewpwArtist)
		{
	?>
				<script> alert("Password Fields are not matched. No 4"); </script>
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
						$insertArtist = "INSERT INTO user (fname,lname,username,usertype,password,adulthood,termsofservice) VALUES (?,?,?,?,?,?,?);";
						
						$insertprepare = mysqli_prepare($connection, $insertArtist);
						mysqli_stmt_bind_param($insertprepare, 'sssisii', $fnameArtist, $lnameArtist, $pseudonyme, $usertype, $hashednewpwArtist, $adult, $termsofservice);
						mysqli_stmt_execute($insertprepare);
						
						$sel_New_Family_qry = "SELECT uno, username FROM user WHERE username=? AND usertype=?;";
						
						$stmtf = mysqli_prepare($connection, $sel_New_Family_qry);
						mysqli_stmt_bind_param($stmtf,'si', $pseudonyme, $usertype);
						mysqli_stmt_execute($stmtf);
						$result_sel_New_Family = mysqli_stmt_get_result($stmtf);
						
						if(mysqli_num_rows($result_sel_New_Family) > 0)
						{
							$rowUserData = mysqli_fetch_assoc($result_sel_New_Family);
							
							$insertFamily = "INSERT INTO user_family (user_uno,user_family_uno) VALUES (?,?);";
							
							$insertprepare2 = mysqli_prepare($connection, $insertFamily);
							mysqli_stmt_bind_param($insertprepare2, 'ii', $row['uno'], $rowUserData['uno']);
							mysqli_stmt_execute($insertprepare2);
							
							header("Refresh: 3");
?>
							<div class="container">
								<div class="alert alert-success alert-dismissible fade in">
									<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
									Your family account with unique Pseudonyme <?php echo $pseudonyme; ?> has been created successfully.
								</div>
							</div>
<?php
						}
					}
					else
					{
						echo '<script>alert("Wrong User Details 1. No 4");</script>';
					}
				}
				mysqli_free_result($result_sel_New_Family);	
			}
			else  
			{
				echo '<script>alert("Wrong User Details 2. No 4");</script>';  
			}
		}
		mysqli_free_result($result);
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
					<tr>
						<td class="user-cell">
							<a class="active" href="userfamily.php"><span class="glyphicon glyphicon-apple"></span> New Family Account</a>
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
			
			
			<!--****************************************************************Account's Family Account*****************************************************************-->
			
			<div style="width: 55%; margin-left: 100px;">
				<form name="createFamilyForm" id="createFamilyForm" action="userfamily.php" method="post">
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
								<input name="fnameArtist" type="text" class="form-control" placeholder="First Name" autocomplete="off">
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td class="view-cell">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<input name="lnameArtist" type="text" class="form-control" placeholder="Last Name" autocomplete="off">
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td class="view-cell">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<input name="pseudonyme" type="text" class="form-control" placeholder="Family Pseudonyme*" autocomplete="off">
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td class="view-cell">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<input name="newpwArtist" type="password" class="form-control" data-type="password" placeholder="Family password*" autocomplete="off">
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td class="view-cell">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<input name="repnewpwArtist" type="password" class="form-control" data-type="password" placeholder="Repeat family password*" autocomplete="off">
							</font>
						</td>
					</tr>
					<tr class="view-row">
						<td class="view-cell">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<input name="btnCreateFamilyArtist" type="submit" class="button" value="Create It" style="color:white; font-weight:bold; background-color: #173457;" id="btnCreateFamilyArtist" onclick="JavaScript:return validateCreateFamilyForm();" />
							</font>
						</td>
					</tr>
				</table>
				</form>
				<table class="view-table" border=0>
					<tr class="view-row">
						<th class="view-cell" align="left">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								First Name & Last Name
							</font>
						</th>
						<th class="view-cell" align="left">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								Account No
							</font>
						</th>
						<th class="view-cell" align="left">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								Date & Time
							</font>
						</th>	
					</tr>
	<?php
	$selectFamilyqry = "SELECT * FROM user_family WHERE user_uno IN (SELECT uno FROM user WHERE username = ? AND usertype = ?);";

	$stmth = mysqli_prepare($connection, $selectFamilyqry);
	mysqli_stmt_bind_param($stmth,'si', $username, $usertype);
	mysqli_stmt_execute($stmth);

	$resultHistory = mysqli_stmt_get_result($stmth);

	if(mysqli_num_rows($resultHistory)>0)
	{
		while($rowHistory = mysqli_fetch_assoc($resultHistory))
		{
			$fa_uno = (int)$rowHistory['user_family_uno'];
			
			$selectHistoryqry = "SELECT 
								uno,
								CONCAT(fname, ' ', lname) AS fullname,
								created
							FROM
								user
							WHERE
								uno = ?;";
			$stmtfr = mysqli_prepare($connection, $selectHistoryqry);
			mysqli_stmt_bind_param($stmtfr,'i', $fa_uno);
			mysqli_stmt_execute($stmtfr);
			
			$resultHistoryDat = mysqli_stmt_get_result($stmtfr);
			$rowHistoryDat = mysqli_fetch_assoc($resultHistoryDat);
	?>
					<tr class="view-row">
						<td class="view-cell">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Account User:</b> <?php echo htmlentities($rowHistoryDat["fullname"], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
						<td class="view-cell" align="right">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Account Number:</b> <?php echo htmlentities($rowHistory["uno"], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
						<td class="view-cell" align="right">
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b>Created:</b> <?php echo htmlentities($rowHistoryDat["created"], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>	
					</tr>
	<?php
		}
		mysqli_free_result($resultHistory);
		mysqli_free_result($resultHistoryDat);
	}
	?>
				</table>
			</div>
		</div>
		<script type="text/javascript">
		function validateCreateFamilyForm() {

		}
		</script>
	</body>  
	</html>
