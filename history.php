<html>
 <head>
  <title>Music Project History</title>
 </head>
 <body>
 <p>Welcome to your Music Project History</p>
 <p>Please choose a piece from the drop down:</p>
 <script src="Chart.min.js"></script>
 <script>

  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("piecesTable").innerHTML=xmlhttp.responseText;
	  addRowHandlers();
    }
  }
  xmlhttp.open("GET","view.php?action=getPiecesHistory");
  xmlhttp.send();
  
  function addRowHandlers() {
    var table = document.getElementById("tableId");
    var rows = table.getElementsByTagName("tr");
    for (i = 0; i < rows.length; i++) {
        var currentRow = table.rows[i];
        var createClickHandler = 
            function(row) 
            {
                return function() { 
					var cell = row.getElementsByTagName("td")[0];
					var pieceId = cell.innerHTML;
					var thisRowIndex = this.rowIndex+1
					var newRow = table.insertRow(this.rowIndex+1);
					//var cell1 = newRow.insertCell(0);
					//cell1.innerHTML = "Hello there";
					
					xmlhttp.onreadystatechange=function() {
						if (xmlhttp.readyState==4 && xmlhttp.status==200) {
							var playbackData = JSON.parse(xmlhttp.responseText);
							for (i = 0; i < playbackData.length;i++)
							{
								var newRow = table.insertRow(thisRowIndex);
								var creationDate = newRow.insertCell(0);
								var eventCount = newRow.insertCell(1);
								var playbackId = newRow.insertCell(2);
								
								creationDate.innerHTML = playbackData[i]["creationDate"];
								eventCount.innerHTML = playbackData[i]["EventCount"];
								playbackId.innerHTML = playbackData[i]["PlaybackId"];
								
								var createClickHandler = 
								function(row) 
								{
									var playbackId2 = playbackId.innerHTML;
									
									return function() {
										xmlhttp.onreadystatechange=function() {
										if (xmlhttp.readyState==4 && xmlhttp.status==200) {
											var pieceLength = xmlhttp.responseText;
											
											xmlhttp2=new XMLHttpRequest();
											xmlhttp2.onreadystatechange=function() {
												if (xmlhttp2.readyState==4 && xmlhttp2.status==200) {
												
													var newRow = table.insertRow(thisRowIndex);
													var graph = newRow.insertCell(0);
													graph.innerHTML = '<canvas id="myChart'+playbackId2+'" width="400" height="400"></canvas>';
													drawGraph(JSON.parse(xmlhttp2.responseText), JSON.parse(pieceLength)[0], playbackId2);
													
												}
											}	
											xmlhttp2.open("GET","view.php?action=getPlaybackStats&playbackId="+playbackId2);
											xmlhttp2.send();
											}
										}
										xmlhttp.open("GET","view.php?action=getPieceInfo&pieceId="+pieceId);
										xmlhttp.send();
									};
								};
								newRow.onclick = createClickHandler(newRow);
							}
						}
					  }
					  xmlhttp.open("GET","view.php?action=getPlaybackTable&pieceId="+pieceId);
					  xmlhttp.send();
				};
			};
        currentRow.onclick = createClickHandler(currentRow);
    }
}

function drawGraph(rawData, duration, playbackId) {

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
		
		var ctx = document.getElementById("myChart"+playbackId).getContext("2d");
		var myNewChart = new Chart(ctx).Line(graphData, {
												bezierCurve: false
											});
	}

</script>
 
 <div id="piecesTable"><b>Playback info will be listed here.</b></div>
 
 <p><a href="index.php">Back to index</a></p>
 
 </body>
</html>