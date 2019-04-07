<?php

require_once (__DIR__ . "/../API/class-API.php");

class File extends API
{
    function __construct ($tmpFilePath)
    {
        parent::__construct();
        $this->tmpFileFlag = 1;
        $this->tmpFilePath = $tmpFilePath;
    }
    
    function tmpJSONRead()
    {
       parent::tmpJSONRead();
       if (empty ($this->JSONDecoded) )
            return FALSE;
        return TRUE;
    }
    
    // Filter the currency according to the defined set
    function getRates()
	{
	    
		if ( ! (empty ( $this->JSONDecoded ) ) ) 
		{
	        $CUR_RESULT = array ();
		    
		    foreach ( $this->JSONDecoded as $key -> $value )
			{
				foreach ($this->CUR_TYPES as $TYPE)
				{
					if ( strcmp ( $TYPE ,  $key ) == 0 )
					{
						$CUR_RESULT[$key] = $value ; 
						
					}
				}
			
			}
		}
		$CUR_RESULT = json_encode ($CUR_RESULT);
		return (empty ($CUR_RESULT)) ? false : $CUR_RESULT ;
	
	}
    
}


?>