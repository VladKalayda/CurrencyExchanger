<?php
    require_once (__DIR__ . "/class-rand.php");
    
    $randObj = new RAND();
    
    $randObj->generateRands();
    
    echo json_encode ($randObj->getRands());
    
?>