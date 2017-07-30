<?php
include 'dbconnection.php';

if (isset($_POST['username']))
{
		$username=$_POST['username'];
		$password=$_POST['password'];
	$sql="SELECT * FROM member WHERE username ='$username' AND password='$password'";
	if(mysql_num_rows($resultlogin) == 1)
{	
echo "you have successfully logged in.";
	exit();
}
	else
{
echo "invalid username & password.";
}
}
?>
