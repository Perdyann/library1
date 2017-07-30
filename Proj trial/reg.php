<!doctype html>
<html>
<head>
<?php
include("dbconnection.php")
?>

<meta charset="utf-8">
<title>Library</title>
<style type="text/css">
<!--
body {
	font: 100%/1.4 Verdana, Arial, Helvetica, sans-serif;
	background-color: #42413C;
	margin: 0;
	padding: 0;
	color: #000;
}

ul, ol, dl { 
	padding: 0;
	margin: 0;
}
h1, h2, h3, h4, h5, h6, p {
	margin-top: 0;
	padding-right: 15px;
	padding-left: 15px;
	font-family: Segoe, "Segoe UI", "DejaVu Sans", "Trebuchet MS", Verdana, sans-serif;
}
a img { 
	border: none;
}

a:link {
	color: #42413C;
	text-decoration: underline; 
}
a:visited {
	color: #6E6C64;
	text-decoration: underline;
}
a:hover, a:active, a:focus { 
	text-decoration: none;
}

.container {
	width: 960px;
	background-color: #FFFFFF;
	margin: 0 auto; 
}

header {
	background-color: #ADB96E;
}

.sidebar1 {
	float: left;
	width: 180px;
	background-color: #EADCAE;
	padding-bottom: 10px;
}
.content {
	padding: 10px 0;
	width: 600px;
	float: left;
}
aside {
	float: left;
	width: 180px;
	background-color: #EADCAE;
	padding: 10px 0;
}


.content ul, .content ol {
	padding: 0 15px 15px 40px; 
}


nav ul{
	list-style: none; 
	border-top: 1px solid #666; 
	margin-bottom: 15px; 
}
nav li {
	border-bottom: 1px solid #666; 
}
nav a, nav a:visited { 
	padding: 5px 5px 5px 15px;
	display: block; 
	width: 160px;  
	text-decoration: none;
	background-color: #C6D580;
}
nav a:hover, nav a:active, nav a:focus { 
	background-color: #ADB96E;
	color: #FFF;
}


footer {
	padding: 10px 0;
	background-color: #CCC49F;
	position: relative;
	clear: both; 
}

.fltrt {  
	float: right;
	margin-left: 8px;
}
.fltlft { 
	float: left;
	margin-right: 8px;
}
.clearfloat { 
	clear:both;
	height:0;
	font-size: 1px;
	line-height: 0px;
}


header, section, footer, aside, article, figure {
	display: block;
}
-->
</style><!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]--></head>

<body>

<div class="container">

  <header>
  <table id="headerTop">
		<tr>
		  <td height="152" id="headerL">&nbsp;
				
		  </td>
			<td align="left" valign="middle" id="headerC"><h1><a href="index.php">ONLINE LIBRARY MANAGEMENT SYSTEM</a></a>
		  </h1></td>
			<td id="headerR">
				<a href="index.php"><img src="library/library3.jpg" alt="Insert Logo Here" width="273" height="151" id="Insert_logo" style="background-color: #C6D580; display:block;" /></a>			</td>
		</tr>
	</table>
    
  </header>
  <div class="sidebar1">
  <nav>
    <ul>
      <li><a href="catalogue.php">Catalogue</a><a href="#"></a></li>
      <li><a href="login.php">My Account</a><a href="#"></a></li>
      <li><a href="register.php">Join the Library</a><a href="#"></a></li>
      <li><a href="#">Journal Articles</a></li>
    </ul>
</nav>
    <aside>
      <p>&nbsp;</p>
    </aside>
<!-- end .sidebar1 --></div>
  <article class="content">
  

    
    <section>
    
    
    <?php
	echo "<h4>Please enter the information below to register </h4>";
	 $submit=@$_POST['submit'];

    $f_name =@ strip_tags($_POST['f_name']);
	$l_name =@ strip_tags($_POST['l_name']);
    $username =@ strip_tags($_POST['username']);
    $password =@ strip_tags($_POST['password']);
	$email =@ strip_tags($_POST['email']);;
	$dob =@ strip_tags($_POST['dob']);;
	$sex =@ strip_tags($_POST['sex']);
    $query = "INSERT INTO member (f_name, l_name, username,password, email, dob, sex) VALUES ('$f_name','$l_name','$username','$password','$email','$dob','$sex')";
	if ($submit)
 {
          
        if 													// check for existence//
          ($f_name && $l_name && $username && $password &&$email && $dob &&$sex)
{	
	die( "You have been registered! <a href='login.php'> Return to login page </a>");			

} 
else 
       echo "<h2>Please fill all fields</h2>";			
	   }
	
?>
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'library');
define('DB_USER','root');
define('DB_PASSWORD','');
 
$con=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("Failed to connect to MySQL: " . mysql_error());
$db=mysql_select_db(DB_NAME,$con) or die("Failed to connect to MySQL: " . mysql_error());
?>
<?php
$con = mysql_connect("localhost","root","");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("library",$con);

								

@$queryreg = mysql_query ("INSERT INTO members VALUES ('','$f_name', '$l_name', '$username', '$password','$email','$dob','$sex')	

");
	
?>
<form action="" method="POST">
<p><table width="316" height="307">
<tr>
<td height="35">First name:</td>
<td>
<input type="text" name ="f_name">
</td>
</tr>
<tr>
<td height="35">Last name:</td>
<td>
<input type="text" name ="l_name">
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
<td height="35">Email:</td>
<td>
<input type="text" name ="email">
</td>
</tr>
<tr>
<td height="54">date of Birth:</td>
<td>
<input type="text" name ="dob">
</td>
	</tr> 
    <tr>
<td height="54">gender:</td>
<td>
<input type="text" name ="sex">
</td>
	</tr>
	</table>
</p>
<p><input type="submit" name="submit" class="button"value="Register"></p>
</form>

 </section>
   
  <!-- end .content --></article>
  <aside>
  <td id="sideBar">	
	<!-- side_login.htm -->
			
<!-- ReaderLogin=OK -->
<div class="SideContents">
	<div class="SLogin1">
		<form id="SLogin1" action="/HeritageScripts/Hapi.dll" method="post">
			<table>
				<tr>
					<th>Please log in</th>
				</tr>
				<tr>
					<td><label for="loginname">Username</label></td>
				</tr>
                
				<tr>
					<td><input type="text"  class="loginname" name="LoginName" /></td>
				</tr>
                <tr>
					<td><label for="loginname">Password</label></td>
				</tr>
                
				<tr>
					<td><input type="password"  class="loginname" name="LoginName" /></td>
				</tr>
								<tr>
					<td><input type="submit" class="ButtonSmall" id="loginSubmit" name="loginbtn" value="Login" alt="Click this to proceed" title="Click this to proceed" /></td>
				</tr>
			</table>
			<input type="hidden" name="DataSetName" value="HERITAGE" />
		  </form>
	</div>
</div>

    <h4>Contact us:</h4>
    <p>+44(0)2073570077</p>
    <p>info@zurielann.org</p>
    <p>zuriel@zurielann.org</p>
  </aside>
  <footer>
    <p>All rights reserved (c) Perdita Okeke. 2014</p>
    <address>
    </address>
  </footer>
<!-- end .container --></div>
</body>
</html>