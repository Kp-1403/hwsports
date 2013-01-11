<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tms extends MY_Controller {

	function __construct()
	{
		parent::__construct();

		// If user is an admin, we grant full permission

		// If user is centreadmin, we need to check if they are actually part of
		// the centre, and not of another centre.

		// If the user is staff, we need to check if they are actually part of
		// the centre, and not of another centre.

		// If user is regular user, then redirect to sis


		/*
		 *  ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW 
		 *	ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW ANDREW 
		 *  DO WE ACTUALLY MAKE SURE STAFF AND CENTRE ADMIN ARE PART OF THE CENTRE, AND NOT ANOTHER CENTRE???
		 */
		/*$authorized = False;
		if ( $this->ion_auth->in_group('admin') ){
			$authorized = True;
		} else if ( $this->ion_auth->in_group('centreadmin') && $this->data['centre']['id'])*/

		if ( $this->ion_auth->in_group('admin') || $this->ion_auth->in_group('centreadmin') ) {
			$this->data['currentUser'] = $currentUser = $this->ion_auth->user()->row();
			$query = $this->db->query("SELECT `key`,`value` FROM `userData` WHERE `userID` = '{$currentUser->id}'");
			foreach($query->result_array() as $userDataRow) {
				$currentUser->$userDataRow['key'] = $userDataRow['value'];
			}
		} else {
			//redirect them to the sis homepage
			redirect('/', 'refresh');
		}
	}
	public function index()
	{
		$this->data['title'] = "Home";
		$this->data['page'] = "tmshome";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/home',$this->data);
		$this->load->view('tms/footer',$this->data);
	}
	public function tournaments()
	{
		$this->data['title'] = "Tournaments";
		$this->data['page'] = "tournaments";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/tournaments',$this->data);
		$this->load->view('tms/footer',$this->data);
	}
	public function venues($action='portal')
	{
		$this->data['title'] = "Venues";
		$this->data['page'] = "venues";
		
		// Get list of all venues
		$this->data['venues'] = array();
		$venueQueryString = "SELECT venueID FROM venues WHERE centreID = {$this->data['centre']['id']}";
		$venueQuery = $this->db->query($venueQueryString);
		$venuesArray = $venueQuery->result_array();
		foreach($venuesArray as $venue) {
			$venueDataQueryString = "SELECT " .
				"MAX(CASE WHEN `key`='name' THEN value END ) AS name, " .
				"MAX(CASE WHEN `key`='description' THEN value END ) AS description, " .
				"MAX(CASE WHEN `key`='directions' THEN value END ) AS directions, " .
				"MAX(CASE WHEN `key`='lat' THEN value END ) AS lat, " .
				"MAX(CASE WHEN `key`='lng' THEN value END ) AS lng " .
				"FROM venueData WHERE venueID = {$venue['venueID']}";
			$venueDataQuery = $this->db->query($venueDataQueryString);
			$this->data['venues'][] = array_merge($venue, $venueDataQuery->row_array());
		}
		
		//validate form input
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		$this->form_validation->set_rules('directions', 'Directions', 'required');
		$this->form_validation->set_rules('lat', 'Latitude', 'required');
		$this->form_validation->set_rules('lng', 'Longitude', 'required');

		// If form has been submitted and it validates ok
		if ($this->form_validation->run() == true) {
			// Form validated ok, process input
			$this->db->query("INSERT INTO venues (centreID) VALUES ({$this->data['centre']['id']})");
			$venueID = $this->db->insert_id();
			
			$venueDataArray = array(
				array(
					'venueID' => $venueID,
					'key' => 'name',
					'value' => $this->input->post('name')
				),
				array(
					'venueID' => $venueID,
					'key' => 'description',
					'value' => $this->input->post('description')
				),
				array(
					'venueID' => $venueID,
					'key' => 'directions',
					'value' => $this->input->post('directions')
				),
				array(
					'venueID' => $venueID,
					'key' => 'lat',
					'value' => $this->input->post('lat')
				),
				array(
					'venueID' => $venueID,
					'key' => 'lng',
					'value' => $this->input->post('lng')
				)
			);
			   
			if ($this->db->insert_batch('venueData',$venueDataArray)) {
				// db success
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('/tms/venues', 'refresh');
			} else {
				// db fail
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('/tms/venues', 'refresh');
			}
		} else {
			//either form not submitted yet or validation failed
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			
			// query google maps api for lat / lng of sports centre
			$address = urlencode($this->data['centre']['address']);
			$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=uk";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$json = curl_exec($ch);
			curl_close($ch);
			$apiData = json_decode($json);
			
			//$this->data['apiData'] = $json;
			$lat = $apiData->results[0]->geometry->location->lat;
			$lng = $apiData->results[0]->geometry->location->lng;

			$this->data['createLatLng'] = array('lat' => $lat, 'lng' => $lng);
			$this->data['createName'] = array('name' => 'name');
			$this->data['createDescription'] = array('name' => 'description');
			$this->data['createDirections'] = array('name' => 'directions');
		}
		
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/venues',$this->data);
		$this->load->view('tms/footer',$this->data);
	}




	public function altVenues(){

		$this->load->library('table');
		$this->load->model('venues_model');

		// Get data for all venues.
		$this->data['venues'] = $this->venues_model->get_venues($this->data['centre']['id']);

		//validate form input
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		$this->form_validation->set_rules('directions', 'Directions', 'required');
		$this->form_validation->set_rules('lat', 'Latitude', 'required');
		$this->form_validation->set_rules('lng', 'Longitude', 'required');

		// If form has been submitted and it validates ok
		if ($this->form_validation->run() == true) {

			// Form validated ok, process input
			$venueDataArray = array(
				array(
					'key' => 'name',
					'value' => $this->input->post('name')
				),
				array(
					'key' => 'description',
					'value' => $this->input->post('description')
				),
				array(
					'key' => 'directions',
					'value' => $this->input->post('directions')
				),
				array(
					'key' => 'lat',
					'value' => $this->input->post('lat')
				),
				array(
					'key' => 'lng',
					'value' => $this->input->post('lng')
				)
			);

			if(insert_venue($this->data['centre']['id'],$venueDataArray)>=0){
				// db success
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('/tms/venues', 'refresh');
			} else {
				// db fail
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('/tms/venues', 'refresh');
			}

		} else {
			//either form not submitted yet or validation failed
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			
			// query google maps api for lat / lng of sports centre
			$address = urlencode($this->data['centre']['address']);
			$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=uk";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$json = curl_exec($ch);
			curl_close($ch);
			$apiData = json_decode($json);
			
			//$this->data['apiData'] = $json;
			$lat = $apiData->results[0]->geometry->location->lat;
			$lng = $apiData->results[0]->geometry->location->lng;

			$this->data['createLatLng'] = array('lat' => $lat, 'lng' => $lng);
			$this->data['createName'] = array('name' => 'name');
			$this->data['createDescription'] = array('name' => 'description');
			$this->data['createDirections'] = array('name' => 'directions');
		}

		// Display the page.
		$this->data['title'] = "Venues";
		$this->data['page']  = "venues";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/altvenues',$this->data);
		$this->load->view('tms/footer',$this->data);
	}




	public function sports()
	{
		$this->data['title'] = "Sports";
		$this->data['page'] = "sports";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/sports',$this->data);
		$this->load->view('tms/footer',$this->data);
	}
	public function matches()
	{
		$this->data['title'] = "Matches";
		$this->data['page'] = "matches";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/matches',$this->data);
		$this->load->view('tms/footer',$this->data);
	}
	public function groups()
	{
		$this->data['title'] = "Groups";
		$this->data['page'] = "groups";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/groups',$this->data);
		$this->load->view('tms/footer',$this->data);
	}
	public function users()
	{
		$this->data['title'] = "Users";
		$this->data['page'] = "users";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/users',$this->data);
		$this->load->view('tms/footer',$this->data);
	}
	public function news()
	{
		$this->data['title'] = "News";
		$this->data['page'] = "news";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/news',$this->data);
		$this->load->view('tms/footer',$this->data);
	}
	public function tickets()
	{
		$this->data['title'] = "Tickets";
		$this->data['page'] = "tickets";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/tickets',$this->data);
		$this->load->view('tms/footer',$this->data);
	}
	public function reports()
	{
		$this->data['title'] = "Reports";
		$this->data['page'] = "reports";
		$this->load->view('tms/header',$this->data);
		$this->load->view('tms/reports',$this->data);
		$this->load->view('tms/footer',$this->data);
	}

}