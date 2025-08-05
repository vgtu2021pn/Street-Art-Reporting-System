<?php
    $servername = "localhost";
    $user = "sardb";
    $pw = "mypassword";
    $db = "streetartreportingdb";
    
    $connection = mysqli_connect($servername, $user, $pw, $db);			

    if(!$connection)
    {
	die("Connection failed: " .mysqli_connect_error());
    }

    session_start(); 
    if(isset($_SESSION["usertype"]))
    {
	if((int)$_SESSION["usertype"] == 1)
	{
	    header("Location: useraccount.php");
	}
	elseif((int)$_SESSION["usertype"] == 2)
	{
	    header("Location: SAGOList.php");
	}
	else
	{
	    header("Location: sagi.php");
	}
    }
					
    if(isset($_POST['btnLogin']))
    {
						
    $regType = $_POST['regType'];
    $usernameLogin = $_POST['usernameLogin'];
    $passwordLogin = $_POST['passwordLogin'];
					   
    if($regType=="" || $usernameLogin=="" || $passwordLogin=="" )
    {
?>
	<script> alert("Please Enter required Fields"); </script>
<?php	
    }
					
    if($regType=="artist")
    {
	$selectqry = "SELECT * FROM user WHERE username=? AND usertype=1;";
	$stmt = mysqli_prepare($connection, $selectqry);
	mysqli_stmt_bind_param($stmt,'s', $usernameLogin);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if(mysqli_num_rows($result) > 0)    
	{
	    while($row = mysqli_fetch_array($result))  
	    {
		if(password_verify($passwordLogin, $row["password"]))  
		{
		    mysqli_free_result($result);
		    $_SESSION["username"] = $usernameLogin;
		    $_SESSION["usertype"] = 1;
		    header("Location: useraccount.php");
                }
                else
                {
		    echo '<script>alert("Wrong User Details 1")</script>';  
                }
	    }
	}
	else  
	{  
	    echo '<script>alert("Wrong User Details 2")</script>';  
	}  
    }
    elseif($regType=="evaluator")
    {
	$selectqry = "SELECT * FROM user WHERE username=? and usertype=2;";
	$stmt = mysqli_prepare($connection, $selectqry);
	mysqli_stmt_bind_param($stmt,'s', $usernameLogin);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if(mysqli_num_rows($result) > 0)
	{
	    while($row = mysqli_fetch_array($result))  
	    {
		if(password_verify($passwordLogin, $row["password"]))  
		{
		    mysqli_free_result($result);
		    $_SESSION["username"] = $usernameLogin;
		    $_SESSION["usertype"] = 2;
                    header("Location: SAGOList.php");
                }
                else
                {
                    echo '<script>alert("Wrong User Details 1")</script>';  
                }
            }
         }
         else
        {
	    echo '<script>alert("Wrong User Details 2")</script>';  
        }
    }
    
    }

    if(isset($_POST['btnSignUpArtist']))
    {
	$fnameArtist = $_POST['fnameArtist'];
	$lnameArtist = $_POST['lnameArtist'];
	$username = $_POST['pseudonyme'];
	$usertype = 1;
	$pwArtist = $_POST['pwArtist'];
	$reppwArtist = $_POST['reppwArtist'];
	$hashedpwArtist = password_hash($pwArtist,PASSWORD_DEFAULT);
	$adult = ($_POST['adult'] == 'on')? 1 : 0;
	$termsofservice = ($_POST['termsofservice'] == 'on')? 1 : 0;
	
	$contactTypeOne = $_POST['contactTypeOneArtist'];
	$contactTypeTwo = $_POST['contactTypeTwoArtist'];
	$contactTypeThree = $_POST['contactTypeThreeArtist'];
	$contactNameOne = $_POST['contactNameOneArtist'];
	$contactNameTwo = $_POST['contactNameTwoArtist'];
	$contactNameThree = $_POST['contactNameThreeArtist'];
	
	$checkqry = "SELECT uno, username FROM user WHERE username=? AND usertype=?;";
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'si', $username, $usertype);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if($username=="" || $pwArtist=="" || $reppwArtist=="" || $adult==0 || $termsofservice==0)
        {
?>
	    <script> alert("Please Enter required Fields. No 1"); </script>
<?php
        }
	elseif(mysqli_num_rows($result2) > 0)
	{
?>
	    <script> alert("Please Enter different Pseudonyme. No 1"); </script>
<?php	    
	}
        elseif($contactNameOne=="" && $contactNameTwo=="" && $contactNameThree=="")
	{
?>
            <script> alert("Please Enter at least one Contact Information. No 1"); </script>
<?php
	}
	elseif($pwArtist!=$reppwArtist)
	{
?>
            <script> alert("Password Fields are not matched. No 1"); </script>
<?php
	}
        else
        {
	    $insertArtist = "INSERT INTO user (fname,lname,username,usertype,password,adulthood,termsofservice) 
		VALUES (?,?,?,?,?,?,?)";
	    
	    $insertprepare = mysqli_prepare($connection, $insertArtist);
	    mysqli_stmt_bind_param($insertprepare, 'sssisii', $fnameArtist, $lnameArtist, $username, $usertype, $hashedpwArtist, $adult, $termsofservice);
	    mysqli_stmt_execute($insertprepare);
	    
	    $selectqry = "SELECT uno, username FROM user WHERE username=? AND usertype=?;";
	    $stmt = mysqli_prepare($connection, $selectqry);
	    mysqli_stmt_bind_param($stmt,'si', $username, $usertype);
	    mysqli_stmt_execute($stmt);
	    $result = mysqli_stmt_get_result($stmt);
	    
	    if(mysqli_num_rows($result) > 0)
	    {
		$rowUserData = mysqli_fetch_assoc($result);
		
		if(!empty($contactNameOne))
		{
		    $contactTypeOne = !empty($contactTypeOne)? $contactTypeOne : "Mail";
		    $ico = "INSERT INTO user_contact (user_id,contact_type,contact_data) VALUES (?,?,?);";
		    
		    $icoprepare = mysqli_prepare($connection, $ico);
		    mysqli_stmt_bind_param($icoprepare, 'iss', $rowUserData['uno'], $contactTypeOne, $contactNameOne);
		    mysqli_stmt_execute($icoprepare);
		}
		
		if(!empty($contactNameTwo))
		{
		    $contactTypeTwo = !empty($contactTypeTwo)? $contactTypeTwo : "Mail";
		    $ict = "INSERT INTO user_contact (user_id,contact_type,contact_data) VALUES (?,?,?);";
		    
		    $ictprepare = mysqli_prepare($connection, $ict);
		    mysqli_stmt_bind_param($ictprepare, 'iss', $rowUserData['uno'], $contactTypeTwo, $contactNameTwo);
		    mysqli_stmt_execute($ictprepare);
		}
		
		if(!empty($contactNameThree))
		{
		    $contactTypeThree = !empty($contactTypeThree)? $contactTypeThree : "Mail";
		    $icth = "INSERT INTO user_contact (user_id,contact_type,contact_data) VALUES (?,?,?);";
		    
		    $icthprepare = mysqli_prepare($connection, $icth);
		    mysqli_stmt_bind_param($icthprepare, 'iss', $rowUserData['uno'], $contactTypeThree, $contactNameThree);
		    mysqli_stmt_execute($icthprepare);
		}
		
		//$_SESSION["username"] = $username;
		//$_SESSION["usertype"] = 1;
		
		echo '<script> alert("Registration was a Success") </script>';
	    }
	}
	mysqli_free_result($result);
	mysqli_free_result($result2);
    }
    				                                                         
    if(isset($_POST['btnSignUpEvaluator']))
    {
	$fnameEvaluator = $_POST['fnameEvaluator'];
	$lnameEvaluator = $_POST['lnameEvaluator'];
	$username = $_POST['username'];
	$usertype = 2;
	$pwEvaluator = $_POST['pwEvaluator'];
	$hashedpwEvaluator = password_hash($pwEvaluator,PASSWORD_DEFAULT);
	$reppwEvaluator = $_POST['reppwEvaluator'];
	$adult = ($_POST['adult'] == 'on')? 1 : 0;
	$termsofservice = ($_POST['termsofservice'] == 'on')? 1 : 0;
	
	$contactTypeOne = $_POST['contactTypeOneEvaluator'];
	$contactTypeTwo = $_POST['contactTypeTwoEvaluator'];
	$contactTypeThree = $_POST['contactTypeThreeEvaluator'];
	$contactNameOne = $_POST['contactNameOneEvaluator'];
	$contactNameTwo = $_POST['contactNameTwoEvaluator'];
	$contactNameThree = $_POST['contactNameThreeEvaluator'];
	
	$checkqry = "SELECT uno, username FROM user WHERE username=? AND usertype=?;";
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'si', $username, $usertype);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if($username=="" || $pwEvaluator=="" || $reppwEvaluator=="" || $adult==0 || $termsofservice==0)
	{
?>
            <script> alert("Please Enter required Fields. No 2"); </script>
<?php
        }
	elseif(mysqli_num_rows($result2) > 0)
	{
?>
	    <script> alert("Please Enter different Username. No 2"); </script>
<?php	    
	}
        elseif($contactNameOne=="" && $contactNameTwo=="" && $contactNameThree=="")
	{
?>		
            <script> alert("Please Enter at least one Contact Information. No 2"); </script>
<?php
	}
        elseif($pwEvaluator!=$reppwEvaluator)
	{
?>						
            <script> alert("Password Fields are not matched. No 2"); </script>
<?php						    
	}
        else 
        {
	    $insertEvaluator = "INSERT INTO user (fname,lname,username,usertype,password,adulthood,termsofservice) 
		VALUES (?,?,?,?,?,?,?)";
	    
	    $insertprepare = mysqli_prepare($connection, $insertEvaluator);
	    mysqli_stmt_bind_param($insertprepare, 'sssisii', $fnameEvaluator, $lnameEvaluator, $username, $usertype, $hashedpwEvaluator, $adult, $termsofservice);
	    mysqli_stmt_execute($insertprepare);
	    
	    $selectqry = "SELECT uno, username FROM user WHERE username=? AND usertype=?;";
	    $stmt = mysqli_prepare($connection, $selectqry);
	    mysqli_stmt_bind_param($stmt,'si', $username, $usertype);
	    mysqli_stmt_execute($stmt);
	    $result = mysqli_stmt_get_result($stmt);
	    
	    if(mysqli_num_rows($result) > 0)
	    {
		$rowUserData = mysqli_fetch_assoc($result);
		
		if(!empty($contactNameOne))
		{
		    $contactTypeOne = !empty($contactTypeOne)? $contactTypeOne : "Mail";
		    $ico = "INSERT INTO user_contact (user_uno,contact_type,contact_data) VALUES (?,?,?);";
		    
		    $icoprepare = mysqli_prepare($connection, $ico);
		    mysqli_stmt_bind_param($icoprepare, 'iss', $rowUserData['uno'], $contactTypeOne, $contactNameOne);
		    mysqli_stmt_execute($icoprepare);
		}
		
		if(!empty($contactNameTwo))
		{
		    $contactTypeTwo = !empty($contactTypeTwo)? $contactTypeTwo : "Mail";
		    $ict = "INSERT INTO user_contact (user_uno,contact_type,contact_data) VALUES (?,?,?);";
		    
		    $ictprepare = mysqli_prepare($connection, $ict);
		    mysqli_stmt_bind_param($ictprepare, 'iss', $rowUserData['uno'], $contactTypeTwo, $contactNameTwo);
		    mysqli_stmt_execute($ictprepare);
		}
		
		if(!empty($contactNameThree))
		{
		    $contactTypeThree = !empty($contactTypeThree)? $contactTypeThree : "Mail";
		    $icth = "INSERT INTO user_contact (user_uno,contact_type,contact_data) VALUES (?,?,?);";
		    
		    $icthprepare = mysqli_prepare($connection, $icth);
		    mysqli_stmt_bind_param($icthprepare, 'iss', $rowUserData['uno'], $contactTypeThree, $contactNameThree);
		    mysqli_stmt_execute($icthprepare);
		}
		
		//$_SESSION["username"] = $username;
		//$_SESSION["usertype"] = 2;
		
		echo '<script> alert("Registration was a Success") </script>';
	    }
	}
	mysqli_free_result($result);
	mysqli_free_result($result2);
    }
    
    if(isset($_POST['btnSpectator']))
    {
	$_SESSION["username"] = 'none';
	$_SESSION["usertype"] = 0;
	header("Location: sagi.php");
    }
