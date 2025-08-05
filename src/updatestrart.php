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

	if(!$connection){
		die("Connection failed: " .mysqli_connect_error());
	}

?>
<!DOCTYPE html>
<html lang="en">
<head> 
<meta charset="utf-8">
<title>Update a Street Art and (or) Graffiti</title>
<link rel="icon" href="images/brush.webp" type="image/gif">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">Street Art and (or) Graffiti Reporting System</a>
			</div>
    
			<ul class="nav navbar-nav">
				<li class="active"><a href="#">Update a Street Art and (or) Graffiti</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Log Out</a></li>
			</ul>
		</div>
	</nav>

	<!--****************************************************************Reporting Form*****************************************************************-->
<?php
$sa_uno = (int)$_POST['sa_uno'];

$selectqry = "SELECT * FROM street_art WHERE street_art_uno = ?;";

$stmt = mysqli_prepare($connection, $selectqry);
mysqli_stmt_bind_param($stmt,'i', $sa_uno);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
?>
	<form method="POST" action="updatestrart.php" enctype="multipart/form-data">
		<input type="hidden" name="sa_uno" value="<?php echo $sa_uno; ?>">
		<center>
		<div style="width: 1000px;">
		
			<table width="100%" >
				<tr>
					<td colspan="2" style="padding: 5px;">
						<div class="col-md-4 mb-3" style="width: 500px;">
							<h1 style="width: 500px; color: white;">Update the Street Art and (or) Graffiti</h1>
						</div>
					</td>
				</tr>
		
				<tr>
					<td style="padding: 5px;">														<!--author-->
						<div class="col-md-4 mb-3">
							<label for="validation01">Author</label>
							<input type="text" name="reportAuthor" class="form-control" id="validation01" placeholder="Author" value="<?php echo $row['author']; ?>" style="width: 500px;" required>
						</div>	
					</td>
			
					<td rowspan="3" style="padding: 5px;">											<!--location-->
						<div class="col-md-6 mb-3">
							<label for="validation03">Location</label>
							<input type="text" name="reportLocation" class="form-control" id="validation03" placeholder="Location" value="<?php echo $row['location']; ?>" style="width: 500px;" required><br>
							
							<div id="map"></div>
							
							<input type="hidden" name="lat" id="lat" value="<?php echo $row['lat']; ?>">
							<input type="hidden" name="lng" id="lng" value="<?php echo $row['lng']; ?>">
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">														<!--description-->
						<div class="col-md-4 mb-3">
							<label for="validation02">Description</label>
							<textarea name="reportDescription" class="form-control" id="validation02" placeholder="Description (e.g. Important notice, Consent documents, received budget)" rows="5" style="width: 500px;"required><?php echo $row['description']; ?></textarea>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">														<!--category-->
						<div class="col-md-4 mb-3">
							<label for="validation04" >Category</label>
							<select name="reportCategory" class="form-control" id="validation04" style="width: 500px; padding: 0px;">
								<option value="0" <?php if(empty($row['category'])){ ?>selected="selected"<?php } ?>>--Category--</option>
								<option value="Graffiti" <?php if($row['category'] == 'Graffiti'){ ?>selected="selected"<?php } ?>>Graffiti</option>
								<option value="Street Art" <?php if($row['category'] == 'Street Art'){ ?>selected="selected"<?php } ?>>Street Art</option>
							</select>
						</div>
					</td>
				</tr>
		
				<tr>
					<td style="padding: 5px;">														<!--date-->
						<div class="col-md-3 mb-3">
						  <label for="validation05">Date</label>
						  <input type="date" name="reportDate" class="form-control" id="validation05" placeholder="Date" value="<?php echo $row['date']; ?>" style="width: 500px;" required>
						</div>
					</td>
					
					<td style="padding: 5px;">														<!--images-->
						<div class="col-md-4 mb-3">
							<label for="exampleFormControlFile1">Photos</label>
							<input type="file" name="file[]" accept="image/*" class="form-control-file" id="exampleFormControlFile1" style="width: 500px; color: white; font-size: 15px;" multiple>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">														<!--time-->
						<div class="col-md-3 mb-3">
						  <label for="validation06">Time</label>
						  <input type="time" name="reportTime" class="form-control" id="validation06" placeholder="Time" value="<?php echo $row['time']; ?>" style="width: 500px;" required>
						</div>
					</td>
					
					<td style="padding: 5px;">														<!--message-->
						<div class="col-md-3 mb-3">
						  <label for="validation08">Message</label>
							<textarea name="reportMessage" class="form-control" id="validation08" placeholder="Message" rows="5" style="width: 500px;"required><?php echo $row['message']; ?></textarea>
						</div>
					</td>
				</tr>
		        <tr>
				    <td colspan=2 style="padding-left:250px;">										<!--submit button-->
						<div class="col-md-4 mb-3" style="padding-top: 5px;">
							<button name="updateStrArt" class="btn btn-primary" type="submit" style="width: 500px; color: white; font-size: 15px;align:center;">Update Report</button>
		                </div>
					</td>
				</tr>
			</table>
  
		</div>
		</center>
