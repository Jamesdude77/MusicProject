<html>
<head>
  <title>Music Project Google Chart test Review Page</title>
    <!--Load the AJAX API-->
	<script type="text/javascript" src="swfobject/swfobject.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
	
	
	var ytplayer;

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('number', 'time');
		data.addColumn({type: 'string', role: 'annotation'});
        data.addColumn('number', 'Events');
		
		// get data
		var jsonData = $.ajax({
          url: "view.php?action=getPlaybackStats&playbackId=120",
          dataType:"json",
          async: false
          }).responseText;
		  
		jsonData = JSON.parse(jsonData);
		  
		var duration = 136;
		// for each row add data row
		for (x = 0; x < duration; x+=5)
		{
			var label = x;
			var eventData = 0;
			for  (y = 0; y < jsonData.length; y++)
			{
				var test = jsonData[y];
				if (jsonData[y] < x+2.5 && jsonData[y] > x-2.5)
				{
					eventData += 1;
				}
				else if (jsonData[y] > x+2.5)
					break;
			}
			
			data.addRow([label, null, eventData]);
		}
		data.addRow([null,null,null]);

        // Set chart options
        var options = {annotation: {
							1: {
								// set the style of the domain column annotations to "line"
								style: 'line'
							}
						},
                       'width':425,
                       'height':356};
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
		
		setInterval(function(){drawAnnotation(chart, data, options)}, 200);
      }
	  
	function drawAnnotation(chart, data, options) {
		var cli = chart.getChartLayoutInterface();
                // set the x-axis value of the annotation
				var annotationRowIndex = data.getNumberOfRows() - 1;
                data.setValue(annotationRowIndex, 0, ytplayer.getCurrentTime());
				data.setValue(annotationRowIndex, 1, "t");
		chart.draw(data, options);
	}
	
    var params = { allowScriptAccess: "always" };
    var atts = { id: "myytplayer" };
    swfobject.embedSWF("http://www.youtube.com/v/41AJ5sLKgPo?enablejsapi=1&playerapiid=ytplayer&version=3",
                       "ytapiplayer", "425", "356", "8", null, null, params, atts);

	function onYouTubePlayerReady(playerId) {
      ytplayer = document.getElementById("myytplayer");
    }
	
    </script>
  
  </head>
  
  <body>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
	
	  <div id="ytapiplayer">
    You need Flash player 8+ and JavaScript enabled to view this video.
	</div>
  </body>

</html>