?>
<!DOCTYPE html>  
<html lang="en">
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="icon" href="images/brush.webp" type="image/webp">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<style>
@import url("https://fonts.googleapis.com/css?family=Lato");

/* Main Tabs */
label{
 background-color: #173457;
 color: white;
 display: inline-block;
 cursor: pointer;
 padding: 10px;
 font-size: 20px;
 border-color: black;
 border-style: solid;
 border-width: 0.5px;
 width:455px;
 opacity:0.95;
 border-radius: 10px;
}

label:hover {
 background-color: #02404b;
}

label input:checked {
 background-color: red;
}

.tab-radio {
 display: none;
}

/* Tabs behaviour, hidden if not checked/clicked */
.sub-tab-content,
.tab-content {
 display: none;
}

.tab-radio:checked + .tab-content,
.tab-radio:checked + .sub-tab-content {
 display: block;
}

/* Sub-tabs */
.sub-tabs-container label {
 background-color: #1f4147;
 color: white;
}

.sub-tabs-container label:hover {
 background-color: #50bcbf;
 color:black;
}

/* Tabs Content */
.tab-content {
 padding: 30px;
 background-color: #173457;
 border-radius: 10px;
  border-color: black;
 border-style: solid;
 border-width: 0.5px;
 box-shadow: 2px 10px 6px -3px rgba(0, 0, 0, 0.5);
 width:60%;
 height:80%;
 opacity:0.95;
 align:center;
}

