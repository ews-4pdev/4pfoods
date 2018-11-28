<?php

require_once(APPPATH.'core/AJAX_Controller.php');

echo 'here'; exit;

class Main extends AJAX_Controller {

	
	function testSystem($input) {
		
		jsonSuccess();
		
	}
	
}
