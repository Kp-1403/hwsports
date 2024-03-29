<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    protected $the_user;

    public function __construct() {

        parent::__construct();
		
		$centreSite = TRUE;
		switch($_SERVER['HTTP_HOST']) {
			case "hwsports.co.uk":
				$this->data['slug'] = "hwsports";
			break;
			case "infusionsports.co.uk":
				$centreSite = FALSE;
				$this->data['slug'] = "product";
			break;
			default:
				redirect('http://infusionsports.co.uk');
		}
		
		// Define other models so we can access objects from the database
		$this->objects_models = array(
			"users" => $this->users_model,
			"teams" => $this->teams_model,
			"tournament_actors" => $this->tournament_actors_model
		);
		
		if($centreSite) {
			// Get Sports Centre ID from slug/domain
			$query = $this->db->query("SELECT `centreID` FROM `centreData` WHERE `key` = 'slug' AND `value` = '{$this->data['slug']}' LIMIT 1");
			$row = $query->row_array();
				
			// Give us access to centre database methods

			
			// Make all centre data accessible from all controllers and views
			$this->data['centre'] = $this->centre_model->get( $row['centreID'] );
			$this->centreID = $this->data['centre']['centreID'];
			
			$weekdaysShort = array('mon','tue','wed','thu','fri','sat','sun');
			foreach($weekdaysShort as $day){
				if(!isset($this->data['centre'][$day.'Open'])) $this->data['centre'][$day.'Open'] = 0;
				if(!isset($this->data['centre'][$day.'CloseTime'])) $this->data['centre'][$day.'CloseTime'] = "00:00";
				if(!isset($this->data['centre'][$day.'OpenTime'])) $this->data['centre'][$day.'OpenTime'] = "00:00";
			}
		}
		
		// Define other models so we can access objects from the database
		$this->objects_models = array(
			"users" => $this->users_model,
			"teams" => $this->teams_model,
			"tournament_actors" => $this->tournament_actors_model
		);
	}
	
	public function obj2arr($obj) {
		if(is_object($obj)) $obj = (array) $obj;
		if(is_array($obj)) {
			$new = array();
			foreach($obj as $key => $val) {
				$new[$key] = obj2arr($val);
			}
		}
		else $new = $obj;
		return $new;       
	}
		
	public function datetime_to_standard($inDateTime) {
		try{
			if(empty($inDateTime)) return $inDateTime;
			if(is_object($inDateTime)) return $inDateTime->format(DATE_TIME_FORMAT);
			$dateTime = new DateTime($inDateTime);
			return $dateTime->format(DATE_TIME_FORMAT);
		} catch (Exception $e) {
			//$this->form_validation->set_message('datetime_check', $e->getMessage());
			return $inDateTime;
		}
	}
	public function datetime_to_unix($inDateTime) {
		try{
			if(empty($inDateTime)) return $inDateTime;
			if(is_object($inDateTime)) return $inDateTime->format(DATE_TIME_UNIX_FORMAT);
			$dateTime = new DateTime($inDateTime);
			return $dateTime->format(DATE_TIME_UNIX_FORMAT);
		} catch (Exception $e) {
			//$this->form_validation->set_message('datetime_check', $e->getMessage());
			return $inDateTime;
		}
	}
	public function datetime_to_public($inDateTime) {
		try{
			if(empty($inDateTime)) return $inDateTime;
			if(is_object($inDateTime)) return $inDateTime->format(PUBLIC_DATE_TIME_FORMAT);
			$dateTime = new DateTime($inDateTime);
			return $dateTime->format(PUBLIC_DATE_TIME_FORMAT);
		} catch (Exception $e) {
			//$this->form_validation->set_message('datetime_check', $e->getMessage());
			return $inDateTime;
		}
	}
	public function datetime_to_public_date($inDateTime) {
		try{
			if(empty($inDateTime)) return $inDateTime;
			if(is_object($inDateTime)) return $inDateTime->format(PUBLIC_DATE_FORMAT);
			$dateTime = new DateTime($inDateTime);
			return $dateTime->format(PUBLIC_DATE_FORMAT);
		} catch (Exception $e) {
			//$this->form_validation->set_message('datetime_check', $e->getMessage());
			return $inDateTime;
		}
	}
	public function datetime_to_public_time($inDateTime) {
		try{
			if(empty($inDateTime)) return $inDateTime;
			if(is_object($inDateTime)) return $inDateTime->format(PUBLIC_TIME_FORMAT);
			$dateTime = new DateTime($inDateTime);
			return $dateTime->format(PUBLIC_TIME_FORMAT);
		} catch (Exception $e) {
			//$this->form_validation->set_message('datetime_check', $e->getMessage());
			return $inDateTime;
		}
	}
	
	public function generatePassword($length = 9, $available_sets = 'lud') {
		$sets = array();
		if(strpos($available_sets, 'l') !== false)
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'u') !== false)
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'd') !== false)
			$sets[] = '23456789';
		if(strpos($available_sets, 's') !== false)
			$sets[] = '!@#$%&*?';

		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}

		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
			$password .= $all[array_rand($all)];

		$password = str_shuffle($password);

		return $password;
	}
}

?>