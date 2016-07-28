<html>
<head>
<title>FGI - Scheduled Downtimes</title>
<style>
table, td, th
{
border:0px solid blue;
}
th
{
background-color:blue;
color:white;
}
</style>
</head>
<body>
<a href="https://confluence.csc.fi/display/fgi/FGI+User+Pages"><img src="FGI_vaaka_kehys_RGB_72dpi.jpg" alt="FGI logo"></a>
<h1>FGI Scheduled Downtimes</h1>
<p>Lists FGI's Scheduled Downtimes with "Startdate" one week ago and "Enddate" within 90 days. Update hourly.</p>
<?php
$con=mysqli_connect("HOSTNAME","pjp","PASSWORD","test");
// Check connection
if (mysqli_connect_errno()) {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_query($con,"SELECT * FROM downtimes");

echo "<table border='1'>
<tr>
<th>Startdate</th>
<th>Downtime-ID</th>
<th>Severity</th>
<th>Classification</th>
<th>Hostname</th>
<th>Enddate</th>
<th>Description</th>
</tr>";

while($row = mysqli_fetch_array($result)) {
  echo "<tr>";
  echo "<td>" . $row['Startdate'] . "</td>";
  echo "<td>" . $row['Downtime-ID'] . "</td>";
  echo "<td>" . $row['Severity'] . "</td>";
  echo "<td>" . $row['Classification'] . "</td>";
  echo "<td>" . $row['Hostname'] . "</td>";
  echo "<td>" . $row['Enddate'] . "</td>";
  echo "<td>" . $row['Description'] . "</td>";
  echo "</tr>";
}

echo "</table>";

mysqli_close($con);

?>

<br><a href=index.php>Back</a>
</body>
</html>
