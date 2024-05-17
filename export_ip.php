<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();
$select = array('id', 'ip', 'name','device_type', 'department_name_','department_no','city', 'area', 'created_at', 'updated_at');


$chunk_size = 100;
$offset = 0;

$data = $db->withTotalCount()->get('ip');
$total_count = $db->totalCount;

$handle = fopen('php://memory', 'w');

fputcsv($handle,$select);

$filename = 'export_ip_'.time().'.csv';


$num_queries = ($total_count/$chunk_size) + 1;

//Prevent memory leak for large number of rows by using limit and offset :
for ($i=0; $i<$num_queries; $i++){

    $rows = $db->get('ip',Array($offset,$chunk_size), $select);
    $offset = $offset + $chunk_size;
  
    foreach ($rows as $row) {
        
        if($row["device_type"] ==0)$row["device_type"] ="Router";
        elseif($row["device_type"] ==1)$row["device_type"] ="Switch";
        elseif($row["device_type"] ==2)$row["device_type"] ="Pc";

        fputcsv($handle,array_values($row));
    }
}

// reset the file pointer to the start of the file
fseek($handle, 0);
// tell the browser it's going to be a csv file
header('Content-Type: application/csv');
// Save instead of displaying csv string
header('Content-Disposition: attachment; filename="'.$filename.'";');
//Send the generated csv lines directly to browser
fpassthru($handle);

