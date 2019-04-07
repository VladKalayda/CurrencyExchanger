<?php

    // Custom descending sorting function for dates
    function sortFunction( $a, $b ) 
    {  
        return strtotime($b) - strtotime($a);
    }


class API 
{
    // Types file path used to determine which currency types to use
	const CUR_TYPES_FILE = __DIR__ . "/../../types.txt";
	
	// Path to the tmp directory
	const TMP_DIR = __DIR__ . "/../../tmp";
	
	// URL for API call processing
	const API_URL = "https://api.privatbank.ua/p24api/exchange_rates?json&date=";
	
	// Default currecy types used if the types file is not available
	const DEFAULT_TYPES = array ("USD" , "EUR"); 
	
	// A flag for indicating the presence of a temporary file
	var $tmpFileFlag;
	// Local JSON file path
	var $tmpFilePath; 
	
	// Full date, day, month and year separately
	var $currentDate;
	var $currentDay;
	var $currentMonth;
	var $currentYear;
	
	var $currencyTypes; // Current currency types
	
	
	var $JSON = null; // Raw JSON data from the tmp file or a call
	var $JSONDecoded = null; // Parsed JSON data
	
	
	function __construct ()
	{
		$this->JSON = null;
		$this->JSONDecoded = null;
		
		// Init current date in day.month.year format and day, month and year separately
		$this->currentDate = date ("d.m.Y");
		$this->currentDay = date ("d");
		$this->currentMonth = date ("m");
		$this->currentYear = date ("Y");
		
		$this->tmpFileFlag = 0;
		
		// Is the type.txt file present?
		if (file_exists (self::CUR_TYPES_FILE) )
		    // Yes, getting the currency types from it
		    $this->getTypesFile();
		else
		    // No, creating the file with the declared default types
		    $this->generateTypesFile();
		
	}
	
	// return currenct status of the tmp file
	function getTMPStatus()
	{
	    return $this->tmpFileFlag ;
	}
	
	// return the URL for the API call
	function getURL ()
	{
		return empty($this->currentDate) ? null : self::API_URL . $this->currentDate ;
	}
	
	
	//Generating the types file file
    private function generateTypesFile ()
    {
        // Are the path to the types file and default currency types defined?
		if ( ! empty (self::CUR_TYPES_FILE ) && ! empty (self::DEFAULT_TYPES ) )
		{	
		    // Yes, processing the types file generation
			try
			{
			    $i=0;
			    // Create handler for the types file
				$handler = fopen ( self::CUR_TYPES_FILE , "w+" );
				
				// Fill the types file file with the defined default types
				foreach (self::DEFAULT_TYPES as $TYPES)
				    {
				        if ($i == ( sizeof ( self::DEFAULT_TYPES) - 1 ))
				            $TMP = $TYPES ;
				        else
				            $TMP = $TYPES . PHP_EOL ; 
				            
				        fwrite ($handler , $TMP );
				        $i++;
				    }
			}
			catch (Exception $e)
			{
				$e->getMessage();
			}
			
			fclose ($handler);
			
			//set read+write permissions for the file
			chmod ( self::CUR_TYPES_FILE , 0600 );
			
			$this->currencyTypes= self::DEFAULT_TYPES ;
			
			// return true upon success
			return true;
		}
		// No, return false
		else return false;
    }
	
	// Get data from the types file
	private function getTypesFile ()
    {
		try 
		{
			$handler = fopen ( self::CUR_TYPES_FILE , "r");
		
		    while (!feof ($handler) )
            {
                $data = fgets ($handler);
                if ( $data !== "" && $data !== " " && $data !== NULL && $data !== PHP_EOL && is_string($data)  )
                    $this->currencyTypes[] = trim ($data);
            }
		}
		catch (Exception $e) 
		{
			$e->getMessage();
		}
        
        fclose ($handler);
        
        // return false if the obtained data is empty, otherwise return true
		return empty ( $this->currencyTypes) ? false : true ;
    }
	
