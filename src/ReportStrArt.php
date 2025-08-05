<?php 
session_start();  
if(!isset($_SESSION["username"]) || (isset($_SESSION["usertype"]) && $_SESSION["usertype"] == 2))
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

$username = $_SESSION['username'];
$usertype = $_SESSION['usertype'];

if(isset($_POST['btnSubmitStrArt']))   
{
	$reportAuthor = $_POST['reportAuthor'];
	$reportDescription = $_POST['reportDescription'];
	$reportCategory = $_POST['reportCategory'];
	$reportMessage = $_POST['reportMessage'];
	$reportLocation = $_POST['reportLocation'];
	$reportDate = $_POST['reportDate'];
	$reportTime = $_POST['reportTime'];
	$reportLat = $_POST['lat'];
	$reportLng = $_POST['lng'];
	
	$selectqry = "SELECT max(street_art_uno) as maxuno from street_art;";
	if($result=mysqli_query($connection,$selectqry)){
		$row=mysqli_fetch_assoc($result);
		if(is_null($row['maxuno'])){$sa_uno=1;}
		else{$sa_uno = (int)$row['maxuno']+1;}
		mysqli_free_result($result);
	}
	
	// Inserting data to street art table
	
	$insertstrart = "INSERT INTO street_art (street_art_uno,author,category,message,description,location,lat,lng,date,time) 
							VALUES (?,?,?,?,?,?,?,?,?,?);";
	
	$insertprepare = mysqli_prepare($connection, $insertstrart);
	mysqli_stmt_bind_param($insertprepare, 'isssssssss', $sa_uno, $reportAuthor, $reportCategory, $reportMessage, $reportDescription, $reportLocation, $reportLat, $reportLng, $reportDate, $reportTime);
	mysqli_stmt_execute($insertprepare);
	
	// Inserting data to user street art
	
	$selectUserDataqry = "SELECT uno, username FROM user WHERE username = ? AND usertype = ?;";
	
	$stmt = mysqli_prepare($connection, $selectUserDataqry);
	mysqli_stmt_bind_param($stmt,'si', $username, $usertype);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if(mysqli_num_rows($result) > 0)
	{
		$rowUserData = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		
		$insertuser_strart = "INSERT INTO user_street_art (street_art_uno,user_uno) VALUES (?,?);";
		
		$insertscndprepare = mysqli_prepare($connection, $insertuser_strart);
		mysqli_stmt_bind_param($insertscndprepare, 'ii', $sa_uno, $rowUserData['uno']);
		mysqli_stmt_execute($insertscndprepare);
		
		// Insering data to street art photo table
		
		$fileCount = count($_FILES['file']['name']);
		
		for($i=0;$i<$fileCount;$i++)
		{
			if (!empty($_FILES['file']['name'][$i]))
			{
				$fileName = $_FILES['file']['name'][$i];
				$fileName_r = preg_replace('/[^a-zA-Z0-9_.]+/','-', $fileName);
				$fileSize = $_FILES['file']['size'][$i];
				$fileType = $_FILES['file']['type'][$i];
				$fileContent = base64_encode(file_get_contents($_FILES['file']['tmp_name'][$i]));
			
				$insertstrart_photo = "INSERT INTO street_art_photo (street_art_uno,photo_name,photo_size,photo_type,photo_data) VALUES (?,?,?,?,?);";
			
				$itprepare = mysqli_prepare($connection, $insertstrart_photo);
				mysqli_stmt_bind_param($itprepare, 'issss', $sa_uno, $fileName_r, $fileSize, $fileType, $fileContent);
				mysqli_stmt_execute($itprepare);
			}
		}
		
		echo '<script> alert("Report Complete"); </script>';
		
		header("Location: useraccount.php"); 
	}
}
?>
<!DOCTYPE html> 
<html lang="en">
<head>
<meta charset="utf-8">
<title>Report a Street Art and (or) Graffiti</title>
<link rel="icon" href="images/brush.webp" type="image/webp">
<!--nav bar-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<!--reporting form-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<!--location-->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
<style>
label{
	font-size: 15px;
	color: white;
}

