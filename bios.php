<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("America/Chicago");

// header('Content-Type: application/json');

// real timestamp
$GameDateTimeObject = $realDateTime = new DateTime('now');

// Elite Dangerous time
$GameDateTimeObject->add(new DateInterval('P1286Y'));

$report = [];

$db = new SQLite3('./databases/explodata.db');
if (!$db) {
    die("Error opening database: " . $db->lastErrorMsg());
}
        

$query = "SELECT s.id, s.name, p.name bodyName, p.type bodyType, pf.genus, pf.species, pf.color
          FROM systems s 
          JOIN planets p on p.system_id=s.id 
          JOIN planet_flora pf on pf.planet_id=p.id
          WHERE p.bio_signals > 0 ORDER by s.id DESC";

$results = $db->query($query);

if (!$results) {
        die("Error executing query: " . $db->lastErrorMsg());
}

// $tmpName = $regionName ='';

 while ($row = $results->fetchArray( SQLITE3_ASSOC )) 
    {

        $name           = $row['name']; // get the system name
        
        // if name does not equal tempName, get RegionName, else use tempName
        // if($name != $tmpName)
        // {
        //     // echo "fetching Region Name...<br/>";
        //     // $regionData = getRegionName($name);
        //     // $row['region'] = $regionData->region;
        //     // $regionName = $regionData->region;
        // }else{
            
        //     // echo "Using $regionName because $name equals $tmpName<br/>";

        //     $row['region']  = $regionName;
        // }
        
        // $tmpName = $row['name'];
        // echo  "<pre>", print_r( $regionData ), "</pre>";
        // echo  "<pre>", print_r( $row ), "</pre>";
        
        array_push($report, $row); 

    }

    $reportObj = new stdClass();
    $reportObj->EDTime = $GameDateTimeObject->format('Y-m-d H:i:s');
    $reportObj->EarthTime = $realDateTime->format('Y-m-d H:i:s');
    $reportObj->count = count($report);
    $reportObj->bodies = (object) $report;

    echo "<pre>", print_r($reportObj), "</pre>";

    



    // Get a region for a given system name from spansh api

    function getRegionName( $systemName, $arr = array() )
    {
        $url        = sprintf("https://spansh.co.uk/api/search?q=%s" , urlencode( $systemName) );
        $contents   = file_get_contents($url);
        $data       = json_decode( $contents, false );

        // check if Spansh knows this system, because undiscovered systems are empty results.
        if(!$data->results) return;

        // iterate through results gather type->system arrays
        foreach( $data->results as $rec ){
            if( $rec->type == 'system'){
                array_push($arr, array("id64" => $rec->record->id64, "name" => $rec->record->name, "region" => $rec->record->region ));
            }
        }
        return (object) $arr[0];
    }