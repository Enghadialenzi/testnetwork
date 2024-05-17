<?php

function ping($host)
{
    // Execute the ping command and capture the output
    exec(sprintf('ping -c 1 -W 1 %s', escapeshellarg($host)), $output, $result);

    // If the ping was successful
    if ($result === 0) {
        // Extract the ping time from the output
        if (preg_match('/time=([0-9.]+) ms/', implode("\n", $output), $matches)) {
            return $matches[1];
        }
    }

    // If ping failed or no ping time found
    return false;
}

if(isset($_GET["ip"])){
    $host =  $_GET["ip"];
    $pingTime = ping($host);
    
    if ($pingTime !== false) {
        echo "$pingTime ms";
    } else {
        echo "Unreachable";
    }
}

?>