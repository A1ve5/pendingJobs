<!DOCTYPE htmlnnerHTML = JSON.stringify(columnChartData, null, 4);>
<html>
<head>
	<title>FGI - Number of PENDING jobs per cluster</title>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
			<script type="text/javascript">

			var cluster = getUrlVars()["cluster"];
			var min = getUrlVars()["min"];
			var max = getUrlVars()["max"];
			// Load the Visualization API and the piechart package.
			google.load('visualization', '1', {'packages':['corechart']});

			// Set a callback to run when the Google Visualization API is loaded.
			google.setOnLoadCallback(drawChart);

			function drawChart() {
				var json = $.ajax({
					url: 'get_json_pdj.php?cluster=' + cluster + '&min=' + min + '&max=' + max, // make this url point to the data file
					dataType: 'json',
					async: false
				}).responseText;
				
				// Create our data table out of JSON data loaded from server.
				var data = new google.visualization.DataTable(json);
				var options = {
					//title: 'Elapsed time on PENDING status per Job',
					title: cluster,
					is3D: true,
					width: 800,
					height: 600,
					//colors: ['#e0440e', '#e6693e', '#ec8f6e', '#f3b49f', '#f6c7b6'],
					colors: ['#152E8F'],
					hAxis: {title: 'Job ID',  titleTextStyle: {color: 'black'}},
					vAxis: {title: 'Time on PENDING state',  titleTextStyle: {color: 'black'}, logScale: false}
				};
				// Instantiate and draw our chart, passing in some options.
				//do not forget to check ur div ID
				//var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
				var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
				chart.draw(data, options);

//				document.getElementById('chart_div').innerHTML = JSON.stringify(data, null, 4);

//				setInterval(drawChart, 180000 );
			}

			function getUrlVars() {
				var vars = {};
				var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
					vars[key] = value;
				});
				return vars;
			}

		</script>  

</head>

<body>
	<a href="https://confluence.csc.fi/display/fgi/FGI+User+Pages"><img src="FGI_vaaka_kehys_RGB_72dpi.jpg" alt="FGI logo"></a>
	<p>
	<small>
	Change lower and upper thresholds by editing the "min" and "max" value in the URL and then press enter.
	</small>
	</p>
	<a href=index.php>Back</a><br>
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
	<a href=index.php>Back</a>
</body>
</html>
