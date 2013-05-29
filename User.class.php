<?php 

require_once "DB.class.php";

class User {		
	
	/**
	 * Array containing this user's db values
	 * @var array
	 */
	private $props;
	
	/**
	 * DB class passed in through the construtor
	 * @var DB
	 */
	private $db;
	
	public function __construct(DB $db){
		$this->db = $db;
		$this->props = array_fill_keys(array("id", "first_name", "last_name", "email"), null);    
	}
	
	/**
	 * Persists the user to the database. 
	 * If the $this->props["id"] field is set, it'll do an update otherwise an INSERT is performed.
	 * @return User 
	 */
	public function save(){		
    	if (isset($this->props["id"]))
    	{
    		echo "update";
    		$user = $this->db->updateRow('user', $this->props , array("id" => $this->props["id"]) );
    		var_dump($user);
    	}else{
    		echo "insert";
    		$user = $this->db->insertRow('user', $this->props);
    	}
		return $this;
	}
	
	/**
	 * Sets the user's first name field
	 * @param string $val
	 * @return User
	 */
	public function setFirstName($val){
		$this->props["first_name"] = $val;
		return $this;
	}
	
	/**
	 * Returns the user's first name
	 * @return string
	 */
	public function getFirstName(){
    return $this->props['first_name'];
	}
	
	
  /**
   * Sets the user's first last field
   * @param string $val
   * @return User
   */
  public function setLastName($val){
    $this->props["last_name"] = $val;
    return $this;
  }
  
  /**
   * Returns the user's last name
   * @return string
   */
  public function getLastName(){
	return $this->props['last_name'];
  }
  
  /**
   * Sets the user's email field
   * @param string $val
   * @return User
   */
  public function setEmail($val){
    $this->props["email"] = $val;
    return $this;
  }
  
  /**
   * Returns the user's email
   * @return string
   */
  public function getEmail(){
    return $this->props["email"];
  }
  
  /**
   * Sets the user's id field
   * @param string $val
   * @return User
   */
  public function setId($val){
    $this->props["id"] = $val;
    return $this;
  }
  
  /**
   * Returns the user's id id
   * @return string
   */
  public function getId(){
    $row = $this->db->findById('user',$this->props['id']);
    echo $this->props['id'];
    var_dump($row);
    return $row->id;
  }
  
	/**
	 * Only used internally. Sets the $this->prop array with values from the database.  
   * Use this when hydrating this object from a database row.
	 * @param array $properties
	 * @return User
	 */
	public function hydrate(array $properties){		
		$this->props=$properties;
	}
}
