<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_STRICT);
date_default_timezone_set("America/Chicago");


    $db = new SQLite3('./databases/explodata.db');
    if (!$db) {
        die("Error opening database: " . $db->lastErrorMsg());
    }
        
    $results = $db->query(
        "select 
        s.name System, 
        st.name starName, 
        st.type starType,
        st.subclass starClass,
        st.luminosity starLuminosity,
        p.id planetId,
        p.name body, 
        p.type BodyType, 
        pg.gas_name Atmosphere, 
        pg.percent level
  FROM systems s
  JOIN stars st on st.system_id=s.id
  JOIN planets p on p.system_id=s.id 
  JOIN planet_gasses pg on pg.planet_id=p.id 
  WHERE p.type like '%giant%' AND (pg.gas_name in ('Helium')) AND (pg.percent > 30)"
);

    if (!$results) {
        die("Error executing query: " . $db->lastErrorMsg());
    }


/**
 * Array Output
Array
(
    [System] => Cyoidai MS-S d4-3
    [body] => B 1
    [starName] => A
    [starType] => F
    [starClass] => 5
    [starLuminosity] => VI
    [honked] => 1
    [fully_scanned] => 1
    [fully_mapped] => 0
    [commander] => SCOTT FLEMING
    [body_id] => 23
    [BodyType] => Sudarsky class I gas giant
    [Atmosphere] => Helium
    [level] => 30.394852
)
 */

    $output = [];

    // build our big array of shit
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

        $row['level'] = round($row['level'], 2);
        $row['rings'] = fetchRings($row['planetId']);

        unset($row['planetId']);
        array_push($output, $row);
    }


    function fetchRings( $planet_id, $line="",$rings=0 )
    {
        $db = new SQLite3('./databases/explodata.db');
        $results = $db->query(
            "select name, type from planet_rings where planet_id=$planet_id"
        );
         if (!$results) {
               die("Error executing query: " . $db->lastErrorMsg());
        }

        while($row = $results->fetchArray(SQLITE3_ASSOC))
        {
            $line .= $row['name'] . ":" . str_replace('eRingClass_', '', $row['type']) . " ";
        }

        return $line;
    }


    // Free the result set
    $results->finalize();

    // lets send it
    array_to_csv_download($output, 'helium-rich-system-report.csv');
    // echo "<pre>", print_r( $output ), "</pre>";



/**
 * array_to_csv_download
* 
*/
  function array_to_csv_download($array, $filename = "export.csv", $delimiter = ",")
  {
    header('Content-Type: application/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '";');


    // clean output buffer
    ob_end_clean();

    $handle = fopen('php://output', 'w');


    // Write utf-8 bom to the file
    fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // use keys as column titles
    fputcsv($handle, array_keys($array['0']), $delimiter);

    foreach ($array as $value) {
      fputcsv($handle, $value, $delimiter);
    }

    fclose($handle);

    // flush buffer
    // ob_flush();

    // use exit to get rid of unexpected output afterward
    exit();
  } 
    ?>


    ?>