/* General */

body {
 width: 90%;
 margin: 10px auto;
 background-image:url("images/bg2.jpg");
 background-repeat: repeat-y;
 background-size: cover;
 font-family: Lato, sans-serif;
 letter-spacing: 1px;
}

*, *:hover {
 transition: all .3s;
}

.button {
 background-color: #6786ab;
 color: black;
 padding: 12px 20px;
 border: none;
 border-radius: 4px;
 cursor: pointer;
 
}

.form-control{
width:100%;
}

td{
padding: 10px;
}

</style>
</head>

<body>
<section>
<center>
<div>
<div class="top-tabs-container">
  <label for="main-tab-1">Login</label>
  <label for="main-tab-2">Sign Up</label>
  <label for="main-tab-3">Spectator</label>
</div>

<!-- Tab Container -->
<form name="loginSignupForm" id="loginSignupForm" action="" method="post">
<input class="tab-radio" id="main-tab-1" name="main-group" type="radio" checked="checked">

<div class="tab-content" style="margin-left:0%;">
 
    <table style="padding:500px;">
        <tr style="">
            <div class="" style="width:500px;">  
		<td>
		    <select name="regType" id="selRegType" class="form-control">
			<option value="">--Select Registration Type--</option>
			<option value="artist">Artist</option>
			<option value="evaluator">Evaluator</option>
                    </select>
		</td>
            </div>
        </tr>
        <tr>
            <div class="col" style="">
                <td>
		    <input name="usernameLogin" id="txtUsername" type="text" class="form-control" placeholder="Enter your pseudonyme or username">
		</td>
            </div>
        </tr>
	<tr>
            <div class="" style="width:500px;margin-top:20px;">
                <td>
		    <input name="passwordLogin" id="txtPassword" type="password" class="form-control" data-type="password" placeholder="Enter your password" autocomplete="off">
		</td>
            </div>
        </tr>
        <tr>
            <td>
                <div class="" style="width:500px;margin:20px;">
                    <input name="btnLogin" type="submit" class="button" value="Sign In" style="color:white; font-weight:bold;" id="btnSignin" onclick="JavaScript:return validateLoginForm();" > <br>
                </div>
                <div class="hr">
		</div>
                <div class="foot"> 
		    <a href="#">Forgot Password?</a>
		</div>
            </td>
        </tr>
    </table>
