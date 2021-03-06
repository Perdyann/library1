<!doctype html>
<html>
<head>
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
			<td align="left" valign="middle" id="headerC"><h1><a href="index.php">ONLINE LIBRARY MANAGEMENT SYSTEM</a></h1></td>
			<td id="headerR">
				<a href="index.php"><img src="library/library3.jpg" width="273" height="184"  alt=""/></a>			</td>
		</tr>
	</table>
    
  </header>
  <div class="sidebar1">
  <nav>
    <ul>
      <li><a href="index.php">Home</a><a href="#"></a></li>
      <li><a href="login.php">My Account</a><a href="#"></a></li>
      <li><a href="catalogue.php">Catalogue</a><a href="#"></a></li>
      <li><a href="register.php"> Join the Library</a></li>
    </ul>
</nav>
    <aside>
      <p>&nbsp;</p>
    </aside>
<!-- end .sidebar1 --></div>
  <article class="content">
    <h1>Welcome to Zurielann Library</h1>
    
<form method="POST" action="http://localhost/proj trial/catalogue.php">
<p> Search Library Catalogue<br />
<select name="search_results">
  <option value="jhr">Journey to the Higher Realm</option>
  <option value="tsw">The Spoken Word</option>
  <option value="th">The Harvest</option>
  <option value="lwtf">Long Walk to Freedom </option>
  <option value="tph">The Purple Hibiscus</option>
  <option value="hoays">Half of a Yellow Sun</option>
  <option value="candsn">Cloud and Network Data Storage Networking</option>
  <option value="rdpd">Rich Dad Poor Dad</option>
  <option value="tsotf">The Shock of the Fall</option>
</select>
<br />
<input type="submit" value="Search" />
<input type="hidden" name="recipient" value="zuriel@zurielann.org" />
<input type="hidden" name="subject" value="Search Catalogue" />
<div>

  <p> Welcome to Zurielann Online Library, a site where resourceful materials could be browsed, reserved or borrowed. To get started, <a href="register.php">Join the Library</a> by simply registering. After you become a member of the library officially, you can reserve and borrow as many copies of books as available. Guess what... It is absolutely free.<a href="register.php"></a></p>
</div>
</form>
    <section>
     <h2>Most Popular Loans</h2>
     <p><a href="jhr.php"><img src="journey to the higher realm.jpg" width="119" height="179"  alt=""/></a> <a href="half of a yellow sun.php"><img src="half of a yellow sun.png" width="121" height="180"  alt=""/></a>  <a href="the spoken word.php"><img src="the spoken word.png" width="121" height="180"  alt=""/></a> <a href="long walk to freedom.php"><img src="long walk to freedom.png" width="126" height="183"  alt=""/></a></p>
     <p>Click on book image to  check availability &amp; Reserve</p>
    </section>
    <section>
      <h2>&nbsp;</h2>
    </section>
    <section>    </section>
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
