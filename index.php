<html>
 <head>
  <title>Music Project Home</title>
 </head>
 <body>
 <p>Welcome to the Music Project</p>
 <p>Please enter a Youtube ID to go to the playback page</p>
	 <form name="input" action="playback.php" method="get">
	Youtube ID: <input type="text" name="youtubeId">
	<input type="submit" value="Submit">
	</form> 
	
	<p><a href="history.php">History Page</a></p>
 <?php
 session_start();
 $_SESSION['userId'] = 1;
 ?> 
 </body>
</html>