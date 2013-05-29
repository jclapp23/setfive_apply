<?php 

require_once "DB.class.php";
require_once "User.class.php";
require_once "UserTable.class.php";
require_once "config.class.php";

class UserTest extends PHPUnit_Framework_TestCase {

  public function testSetters(){
    
    $db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
    $user = new User($db);
    
    $setters = array("setFirstName", "setLastName","setEmail");
    
    foreach( $setters as $st ){
      $res = $user->$st("something");
      $this->assertSame( $res , $user );
    }
    
  }
  
  public function testGetters(){
    
    $db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
    $user = new User($db);
    
    $setters = array("setFirstName", "setLastName", "setEmail");
    
    foreach( $setters as $st ){
      $get = str_replace("set", "get", $st);
      $user->$st("something");
      $res = $user ? $user->$get() : null;
      $this->assertEquals( $res, "something", $get . " did not return expected value." );
    }
    
  }


  /**
   * @depends testSetters
   */
  public function testSave(){

    $pdo = $this->getPDO();
    $pdo->query("TRUNCATE user");   
    
    $db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
    
    $user = new User($db);
    $user->setFirstName("Patrick");
    $user->setLastName("Bateman");
    $user->setEmail("pbateman@setfive.com");
    $user->save();
    
    $rows = $pdo->query("SELECT * FROM user")->fetchAll();
    
    $this->assertEquals( $rows[0]["email"], "pbateman@setfive.com", "MySQL row doesn't match expected." );
  }



   /**
    * @depends testSetters
    * @depends testSave
    */
  public function testFindBy(){

    $pdo = $this->getPDO();
    $pdo->query("TRUNCATE user");
    
    $db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
    
    $user = new User($db);
    $user->setFirstName("Patrick");
    $user->setLastName("Bateman");
        $user->setEmail("pbateman@setfive.com");
        $user->save();
    
    UserTable::setDB($db);
    $foundUser = UserTable::findOneBy( array("email" => "pbateman@setfive.com") );
    $this->assertEquals( $foundUser ? $foundUser->getId() : null, $user->getId() );
    
  }  


  /**
   * @depends testFindBy
   * @depends testSetters
   */
	public function testUpdate(){

		$pdo = $this->getPDO();
		$pdo->query("TRUNCATE user");
		
		$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
		
		$user = new User($db);
		$user->setFirstName("Patrick");		
    $this->assertEquals($user->getFirstName(),'Patrick');
		$user->setLastName("Bateman");
		$user->setEmail("pbateman@setfive.com");
		$user->save();
		
		UserTable::setDB($db);
		$foundUser = UserTable::findOneBy( array("email" => "pbateman@setfive.com") );

    $this->assertInstanceOf('User',$foundUser,'UserTable::findOneBy did not return User object');
    
		if( $foundUser ){
		    $foundUser->setLastName("Ramathorn");
		    $foundUser->save();
		}
		
		$rows = $pdo->query("SELECT * FROM user")->fetchAll();
		$this->assertEquals($rows[0]["last_name"], "Ramathorn", "MySQL value does not match expected.");
	}
	
 

	
	
	public function getPDO(){
		return new PDO(Config::$dsn, Config::$username, Config::$password);
	}
}