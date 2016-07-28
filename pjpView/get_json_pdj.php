<?php
/* $server = the IP address or network name of the server
 * $userName = the user to log into the database with
 * $password = the database account password
 * $databaseName = the name of the database to pull data from
 * table structure - colum1 is cas: has text/description - column2 is data has the value
 */
$con = mysql_connect('HOSTNAME', 'USER', 'PASSWORD') or die('Error connecting to server');
 
mysql_select_db('test', $con); 

// write your SQL query here (you may use parameters from $_GET or $_POST if you need them)
//$query = mysql_query('SELECT jobid,submitTime FROM pendingTime WHERE clusterName="GRID_FRONTEND_HOSTNAME"');

$query = mysql_query('SELECT jobid,submitTime FROM pendingTime WHERE clusterName="' . $_GET["cluster"] . '" ORDER BY jobid DESC');

$table = array();
$table['cols'] = array(
	/* define your DataTable columns here
	 * each column gets its own array
	 * syntax of the arrays is:
	 * label => column label
	 * type => data type of column (string, number, date, datetime, boolean)
	 */
	array('label' => 'Job ID', 'type' => 'string'),
	array('label' => 'Time (Hours)', 'type' => 'number')
);

$currentv = date("Y-m-d H:i:s");
$now = strtotime($currentv);
$min = $_GET["min"];
$max = $_GET["max"];
$rows = array();
while($r = mysql_fetch_assoc($query)) {
	$temp = array();
// each column needs to have data inserted via the $temp array
// typecast all numbers to the appropriate type (int or float) as needed - otherwise they are input as strings
	$hours = ($now - strtotime($r['submitTime']))/3600;
	if (( $hours > $min ) && ( $hours < $max )) { 	
		$temp[] = array('v' => $r['jobid']);
		$temp[] = array('v' => $hours); //time in Hours
		$rows[] = array('c' => $temp); // insert the temp array into $rows
	}
}

// populate the table with rows of data
$table['rows'] = $rows;

// encode the table as JSON
$jsonTable = json_encode($table);

// set up header; first two prevent IE from caching queries
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

// return the JSON data
echo $jsonTable;
?>
