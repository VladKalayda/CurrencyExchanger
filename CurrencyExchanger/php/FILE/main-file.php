<?php
require_once (__DIR__ . "/class-file.php");

    if (isset ($_FILES))
    {
        if (! empty ( $_FILES ['file']['tmp_name'] ))
            {
                $fileObj = new File( $_FILES ['file']['tmp_name'] );
                
                $fileObj->tmpJSONRead();

                if ($fileObj->tmpJSONRead() === FALSE )
                    echo "error";
                else 
                    echo json_encode ( $fileObj->JSON) ;
            }
    }
    else
        {
        // The file was not sent in the request
        echo "error";
        }
?>                