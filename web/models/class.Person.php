<?php
require_once('Path.php');
require_once(Path::models() . 'config.php');

class Person {
	private $id;
	private $personKindID; // int id of person type
	//private $personKind; // string person type
	private $username; 
	private $name;
	private $phone;

	//------------------------ GETTERS ------------------------
	public function getID(){
		return $this->id;
	}

	public function getPersonKindID(){
		return $this->personKindID;
	}

	/*
	public function getPersonKind(){
		return $this->personKind;
	}*/

	public function getUsername(){
		return $this->username;
	}

	public function getName(){
		return $this->name;
	}
	
	public function getPhone(){
		return $this->phone;
	}

	//------------------------ SETTERS ------------------------

	public function setID($id){
		$this->id = $id;
	}

	public function setPersonKindID($personKindID){
		$this->personKindID = $personKindID;
	}

	/*
	public function setPersonKind($personKind){
		$this->personKind = $personKind;
	}*/

	public function setUsername($username){
		$this->username = $username;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function setPhone($phone){
		$this->phone = $phone;
	}	

	//------------------------ DB METHODS ------------------------

	/*
	*	takes the person object and saves to DB.

	*	@case1 new person | add new record to DB
	*	@case2 old person with ID | update record in DB
	*	@case3 old person with no ID | look up id from username, set local, update record in DB
	*	
	*	@return boolean | false on fail to get id from DB in case 3
	*/
	public function save(){
		$db = new Database();

		//check to see if a person with this username already has a record
		$sql = "SELECT * FROM people WHERE username=?";
		$sql = $db->prepareQuery($sql, $this->username);
		$results = $db->select($sql);

		if(count($results) == 0){ //new person
			//create new DB entry
			$sql = "INSERT INTO people(username, name, phone, personKindID) VALUES(?,?,?,?)";
			$sql = $db->prepareQuery($sql, $this->username, $this->name, $this->phone, $this->personKindID);
			$db->query($sql); 

			//get id from DB
			$sql = "SELECT id FROM people WHERE username=?";
			$sql = $db->prepareQuery($sql, $this->username);
			$results = $db->select($sql);
			if(isset($results[0]['id'])){
				$this->setID($results[0]['id']);	
			}
		} else { //old person
			if(is_null($this->id)){ //old person, new object. no local id yet
				//get id from DB
				if(isset($results[0]['id'])){
					$this->setID($results[0]['id']);	
				} else return false;
			}
			
			//update old DB entry, removing/adding field entries with same username
			$sql = "UPDATE people SET name=?, phone=?, personKindID=? WHERE id=?";
			$sql = $db->prepareQuery($sql,$this->name, $this->phone, $this->personKindID, $this->id);
			$db->query($sql);
		}
	}

	/*
	*	given a person's id, looks up and sets the person's local vars
	* 	@param $id | int person ID in DB
	*	@return bool | only if lookup fails (not in DB)
	*/
	public function fetch($id){
		$db = new Database();
		$sql = "SELECT * FROM people WHERE id=?";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);

		if(count($results) != 0){
			$this->setID($id);
			$this->setPersonKindID($results[0]['personKindID']);
			$this->setUsername($results[0]['username']);
			$this->setName($results[0]['name']);
			$this->setPhone($results[0]['phone']);
		} else return false;
	}

	/*
	*	finds a person type's id for usage in the person object
	*
	*	@param $personKind | string type name to lookup id for
	*
	*	@return int | id if found, bool (false) otherwise
	*/
	/*
	public static function lookupPersonKindID($personKind){
		$db = new Database();
		$sql = "SELECT id FROM personKinds WHERE personKind=?";
		$sql = $db->prepareQuery($sql, $personKind);

		$results = $db->select($sql);
		if(isset($results[0]['id'])){
			return $results[0]['id'];
		} else return false;
	}*/
}


?>