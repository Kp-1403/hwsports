<?php
class Db_venues extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('venues_model');
	}

	public function getVenues($centreID)
	{
		$output = $this->venues->get_venues($centreID);

		$this->load->view('tms/header',$this->data);
		echo print_r($output);
		$this->load->view('tms/footer',$this->data);

	}
}