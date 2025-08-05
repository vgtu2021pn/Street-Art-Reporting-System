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
	die("Connection failed: " .mysqli_connect_error());
}
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>
<meta charset="utf-8">
<title>Registration of Street Art and (or) Graffiti</title>
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
				<li class="active"><a href="#">Management of Street Art and (or) Graffiti</a></li>
				<li ><a href="graph.php">Street Art and (or) Graffiti Analysis</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Log Out</a></li>
			</ul>
		</div>
	</nav>
	
<?php

/****************************************************************Adding Sticker**********************************************************************/

if(isset($_POST['street_art_uno']) || isset($_GET['street_art_uno']))
{
	if(isset($_POST['street_art_uno']))
	{
		$sa_uno = (int)$_POST['street_art_uno'];
	}
	else
	{
		$sa_uno = (int)$_GET['street_art_uno'];
	}
	
	$checkqry = "SELECT * FROM street_art WHERE street_art_uno=?;";
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'i', $sa_uno);
	mysqli_stmt_execute($chstmt);
	$result = mysqli_stmt_get_result($chstmt);

	if(empty(mysqli_num_rows($result)))
	{
		mysqli_free_result($result);
		header("Location: SAGOList.php");
	}
	mysqli_free_result($result);
}
else
{
	header("Location: SAGOList.php");
}

if(isset($_POST['btnRegister']))
{
	$reg_number = $_POST['reg_number'];
	$reg_date = $_POST['reg_date'];
	$reg_status = ($_POST['reg_status'] == 'register')? 1 : 0;
	
	$checkqry = "SELECT 
					uno
				FROM 
					registrar 
				WHERE 
					registrar_number=? 
				AND 
					registrar_date=?;";
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'is', $reg_number, $reg_date);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	$check2qry = "SELECT 
					*
				FROM 
					street_art_damage
				WHERE 
					street_art_uno = ?
				AND
					sticker_status = 1
				ORDER BY 
					created DESC
				LIMIT 1;";
	$ch2stmt = mysqli_prepare($connection, $check2qry);
	mysqli_stmt_bind_param($ch2stmt,'i', $sa_uno);
	mysqli_stmt_execute($ch2stmt);
	$result3 = mysqli_stmt_get_result($ch2stmt);
	
	if($reg_number=="" || $reg_date=="")
	{
?>
	    <script> alert("Please Enter required Field. No 8"); </script>
<?php
    }
    elseif(mysqli_num_rows($result2) > 0)
	{
?>
	    <script> alert("Please Enter different Date and (or) Number. No 8"); </script>
<?php	    
	}
	elseif($reg_status == 1 && mysqli_num_rows($result3) > 0)
	{
?>
	    <script> alert("Please Remove Damage Sticker. No 8"); </script>
<?php	
	}
    else
    {
		$selectqry = "SELECT max(uno) as maxuno from registrar;";
		if($result=mysqli_query($connection,$selectqry))
		{
			$row=mysqli_fetch_assoc($result);
			if(is_null($row['maxuno'])) { $uno=1; }
			else { $uno = (int)$row['maxuno']+1; }
			mysqli_free_result($result);
		}
		
		$insertRegistrar = "INSERT INTO registrar (uno,street_art_uno,registrar_number,registrar_date,registrar_status) VALUES (?,?,?,?,?);";
	    
	    $insertprepare = mysqli_prepare($connection, $insertRegistrar);
	    mysqli_stmt_bind_param($insertprepare, 'iiisi', $uno, $sa_uno, $reg_number, $reg_date, $reg_status);
	    mysqli_stmt_execute($insertprepare);
	    
	    $sel_Registrar_qry = "SELECT uno FROM registrar WHERE uno = ?;";
	    
	    $stmtf = mysqli_prepare($connection, $sel_Registrar_qry);
	    mysqli_stmt_bind_param($stmtf,'i', $uno);
	    mysqli_stmt_execute($stmtf);
	    
	    $result = mysqli_stmt_get_result($stmtf);
	    
	    if(mysqli_num_rows($result) != 0)
	    {
			header("Refresh: 3");
?>
			<div class="container">
				<div class="alert alert-info alert-dismissible fade in">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					Street Art No. <?php echo $sa_uno; ?> has been registered successfully.
				</div>
			</div>
<?php
		}
		mysqli_free_result($result);
	}
	mysqli_free_result($result2);
}
?>
	
	<!--****************************************************************Displaying info*****************************************************************-->
 
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
mysqli_free_result($resultUserData);

$selectStreetArtDataqry = "	SELECT 
								* 
							FROM 
								street_art 
							WHERE 
								EXISTS 
									(SELECT 
										* 
									FROM 
										user_street_art 
									WHERE 
										user_street_art.street_art_uno = street_art.street_art_uno)
								AND
									street_art_uno = ?;";