.form-control{
	font-size: 15px;
}

#map{
	width:500px; 
	height: 250px;
}
</style>
</head>
<body style="background: #173457;">

	<!--****************************************************************Navigation Bar*****************************************************************-->
	<nav class="navbar navbar-inverse" style="height: 52px;">
		<div class="container-fluid" style="padding: 0px 0px; height: 50px;">
		<table width="100%" style="margin-top: -10px; margin-left: -18px; margin-left: 0px;">
			<tr>
				<td style="width: 145px;">
					<div class="navbar-header" >
						<a class="navbar-brand" style="padding: 15px; margin: 0px 0px 0px -15px; font-size: 18px; font-family: Helvetica Neue, Helvetica, Arial, sans-serif;" href="#">Street Art and (or) Graffiti Reporting System</a>
					</div>
				</td>

				<td>
					<table>
						<tr>
							<td>
								<ul class="nav navbar-nav">
									<li class="active">
										<a href="#" style="padding: 15px; font-size: 14px; font-family: Helvetica Neue, Helvetica, Arial, sans-serif;">Report a Street Art and (or) Graffiti</a>
									</li>
								</ul>
							</td>
							<td>
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
				
				<td>
					<table align="right">
						<tr>
							<td>
								<ul class="nav navbar-nav navbar-right">
									<li>
										<a href="useraccount.php" style="padding: 15px; font-size: 14px; font-family: Helvetica Neue, Helvetica, Arial, sans-serif;"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a>
									</li>
								</ul>
							</td>
							<td style="padding: 5px;">
								<ul class="nav navbar-nav navbar-right">
									<li>
										<a href="logout.php" style="padding: 15px; font-size: 14px; font-family: Helvetica Neue, Helvetica, Arial, sans-serif;"><span class="glyphicon glyphicon-log-in"></span>Log Out</a>
									</li>
								</ul>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>
	</nav>

	
	<!--****************************************************************Reporting Form*****************************************************************-->
	
	
	<form method="POST" action="" enctype="multipart/form-data">
	
		<center>
		<div style="width: 1000px;">
		
			<table width="100%" >
  
				<tr>
					<td colspan="2" style="padding: 5px;">
						<div class="col-md-4 mb-3" style="width: 500px;">
							<h1 style="width: 500px; color: white;">Report here a Street Art and (or) Graffiti...</h1>
						</div>
					</td>
				</tr>
		
				<tr>
					<td style="padding: 5px;">														<!--author-->
						<div class="col-md-4 mb-3">
							<label for="validation01">Author</label>
							<input type="text" name="reportAuthor" class="form-control" id="validation01" placeholder="Author" style="width: 250px;" required>
						</div>	
					</td>
			
					<td rowspan="3" style="padding: 5px;">											<!--location-->
						<div class="col-md-6 mb-3">
							<label for="validation03">Location</label>
							<input type="text" name="reportLocation" class="form-control" id="validation03" placeholder="Location" style="width: 500px;" required><br>
						  
							<div id="map"></div>
				
							<input type="hidden" name="lat" id="lat" value="">
							<input type="hidden" name="lng" id="lng" value="">
						</div>
					</td>
				</tr>
		
				<tr>
					<td style="padding: 5px;">														<!--description-->
						<div class="col-md-4 mb-3">
							<label for="validation02">Description</label>
							<textarea name="reportDescription" class="form-control" id="validation02" placeholder="Description (e.g. Important notice, Consent documents, received budget)" rows="5" style="width: 500px;"required></textarea>
						</div>
					</td>
				</tr>
		
				<tr>
					<td style="padding: 5px;">														<!--category-->
						<div class="col-md-4 mb-3">
							<label for="validation04" >Category</label>
							<select name="reportCategory" class="form-control" id="validation04" style="width: 500px; padding: 0px;">
								<option value="0">--Category--</option>
								<option value="Graffiti">Graffiti</option>
								<option value="Street Art">Street Art</option>
							</select>
						</div>
					</td>
					
				</tr>
		
				<tr>
					<td style="padding: 5px;">														<!--date-->
						<div class="col-md-3 mb-3">
						  <label for="validation05">Date</label>
						  <input type="date" name="reportDate" class="form-control" id="validation05" placeholder="Date" style="width: 500px;" required>
						</div>
					</td>
					
					<td style="padding: 5px;">														<!--photo-->
						<div class="col-md-4 mb-3">
							<label for="exampleFormControlFile1">Three Photos</label>
							<input type="file" name="file[]" accept="image/*" class="form-control-file" id="exampleFormControlFile1" style="width: 500px; color: white; font-size: 15px;" multiple>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">														<!--time-->
						<div class="col-md-3 mb-3">
						  <label for="validation06">Time</label>
						  <input type="time" name="reportTime" class="form-control" id="validation06" placeholder="Time" style="width: 500px;" required>
						</div>
					</td>

					<td style="padding: 5px;">														<!--message-->
						<div class="col-md-4 mb-3">
							<label for="validation08">Message</label>
							<textarea name="reportMessage" class="form-control" id="validation08" placeholder="Message" rows="5" style="width: 500px;"required></textarea>
						</div>
					</td>	
				</tr>
		        <tr>
				    <td colspan=2 style="padding-left:250px;">												<!--submit street art-->
						<div class="col-md-4 mb-3" style="padding-top: 5px;">
							<input name="btnSubmitStrArt" type="submit" class="btn btn-primary" value="Submit" style="width: 500px; color: white; font-size: 15px;align:center;" id="btnSubmitStrArt" onclick="JavaScript:return validateReportForm();">
		                </div>
					</td>
				</tr>
			</table>
  
		</div>
		</center>
		
	</form>

