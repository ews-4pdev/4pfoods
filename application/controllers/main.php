<?php

class Main extends MY_Controller {

	public function index()
    {
        redirect('/gateway/signup');

	}

  public function testsendgrid() {

    $helper = new SendGridHelper('robin.arenson@gmail.com', 'bag-reminder');
    $helper->merge(array(
      'FirstName'   => 'Nibor'
    ));
    $helper->send();
    

  }

}