</div>

<!-- Tab Container -->

<input class="tab-radio" id="main-tab-2" name="main-group" type="radio">

<div class="tab-content">

  <div class="sub-tabs-container">
  <!-- NOTE: due to id note below, remember to match the for.
  The actual title doesn't matter, just to show it works... -->
    <label for="sub-tab2-1" style="width:150px;font-size:12.5px;height:40px;font-weight:bold;">Artist</label>
    <label for="sub-tab2-2" style="width:150px;font-size:12.5px;height:40px;font-weight:bold;">Evaluator</label>
  </div>
  
  <!-- Sub Tab -->
  <!-- NOTE: name="sub-group" will require to be unique to the tab, 
        ie: tab2 = sub-group2, tab3 = sub-group 3 etc. -->
  <!-- NOTE: id have to be unique. So for each sub tabs, the input id will have to change-->
  
  <!--Artist-->
  <input class="tab-radio" id="sub-tab2-1" name="sub-group2" type="radio" checked="checked">
  <div class="sub-tab-content">
        <table>
            <tr>
                <td class="sign-up-table">
                    <!--fname lname-->
                    <table>
			<tr>
                            <td style="padding:0px;width:50%;">
                                <div class="">
                                    <input  name="fnameArtist" style="width:100%;" id="txtFnameArtist" type="text" class="form-control" placeholder="First Name">
                                </div>
                            </td>
                            <td style="padding:0px 0px 0px 10px; width:50%;">
                                <div class="">
                                    <input name="lnameArtist" style="width:100%;" id="txtLnameArtist" type="text" class="form-control" placeholder="Last Name">
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="" style="width:50%;">
                    <!--pseudonyme-->
                    <div class="">
                        <input name="pseudonyme" style="width:100%;" id="txtPseudonymeArtist" type="text" class="form-control" placeholder="Pseudonyme*">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="sign-up-table" style="width:50%;">
                    <!--contact type-->
		    <select name="contactTypeOneArtist" class="form-control" id="txtContactTypeOneArtist" placeholder="Choose First Contact Type*">
			<option value="">--First Contact Type--</option>
			<option value="Mail">Mail Addr.</option>
			<option value="Telephone">Telephone No.</option>
			<option value="Email">Email Addr.</option>
			<option value="X">X (Twitter) URL</option>
		    </select>
                </td>
                <td class="sign-up-table">
		    <!--contact details-->
                    <div class="">
                        <input name="contactNameOneArtist" id="txtContactNameOneArtist" type="text" class="form-control" placeholder="Enter First Contact Detail*">
                    </div>
		</td>
            </tr>
	    <tr>
                <td class="sign-up-table" style="width:50%;">
                    <!--contact type-->
		    <select name="contactTypeTwoArtist" class="form-control" id="txtContactTypeTwoArtist" placeholder="Choose Second Contact Type">
			<option value="">--Second Contact Type--</option>
			<option value="Mail">Mail Addr.</option>
			<option value="Telephone">Telephone No.</option>
			<option value="Email">Email Addr.</option>
			<option value="X">X (Twitter) URL</option>
		    </select>
                </td>
                <td class="sign-up-table">
		    <!--contact details-->
                    <div class="">
                        <input name="contactNameTwoArtist" id="txtContactNameTwoArtist" type="text" class="form-control" placeholder="Enter Second Contact Detail">
                    </div>
		</td>
            </tr>
	    <tr>
                <td class="sign-up-table" style="width:50%;">
                    <!--contact type-->
		    <select name="contactTypeThreeArtist" class="form-control" id="txtContactTypeThreeArtist" placeholder="Choose Third Contact Type">
			<option value="">--Third Contact Type--</option>
			<option value="Mail">Mail Addr.</option>
			<option value="Telephone">Telephone No.</option>
			<option value="Email">Email Addr.</option>
			<option value="X">X (Twitter) URL</option>
		    </select>
                </td>
                <td class="sign-up-table">
		    <!--contact details-->
                    <div class="">
                        <input name="contactNameThreeArtist" id="txtContactNameThreeArtist" type="text" class="form-control" placeholder="Enter Third Contact Detail">
                    </div>
		</td>
            </tr>
            <tr>
                <td class="sign-up-table">
                    <!--pass-->
                    <div class="">
                        <input name="pwArtist" id="txtSignUpPasswordArtist" type="password" class="form-control" data-type="password" placeholder="Create your password*" autocomplete="off">
                    </div>
                </td>
                <td class="sign-up-table">
                    <!--rep pass-->
                    <div class="">
                        <input name="reppwArtist" id="txtSignUpRepPassArtist" type="password" class="form-control" data-type="password" placeholder="Repeat your password*" autocomplete="off">
                    </div>
                </td>
            </tr>
	    <tr>
		<td colspan="2" class="">
		    <input name="adult" id="approveAdultArtist" type="checkbox">
		    <label for="approveAdultArtist">I'm approving, that I'm or have been 18 Years old.</label>
		</td>
	    </tr>
	    <tr>
		<td colspan="2" class="">
		    <input name="termsofservice" id="approveTermsOfServiceArtist" type="checkbox">
		    <label for="approveTermsOfServiceArtist">I'm accepting Terms of Service of this Site.</label>
		</td>
	    </tr>
            <tr>
                <td colspan="2" class="sign-up-table">
                    <div class="" style="margin:20px;">
                        <input type="submit" name="btnSignUpArtist" class="button" value="Sign Up" style="color:white; font-weight:bold;" id="btnSignUpArtist" onclick="JavaScript:return validateSignupArtistForm();">
                    </div>
                    <div class="hr">
		    </div>
                    <div class="foot">
                        <label for="main-tab-1" style="font-size:16px;border:0px; width: 160px;">Already Member?</label>
                    </div>
                </td>
            </tr>
        </table>
  </div>
  
  <!-- Sub Tab -->
  <!--Evaluator-->
  <input class="tab-radio" id="sub-tab2-2" name="sub-group2" type="radio">
  <div class="sub-tab-content">
    <table>
        <tr>
	    <td class="sign-up-table">
		<!--fname lname-->
		<table>
		    <tr>
			<td style="padding:0px; width:50%;">
			    <div class="">
				<input  name="fnameEvaluator" style="width:100%;" id="txtFnameEvaluator" type="text" class="form-control" placeholder="First Name">
			    </div>
			</td>
			<td style="padding:0px 0px 0px 10px; width:50%;">
			    <div class="">
				<input name="lnameEvaluator" style="width:100%;" id="txtLnameEvaluator" type="text" class="form-control" placeholder="Last Name">
			    </div>
			</td>
		    </tr>
		</table>
	    </td>
	    <td class="" style="width:50%;">
		<!--username-->
		<div class="">
		    <input name="username" style="width:100%;" id="txtUsernameEvaluator" type="text" class="form-control" placeholder="Username*">
		</div>
	    </td>
	</tr>
	<tr>
	    <td class="sign-up-table" style="width:50%;">
		<!--contact type-->
		<select name="contactTypeOneEvaluator" class="form-control" id="txtContactTypeOneEvaluator" placeholder="Choose First Contact Type*">
		    <option value="">--First Contact Type--</option>
		    <option value="Mail">Mail Addr.</option>
		    <option value="Telephone">Telephone No.</option>
		    <option value="Email">Email Addr.</option>
		    <option value="X">X (Twitter) URL</option>
		</select>
	    </td>
	    <td class="sign-up-table">
		<!--contact details-->
		<div class="">
		    <input name="contactNameOneEvaluator" id="txtContactNameOneEvaluator" type="text" class="form-control" placeholder="Enter First Contact Detail*">
		</div>
	    </td>
	</tr>
	<tr>
	    <td class="sign-up-table" style="width:50%;">
		<!--contact type-->
		<select name="contactTypeTwoEvaluator" class="form-control" id="txtContactTypeTwoEvaluator" placeholder="Choose Second Contact Type">
		    <option value="">--Second Contact Type--</option>
		    <option value="Mail">Mail Addr.</option>
		    <option value="Telephone">Telephone No.</option>
		    <option value="Email">Email Addr.</option>
		    <option value="X">X (Twitter) URL</option>
		</select>
	    </td>
	    <td class="sign-up-table">
		<!--contact details-->
		<div class="">
		    <input name="contactNameTwoEvaluator" id="txtContactNameTwoEvaluator" type="text" class="form-control" placeholder="Enter Second Contact Detail">
		</div>
	    </td>
	</tr>
	<tr>
	    <td class="sign-up-table" style="width:50%;">
		<!--contact type-->
		<select name="contactTypeThreeEvaluator" class="form-control" id="txtContactTypeThreeEvaluator" placeholder="Choose Third Contact Type">
		    <option value="">--Third Contact Type--</option>
		    <option value="Mail">Mail Addr.</option>
		    <option value="Telephone">Telephone No.</option>
		    <option value="Email">Email Addr.</option>
		    <option value="X">X (Twitter) URL</option>
		</select>
	    </td>
	    <td class="sign-up-table">
		<!--contact details-->
		<div class="">
		    <input name="contactNameThreeEvaluator" id="txtContactNameThreeEvaluator" type="text" class="form-control" placeholder="Enter Third Contact Detail">
		</div>
	    </td>
	</tr>
	<tr>
	    <td class="sign-up-table">
		<!--pass-->
		<div class="">
		    <input name="pwEvaluator" id="txtSignUpPasswordEvaluator" type="password" class="form-control" data-type="password" placeholder="Create your password*" autocomplete="off">
		</div>
	    </td>
	    <td class="sign-up-table">
		<!--rep pass-->
		<div class="">
		    <input name="reppwEvaluator" id="txtSignUpRepPassEvaluator" type="password" class="form-control" data-type="password" placeholder="Repeat your password*" autocomplete="off">
		</div>
	    </td>
	</tr>
	<tr>
	    <td colspan="2" class="">
		<input name="adult" id="approveAdultEvaluator" type="checkbox">
		<label for="approveAdultEvaluator">I'm approving, that I'm or have been 18 Years old.</label>
	    </td>
	</tr>
	<tr>
	    <td colspan="2" class="">
		<input name="termsofservice" id="approveTermsOfServiceEvaluator" type="checkbox">
		<label for="approveTermsOfServiceEvaluator">I'm accepting Terms of Service of this Site.</label>
	    </td>
	</tr>
	<tr>
            <td colspan="2" class="sign-up-table">
                <div class="group" style="margin:20px;">
                    <input  name="btnSignUpEvaluator" type="submit" class="button" value="Sign Up" style="color:white; font-weight:bold;" id="btnSignUpEvaluator" onclick="JavaScript:return validateSignupEvaluatorForm();" />
                </div>
                <div class="hr"></div>
                <div class="foot">
                    <label for="main-tab-1" style="font-size:16px;border:0px; width: 160px;">Already Member?</label>
                </div>
            </td>
        </tr>    
    </table>
  </div>
  
  </div>
