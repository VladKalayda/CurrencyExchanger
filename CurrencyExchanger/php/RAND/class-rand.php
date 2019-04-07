<?php

class RAND
{
    const constRates = array('USD'=>27.00, 'EUR'=>30.50);
    var $randRates;
    
    function __construct ()
    {
        $randRates = array();  
    }
    
    function getRands()
    {
        return (empty($this->randRates)) ? false : $this->randRates;
    }
    
    function generateRands()
    {
        foreach ( self::constRates as $key => $value ) 
        {
            $this->randRates[$key] =  $value + $this->randomFloat (-2.50 , 2.50 ) ;
        }
    }
    
    private function randomFloat($min = 0, $max = 1) 
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
?>