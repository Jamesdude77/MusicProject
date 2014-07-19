<?php
session_start();
$_SESSION['youtubeId'] = $_GET['youtubeId'];
?>

<html>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="Chart.min.js"></script>

<script>
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');
	  
      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

	  function getQueryString() {
	  var result = {}, queryString = location.search.slice(1),
		  re = /([^&=]+)=([^&]*)/g, m;
	  while (m = re.exec(queryString)) {
		result[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
	  }
		  return result;
	}
	  
      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '390',
          width: '640',
          videoId: getQueryString()['youtubeId'],
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
		xmlhttp.open("GET","controller.php?action=initialisePlayback&pieceName="+player.getVideoData().title+"&pieceLength="+player.getDuration(),true);
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
		
		if(event.data === 0) {
			 $.ajax({
				type: "GET",
				dataType: "json",
				url: "view.php?playbackId=26", //Relative or absolute path to response.php file
				success: function(response) {
				
					alert("Form submitted successfully.\nReturned json: " + response);
					drawGraph(response);
				}
			});
		}
	  }
	  
      function stopVideo() {
        player.stopVideo();
      }
	  
	$(document).keypress(function(){
		xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			  //$("span").text("data inserted");
			}
		  }
		xmlhttp.open("GET","controller.php?action=insertPlaybackEvent&playbackEventTime="+player.getCurrentTime(),true);
		xmlhttp.send();
  	});
	
	function drawGraph(data) {
		var graphData = {
							labels: [0,5,10,15,20,25,30,35,40,45,50,55,60],
							datasets: [
								{
									label: "My First dataset",
									fillColor: "rgba(220,220,220,0.2)",
									strokeColor: "rgba(220,220,220,1)",
									pointColor: "rgba(220,220,220,1)",
									pointStrokeColor: "#fff",
									pointHighlightFill: "#fff",
									pointHighlightStroke: "rgba(220,220,220,1)",
									data: []
								}
							]
						};
		graphData.datasets[0].data = data;
		
		var ctx = document.getElementById("myChart").getContext("2d");
		var myNewChart = new Chart(ctx).Line(graphData, {
												bezierCurve: false
											});
	}
</script>
<body>
Test page for James' music appreciation project

<p><div id="player"></div></p>

<p>Graph: <canvas id="myChart" width="400" height="400"></canvas></p>

</body>
</html>