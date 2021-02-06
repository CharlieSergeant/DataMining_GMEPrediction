<?php
include('dBConnection.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM GMEData";
$result = $conn->query($sql);

echo "The data: \n";
echo '<table border="1">
      <tr>
          <td> <font face="Arial">Date</font> </td>
          <td> <font face="Arial">Open</font> </td>
          <td> <font face="Arial">Close</font> </td>
          <td> <font face="Arial">High</font> </td>
          <td> <font face="Arial">Low</font> </td>
          <td> <font face="Arial">Volume</font> </td>
          <td> <font face="Arial">Change</font> </td>
          <td> <font face="Arial">Change Over Time</font> </td>
      </tr>';

$dateArr = array();
$openArr = array();
$closeArr = array();
$highArr = array();
$lowArr = array();
$volumeArr = array();
$changeArr = array();
$changeOverTimeArr = array();

if ($result->num_rows > 0) {
    // output data of each row
    $i = 0;
    while($row = $result->fetch_assoc()) {
      $date = $row["myDate"];
      $open = $row["open"];
      $close = $row["close"];
      $high = $row["high"];
      $low = $row["low"];
      $volume = $row["volume"];
      $change = $row["myChange"];
      $changeOverTime = $row["changeOverTime"];

      $dateArr[$i] = $date;
      $openArr[$i] = $open;
      $closeArr[$i] = $close;
      $highArr[$i] = $high;
      $lowArr[$i] = $low;
      $volumeArr[$i] = $volume;
      $changeArr[$i] = $change;
      $changeOverTimeArr[$i] = $changeOverTime;
      echo '<tr>
        <td>'.$date.'</td>
        <td>'.$open.'</td>
        <td>'.$close.'</td>
        <td>'.$high.'</td>
        <td>'.$low.'</td>
        <td>'.$volume.'</td>
        <td>'.$change.'</td>
        <td>'.$changeOverTime.'</td>
    </tr>';
    $i++;
    }
    echo "</table>";
} else {
    echo "0 results";
}

$dataPoints = array();
for ($i = 0;$i<count($dateArr);$i++){
  $dataPoints[$i] = array("x" =>$openArr[$i],"y"=>$volumeArr[$i]);
}



function Corr($x, $y){
    $length= count($x);
    $mean1=array_sum($x) / $length;
    $mean2=array_sum($y) / $length;

    $a=0;
    $b=0;
    $axb=0;
    $a2=0;
    $b2=0;

    for($i=0;$i<$length;$i++)
    {
    $a=$x[$i]-$mean1;
    $b=$y[$i]-$mean2;
    $axb=$axb+($a*$b);
    $a2=$a2+ pow($a,2);
    $b2=$b2+ pow($b,2);
    }

    $corr= $axb / sqrt($a2*$b2);

    return $corr;
}
?>

</div>
</html>
<html>
<head>
<script>
window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "light1",
	title:{
		text: "Date vs Volume of Game Stop"
	},
	axisX:{
		title: "Open",
		suffix: " open"
	},
	axisY:{
		title: "Volume",
		suffix: " vol"
	},
	data: [{
		type: "scatter",
		markerType: "square",
		markerSize: 10,
		toolTipContent: "Volume: {y} num<br>Open: {x} price",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();

}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>
