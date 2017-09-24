<!DOCTYPE html>
<html>
<head>
	<title>CarParkr - See live data</title>
	<link rel="stylesheet" type="text/css" href="reset.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<header>
	<img src="logo.png">
</header>
<main>
<?php
include('class_request/class_request.php');
include("garageNames.php");    
$request = new Request();

$data = json_decode($request->getFile("http://www.odaa.dk/api/action/datastore_search?resource_id=2a82a145-0195-4081-a13c-b0e587e9b89c"));
    
// create new array to store the combined data
$dataList = array();
    
// loop through all of the result records from the live data
foreach ($data->result->records as $record) {

    $name = "";
    foreach ($garageNames as $garage) {
        $garageName = $garage["garageName"];
        $garageCode = $garage["garageCode"]; 
        
        if($record->garageCode === $garageCode){
            $name = $garageName;
                $date = date_create($record->date);
    $timestamp = date_format($date,"Y-m-d H:i:s");
    $record->date = $timestamp;
    
    $dataList[] = array("name"=>$name, "data"=>$record);
        }        
}
}
    
    
    foreach ($dataList as $record) {
        $occupancy = $record["data"]->vehicleCount / $record["data"]->totalSpaces * 100;
        $highMedLow = "";
        if($occupancy > 100){
           $occupancy = 100;
            $highMedLow = "full";
        }
        else if($occupancy > 75 && $occupancy <= 100){
            $highMedLow = "high";  
        }else if($occupancy >= 50 && $occupancy <= 75){
            $highMedLow = "med";
        }
        else {
            $highMedLow = "low";
        }
?>
    	<article class="card">
		<h2 class="title"><?php echo $record["name"]; ?></h2>
		<div class="bar">
			<div class="indicator <?php echo $highMedLow;?>" style="width:<?php echo $occupancy;?>%"></div>
		</div>
		<dl class="stats">
		    <dt><?php echo $record["data"]->vehicleCount; ?></dt>
		    <dd>Occupied</dd>
	    </dl>
		<dl class="stats">
		    <dt><?php echo $record["data"]->totalSpaces; ?></dt>
		    <dd>Capacity</dd>
	    </dl>
		<dl class="stats">
		    <dt><?php echo $record["data"]->totalSpaces - $record["data"]->vehicleCount; ?></dt>
		    <dd>Free</dd>
	    </dl>
	</article>    
<?php
    }
?>  
    
	

</main>
</html>