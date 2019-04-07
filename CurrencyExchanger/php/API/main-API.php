<?php
    require_once (__DIR__ . "/class-API.php");
    
    // new API class object
    $pbAPI = new API();
    
    // Check the tmp directory existence and the presence of a JSON file inside of it
    // If the tmp dir does not exist, it is created
    $pbAPI->tmpDirCheck();
    
    // Getting result of the tmp directory check
    $tmpStatus = $pbAPI->getTMPStatus();
    
    // Check if the result is not null
    if (!( $tmpStatus === null))
    {
        // Is a JSON file present in the tmp directory?
        if ($tmpStatus)
            {
            // JSON file is present
            $pbAPI->tmpJSONRead();
            
            // Return the data as JSON
            echo $pbAPI->GetRates();
            }
        else
            {
            // Local tmp JSON file is not available
            
            // Make an API call
                $pbAPI->call();
            
            // Save the obtained JSON data in a file
                $pbAPI->tmpJSONWrite();
            
            // Return the data as JSON
                echo $pbAPI->GetRates();
            }
    }
?>