</form>

<script type="text/javascript">

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

<?php

if(isset($_POST['updateStrArt']))  
{
	$sa_uno = (int)$_POST['sa_uno'];
	$reportAuthor = $_POST['reportAuthor'];
	$reportDescription = $_POST['reportDescription'];
	$reportCategory = $_POST['reportCategory'];
	$reportMessage = $_POST['reportMessage'];
	$reportLocation = $_POST['reportLocation'];
	$reportDate = $_POST['reportDate'];
	$reportTime = $_POST['reportTime'];
	$reportLat = $_POST['lat'];
	$reportLng = $_POST['lng'];
	
	$checkqry = "SELECT
					IF(EXISTS(SELECT 1 FROM registrar WHERE street_art_uno = ? ORDER BY created DESC LIMIT 1), 1, 0) AS one,
					IF(EXISTS(SELECT 1 FROM street_art_damage WHERE street_art_uno = ? ORDER BY created DESC LIMIT 1), 1, 0) AS two;";
				
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'ii', $sa_uno, $sa_uno);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if(mysqli_num_rows($result2) > 0)
	{
		$rowCheckData = mysqli_fetch_assoc($result2);
		
		if($rowCheckData['one']== 1 || $rowCheckData['two']== 1)
        {
?>
	    <script> alert("Data of Street Art and (or) Graffiti can't be updated. No 0"); </script>
<?php
        }
        else
        {
			$updatestrart = "UPDATE 
								street_art 
							 SET 
								author = ?,
								category = ?,
								message = ?,
								description = ?,
								location = ?,
								lat = ?,
								lng = ?,
								date = ?,
								time = ?
							 WHERE
								street_art_uno = ?;";
			
			$updateprepare = mysqli_prepare($connection, $updatestrart);
			mysqli_stmt_bind_param($updateprepare, 'sssssssssi', $reportAuthor, $reportCategory, $reportMessage, $reportDescription, $reportLocation, $reportLat, $reportLng, $reportDate, $reportTime, $sa_uno);
			mysqli_stmt_execute($updateprepare);
			
			// Remove photo data from street art photo table
			
			$delete_StrArt_Photo_qry = "DELETE FROM street_art_photo WHERE street_art_uno = ?;";
			
			$stmt = mysqli_prepare($connection, $delete_StrArt_Photo_qry);
			mysqli_stmt_bind_param($stmt,'i', $sa_uno);
			mysqli_stmt_execute($stmt);
			
			// Insert new street art photo data to photo table
			
			if(!empty(count($_FILES['file']['name'])))
			{
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
				
				$protocol = empty($_SERVER['HTTPS'])? 'http' : 'https';
				$host = $_SERVER['HTTP_HOST'];
				$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
				$extra = 'useraccount.php';
				?>
				<script>
					alert("Completed update of the Street Art and (or) Graffiti.");
					window.location.replace(<?php echo "'"."{$protocol}://{$host}{$uri}/{$extra}"."'"; ?>);
				</script>
				<?php
			}
			else
			{
?>
				<script> alert("Error uploading Photos."); </script>
<?php
			}
		}
	}
	mysqli_free_result($result2);
}
?>
</body>
</html>
