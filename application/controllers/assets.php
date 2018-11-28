<?php

class Assets extends MY_Controller {
	
	function js($build) {
		
		require_once(RLIB.'JSHandler.class.php');
		JSHandler::getSource($build);
		
	}
	
}

?>