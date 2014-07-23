<!-- if you need user information, just put them into the $_SESSION variable and output them here -->
Hey, . You are logged in.
Try to close this browser tab and open it again. Still logged in! ;)

	
<html>
 <head>
  <title>Music Project Home</title>
 </head>
 <body>
 <p>Welcome to the Music Project</p>
 
 <p>You are logged in as <?php echo $_SESSION['user_name']; ?> </p>
 
 <p>Please enter a Youtube ID to go to the playback page</p>
	 <form name="input" action="playback.php" method="get">
	Youtube ID: <input type="text" name="youtubeId">
	<input type="submit" value="Submit">
	</form> 
	
	<p><a href="history.php">History Page</a></p>
 </body>
</html>

<!-- because people were asking: "index.php?logout" is just my simplified form of "index.php?logout=true" -->
<a href="index.php?logout">Logout</a>
