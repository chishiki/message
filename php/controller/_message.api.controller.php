<?php

final class MessageAPI {
		
	    private $loc;
	    private $input;
	    
	    public function __construct($loc, $input) {
			
	        $this->loc = $loc;
	        $this->input = $input;
			
		}
		
		public function response() {

	    	if ($this->loc[0] == 'api' && $this->loc[1] == 'message') {

	    		$response = '{"api":"message"}';
				return $response;

			}

		}
		
	}

?>