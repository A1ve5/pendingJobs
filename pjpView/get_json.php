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
$query = mysql_query('SELECT * FROM pendingJobs ORDER BY clusterName ASC');

$table = array();
$table['cols'] = array(
	/* define your DataTable columns here
	 * each column gets its own array
	 * syntax of the arrays is:
	 * label => column label
	 * type => data type of column (string, number, date, datetime, boolean)
	 */
	array('label' => 'Grid Frontend', 'type' => 'string'),
	array('label' => 'Grid Jobs on PENDING State', 'type' => 'number'),
	array('label' => 'Grid Jobs on RUNNING State', 'type' => 'number')
);

$rows = array();
while($r = mysql_fetch_assoc($query)) {
	$temp = array();
// each column needs to have data inserted via the $temp array
// typecast all numbers to the appropriate type (int or float) as needed - otherwise they are input as strings
	$temp[] = array('v' => $r['clusterName']);
	$temp[] = array('v' => (int) $r['nrPDjobs']);
	$temp[] = array('v' => (int) $r['totalRunJobs']);
// insert the temp array into $rows
	$rows[] = array('c' => $temp);
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
