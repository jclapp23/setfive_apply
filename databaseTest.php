<?php 

require_once "DB.class.php";
require_once "config.class.php";

class DatabaseTest extends PHPUnit_Framework_TestCase
{
	
	public function testGetConnection(){
		$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
		$con = $db->getConnection();
		$this->assertInstanceOf( 'PDO', $con, "getConnection did not return a PDO object." );
	}
	
	/**
	 * @depends testGetConnection
	 */
	public function testInsertRow(){		
		$pdo = $this->getPDO();
		$pdo->query("TRUNCATE user");
		
		$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
		$db = $db->insertRow( "user", array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "5") );
		$this->assertInstanceOf('DB',$db,'insertRow did not return DB Object');

		$row = $pdo->query("SELECT COUNT(*) FROM user WHERE id = 5")->fetchAll();
		
		$this->assertCount( 1, $row, "Expected 1 row in user. Got " . count($row) );
	}
	
	/** 
	 * @depends testInsertRow
	 */	
	public function testLastInsertId(){

		$pdo = $this->getPDO();
		$pdo->query("TRUNCATE user");
		
		$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
		$rowId = $db->insertRow( "user", 
						array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "5") )
					->getLastInsertId();
		
		$row = $pdo->query("SELECT * FROM user WHERE id = 5")->fetchAll();		
		
		$this->assertEquals( $row[0]["id"], $rowId, "Last insert id does not match MySQL value." );
	}
	
	/**
	 * @depends testInsertRow
	 */
	public function testBulkInsert(){
		$pdo = $this->getPDO();
		$pdo->query("TRUNCATE user");
		
		$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
		$db->bulkInsertRows( "user",
				 		array(array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "5"),
				 			  array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "7"),
				 			  array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "9")));
		
		$row = $pdo->query("SELECT COUNT(*) AS c FROM user")->fetchAll();
		
		$this->assertEquals( $row[0]["c"], 3, "Number of MySQL results does not match expected." );
	}
	
	/**
	 * @depends testBulkInsert
	 */
	public function testFindById(){
		$pdo = $this->getPDO();
		$pdo->query("TRUNCATE user");
		
		$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
		$rowId = $db->bulkInsertRows( "user",
				array(array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "5"),
						array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "7"),
						array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "9")));
		
		$pdoRows = $pdo->query("SELECT * FROM user")->fetchAll();
		$dbRow = $db->findById("user", $pdoRows[0]["id"]);
		
		$this->assertEquals( $pdoRows[0]["first_name"], $dbRow["first_name"], "Returned row does not match expected." );
	}
	
	/**
	 * @depends testBulkInsert
	 */
	public function testDelete(){

		$pdo = $this->getPDO();
		$pdo->query("TRUNCATE user");
		
		$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
		$rowId = $db->bulkInsertRows( "user",
				array(array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "5"),
						array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "7"),
						array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "9")));
		
		$pdoRows = $pdo->query("SELECT * FROM user")->fetchAll();
		$db->deleteById("user", $pdoRows[0]["id"]);
		
		$countRow = $pdo->query("SELECT COUNT(*) AS c FROM user")->fetchAll();
		$this->assertEquals( $countRow[0]["c"] , 2, "MySQL row count does not match expected." );
	}
	
	/**
	 * @depends testBulkInsert
	 */	
	public function testSelectWhere(){

		$pdo = $this->getPDO();
		$pdo->query("TRUNCATE user");
		
		$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
		$rowId = $db->bulkInsertRows( "user",
				array(array("first_name" => "Larry", "last_name" => "Bateman", "id" => "5"),
						array("first_name" => "Moe", "last_name" => "Bateman", "id" => "7"),
						array("first_name" => "Curly", "last_name" => "Bateman", "id" => "9")));
		
		$rows = $db->selectWhere("user", array("first_name" => "Moe", "id" => "7"));				
		$this->assertCount( 1, $rows, "Number of results does not match expected." );
		
		$rows = $db->selectWhere("user", array("first_name" => "Moe", "id" => "17"));		
		$this->assertCount( 0, $rows, "Number of results does not match expected." );
				
	}
	
	/**
	 * @depends testInsertRow
	 */	
	public function testUpdate(){

		$pdo = $this->getPDO();
		$pdo->query("TRUNCATE user");
		
		$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));
		
		$db->insertRow( "user", array("first_name" => "Patrick", "last_name" => "Bateman", "id" => "5") );		
		$db->updateRow( "user", array("id" => "5"), array("first_name" => "Moe") );
		
		$row = $pdo->query("SELECT * FROM user WHERE id = 5")->fetchAll();
		$this->assertEquals( $row[0]["first_name"], "Moe", "First name did not reflect updated value." );
	}
	
	public function getPDO(){
		return new PDO(Config::$dsn, Config::$username, Config::$password);
	}
}