<html>
 <head>
  <title>Music Project Comparison Page</title>
 </head>
 
  <script src="Chart.min.js"></script>
 <script>
 
 var loadGraph = function() {
	// is it my graph?
	// get playbackId value
	myPlayback = document.getElementById('myPlaybackSelect');
	myPlaybackId = myPlayback.options[myPlayback.selectedIndex].value;
	
	piecesSelectElement = document.getElementById('piecesSelect');
	pieceId = piecesSelectElement.options[piecesSelectElement.selectedIndex].value;
	
	otherPlayback = document.getElementById('otherPlaybackSelect');
	otherPlaybackId = otherPlayback.options[otherPlayback.selectedIndex].value;
	
	//get duration
	xmlhttpDuration=new XMLHttpRequest();
	xmlhttpDuration.onreadystatechange=function() {
		if (xmlhttpDuration.readyState==4 && xmlhttpDuration.status==200) {
			var pieceLength = JSON.parse(xmlhttpDuration.responseText)[0]["PieceLength"];
			
			//draw my graph
			playbackId = myPlaybackId;
			var newDiv = document.getElementById("myGraph");
			newDiv.innerHTML = '<canvas id="myChart'+playbackId+'" width="500" height="400"></canvas>';
			//newDiv.setAttribute("id", "chart"+playbackId);
			
			xmlhttpGraph=new XMLHttpRequest();
			xmlhttpGraph.onreadystatechange=function() {
				if (xmlhttpGraph.readyState==4 && xmlhttpGraph.status==200) {
					var ctx = document.getElementById("myChart"+playbackId).getContext("2d");
					drawGraph(JSON.parse(xmlhttpGraph.responseText), pieceLength, ctx);
				}
			}	
			xmlhttpGraph.open("GET","view.php?action=getPlaybackStats&playbackId="+playbackId);
			xmlhttpGraph.send();
			
			// draw other graph
			var newDiv = document.getElementById("otherGraph");
			newDiv.innerHTML = '<canvas id="myChart'+otherPlaybackId+'" width="500" height="400"></canvas>';
			//newDiv.setAttribute("id", "chart"+playbackId);
			
			xmlhttpGraph2=new XMLHttpRequest();
			xmlhttpGraph2.onreadystatechange=function() {
				if (xmlhttpGraph2.readyState==4 && xmlhttpGraph2.status==200) {
					var ctx = document.getElementById("myChart"+otherPlaybackId).getContext("2d");
					drawGraph(JSON.parse(xmlhttpGraph2.responseText), pieceLength, ctx);
				}
			}	
			xmlhttpGraph2.open("GET","view.php?action=getPlaybackStats&playbackId="+otherPlaybackId);
			xmlhttpGraph2.send();
		}
	}	
	xmlhttpDuration.open("GET","view.php?action=getPieceDuration&pieceId="+pieceId);
	xmlhttpDuration.send();

}
 
 // wrap these next two in onselect function call