<script type="text/javascript">

	/****************************************Validating Number of Files uploaded*******************************************************/
	const inputImages = document.querySelector('#exampleFormControlFile1');

	// Listen for files selection
	inputImages.addEventListener('change', (e) => {
		// Retrieve all files
		const files = inputImages.files;

		// Check files count
		if (files.length < 3) {
			alert('A minimum number of 3 photos are required.');
			inputImages.value = '';
			return;
		}
		if (files.length > 3) {
			alert('Only 3 photos are allowed to upload.');
			inputImages.value = '';
			return;
		}

	});
	
	
	/****************************************Google Location*******************************************************/
	var map; //Will contain map object.
	var marker = false; ////Has the user plotted their location marker? 
        
	//Function called to initialize / create the map.
	//This is called when the page has loaded.
	function initMap() {

		//The center location of our map.
		var centerOfMap = new google.maps.LatLng(54.7259537, 25.3411514);

		//Map options.
		var options = {
			center: centerOfMap, //Set center.
			zoom: 10 //The zoom value.
		};

		//Create the map object.
		map = new google.maps.Map(document.getElementById('map'), options);

		//Listen for any clicks on the map.
		google.maps.event.addListener(map, 'click', function(event) {                
			//Get the location that the user clicked.
			var clickedLocation = event.latLng;
			//If the marker hasn't been added.
			if(marker === false){
				//Create the marker.
				marker = new google.maps.Marker({
					position: clickedLocation,
					map: map,
					draggable: true //make it draggable
				});
				//Listen for drag events!
				google.maps.event.addListener(marker, 'dragend', function(event){
					markerLocation();
				});
			} else{
				//Marker has already been added, so just change its location.
				marker.setPosition(clickedLocation);
			}
			//Get the marker's location.
			markerLocation();
		});
	}
        
	//This function will get the marker's current location and then add the lat/long
	//values to our textfields so that we can save the location.
	function markerLocation(){
		//Get location.
		var currentLocation = marker.getPosition();
		//Add lat and lng values to a field that we can save.
		document.querySelector("input[name='lat']").value = currentLocation.lat(); //latitude
		document.querySelector("input[name='lng']").value = currentLocation.lng(); //longitude
	}
        
        
	//Load the map when the page has finished loading.
	google.maps.event.addDomListener(window, 'load', initMap);
	
</script>				
</body>
</html>
