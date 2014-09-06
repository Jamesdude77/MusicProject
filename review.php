<html>
 <head>
  <title>Music Project Review Page</title>
 </head>
 <script type="text/javascript" src="https://www.google.com/jsapi"></script>
 <script type="text/javascript" src="swfobject/swfobject.js"></script>
  <script src="Chart.min.js"></script>
 <script>
 
 var ytplayer;
 
 var myVar = setInterval(function(){updateVidTime()}, 100);

function updateVidTime() {
	if (ytplayer != null)
	{
		document.getElementById("vidTime").innerHTML = ytplayer.getCurrentTime();
	}
	else
	{
		document.getElementById("vidTime").innerHTML = "video not loaded";
	}
}
 
 function onYouTubePlayerReady(playerId) {
      ytplayer = document.getElementById("myytplayer");
    }
 
 var loadPlayback = function()
 {
	alert("in load playback");
	// clear existing elements
	
	var existingPlayer = document.getElementById("myytplayer");
	if (existingPlayer != null)
	{
		newDiv = document.createElement("div");
		newDiv.setAttribute('id', 'player');
		newDiv.innerHTML = 'unloaded player';
		myytplayer.parentNode.replaceChild(newDiv, myytplayer);
	}
	document.getElementById("graph").innerHTML = "unloaded graph";
	
	// get selected values
	piecesSelectElement = document.getElementById('piecesSelect');
	pieceId = piecesSelectElement.options[piecesSelectElement.selectedIndex].value;
	
	playbacksSelectElement = document.getElementById('playbacksSelect');
	playbackId = playbacksSelectElement.options[playbacksSelectElement.selectedIndex].value;
    
	// replace graph element with actual graph
	
	//get duration + youtubeId
	xmlhttpDuration=new XMLHttpRequest();
	xmlhttpDuration.onreadystatechange=function() {
		if (xmlhttpDuration.readyState==4 && xmlhttpDuration.status==200) {
			var pieceLength = JSON.parse(xmlhttpDuration.responseText)[0]["PieceLength"];
			var youtubeId = JSON.parse(xmlhttpDuration.responseText)[0]["YoutubeId"];
			
			// replace player with youtube player
			var params = { allowScriptAccess: "always" };
			var atts = { id: "myytplayer" };
			swfobject.embedSWF("http://www.youtube.com/v/"+youtubeId+"?enablejsapi=1&playerapiid=ytplayer&version=3",
							   "player", "425", "356", "8", null, null, params, atts);
			
			//draw my graph
			var newDiv = document.getElementById("graph");
			newDiv.innerHTML = '<canvas id="myChart'+playbackId+'" width="500" height="400"></canvas>';
			
			xmlhttpGraph=new XMLHttpRequest();
			xmlhttpGraph.onreadystatechange=function() {
				if (xmlhttpGraph.readyState==4 && xmlhttpGraph.status==200) {
					var ctx = document.getElementById("myChart"+playbackId).getContext("2d");
					drawGraph(JSON.parse(xmlhttpGraph.responseText), pieceLength, ctx);
					
				}
			}	
			xmlhttpGraph.open("GET","view.php?action=getPlaybackStats&playbackId="+playbackId);
			xmlhttpGraph.send();
		}
	}	
	xmlhttpDuration.open("GET","view.php?action=getPieceDuration&pieceId="+pieceId);
	xmlhttpDuration.send();
 }
 
 var getPlaybacks = function()
{
	piecesSelectElement = document.getElementById('piecesSelect');
	pieceId = piecesSelectElement.options[piecesSelectElement.selectedIndex].value;
	
 // now load pieces dropdown
	xmlhttpPlaybacks=new XMLHttpRequest();
	xmlhttpPlaybacks.onreadystatechange=function() {
		if (xmlhttpPlaybacks.readyState==4 && xmlhttpPlaybacks.status==200) {
		
			var playbackDiv = document.getElementById("playbacksDropdown");
			if (playbackDiv != null)
			{
				playbackDiv.parentNode.removeChild(playbackDiv);
			}
			
			var playbacksDropdown = document.createElement('form');
			playbacksDropdown.setAttribute('id', 'playbacksDropdown');
			var selectElement2 = document.createElement('select');
			selectElement2.setAttribute('id', 'playbacksSelect');
			
			playbackData = JSON.parse(xmlhttpPlaybacks.responseText);
			for (i = 0; i < playbackData.length;i++)
			{
				var op = new Option();
				op.value = playbackData[i]["PlaybackId"];
				op.text = playbackData[i]["creationDate"] + ": " + playbackData[i]["EventCount"];
				selectElement2.options.add(op);
			}
			selectElement2.onchange = loadPlayback;
			
			playbacksDropdown.appendChild(selectElement2);
			document.getElementById('playbackDropdown').appendChild(playbacksDropdown);
			
		}
	}
	xmlhttpPlaybacks.open("GET","view.php?action=getPlaybackList&pieceId="+pieceId);
	xmlhttpPlaybacks.send();
 }
 
xmlhttpPieces=new XMLHttpRequest();
xmlhttpPieces.onreadystatechange=function() {
	if (xmlhttpPieces.readyState==4 && xmlhttpPieces.status==200) {
		
		var piecesDropdown = document.createElement('form');
		var selectElement2 = document.createElement('select');
		selectElement2.setAttribute('id', 'piecesSelect');
		
		pieceData = JSON.parse(xmlhttpPieces.responseText);
		for (i = 0; i < pieceData.length;i++)
		{
			var op = new Option();
			op.value = pieceData[i]["PieceId"];
			op.text = pieceData[i]["PieceName"];
			selectElement2.options.add(op);
		}
		selectElement2.onchange = getPlaybacks;
		
		piecesDropdown.appendChild(selectElement2);
		document.getElementById('piecesDropdown').appendChild(piecesDropdown);
		
	}
}
xmlhttpPieces.open("GET","view.php?action=getPiecesList");
xmlhttpPieces.send();

function drawGraph(rawData, duration, canvasElement) {

	var graphData = {
						labels: [],
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
	var labels = [];
	var data = [];
	var z = 0;
	for (x = 0; x < duration; x+=5)
	{
		labels[z] = x;
		z++;
		data[x] = 0;
		for  (y = 0; y < rawData.length; y++)
		{
			if (rawData[y] < x+2.5 && rawData[y] > x-2.5)
			{
				data[x] = data[x] + 1;
			}
			else if (rawData[y] > x+2.5)
				break;
		}
	}
	graphData.labels = labels;
	graphData.datasets[0].data = data;
	
	new Chart(canvasElement).Line(graphData, {});
}

 </script>

 <body>
 <p>Welcome to your Music Project Review</p>
 <p>choose a piece and playback from the drop down menus:</p>

 
 Select piece: <div id="piecesDropdown" ></div>
 Select playback: <div id="playbackDropdown" ></div>
 
 <p>Video Time: <div id="vidTime">unknown</div>
 </p>
 
 <p>
 <table>
 <tr>
 <td><div id="player"> </div></td><td><div id="graph">graph</div></td> <td><div id="googleChart">google chart</div></td>
 </tr>
 </table>
 </p>

 <p><a href="index.php">Back to index</a></p>
 
 </body>
</html>