var updateTable = function()
{
	usersSelectElement = document.getElementById('usersSelect');
	piecesSelectElement = document.getElementById('piecesSelect');
	
	userId = usersSelectElement.options[usersSelectElement.selectedIndex].value;
	pieceId = piecesSelectElement.options[piecesSelectElement.selectedIndex].value;

	// get my playbacks
	xmlhttpMyPlaybacks=new XMLHttpRequest();
	xmlhttpMyPlaybacks.onreadystatechange=function() {
		if (xmlhttpMyPlaybacks.readyState==4 && xmlhttpMyPlaybacks.status==200) {
			
			// get if exists else create***
			var playbacksDropdown = document.createElement('form');
			var selectElement = document.createElement('select');
			selectElement.setAttribute('id', 'myPlaybackSelect');
				
			playbackData = JSON.parse(xmlhttpMyPlaybacks.responseText);
			for (i = 0; i < playbackData.length;i++)
			{
				var op = new Option();
				op.value = playbackData[i]["PlaybackId"];
				op.text = playbackData[i]["creationDate"] + " Events: "+ playbackData[i]["EventCount"];
				selectElement.options.add(op);
			}
			
			selectElement.onchange = loadGraph;
			
			playbacksDropdown.appendChild(selectElement);
			playbacksElement = document.getElementById('myPlaybacks');
			while (playbacksElement.firstChild) {
				playbacksElement.removeChild(playbacksElement.firstChild);
			}
			playbacksElement.appendChild(playbacksDropdown);
			
		}
	}
	xmlhttpMyPlaybacks.open("GET","view.php?action=getPlaybackList&pieceId="+pieceId);
	xmlhttpMyPlaybacks.send();
	
	// get other playbacks
	xmlhttpOtherPlaybacks=new XMLHttpRequest();
	xmlhttpOtherPlaybacks.onreadystatechange=function() {
		if (xmlhttpOtherPlaybacks.readyState==4 && xmlhttpOtherPlaybacks.status==200) {
			// get if exists else create***
			var playbacksDropdown = document.createElement('form');
			var selectElement = document.createElement('select');
			selectElement.setAttribute('id', 'otherPlaybackSelect');
			
			playbackData = JSON.parse(xmlhttpOtherPlaybacks.responseText);
			for (i = 0; i < playbackData.length;i++)
			{
				var op = new Option();
				op.value = playbackData[i]["PlaybackId"];
				op.text = playbackData[i]["creationDate"] + " Events: "+ playbackData[i]["EventCount"];
				selectElement.options.add(op);
			}
			
			selectElement.onchange = loadGraph;
			
			playbacksDropdown.appendChild(selectElement);
			playbacksElement = document.getElementById('otherPlaybacks');
			while (playbacksElement.firstChild) {
				playbacksElement.removeChild(playbacksElement.firstChild);
			}
			playbacksElement.appendChild(playbacksDropdown);
			
		}
	}
	xmlhttpOtherPlaybacks.open("GET","view.php?action=getOtherPlaybackList&userId="+userId+"&pieceId="+pieceId);
	xmlhttpOtherPlaybacks.send();

}

xmlhttpUsers=new XMLHttpRequest();
xmlhttpUsers.onreadystatechange=function() {
	if (xmlhttpUsers.readyState==4 && xmlhttpUsers.status==200) {
		
		var usersDropdown = document.createElement('form');
		var selectElement = document.createElement('select');
		selectElement.setAttribute('id', 'usersSelect');
		
		userData = JSON.parse(xmlhttpUsers.responseText);
		for (i = 0; i < userData.length;i++)
		{
			var op = new Option();
			op.value = userData[i]["UserId"];
			op.text = userData[i]["UserName"];
			selectElement.options.add(op);
		}
		selectElement.onchange = updateTable;
		usersDropdown.appendChild(selectElement);
		document.getElementById('userSelect').appendChild(usersDropdown);
		
		// now load pieces dropdown
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
				selectElement2.onchange = updateTable;
				
				piecesDropdown.appendChild(selectElement2);
				document.getElementById('piecesDropdown').appendChild(piecesDropdown);
				
			}
		}
		xmlhttpPieces.open("GET","view.php?action=getPiecesList");
		xmlhttpPieces.send();
	}
}
xmlhttpUsers.open("GET","view.php?action=getOtherUsersInfo");
xmlhttpUsers.send();



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
 <p>Welcome to your Music Project Comparison</p>
 <p>choose user and pieces from the drop down menus:</p>

 
 Select piece: <div id="piecesDropdown" ></div>
 Select Other User: <div id="userSelect" ></div>
<p>

<table>
<tr>
	<th> my playbacks </th> <th> other user's playbacks </th>
</tr>
<tr>
	<td> <div id="myPlaybacks" ></div> </td> <td> <div id="otherPlaybacks" ></div> </td>
</tr>
<tr>
	<td> <div id="myGraph" ></div> </td> <td> <div id="otherGraph" ></div> </td>
</tr>
</table>
</p>
 
 
 <p><a href="index.php">Back to index</a></p>
 
 </body>
</html>