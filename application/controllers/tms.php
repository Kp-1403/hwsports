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

		/*$authorized = False;
		if ( $this->ion_auth->in_group('admin') ){
			$authorized = True;
		} else if ( $this->ion_auth->in_group('centreadmin') && $this->data['centre']['centreID'])*/

		if ( $this->ion_auth->in_group('admin') || $this->ion_auth->in_group('centreadmin') ) {
			$this->data['currentUser'] = $currentUser = $this->ion_auth->user()->row();
			$query = $this->db->query("SELECT `key`,`value` FROM `userData` WHERE `userID` = '{$currentUser->userID}'");
			foreach($query->result_array() as $userDataRow) {
				$currentUser->$userDataRow['key'] = $userDataRow['value'];
			}
		} else {
			//redirect them to the sis homepage
			redirect('/', 'refresh');
		}
	}

	/**
	 * A short hand method to basically print out the page with a certain pageid and title
	 *
	 * @param view 		The view to load
	 * @param page 		The page ID it will have
	 * @param title 	
	 * @param data 		passed in data
	 */
	public function view($view,$page,$title,$data){
		$data['title'] = $title;
		$data['page'] = $page;
		$this->load->view('tms/header',$data);
		$this->load->view('tms/'.$view,$data);
		$this->load->view('tms/footer',$data);
	}

	public function index()
	{
		$this->load->model('tournaments_model');
		$this->load->model('matches_model');

		// Get todays date as a string
		// Note we want to say that today is everything until this afternoon.
		$today = new DateTime();
		$today->setTime ( 23, 59, 59 );

		// Get all the tournaments and matches from the database.
		$latestMatches = $this->matches_model->get_all(FALSE,$today); // Get all matches that have occured and today's matches
		$upcomingMatches = $this->matches_model->get_all($today,FALSE); // Get all tournaments that occur after today
		$latestTournaments = $this->tournaments_model->get_all(FALSE,$today); // Get all matches that have occured and today's matches
		$upcomingTournaments  = $this->tournaments_model->get_all($today,FALSE); // Get all tournaments that occur after today

		// We want to remove the matches that already exist in the latest matches
		foreach($upcomingMatches as $u=>$uMatch){
			if(!$uMatch){
				unset($upcomingMatches[$u]);
				break;
			}
			if($today<new DateTime($uMatch['startTime']))
				break;
			foreach($latestMatches as $i=>$lMatch){
				if(!$lMatch){
					unset($latestTournaments[$i]);
					break;
				}
				if($uMatch['matchID']==$lMatch['matchID']){
					unset($upcomingMatches[$u]);
					break;
				}
			}
		}
		// We want to remove the tournaments that already exist in the latest tournaments
		foreach($upcomingTournaments as $u=>$uTournament){
			if(!$uTournament){
				unset($upcomingTournaments[$u]);
				break;
			}
			if($today<new DateTime($uTournament['tournamentStart']))
				break;
			foreach($latestTournaments as $i=>$lTournament){
				if(!$lTournament){
					unset($latestTournaments[$i]);
					break;
				}
				if($utournament['tournamentID']==$lTournament['tournamentID']){
					unset($upcomingTournament[$u]);
					break;
				}
			}
		}
		function cmpMatches($a, $b){
			$a = new DateTime($a['endTime']);
			$b = new DateTime($b['endTime']);
			if ($a == $b) { return 0; }
			return ($a < $b) ? -1 : 1;
		}
		function cmpTournaments($a, $b){
			$a = new DateTime($a['tournamentEnd']);
			$b = new DateTime($b['tournamentEnd']);
			if ($a == $b) { return 0; }
			return ($a < $b) ? -1 : 1;
		}

		usort($latestMatches, "cmpMatches");
		usort($upcomingMatches, "cmpMatches");
		usort($latestTournaments, "cmpTournaments");
		usort($upcomingTournaments, "cmpTournaments");
		$latestMatches 			= array_slice($latestMatches, -0, 5);
		$upcomingMatches 		= array_slice($upcomingMatches, -0, 5);
		$latestTournaments 		= array_slice($latestTournaments, -0, 5);
		$upcomingTournaments 	= array_slice($upcomingTournaments, -0, 5);
		$this->data['latestMatches'] 		= $latestMatches;
		$this->data['upcomingMatches'] 		= $upcomingMatches;
		$this->data['latestTournaments'] 	= $latestTournaments;
		$this->data['upcomingTournaments'] 	= $upcomingTournaments;

		$this->view('home',"tmshome","Home",$this->data);
	}
	public function tournaments()
	{	
		$this->load->model('tournaments_model');
		$this->load->model('sports_model');
		
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
		$this->form_validation->set_rules('sport', 'Sport', 'required|xss_clean');
		$this->form_validation->set_rules('registrationStart', 'registrationStart', 'required|xss_clean|callback_datetime_check[registrationStart]');
		$this->form_validation->set_rules('registrationEnd', 'registrationEnd', 'required|xss_clean|callback_datetime_check[registrationEnd]');
		$this->form_validation->set_rules('tournamentStart', 'tournamentStart', 'required|xss_clean|callback_datetime_check[tournamentStart]');
		$this->form_validation->set_rules('tournamentEnd', 'tournamentEnd', 'required|xss_clean|callback_datetime_check[tournamentEnd]');
		
		// Change dates from public, timepicker-friendly format to database-friendly ISO format.
		if($this->input->post('registrationStart')) $_POST['registrationStart'] = datetime_to_standard($this->input->post('registrationStart'));
		if($this->input->post('registrationEnd')) $_POST['registrationEnd'] = datetime_to_standard($this->input->post('registrationEnd'));
		if($this->input->post('tournamentStart')) $_POST['tournamentStart'] = datetime_to_standard($this->input->post('tournamentStart'));
		if($this->input->post('tournamentEnd')) $_POST['tournamentEnd'] = datetime_to_standard($this->input->post('tournamentEnd'));
		
		if ($this->form_validation->run() == true) {
			$newdata = $_POST;
			unset($newdata['submit']);
			
			$tournamentID = $this->tournaments_model->insert($newdata);
			if($tournamentID > -1) {
				// Successful update, show success message
				$this->session->set_flashdata('message_success',  'Successfully Created Tournament.');
			} else {
				$this->session->set_flashdata('message_error',  'Failed. Please contact Infusion Systems.');
			}
			redirect("/tms/tournament/$tournamentID", 'refresh');
		} else {
			//display the create user form
			//set the flash data error message if there is one
			$this->data['message_error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message_error') );
		
			$this->data['tournaments'] = $this->tournaments_model->get_all();
		
			$this->data['sports'] = array();
			foreach( $this->sports_model->get_all() as $sport) {				
				$this->data['sports'][$sport['sportCategoryData']['name']][$sport['sportID']] = $sport['name'];
			}
			ksort($this->data['sports']);


			
			$this->data['name'] = array(
				'name'  => 'name',
				'id'    => 'name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('name')
			);
			
			$this->data['description'] = array(
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('description')
			);
			$this->data['registrationStart'] = array(
				'name'  => 'registrationStart',
				'id'    => 'registrationStart',
				'type'  => 'text',
				'class' => 'date',
				'value' => datetime_to_public( $this->form_validation->set_value('registrationStart') )
			);
			$this->data['registrationEnd'] = array(
				'name'  => 'registrationEnd',
				'id'    => 'registrationEnd',
				'type'  => 'text',
				'class' => 'date',
				'value' => datetime_to_public( $this->form_validation->set_value('registrationEnd') )
			);
			$this->data['tournamentStart'] = array(
				'name'  => 'tournamentStart',
				'id'    => 'tournamentStart',
				'type'  => 'text',
				'class' => 'date',
				'value' => datetime_to_public( $this->form_validation->set_value('tournamentStart') )
			);
			$this->data['tournamentEnd'] = array(
				'name'  => 'tournamentEnd',
				'id'    => 'tournamentEnd',
				'type'  => 'text',
				'class' => 'date',
				'value' => datetime_to_public( $this->form_validation->set_value('tournamentEnd') )
			);
			
		}
		$this->view('tournaments',"tournaments","Tournaments",$this->data);
	}
	
	public function tournament($tournamentID)
	{
		
		$this->load->model('tournaments_model');
		$this->load->model('sports_model');
		
		$this->data['tournamentID'] = $tournamentID;
		$this->data['tournament'] = $tournament = $this->tournaments_model->get($tournamentID);
		if($tournament==FALSE) {
			$this->session->set_flashdata('message_error',  "Tournament ID $id does not exist.");
			redirect("/tms/tournaments", 'refresh');
		}
					
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');		
		
		switch($tournament['status']) { 
			case "preRegistration": 
				$this->form_validation->set_rules('registrationStart', 'registrationStart', 'required|xss_clean|callback_datetime_check[registrationStart]');
				$this->form_validation->set_rules('registrationEnd', 'registrationEnd', 'required|xss_clean|callback_datetime_check[registrationEnd]');
				$this->form_validation->set_rules('tournamentStart', 'tournamentStart', 'required|xss_clean|callback_datetime_check[tournamentStart]');
				$this->form_validation->set_rules('tournamentEnd', 'tournamentEnd', 'required|xss_clean|callback_datetime_check[tournamentEnd]');	
			break; 
			case "inRegistration": 
				$this->form_validation->set_rules('registrationEnd', 'registrationEnd', 'required|xss_clean|callback_datetime_check[registrationEnd]');
				$this->form_validation->set_rules('tournamentStart', 'tournamentStart', 'required|xss_clean|callback_datetime_check[tournamentStart]');
				$this->form_validation->set_rules('tournamentEnd', 'tournamentEnd', 'required|xss_clean|callback_datetime_check[tournamentEnd]');	
			break; 
			case "postRegistration": 
				$this->form_validation->set_rules('tournamentStart', 'tournamentStart', 'required|xss_clean|callback_datetime_check[tournamentStart]');
				$this->form_validation->set_rules('tournamentEnd', 'tournamentEnd', 'required|xss_clean|callback_datetime_check[tournamentEnd]');	
			break; 
			case "preTournament": 
				$this->form_validation->set_rules('tournamentStart', 'tournamentStart', 'required|xss_clean|callback_datetime_check[tournamentStart]');
				$this->form_validation->set_rules('tournamentEnd', 'tournamentEnd', 'required|xss_clean|callback_datetime_check[tournamentEnd]');	
			break; 
			case "inTournament": 
				$this->form_validation->set_rules('tournamentEnd', 'tournamentEnd', 'required|xss_clean|callback_datetime_check[tournamentEnd]');	
			break; 
			case "postTournament": 
			break;
			default: 
				$this->form_validation->set_rules('registrationStart', 'registrationStart', 'required|xss_clean|callback_datetime_check[registrationStart]');
				$this->form_validation->set_rules('registrationEnd', 'registrationEnd', 'required|xss_clean|callback_datetime_check[registrationEnd]');
				$this->form_validation->set_rules('tournamentStart', 'tournamentStart', 'required|xss_clean|callback_datetime_check[tournamentStart]');
				$this->form_validation->set_rules('tournamentEnd', 'tournamentEnd', 'required|xss_clean|callback_datetime_check[tournamentEnd]');	
			break; 	
		} 
		
		// Change dates from public, timepicker-friendly format to database-friendly ISO format.
		if($this->input->post('registrationStart')) $_POST['registrationStart'] = datetime_to_standard($this->input->post('registrationStart'));
		if($this->input->post('registrationEnd')) $_POST['registrationEnd'] = datetime_to_standard($this->input->post('registrationEnd'));
		if($this->input->post('tournamentStart')) $_POST['tournamentStart'] = datetime_to_standard($this->input->post('tournamentStart'));
		if($this->input->post('tournamentEnd')) $_POST['tournamentEnd'] = datetime_to_standard($this->input->post('tournamentEnd'));
		
		if ($this->form_validation->run() == true) {
			$newdata = $_POST;
			
			if($this->tournaments_model->update($tournamentID, $newdata)) {
				// Successful update, show success message
				$this->session->set_flashdata('message_success',  'Successfully Updated Tournament.');
			} else {
				$this->session->set_flashdata('message_error',  'Failed to update tournament. Please contact Infusion Systems.');
			}
			redirect("/tms/tournament/$tournamentID", 'refresh');
		} else {
			//set the flash data error message if there is one
			$this->data['message_success'] = $this->session->flashdata('message_success');
			$this->data['message_error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message_error') );
			
			$sport = $this->sports_model->get( $tournament['sportID'] );
			$this->data['tournament']['sportName'] = $sport['name'];
		
			$this->data['name'] = array(
				'name'  => 'name',
				'id'    => 'name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('name',(isset($tournament['name']) ? $tournament['name'] : '') )
			);
			$this->data['description'] = array(
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('description',(isset($tournament['description']) ? $tournament['description'] : '') )
			);
			$this->data['registrationStart'] = array(
				'name'  => 'registrationStart',
				'id'    => 'registrationStart',
				'type'  => 'text',
				'class' => 'date',
				'value' => datetime_to_public( $this->form_validation->set_value('registrationStart',(isset($tournament['registrationStart']) ? $tournament['registrationStart'] : '') ) )
			);
			$this->data['registrationEnd'] = array(
				'name'  => 'registrationEnd',
				'id'    => 'registrationEnd',
				'type'  => 'text',
				'class' => 'date',
				'value' => datetime_to_public( $this->form_validation->set_value('registrationEnd',(isset($tournament['registrationEnd']) ? $tournament['registrationEnd'] : '') ) )
			);
			$this->data['tournamentStart'] = array(
				'name'  => 'tournamentStart',
				'id'    => 'tournamentStart',
				'type'  => 'text',
				'class' => 'date',
				'value' => datetime_to_public( $this->form_validation->set_value('tournamentStart',(isset($tournament['tournamentStart']) ? $tournament['tournamentStart'] : '') ) )
			);
			$this->data['tournamentEnd'] = array(
				'name'  => 'tournamentEnd',
				'id'    => 'tournamentEnd',
				'type'  => 'text',
				'class' => 'date',
				'value' => datetime_to_public( $this->form_validation->set_value('tournamentEnd',(isset($tournament['tournamentEnd']) ? $tournament['tournamentEnd'] : '') ) )
			);
			
		}
		
		$this->view('tournament',"tournament","Tournament",$this->data);
	}
	
	
	public function delete_tournament($tournamentID)
	{
		$this->load->model('tournaments_model');

		if($this->tournaments_model->delete($tournamentID) ) {
			// Successful delete, show success message
			$this->session->set_flashdata('message_success',  'Successfully Deleted Tournament.');
		} else {
			$this->session->set_flashdata('message_error',  'Failed. Please contact Infusion Systems.');
		}
		redirect("/tms/tournaments", 'refresh');
	}

	public function venues($action='portal')
	{
	
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

		$this->data['centreLat'] = $lat;
		$this->data['centreLng'] = $lng;
		
		$this->view('venues',"venues","Tournament",$this->data);
	}

	public function venue($venueID)
	{
		$this->load->library('table');
		$this->load->model('venues_model');

		// Get data for this venue
		$this->data['venue'] = $this->venues_model->get($venueID);

		$this->view('venue',"venue",$this->data['venue']['name']." | Venue",$this->data);
	}

	public function sports()
	{
		$this->view('sports',"sports","Sports",$this->data);
	}
	public function match($matchID)
	{
		$this->load->library('table');
		$this->load->model('matches_model');

		// Get data for this venue
		$this->data['match'] = $this->matches_model->get($matchID);
		$this->data['match']['startTime'] = datetime_to_public($this->data['match']['startTime']); 
		$this->data['match']['endTime'] = datetime_to_public($this->data['match']['endTime']); 

		$this->view('match',"match",$this->data['match']['name']." | Match",$this->data);
	}
	public function matches()
	{
		$this->view('matches',"matches","Matches",$this->data);
	}
	public function calendar()
	{
		$this->view('calendar',"calendar","Calendar",$this->data);
	}
	public function groups()
	{
		$this->data['groups'] = $this->ion_auth->groups()->result();
		$this->view('groups',"groups","Groups",$this->data);
	}
	public function users()
	{	
		$this->load->model('users_model');
		$users = $this->users_model->get_all();
		$this->data['users'] = $users;
		
		$this->view('users',"users","Users",$this->data);
	}
	public function user($userID)
	{
		$this->load->model('users_model');
		$user = $this->users_model->get($userID);
		$this->data['user'] = $user;
		
		$this->view('user',"user",$user['firstName']." ".$user['lastName']." | User",$this->data);
	}
	public function teams()
	{	
		$this->load->model('teams_model');
		$teams = $this->teams_model->get_all();
		$this->data['teams'] = $teams;
		
		$this->view('teams',"teams","Teams",$this->data);
	}
	public function team($teamID)
	{
		$this->load->model('teams_model');
		$team = $this->teams_model->get($teamID);
		$this->data['team'] = $user;
		
		$this->view('team',"team",$user['name']." | Team",$this->data);
	}
	public function announcements()
	{
		$this->load->model('announcements_model');
		$announcements = $this->announcements_model->get_all();
		$this->data['announcements'] = $announcements;

		$this->view('announcements',"announcements","Announcements",$this->data);
	}
	public function announcement($announcementID)
	{
		$this->load->model('announcements_model');
		$announcement = $this->announcements_model->get($announcementID);
		$this->data['announcement'] = $announcement;

		$this->view('annoucement',"annoucement",$announcement['title']." | Announcement",$this->data);
	}
	public function reports()
	{
		$this->view('reports',"reports","Reports",$this->data);
	}
	public function playground() {
		$this->view('playground',"playground","Branding Playground",$this->data);
	}
	public function settings()
	{
			
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('shortName', 'Short Name', 'required|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
		$this->form_validation->set_rules('headerColour', 'Header Colour', 'required|xss_clean');
		$this->form_validation->set_rules('backgroundColour', 'Background Colour', 'required|xss_clean');
		$this->form_validation->set_rules('footerText', 'Footer Text', 'required|xss_clean');
		
		$weekdaysShort = array('mon','tue','wed','thu','fri','sat','sun');
		$weekdaysLong  = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');

		for($i=0;$i<7;$i++)
		{
			$this->form_validation->set_rules($weekdayShort[$i].'OpenTime', $weekdayShort[$i].'day Open Time', 'required|xss_clean');
			$this->form_validation->set_rules($weekdayShort[$i].'CloseTime', $weekdayShort[$i].'day Close Time', 'required|xss_clean');
		}
		
		if ($this->form_validation->run() == true) {
			$newdata = $_POST;
			// If checkbox is unticked, it returns no value - this means FALSE
			for($i=0;$i<7;$i++)
				if(!isset($newdata[$weekdayShort[$i].'Open'])) $newdata[$weekdayShort[$i].'Open'] = 0;
			
			if($this->centre_model->update_centre($this->data['centre']['centreID'],$newdata ) ) {
				// Successful update, show success message
				$this->session->set_flashdata('message_success',  'Successfully Updated');
			} else {
				$this->session->set_flashdata('message_error',  'Failed. Please contact Infusion Systems.');
			}
			redirect("/tms/settings", 'refresh');
		} else {
			//display the create user form
			//set the flash data error message if there is one
			$this->data['message_error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message_error') );
			
			$this->data['name'] = array(
				'name'  => 'name',
				'id'    => 'name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('name',(isset($this->data['centre']['name']) ? $this->data['centre']['name'] : '') )
			);
			$this->data['shortName'] = array(
				'name'  => 'shortName',
				'id'    => 'shortName',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('shortName',(isset($this->data['centre']['shortName']) ? $this->data['centre']['shortName'] : '') )
			);
			$this->data['address'] = array(
				'name'  => 'address',
				'id'    => 'address',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('address',(isset($this->data['centre']['address']) ? $this->data['centre']['address'] : '') )
			);
			$this->data['headerColour'] = array(
				'name'  => 'headerColour',
				'id'    => 'headerColour',
				'type'  => 'text',
				'style' => 'background-color: #'.(isset($this->data['centre']['headerColour']) ? $this->data['centre']['headerColour'] : 'FFFFFF'),
				'class' => 'colorpickerinput',
				'value' => $this->form_validation->set_value('headerColour',(isset($this->data['centre']['headerColour']) ? $this->data['centre']['headerColour'] : '') )
			);
			$this->data['backgroundColour'] = array(
				'name'  => 'backgroundColour',
				'id'    => 'backgroundColour',
				'type'  => 'text',
				'style' => 'background-color: #'.(isset($this->data['centre']['backgroundColour']) ? $this->data['centre']['backgroundColour'] : 'FFFFFF'),
				'class' => 'colorpickerinput',
				'value' => $this->form_validation->set_value('backgroundColour',(isset($this->data['centre']['backgroundColour']) ? $this->data['centre']['backgroundColour'] : '') )
			);
			$this->data['footerText'] = array(
				'name'  => 'footerText',
				'id'    => 'footerText',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('footerText',(isset($this->data['centre']['footerText']) ? $this->data['centre']['footerText'] : '') )
			);
			
			for($i=0;$i<7;$i++){
				$this->data[$weekdayShort[$i].'Open'] = array(
					'name'  => $weekdayShort[$i].'Open',
					'id'    => $weekdayShort[$i].'Open',
					'type'  => 'checkbox',
					'value' => '1',
					($this->data['centre'][$weekdayShort[$i].'Open'] ? 'checked' : 'notchecked') => 'checked'
				);
				$this->data[$weekdayShort[$i].'OpenTime'] = array(
					'name'  => $weekdayShort[$i].'OpenTime',
					'id'    => $weekdayShort[$i].'OpenTime',
					'type'  => 'text',
					'class'  => 'time',
					'value' => $this->form_validation->set_value($weekdayShort[$i].'OpenTime',(isset($this->data['centre'][$weekdayShort[$i].'OpenTime']) ? $this->data['centre'][$weekdayShort[$i].'OpenTime'] : '') )
				);
				$this->data[$weekdayShort[$i].'CloseTime'] = array(
					'name'  => $weekdayShort[$i].'CloseTime',
					'id'    => $weekdayShort[$i].'CloseTime',
					'type'  => 'text',
					'class'  => 'time',
					'value' => $this->form_validation->set_value($weekdayShort[$i].'CloseTime',(isset($this->data['centre'][$weekdayShort[$i].'CloseTime']) ? $this->data['centre'][$weekdayShort[$i].'CloseTime'] : '') )
				);
			}

			$this->view('settings',"settings","Centre Settings",$this->data);
		}
	}

	public function appearance()
	{		
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('shortName', 'Short Name', 'required|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
		$this->form_validation->set_rules('headerColour', 'Header Colour', 'required|xss_clean');
		$this->form_validation->set_rules('backgroundColour', 'Background Colour', 'required|xss_clean');
		$this->form_validation->set_rules('footerText', 'Footer Text', 'required|xss_clean');
		
		if ($this->form_validation->run() == true) {
			$newdata = $_POST;
			
			if($this->centre_model->update($newdata)) {
				// Successful update, show success message
				$this->session->set_flashdata('message_success',  'Successfully Updated');
			} else {
				$this->session->set_flashdata('message_error',  'Failed. Please contact Infusion Systems.');
			}
			redirect("/tms/appearance", 'refresh');
		} else {
			//display the create user form
			//set the flash data error message if there is one
			$this->data['message_error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message_error') );
			
			$this->data['name'] = array(
				'name'  => 'name',
				'id'    => 'name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('name',(isset($this->data['centre']['name']) ? $this->data['centre']['name'] : '') )
			);
			$this->data['shortName'] = array(
				'name'  => 'shortName',
				'id'    => 'shortName',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('shortName',(isset($this->data['centre']['shortName']) ? $this->data['centre']['shortName'] : '') )
			);
			$this->data['address'] = array(
				'name'  => 'address',
				'id'    => 'address',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('address',(isset($this->data['centre']['address']) ? $this->data['centre']['address'] : '') )
			);
			$this->data['headerColour'] = array(
				'name'  => 'headerColour',
				'id'    => 'headerColour',
				'type'  => 'text',
				'style' => 'background-color: #'.(isset($this->data['centre']['headerColour']) ? $this->data['centre']['headerColour'] : 'FFFFFF'),
				'class' => 'colorpickerinput',
				'value' => $this->form_validation->set_value('headerColour',(isset($this->data['centre']['headerColour']) ? $this->data['centre']['headerColour'] : '') )
			);
			$this->data['backgroundColour'] = array(
				'name'  => 'backgroundColour',
				'id'    => 'backgroundColour',
				'type'  => 'text',
				'style' => 'background-color: #'.(isset($this->data['centre']['backgroundColour']) ? $this->data['centre']['backgroundColour'] : 'FFFFFF'),
				'class' => 'colorpickerinput',
				'value' => $this->form_validation->set_value('backgroundColour',(isset($this->data['centre']['backgroundColour']) ? $this->data['centre']['backgroundColour'] : '') )
			);
			$this->data['footerText'] = array(
				'name'  => 'footerText',
				'id'    => 'footerText',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('footerText',(isset($this->data['centre']['footerText']) ? $this->data['centre']['footerText'] : '') )
			);
			
			$this->view('appearance',"appearance","Apprearance",$this->data);
		}
	}

	// Callback function for form validation - checks sanity and validity of POST date strings
	public function datetime_check($strDateTime,$field) {
		try {
			// If date string is invalid, this should throw an exception. We're only calling it endDate because of the checking of date ranges 
			$endDate = new DateTime($strDateTime);
			// If the field has "End" at the end, we're assuming there's a corresponding "Start" field. 
			if(substr($field, -3)=="End") {
				$endDateField = $field;
				$startDateField = substr($field, 0, -3)."Start";
				// Create a new DateTime object from the start date string, or today's date if there is no start string
				$startDate = ( ($this->input->post($startDateField)===FALSE) ? new DateTime() : new DateTime($this->input->post($startDateField)) );
				// If start datetime is equal to or after end datetime 
				if( $startDate >= $endDate ) {
					if($this->input->post($startDateField)) {
						$error = "Date '$startDateField': ".datetime_to_public($startDate)." is not before end date: ".datetime_to_public($endDate);
					} else {
						$error = "Date '$endDateField': ".datetime_to_public($endDate)." is before current time: ".datetime_to_public($startDate);
					}						
					$this->form_validation->set_message('datetime_check', "Invalid date range specified: $error");
					return FALSE;
				}
			}
			// SPECIFIC CASE: tournament creation | If we have a registration end date and a tournament start date, check tournament is starting after registration period
			if( $field=="registrationEnd" && ($this->input->post("tournamentStart")!==FALSE) ) {
				$tournamentStart = new DateTime($this->input->post("tournamentStart"));
				$registrationEnd = $endDate;
				if( $tournamentStart < $registrationEnd ) {
					$this->form_validation->set_message('datetime_check', "Tournament must start after registration period has ended. Please correct the tournament start date.");
					return FALSE;
				}
			}
			// Sanity checks passed, assume valid date
			return TRUE;	
		} catch (Exception $e) {
			$this->form_validation->set_message('datetime_check', 'The %s field must contain a valid date in the ISO 8601 format: YYYY-MM-DDThh:mm:ssTZD (eg 1997-07-16T19:20:30+0100) Provided: '.var_export($strDateTime,1).' Debug Exception: '.$e->getMessage() );
			return FALSE;
		}
	}

}