$stmth = mysqli_prepare($connection, $selectStreetArtDataqry);
mysqli_stmt_bind_param($stmth,'i', $sa_uno);
mysqli_stmt_execute($stmth);
$resultStreetArtData = mysqli_stmt_get_result($stmth);

$rowStreetArtData = mysqli_fetch_assoc($resultStreetArtData);
mysqli_free_result($resultStreetArtData);

$selectRegistrationDataqry = "	SELECT 
								* 
							FROM 
								registrar
							WHERE 
								street_art_uno = ?
							ORDER BY 
								created DESC
							LIMIT 1;";

$stmtd = mysqli_prepare($connection, $selectRegistrationDataqry);
mysqli_stmt_bind_param($stmtd,'i', $sa_uno);
mysqli_stmt_execute($stmtd);
$resultRegistrationData = mysqli_stmt_get_result($stmtd);

$rowRegistrationData = mysqli_fetch_assoc($resultRegistrationData);
mysqli_free_result($resultRegistrationData);
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
					<td class="user-cell">Evaluator</td>			
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
					<td class="user-cell" rowspan=7><b>Initiate:</b></td>
					<td class="user-cell">
						<a href="SAGOList.php"><span class="glyphicon glyphicon-home"></span> Home</a>
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
						<a href="userwipe.php"><span class="glyphicon glyphicon-erase"></span> Remove Account</a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>				
			</table>
		</div>
		
		<!--****************************************************************Registrar*****************************************************************-->
		
		<div style="width: 55%; margin-left: 100px;">
			<form name="registrarForm" id="regisrarForm" action="SAGORegistrar.php" method="post">
			<table class="view-table" border=0>
				<tr class="view-row">
					<td class="view-cell">
						<input type="hidden" name="street_art_uno" value="<?php echo $sa_uno; ?>">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<b>Author:</b><?php echo htmlentities($rowStreetArtData['author'], ENT_QUOTES, 'UTF-8'); ?><br/>
							<b>Category:</b> <?php echo htmlentities($rowStreetArtData['category'], ENT_QUOTES, 'UTF-8'); ?><br/>
							<b>Message:</b> <?php echo htmlentities($rowStreetArtData['message'], ENT_QUOTES, 'UTF-8'); ?><br/>
							<b>Location:</b> <?php echo htmlentities($rowStreetArtData['location'], ENT_QUOTES, 'UTF-8'); ?><br/>
							<b>Date:</b> <?php echo htmlentities($rowStreetArtData['date'], ENT_QUOTES, 'UTF-8'); ?><br/>
							<b>Time:</b> <?php echo htmlentities($rowStreetArtData['time'], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<fieldset>
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<legend>Registration status</legend>
							<div>
								<input type="radio" id="register" name="reg_status" value="register" <?php if(empty($rowRegistrationData['registrar_status'])){ echo "checked"; } ?> required>
								<label for="register">Register</label>
							</div>
							<div>
								<input type="radio" id="unregister" name="reg_status" value="unregister" <?php if(!empty($rowRegistrationData['registrar_status'])){ echo "checked"; } ?> required>
								<label for="unregister">Unregister</label>
							</div>
						</font>
						</fieldset>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<fieldset>
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<legend>Registration number (UGMO)</legend>
							<div>
								<input type="text" name="reg_number" class="form-control" id="number" placeholder="Number" value="<?php if(isset($rowRegistrationData['registrar_number'])){ echo $rowRegistrationData['registrar_number']; } ?>" style="width: 250px;" required>
							</div>
						</font>
						</fieldset>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<fieldset>
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<legend>Registration date</legend>
							<div>
								<input type="date" name="reg_date" class="form-control" id="date" placeholder="Date" value="<?php if(isset($rowRegistrationData['registrar_date'])){ echo $rowRegistrationData['registrar_date']; } ?>" style="width: 250px;" required>
							</div>
						</font>
						</fieldset>
					</td>
				</tr>
<?php 
if(!empty($rowRegistrationData['created']))
{
?>		
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							Last update: <?php echo $rowRegistrationData['created']; ?>
						</font>
					</td>
				</tr>
<?php 
}
?>	
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="btnRegister" type="submit" class="button" value="<?php if(!empty($rowRegistrationData['registrar_status'])) { echo "Unregister It"; }else{ echo "Register It"; } ?>" style="color:white; font-weight:bold; background-color: #173457;" id="btnRegister" onclick="JavaScript:return validateRegistrarForm();" />
						</font>
					</td>
				</tr>
			</table>
			</form>
		</div>
	</div>
	<script type="text/javascript">
	function validateRegistrarForm() {
		const userResponse = confirm("Do You want to proceed it further?");
		
	    if (userResponse) {
			document.getElementById('registrarForm').name='btnRegister';
			document.getElementById('registrarForm').action='SAGORegistrar.php';
			document.getElementById('registrarForm').submit();
	    }
	    else {
			document.getElementById('registrarForm').addEventListener('submit',
			function(event) {
				event.preventDefault();
			});
		}
	}
	</script>
</body>  
</html>
