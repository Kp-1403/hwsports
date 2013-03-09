<?php
class Users_model extends MY_Model {

	public function __construct() {
		// Basic variables which apply to all table operations
		$this->objectIDKey = "userID";
		$this->dataTableName = "userData";
		$this->relationTableName = "users";
    }
	
	/**
	 * Returns all data about a specific user
	 *  
	 * @return array
	 **/
	public function get($ID) {
		// Get all the userData
		return $this->get_object($ID, $this->objectIDKey, $this->dataTableName);
	}
	
	/**
	 * Returns all data about all users at current centre
	 * 
	 * @return array
	 **/
	public function get_all() {
		// Fetch the IDs for everything at the current sports centre
		$IDRows = $this->db->get_where($this->relationTableName, array('centreID' => $this->centreID))->result_array();
		// Create empty array to output if there are no results
		$all = array();
		// Loop through all result rows, get the ID and use that to put all the data into the output array 
		foreach($IDRows as $IDRow) {
			$all[] = $this->get($IDRow[$this->objectIDKey]);
		}
		return $all;
	}
	
	/**
	 * Creates a new tournament with data, using the sport ID as specified.
	 * Returns the ID of the new object if it was successful.
	 * Returns FALSE on any error or insertion failure (including foreign key restraints).
	 *  
	 * @return int
	 **/
	public function insert($data, $relationIDs=array()) {
		return $this->insert_object($data, $this->objectIDKey, $this->dataTableName, $relationIDs);
	}

	/**
	 * Updates data for a specific tournament.
	 * Returns TRUE on success.
	 * Returns FALSE on any error or insertion failure (including foreign key restraints).
	 *
	 * @return boolean
	 **/
	public function update($ID, $data) {
		return $this->update_object($ID, $this->objectIDKey, $data, $this->dataTableName);
	}

	/**
	 * Returns all data about users in a particular tournament
	 *  
	 * @return array
	 **/
	public function get_tournament_users($tournamentID) {
		// Loop through all teams in tournament
		foreach($this->teams_model->get_tournament_teams($tournamentID) as $team) {
			// Loop through all users in team, add to output array
			foreach($team['users'] as $user) {
				$all[] = $user;
			}
		}
		return (empty($all) ? FALSE : $all);
	}
	
	/**
	 * returns an array of teamIDs that the user
	 * is a part of.
	 *
	 * @return array
	 **/
	public function team_memberships($userID){
		$output = array();
		$queryString = 	"SELECT teamID FROM usersTeams WHERE userID = ".$this->db->escape($userID);
		$queryData = $this->db->query($queryString);
		$output = $queryData->result_array();

		$teams = array();
		foreach($output as $row)
			$teams[] = $row['userID'];

		return $teams;
	}
	/**
	 * returns an array of tournamentIDs that the user
	 * is a part of.
	 *
	 * @return array
	 **/
	public function tournament_memberships($userID,$centreID){
		$tournamentIDs = array();

		// Get a list of all teams that the user is associated with.
		$teams = $this->team_memberships($userID);

		// Get a list of all tournaments with the key "teams" or "athletes"
		// defined. We will then iterate through every row checking if 
		// the userID or teamID exist in the thing.

		$tournaments = array();
		$queryString = 	"SELECT T.tournamentID, ".
		 				"MAX(CASE WHEN TD.`key`='teams' THEN value END ) AS teams, ".
		 				"MAX(CASE WHEN TD.`key`='athletes' THEN value END ) AS athletes ".
		 				"FROM tournaments AS T, tournamentData AS TD ".
		 				"WHERE TD.tournamentID = T.tournamentID ".
		 				"AND T.centreID = ".$this->db->escape($centreID);
		$queryData = $this->db->query($queryString);
		$tournaments = $queryData->result();

		foreach($tournaments as $tournament) 
			if(isset($tournaments['teams'])){
				$tournamentTeams = explode(",",$tournaments['teams']);
				$intersection = array_intersect($teams, $tournamentTeams);
				if(!empty($intersection)){
					$tournamentIDs[] = $tournamentID;
				}
			} else if(isset($tournaments['athletes'])){
				$tournamentAthletes = explode(",",$tournaments['athletes']);
				$intersection = in_array($userID, $tournamentAthletes);
				if(!empty($intersection)){
					$tournamentIDs[] = $tournamentID;
				}
			}
		
		return $tournamentIDs;
	}
}