</div>

<!-- Tab Container -->

<input class="tab-radio" id="main-tab-3" name="main-group" type="radio">

<div class="tab-content" style="margin-left:0%;">
        <table>
	    <tr>
		<td colspan="2" class="">
		    <input name="termsofservice" id="approveTermsOfServiceSpectator" type="checkbox">
		    <label for="approveTermsOfServiceSpectator">I'm accepting Terms of Service of this Site.</label>
		</td>
	    </tr>
            <tr>
                <td colspan="2" class="">
                    <div class="" style="margin:20px;">
                        <input type="submit" name="btnSpectator" class="button" value="Continue" style="color:white; font-weight:bold;" id="btnSpectator" onclick="JavaScript:return validateSpectatorForm();">
                    </div>
                </td>
            </tr>
        </table>
</div>

</form>
</center>
<script type="text/javascript">
	function validateLoginForm() {
	    var succeed = true;
	    var reg_type_v = $( "#selRegType" ).val();
	    var usr_name = document.querySelector( "input[name='usernameLogin']" );
	    let usr_name_v = usr_name.value;
	    var pass = document.querySelector( "input[name='passwordLogin']" );
	    let pass_v = pass.value;
	    
	    let arr = ["artist", "evaluator"];
	    let lUnsafeCharacters = /[\W|_]/g;
	    
	    if (jQuery.inArray(reg_type_v, arr) == -1){
		alert('Wrong selection.');
		succeed = false;
	    }// end if
	    if (usr_name_v.length < 1){
		alert('Too short.');
		succeed = false;
	    }// end if
	    if (usr_name_v.length > 25){
		alert('Too long.');
		succeed = false;
	    }// end if
	    if (usr_name_v.search(lUnsafeCharacters) > -1 || pass_v.search(lUnsafeCharacters) > -1){
		alert('Special characters are not allowed.');
		succeed = false;
	    }// end if
	    
	    if(succeed == true){
		document.getElementById('loginSignupForm').name='btnLogin';
		document.getElementById('loginSignupForm').action='';
		document.getElementById('loginSignupForm').submit();
		return(true);
	    }else{
		return(false);
	    }
	}
	
	function validateSignupArtistForm() {
	    var succeed = true;
	    
	    var frt_name = document.querySelector( "input[name='fnameArtist']" );
	    let frt_name_v = frt_name.value;
	    var lst_name = document.querySelector( "input[name='lnameArtist']" );
	    let lst_name_v = lst_name.value;
	    var psd_name = document.querySelector( "input[name='pseudonyme']" );
	    let psd_name_v = psd_name.value;
	    
	    var con_typ_ov = $( "#txtContactTypeOneArtist" ).val();
	    var con_typ_tv = $( "#txtContactTypeTwoArtist" ).val();
	    var con_typ_thv = $( "#txtContactTypeThreeArtist" ).val();
	    
	    var con_name_o = document.querySelector( "input[name='contactNameOne']" );
	    let con_name_ov = con_name_o.value;
	    var con_name_t = document.querySelector( "input[name='contactNameTwo']" );
	    let con_name_tv = con_name_t.value;
	    var con_name_th = document.querySelector( "input[name='contactNameThree']" );
	    let con_name_thv = con_name_th.value;
	    
	    var ad = document.getElementById( "approveAdultArtist" );
	    let ad_c = ad.checked;
	    var tos = document.getElementById( "approveTermsOfServiceArtist" );
	    let tos_c = tos.checked;
	    
	    var pwd = document.querySelector( "input[name='pwArtist']" );
	    let pwd_v = pwd.value;
	    var rtpwd = document.querySelector( "input[name='reppwArtist']" );
	    let rtpwd_v = rtpwd.value;
	    
	    let arr = ["", "Mail", "Telephone", "Email", "X"];
	    let lUnsafeCharacters = /[\W|_]/g;
	    let lemail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-z\-0-9]+\.)+[a-z]{2,}))$/;
	    
	    if (frt_name_v != '' && frt_name_v.length > 40){
		alert('Incorrect first name.');
		succeed = false;
	    }// end if
	    if (lst_name_v != '' && lst_name_v.length > 90){
		alert('Incorrect last name.');
		succeed = false;
	    }// end if
	    if (psd_name_v.length < 1 && psd_name_v.length > 25){
		alert('Incorrect pseudonyme.');
		succeed = false;
	    }// end if
	    if (jQuery.inArray(con_typ_ov, arr) == -1){
		alert('Wrong selection.');
		succeed = false;
	    }
	    else
	    {
		if (con_typ_ov == "Mail" && con_name_ov != '' && (con_name_ov.length < 10 || con_name_ov.length > 255)){
		    alert('Incorrect Mail address.');
		    succeed = false;
		}
		else if (con_typ_ov == "Telephone" && con_name_ov != '' && (con_name_ov.length < 7 || con_name_ov.length > 13)){
		    alert('Incorrect Telephone number.');
		    succeed = false;
		}
		else if (con_typ_ov == "Email" && con_name_ov != '' && !lemail.test(con_name_ov)){
		    alert('Incorrect E-mail (name@host.domain).');
		    succeed = false;
		}
		else if (con_typ_ov == "X" && con_name_ov != '' && (con_name_ov.length < 15 || con_name_ov.length > 255)){
		    alert('Incorrect URL address.');
		    succeed = false;
		}
	    }// end if
	    if (jQuery.inArray(con_typ_tv, arr) == -1){
		alert('Wrong selection.');
		succeed = false;
	    }
	    else
	    {
		if (con_typ_tv == "Mail" && con_name_tv != '' && (con_name_tv.length < 10 || con_name_tv.length > 255)){
		    alert('Incorrect Mail address.');
		    succeed = false;
		}
		else if (con_typ_tv == "Telephone" && con_name_tv != '' && (con_name_tv.length < 7 || con_name_tv.length > 13)){
		    alert('Incorrect Telephone number.');
		    succeed = false;
		}
		else if (con_typ_tv == "Email" && con_name_tv != '' && !lemail.test(con_name_tv)){
		    alert('Incorrect E-mail (name@host.domain).');
		    succeed = false;
		}
		else if (con_typ_tv == "X" && con_name_tv != '' && (con_name_tv.length < 15 || con_name_tv.length > 255)){
		    alert('Incorrect URL address.');
		    succeed = false;
		}
	    }// end if
	    if (jQuery.inArray(con_typ_thv, arr) == -1){
		alert('Wrong selection.');
		succeed = false;
	    }
	    else
	    {
		if (con_typ_thv == "Mail" && con_name_thv != '' && (con_name_thv.length < 10 || con_name_thv.length > 255)){
		    alert('Incorrect Mail address.');
		    succeed = false;
		}
		else if (con_typ_thv == "Telephone" && con_name_thv != '' && (con_name_thv.length < 7 || con_name_thv.length > 13)){
		    alert('Incorrect Telephone number.');
		    succeed = false;
		}
		else if (con_typ_thv == "Email" && con_name_thv != '' && !lemail.test(con_name_thv)){
		    alert('Incorrect E-mail (name@host.domain).');
		    succeed = false;
		}
		else if (con_typ_thv == "X" && con_name_thv != '' && (con_name_thv.length < 15 || con_name_thv.length > 255)){
		    alert('Incorrect URL address.');
		    succeed = false;
		}
	    }// end if
	    if (con_name_ov == '' && con_name_tv == '' && con_name_thv == ''){
		alert('At least one contact information must be filled.');
		succeed = false;
	    }// end if
	    if (pwd_v.search(lUnsafeCharacters) > -1){
		alert('Special characters are not allowed.');
		succeed = false;
	    }// end if
	    if (pwd_v == '' || rtpwd_v == ''){
		alert('Password is not provided.');
		succeed = false;
	    }// end if
	    if (pwd_v != rtpwd_v){
		alert('Passwords does not match.');
		succeed = false;
	    }// end if
	    if (ad_c != true || tos_c != true){
		alert('You don\'t meet mandatory requirements of this service.');
		succeed = false;
	    }// end if
	    
	    if(succeed == true){
		document.getElementById('loginSignupForm').name='btnSignUpArtist';
		document.getElementById('loginSignupForm').action='';
		document.getElementById('loginSignupForm').submit();
		return(true);
	    }else{
		return(false);
	    }
	}

	function validateSignupEvaluatorForm() {
	    var succeed = true;
	    var frt_name = document.querySelector( "input[name='fnameEvaluator']" );
	    let frt_name_v = frt_name.value;
	    var lst_name = document.querySelector( "input[name='lnameEvaluator']" );
	    let lst_name_v = lst_name.value;
	    var usr_name = document.querySelector( "input[name='username']" );
	    let usr_name_v = usr_name.value;
	    
	    var con_typ_ov = $( "#txtContactTypeOneEvaluator" ).val();
	    var con_typ_tv = $( "#txtContactTypeTwoEvaluator" ).val();
	    var con_typ_thv = $( "#txtContactTypeThreeEvaluator" ).val();
	    
	    var con_name_o = document.querySelector( "input[name='contactNameOne']" );
	    let con_name_ov = con_name_o.value;
	    var con_name_t = document.querySelector( "input[name='contactNameTwo']" );
	    let con_name_tv = con_name_t.value;
	    var con_name_th = document.querySelector( "input[name='contactNameThree']" );
	    let con_name_thv = con_name_th.value;
	    
	    var ad = document.getElementById( "approveAdultEvaluator" );
	    let ad_c = ad.checked;
	    var tos = document.getElementById( "approveTermsOfServiceEvaluator" );
	    let tos_c = tos.checked;
	    
	    var pwd = document.querySelector( "input[name='pwEvaluator']" );
	    let pwd_v = pwd.value;
	    var rtpwd = document.querySelector( "input[name='reppwEvaluator']" );
	    let rtpwd_v = rtpwd.value;
	    
	    let arr = ["", "Mail", "Telephone", "Email", "X"];
	    let lUnsafeCharacters = /[\W|_]/g;
	    let lemail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-z\-0-9]+\.)+[a-z]{2,}))$/;
	    
	    if (frt_name_v != '' && frt_name_v.length > 40){
		alert('Incorrect first name.');
		succeed = false;
	    }// end if
	    if (lst_name_v != '' && lst_name_v.length > 90){
		alert('Incorrect last name.');
		succeed = false;
	    }// end if
	    if (usr_name_v.length < 1 && usr_name_v.length > 25){
		alert('Incorrect username.');
		succeed = false;
	    }// end if
	    if (jQuery.inArray(con_typ_ov, arr) == -1){
		alert('Wrong selection.');
		succeed = false;
	    }
	    else
	    {
		if (con_typ_ov == "Mail" && con_name_ov != '' && (con_name_ov.length < 10 || con_name_ov.length > 255)){
		    alert('Incorrect Mail address.');
		    succeed = false;
		}
		else if (con_typ_ov == "Telephone" && con_name_ov != '' && (con_name_ov.length < 7 || con_name_ov.length > 13)){
		    alert('Incorrect Telephone number.');
		    succeed = false;
		}
		else if (con_typ_ov == "Email" && con_name_ov != '' && !lemail.test(con_name_ov)){
		    alert('Incorrect E-mail (name@host.domain).');
		    succeed = false;
		}
		else if (con_typ_ov == "X" && con_name_ov != '' && (con_name_ov.length < 15 || con_name_ov.length > 255)){
		    alert('Incorrect URL address.');
		    succeed = false;
		}
	    }// end if
	    if (jQuery.inArray(con_typ_tv, arr) == -1){
		alert('Wrong selection.');
		succeed = false;
	    }
	    else
	    {
		if (con_typ_tv == "Mail" && con_name_tv != '' && (con_name_tv.length < 10 || con_name_tv.length > 255)){
		    alert('Incorrect Mail address.');
		    succeed = false;
		}
		else if (con_typ_tv == "Telephone" && con_name_tv != '' && (con_name_tv.length < 7 || con_name_tv.length > 13)){
		    alert('Incorrect Telephone number.');
		    succeed = false;
		}
		else if (con_typ_tv == "Email" && con_name_tv != '' && !lemail.test(con_name_tv)){
		    alert('Incorrect E-mail (name@host.domain).');
		    succeed = false;
		}
		else if (con_typ_tv == "X" && con_name_tv != '' && (con_name_tv.length < 15 || con_name_tv.length > 255)){
		    alert('Incorrect URL address.');
		    succeed = false;
		}
	    }// end if
	    if (jQuery.inArray(con_typ_thv, arr) == -1){
		alert('Wrong selection.');
		succeed = false;
	    }
	    else
	    {
		if (con_typ_thv == "Mail" && con_name_thv != '' && (con_name_thv.length < 10 || con_name_thv.length > 255)){
		    alert('Incorrect Mail address.');
		    succeed = false;
		}
		else if (con_typ_thv == "Telephone" && con_name_thv != '' && (con_name_thv.length < 7 || con_name_thv.length > 13)){
		    alert('Incorrect Telephone number.');
		    succeed = false;
		}
		else if (con_typ_thv == "Email" && con_name_thv != '' && !lemail.test(con_name_thv)){
		    alert('Incorrect E-mail (name@host.domain).');
		    succeed = false;
		}
		else if (con_typ_thv == "X" && con_name_thv != '' && (con_name_thv.length < 15 || con_name_thv.length > 255)){
		    alert('Incorrect URL address.');
		    succeed = false;
		}
	    }// end if
	    if (con_name_ov == '' && con_name_tv == '' && con_name_thv == ''){
		alert('At least one contact information must be filled.');
		succeed = false;
	    }// end if
	    if (pwd_v.search(lUnsafeCharacters) > -1){
		alert('Special characters are not allowed.');
		succeed = false;
	    }// end if
	    if (pwd_v == '' || rtpwd_v == ''){
		alert('Password is not provided.');
		succeed = false;
	    }// end if
	    if (pwd_v != rtpwd_v){
		alert('Passwords does not match.');
		succeed = false;
	    }// end if
	    if (ad_c != true || tos_c != true){
		alert('You don\'t meet mandatory requirements of this service.');
		succeed = false;
	    }// end if
	    
	    if(succeed == true){
		document.getElementById('loginSignupForm').name='btnSignUpEvaluator';
		document.getElementById('loginSignupForm').action='';
		document.getElementById('loginSignupForm').submit();
		return(true);
	    }else{
		return(false);
	    }
	}
	
	function validateSpectatorForm() {
	    var succeed = true;
	    var tos = document.getElementById( "approveTermsOfServiceSpectator" );
	    let tos_c = tos.checked;
	    
	    if (tos_c != true){
		alert('You don\'t meet mandatory requirements of this service.');
		succeed = false;
	    }// end if
	    
	    if(succeed == true){
		document.getElementById('loginSignupForm').name='btnSpectator';
		document.getElementById('loginSignupForm').action='';
		document.getElementById('loginSignupForm').submit();
		return(true);
	    }else{
		return(false);
	    }
	}

</script>
</body>
</html>
