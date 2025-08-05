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

if(!$connection){
	die("Connection failed: " .mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head> 
<meta charset="utf-8">
<title>Street Art and (or) Graffiti Analysis</title>
<link rel="icon" href="images/brush.webp" type="image/webp">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
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
if(isset($_SESSION["usertype"]) && $_SESSION["usertype"] == 2)
{
?>
				<li ><a href="SAGOList.php">Management of Street Art and (or) Graffiti</a></li>
<?php
}
elseif(isset($_SESSION["usertype"]) && $_SESSION["usertype"] == 1)
{
?>
				<li ><a href="useraccount.php">Street Art and (or) Graffiti</a></li>
<?php
}
else
{
?>
				<li ><a href="sagi.php">Street Art and (or) Graffiti</a></li>
<?php
}
?>
				<li class="active"><a href="#">Analysis</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span>Log Out</a></li>
			</ul>
		</div>
	</nav>

	<?php
		
		/****************************************************************Street Art Type****************************************************************/
		$selectType = "SELECT
							category
						FROM
							street_art
						WHERE
							street_art_uno
						IN
							(SELECT street_art_uno FROM user_street_art WHERE street_art_uno
						IN 
							(SELECT street_art_uno FROM street_art));";
		$result1 = mysqli_query($connection, $selectType);
		
		if(mysqli_num_rows($result1)>0)
		{
			$a = 0;
			$b = 0;
			$c = 0;
			
			while ($row1 = mysqli_fetch_array($result1)) {
				
				if($row1["category"] == 'Graffiti')
				{
					$a++;
				}
				else if($row1["category"] == 'Street Art')
				{
					$b++;
				}				
				
				$c++;
				
			}
		}
		
		mysqli_free_result($result1);
		
		/****************************************************************Street Art Type****************************************************************/
		$selectNextType = "SELECT
							category
						FROM
							street_art
						WHERE
							street_art_uno
						IN
							(SELECT street_art_uno FROM user_street_art WHERE street_art_uno
						IN 
							(SELECT street_art_uno FROM street_art));";
		$result2 = mysqli_query($connection, $selectNextType);
		
		if(mysqli_num_rows($result2)>0)
		{
			$d = 0;
			$e = 0;
			$f = 0;
			
			while ($row2 = mysqli_fetch_array($result2)) {
				
				if($row2["category"] == 'Graffiti')
				{
					$d++;
				}
				else if($row2["category"] == 'Street Art')
				{
					$e++;
				}
				
				$f++;
				
			}
		}
		
		mysqli_free_result($result2);
		
		/****************************************************************Fluctuation****************************************************************/
		
		$three_years_before = (int)date("Y") - 3;
		$two_years_before = (int)date("Y") - 2;
		$one_year_before = (int)date("Y") - 1;
		$today = (int)date("Y");
		$after_one_year = (int)date("Y") + 1;
		
		$selectFluctThreeYears = "SELECT COUNT(REGEXP_INSTR(date, '[0-9]{4}')) AS stats FROM street_art WHERE date = " . $three_years_before . " AND street_art_uno IN (SELECT street_art_uno FROM user_street_art)";
		$result3 = mysqli_query($connection, $selectFluctThreeYears);
		
		$m = mysqli_fetch_array($result3);
		
		mysqli_free_result($result3);
		
		$selectFluctTwoYears = "SELECT COUNT(REGEXP_INSTR(date, '[0-9]{4}')) AS stats FROM street_art WHERE date = " . $two_years_before . " AND street_art_uno IN (SELECT street_art_uno FROM user_street_art)";
		$result4 = mysqli_query($connection, $selectFluctTwoYears);
		
		$n = mysqli_fetch_array($result4);
		
		mysqli_free_result($result4);
		
		$selectFluctOneYear = "SELECT COUNT(REGEXP_INSTR(date, '[0-9]{4}')) AS stats FROM street_art WHERE date = " . $one_year_before . " AND street_art_uno IN (SELECT street_art_uno FROM user_street_art)";
		$result5 = mysqli_query($connection, $selectFluctOneYear);
		
		$o = mysqli_fetch_array($result5);
		
		mysqli_free_result($result5);
		
		$selectFluctToday = "SELECT COUNT(REGEXP_INSTR(date, '[0-9]{4}')) AS stats FROM street_art WHERE date = " . $today . " AND street_art_uno IN (SELECT street_art_uno FROM user_street_art)";
		$result6 = mysqli_query($connection, $selectFluctToday);
		
		$p = mysqli_fetch_array($result6);
		
		mysqli_free_result($result6);
		
		$selectFluctAfter = "SELECT COUNT(REGEXP_INSTR(date, '[0-9]{4}')) AS stats FROM street_art WHERE date = " . $after_one_year . " AND street_art_uno IN (SELECT street_art_uno FROM user_street_art)";
		$result7 = mysqli_query($connection, $selectFluctAfter);
		
		$q = mysqli_fetch_array($result7);
		
		mysqli_free_result($result7);
		
		/****************************************************************Street Art Type****************************************************************/
		
		if($c != 0) {
			$dataPoints_for_PieChart = array( 
				array("label"=>"Graffiti", "y"=> ($a/$c)*100),
				array("label"=>"Street Art", "y"=> ($b/$c)*100),
			);
		}
		else {
			$dataPoints_for_PieChart = array( 
				array("label"=>"Graffiti", "y"=> 0),
				array("label"=>"Street Art", "y"=> 0),
			);
		}
		
		/****************************************************************Street Art Type****************************************************************/
		
		if($f != 0) {
			$dataPoints_for_PieChart1 = array( 
				array("label"=>"Graffiti", "y"=> ($d/$f)*100),
				array("label"=>"Street Art", "y"=> ($e/$f)*100),
			);
		}
		else {
			$dataPoints_for_PieChart1 = array( 
				array("label"=>"Graffiti", "y"=> 0),
				array("label"=>"Street Art", "y"=> 0),
			);
		}

		/****************************************************************Fluctuation****************************************************************/
		$dataPoints = array(
			array("label" => $three_years_before, "y" => $m['stats']),
			array("label" => $two_years_before, "y" => $n['stats']),
			array("label" => $one_year_before, "y" => $o['stats']),
			array("label" => $today, "y" => $p['stats']),
			array("label" => $after_one_year, "y" => $q['stats']),
		);

		/*echo var_export($m['stats']);*/
		/*echo var_export($n['stats']);*/
		/*echo var_export($o['stats']);*/
		/*echo var_export($p['stats']);*/
		/*echo var_export($q['stats']);*/
	?>
	
	<script>

		window.onload = function() {

			//****************************************************************Street Art Type****************************************************************/
			var chart = new CanvasJS.Chart("chartContainer_PieChart", {
				animationEnabled: true,
				title: {
					text: "Street Art Type"
				},
				subtitles: [{
					text: "Year " + <?php echo '"'.$today.'"'; ?>
				}],
				data: [{
					type: "pie",
					yValueFormatString: "#,##0.00\"%\"",
					indexLabel: "{label} ({y})",
					dataPoints: <?php echo json_encode($dataPoints_for_PieChart, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart.render();
			
			//****************************************************************Street Art Type****************************************************************/
			var chart = new CanvasJS.Chart("chartContainer_PieChart1", {
				animationEnabled: true,
				title: {
					text: "Street Art Type"
				},
				subtitles: [{
					text: "Year " + <?php echo '"'.$today.'"'; ?>
				}],
				data: [{
					type: "pie",
					yValueFormatString: "#,##0.00\"%\"",
					indexLabel: "{label} ({y})",
					dataPoints: <?php echo json_encode($dataPoints_for_PieChart1, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart.render();

			//****************************************************************Fluctuation****************************************************************/
			var chart = new CanvasJS.Chart("chartContainer", {
				animationEnabled: true,
				title: {
					text: "Street Art fluctuation over years"
				},
				axisY: {
					title: "Number of Street Art"
				},
				data: [{
					type: "spline",
					dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart.render();

		}

	</script>
	
	<center>
		<div>
			<table style="padding: 10px;">
				<tr>
				
					<!--****************************************************************Street Art Type************************************************************-->
					<td style="padding: 20px;">
						<div id="chartContainer_PieChart" style="height: 400px; width: 600px; border: 1px solid black;"></div>
						
					</td>
					
					
					<!--****************************************************************Street Art Type************************************************************-->
					<td style="padding: 20px;">
						<div id="chartContainer_PieChart1" style="height: 400px; width: 600px; border: 1px solid black;"></div>
					</td>
				</tr>
			
				<tr>
				
					<!--****************************************************************Fluctuation************************************************************-->
					<td colspan=2 style="padding: 20px;">
						<div id="chartContainer" style="height: 370px; width: 100%; border: 1px solid black;"></div>
					</td>
				</tr>
			</table>
		</div>
	</center>

</body>
</html>
