<?php
// Processing queries from javascript

// Is the method specified in the request?
    if (isset ($_GET['method']) ) 
        switch ( $_GET['method'] )
        {
        //Switching available methods
            case "API":
                {
                require_once (__DIR__ . "/API/main-API.php");
                break;
                }
            case "FILE": 
                {
                require_once (__DIR__ . "/FILE/main-file.php");
                break;
                };
            case "RAND":
                {
                require_once (__DIR__ . "/RAND/main-rand.php");
                break;
                }
        default: {
            // an unavailable method has been requested
            "ERR: Incorrect request"; 
            }
        }
    else
    // The request method was not specified
        echo "ERR: Specify your request";
?>