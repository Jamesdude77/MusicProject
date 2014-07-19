<?php
session_start();
$_SESSION['youtubeId'] = $_GET['youtubeId'];
$_SESSION['userId'] = 1;
$_SESSION['pieceId'] = getPieceId($_GET['youtubeId']);
$_SESSION['playbackId'] = getPlaybackId();

function getPieceId($youtubeId) {
	$sql = "SELECT `piece`.`PieceId` FROM `musicprojectdb`.`piece` WHERE `piece`.`YoutubeId` = '".$youtubeId."'; ";
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}
	$result = mysqli_query($con,$sql);
	if (!$result) {
		$message  = 'Invalid query: ' . mysqli_error() . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}
	return implode(mysqli_fetch_assoc($result));
}

function getPlaybackId() {
	$sql = "INSERT INTO `musicprojectdb`.`playback` (`UserId`,`PieceId`) VALUES (".$_SESSION['userId'].",".$_SESSION['pieceId'].");";
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}
	$result = mysqli_query($con,$sql);
	if (!$result) {
		$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}
	return mysqli_insert_id($con);
}

?>

<html>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '390',
          width: '640',
          videoId: 'c7OS_vpMz-s',
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
      }

      // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) {
		// tell php about the video
		xmlhttp=new XMLHttpRequest();
		xmlhttp.open("GET","databaseInsert.php?action=insertPieceDetails&pieceName="+player.getVideoData().title+"&pieceLength="+player.getDuration(),true);
		xmlhttp.send();
	  
        event.target.playVideo();
      }

      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      var done = false;
      function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING && !done) {
          done = true;
        }
      }
      function stopVideo() {
        player.stopVideo();
      }
	  
	$(document).keypress(function(){
		xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			  $("span").text("data inserted");
			}
		  }
		xmlhttp.open("GET","databaseInsert.php?action=insertPlaybackEvent&playbackEventTime="+player.getCurrentTime(),true);
		xmlhttp.send();
  	});
</script>
<body>
Test page for James' music appreciation project

<?php 
echo $_SESSION['youtubeId']."\n";
echo $_SESSION['userId']."\n";
echo $_SESSION['pieceId']."\n";
echo $_SESSION['playbackId']."\n";

?>

<p><div id="player"></div></p>

<p>Keypresses: <span>0</span></p>

</body>
</html>