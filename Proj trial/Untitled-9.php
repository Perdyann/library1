<!DOCTYPE html>
<html lang="en">

<?php
//To include database connection
  include("dbconnection.php");
?>

<body>
<!--==============================header=================================-->
<header>
//Header format included
<div class="row-nav">
<div class="main">
<h1 class="logo"><a href="index.php"><img src="images/logo.jpg" width="137" height="46"></a></h1>
<div class="clear"></div>
</div>
</div>
</header>
<!--==============================content=================================-->
<section id="content">
<div class="ic"></div>
<div class="main-block policy">
<div class="main">
//Registration Form to be filled	
<?php
echo "<h4>Please enter the information below to register </h4>";

$submit=@$_POST['submit'];
// form data//
$firstname=@ strip_tags($_POST['firstname']);
$lastname=@ strip_tags($_POST['lastname']);
$username=@strip_tags($_POST['username']);
$password=@strip_tags($_POST['password']);
$retypepassword= @strip_tags($_POST['retypepassword']);
$email=@strip_tags($_POST['email']);
$contactno=@strip_tags($_POST['contactno']);
if ($submit)
 {
          // check for existence//
        if 
          ($firstname && $lastname && $username && $password &&$retypepassword && $email &&$contactno)
{	
	die( "You have been registered! <a href='login.php'> Return to login page </a>");
} 
else 
       echo "<h2>Please fill all fields</h2>";
               if ($password == $retypepassword)
	{
	}
//If it does not exist
	else
	   echo "Your passwords do not match";

   // password encryption//
	$password = md5($password) ;
	$retypepassword = md5 ($retypepassword);

}
	
?>
<?php 	
	// open database//
	include 'dbconnection.php';

//Update database
@$queryreg = mysql_query ("INSERT INTO members VALUES ('','$firstname', '$lastname', '$username', '$password','$email','$contactno')
");
	
?>
//Html table formatting
<html>
<form action="" method="POST">
<p><table width="316" height="307">
<tr>
<td height="35">First name:</td>
<td>
<input type="text" name ="firstname">
</td>
</tr>
<tr>
<td height="35">Last name:</td>
<td>
<input type="text" name ="lastname">
</td>
</tr>
<tr>
<td height="35">User name:</td>
<td>
<input type="text" name ="username">
</td>
</tr>
<tr>
<td height="35">Password:</td>
<td>
<input type="password" name ="password">
</td>
</tr>
<tr>
<td height="35">Retype password:</td>
<td>
<input type="password" name ="retypepassword">
</td>
</tr>
<tr>
<td height="35">Email:</td>
<td>
<input type="text" name ="email">
</td>
</tr>
<tr>
<td height="54">Contact no:</td>
<td>
<input type="text" name ="contactno">
</td>
	</tr>
	</table>
</p>
<p><input type="submit" name="submit" class="button"value="Register"></p>
</form>
</html>
</div>
</div>
</div>
</section>
</div>
</body>
</html>
