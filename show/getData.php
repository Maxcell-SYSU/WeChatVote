<?

include_once "../../database.php";

$result = mysql_query("SELECT * FROM vote ORDER BY id");
$num = 0;
echo "{\"data\":[";
$first = true;
while ($row = mysql_fetch_array($result)) {
	if($first)
		$first = false;
	else
		echo ",";
	echo "{\"id\":\"".$row['id']."\",\"team\":\"".$row['team']."\",\"vote\":\"".$row['vote']."\"}";
	$num++;
}
echo "],\"num\":\"$num\"}";
