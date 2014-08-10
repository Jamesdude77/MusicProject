<html>
 <head>
  <title>Music Project History</title>
 </head>
 <body>
 <p>Welcome to your Music Project History</p>
 <p>Please click rows to expand:</p>
 <style>
 .hidden
{
display:none;
} 
 </style>
 <script src="Chart.min.js"></script>
 <script>

xmlhttpPieces=new XMLHttpRequest();
xmlhttpPieces.onreadystatechange=function() {
	if (xmlhttpPieces.readyState==4 && xmlhttpPieces.status==200) {
		
		// create table
		var piecesTable = document.createElement('table');
		piecesTable.setAttribute("border", "1");
		// create column headers_list
		var newRow = piecesTable.insertRow(0);
		newRow.insertCell(0).innerHTML = "PieceName";
		newRow.insertCell(1).innerHTML = "PlaybackCount";
		newRow.insertCell(2).innerHTML = "LatestPlayback";
		
		// fill table
		piecesData = JSON.parse(xmlhttpPieces.responseText);
		for (i = 0; i < piecesData.length;i++)
		{
			var newRow = piecesTable.insertRow(i+1);
			newRow.insertCell(0).innerHTML = piecesData[i]["PieceName"];
			newRow.insertCell(1).innerHTML = piecesData[i]["PlaybackCount"];
			newRow.insertCell(2).innerHTML = piecesData[i]["LatestPlayback"];
			
			var pieceId = piecesData[i]["PieceId"];
			var pieceLength = piecesData[i]["PieceLength"];
			
			// add onclick function
			var createClickHandler = 
            function(piecesTableRow, pieceId, pieceLength) 
            {
                return function() {
					var playbackTableDiv = document.getElementById("playbackTable"+pieceId);
					if (playbackTableDiv == null)
					{
						// create playback rows
						var newRow = piecesTableRow.parentNode.insertRow(piecesTableRow.rowIndex+1);
						
						var newDiv = document.createElement("div");
						newDiv.setAttribute("id", "playbackTable"+pieceId);
						newRow.appendChild(newDiv);
						
						// get playback data
						xmlhttpPlaybacks=new XMLHttpRequest(); 
						xmlhttpPlaybacks.onreadystatechange=function() {
							if (xmlhttpPlaybacks.readyState==4 && xmlhttpPlaybacks.status==200) {
								// create table
								var playbacksTable = document.createElement('table');
								playbacksTable.setAttribute("border", "1");
								// fill table
								playbacksData = JSON.parse(xmlhttpPlaybacks.responseText);
								for (x = 0; x < playbacksData.length;x++)
								{
									var playbackRow = playbacksTable.insertRow(x);//thisRowIndex+1);
									playbackRow.insertCell(0).innerHTML = playbacksData[x]["creationDate"];
									playbackRow.insertCell(1).innerHTML = "Events: " + playbacksData[x]["EventCount"];
									
									var playbackId = playbacksData[x]["PlaybackId"];
									
									// create onclick for graph
									var createClickHandler = 
									function(playbacksTableRow, pieceLength, playbackId) 
									{
										return function() {
											// check for div
											var chartDiv = document.getElementById("chart"+playbackId);
											if (chartDiv == null)
											{
												// create playback rows
												var graphRow = playbacksTableRow.parentNode.insertRow(playbacksTableRow.rowIndex+1);
												
												var newDiv = document.createElement("div");
												newDiv.innerHTML = '<canvas id="myChart'+playbackId+'" width="1000" height="400"></canvas>';
												newDiv.setAttribute("id", "chart"+playbackId);
												graphRow.appendChild(newDiv);
												
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
											else
											{
												chartDiv.parentNode.removeChild(chartDiv);
											}
										};
									};
									playbackRow.onclick = createClickHandler(playbackRow, pieceLength, playbackId);
									
								}
								newDiv.appendChild(playbacksTable);
							}
						}
						xmlhttpPlaybacks.open("GET","view.php?action=getPlaybackList&pieceId="+pieceId);
						xmlhttpPlaybacks.send();
					}
					else
					{
						playbackTableDiv.parentNode.removeChild(playbackTableDiv);
					}
				};
			};
			newRow.onclick = createClickHandler(newRow, pieceId, pieceLength);
		}
		document.getElementById("piecesTable").appendChild(piecesTable);
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
 
 <div id="piecesTable"></div>
 
 <p><a href="index.php">Back to index</a></p>
 
 </body>
</html>