	// Checking the presence of the tmp directory and a json file inside of it and creating the dir if it does not exist
	function tmpDirCheck ()
    {
        // Does the tmp directory exist?
        switch ( file_exists (self::TMP_DIR) )
        {
            case true:
            // tmp directory exists, check the presence of a json file there
        {
            if ( ! $TMP_LIST = @scandir (self::TMP_DIR))
                {
                    chmod (self::TMP_DIR , 0755);
                    $TMP_LIST = scandir (self::TMP_DIR);
                }
            $TMP_LIST = array_splice ($TMP_LIST , 2 , sizeof ($TMP_LIST) - 1 ); 
            
            /* 
            * If the file's mtime 'day' !== current 'day' keep $TMP = 0 to make an API call
            * else set $TMP = 1 and proceed with obtaining the data from the file
            * Hence an API call will be made is the file is >=1 day old 
            */
		   if (!(empty ($TMP_LIST)) )
            {
                
                usort ( $TMP_LIST  , "sortFunction" );
                
                $FileMYear = date( "Y" , filemtime( self::TMP_DIR ."/" . $TMP_LIST[0]) );
				$FileMMonth = date( "m" , filemtime( self::TMP_DIR ."/" . $TMP_LIST[0]) );
				$FileMDay = date( "d" , filemtime( self::TMP_DIR ."/" . $TMP_LIST[0]) );
				
				if ( ( $this->currentDay == $FileMDay ) && ( $this->currentMonth == $FileMMonth ) && ( $this->currentYear == $FileMYear ) )
                    {
                        $this->tmpFilePath = self::TMP_DIR . "/" . $TMP_LIST[0];
                        $this->tmpFileFlag = 1;
                    }
            }
            else
                $this->tmpFileFlag = 0;
			
			// tmp dir is present, return true
			return true;
			
			break;
        }
        case false: 
            // tmp directory does not exist, attempt to create it
            {
                try
				{
					mkdir (self::TMP_DIR);
				}
				catch (Exception $e)
				{
					$e->getMessage();
				}
                chmod (self::TMP_DIR , 0755);
				$this->tmpFileFlag = 0;
				
				// tmp dir was absent, created
				return false;
				
				break;
            }
        // Could not check the tmp directory or create it
        default: return "ERROR";
        }   
    }
    
    // Read JSON data from the local temporary JSON file
    function tmpJSONRead()
    {
        try 
        {
            $this->JSON = file_get_contents ($this->tmpFilePath) ;
            $this->JSONDecoded = json_decode ($this->JSON);
        }
        catch (Exception $e)
        {
            $e->getMessage();
        }
    }
	
	// Create and write JSON data from the call to the temporary file
	function tmpJSONWrite()
    {
        $this->tmpFilePath = self::TMP_DIR . "/" . $this->currentDate ;
        
        $handler = @ fopen ( $this->tmpFilePath , "w+" );
		if ( $handler && !( empty ( $this->tmpFilePath )))
        {
			fwrite ($handler , $this->JSON);
			fclose ($handler);
			
			chmod ($this->tmpFilePath , 0600 );
			
			return true;
		}
		else 
			return false;
    }
	
	
	// Process an API call, the reulting data will be saved in $JSON and $JSONDecoded
	function call ()
    {
        $l=0;
        
        // Obtain JSON with the exchange rates from the PB API
        if ($this->JSON = @file_get_contents ( self::API_URL . $this->currentDate ) )
		{	
			// Parsing JSON
			$this->JSONDecoded = json_decode ($this->JSON) ;
				
			// Checking the if the today's data has been generated yet
			// and getting an older version otherwise
			while ( sizeof (  $this->JSONDecoded->exchangeRate ) == 0  )
			{ 
				--$l; // Decrement the day
				
				$this->currentDate = date ( "d.m.Y" , strtotime ($this->currentDate . $l . " days") ) ;
				
				
				$this->JSON = file_get_contents (self::API_URL . $this->currentDate ) ;
				$this->JSONDecoded = json_decode ($this->JSON) ;
				
			}
			// JSON data obtained and parsed
			return true;
		}
		else
		// Could not make an API call with the specified URL
			return false;
			    
    }
	
	// Return a JSON encoded array according to the specififed currency types
	function getRates()
	{
	    $k = 1;
	    $CUR_RESULT = array ();
		if ( ! (empty ( $this->JSONDecoded ) ) && ! ( empty ( $this->JSONDecoded->exchangeRate )) ) 
		{
			
			foreach ( $this->JSONDecoded->exchangeRate as $x )
			{
				foreach ($this->currencyTypes as $TYPE)
				{
					if ( strcmp ( $TYPE ,  @$x->currency ) == 0 )
					{
						$CUR_RESULT[$TYPE] = $x->saleRateNB ; 
						$k++;
						
					}
				}
			
			}
		}
		$CUR_RESULT = json_encode ($CUR_RESULT);
		return (empty ($CUR_RESULT)) ? false : $CUR_RESULT ;
	
	}
	
};
?>