<!DOCTYPE html>
<html>
<head>
	<title>FGI - Number of PENDING jobs per cluster</title>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
			// Load the Visualization API and the piechart package.
			google.load('visualization', '1', {'packages':['corechart']});

			// Set a callback to run when the Google Visualization API is loaded.
			google.setOnLoadCallback(drawChart);

			function drawChart() {
				var json = $.ajax({
					url: 'get_json.php', // make this url point to the data file
					dataType: 'json',
					async: false
				}).responseText;
				
				// Create our data table out of JSON data loaded from server.
				var data = new google.visualization.DataTable(json);
				var options = {
					title: 'Number of jobs per cluster/status',
					is3D: 'true',
					isStacked: 'true',
					width: 800,
					height: 600
				};
				// Instantiate and draw our chart, passing in some options.
				//do not forget to check ur div ID
				//var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
				var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
				chart.draw(data, options);

//				setInterval(drawChart, 180000 );
			}

		</script>  

</head>

<body>
	<a href="https://confluence.csc.fi/display/fgi/FGI+User+Pages"><img src="FGI_vaaka_kehys_RGB_72dpi.jpg" alt="FGI logo"></a>
	<small>
	<p>Pass your cursor over the bars to see the exact number of pending jobs on the queues.</p>
	<p>Click on the links bellow to get more information on each cluster.</p>
	</small>
        <a href=pendingjobs.php?cluster=aesyle-grid.fgi.csc.fi&min=0&max=1000>Aesyle</a>
        <a href=pendingjobs.php?cluster=alcyone-grid.grid.helsinki.fi&min=0&max=1000>Alcyone</a>
        <a href=pendingjobs.php?cluster=asterope-grid.abo.fi&min=0&max=1000>Asterope</a>
        <a href=pendingjobs.php?cluster=celaeno-grid.lut.fi&min=0&max=1000>Celaeno</a>
        <a href=pendingjobs.php?cluster=electra-grid.chem.jyu.fi&min=0&max=1000>Electra</a>
        <a href=pendingjobs.php?cluster=maia-grid.local&min=0&max=1000>Maia</a>
        <a href=pendingjobs.php?cluster=merope-grid.cc.tut.fi&min=0&max=1000>Merope</a>
        <a href=pendingjobs.php?cluster=pleione-grid.utu.fi&min=0&max=1000>Pleione</a>
        <a href=pendingjobs.php?cluster=taygeta-grid.oulu.fi&min=0&max=1000>Taygeta</a>
        <a href=pendingjobs.php?cluster=grid.triton.aalto.fi&min=0&max=1000>Triton</a>
        <a href=pendingjobs.php?cluster=usva.csc.fi&min=0&max=1000>Usva</a>
	<div id="chart_div" style="width: 800px; height: 600px;"></div>
	<br><b><a href=dtlist.php>FGI Scheduled Downtimes</a></b>
</body>
</html>
