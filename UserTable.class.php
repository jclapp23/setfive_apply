<?php 

require_once "DB.class.php";
require_once "User.class.php";

class UserTable {
	
	public static $TABLE_NAME = "user";
	
	/**
	 * @var DB
	 */
	private static $DB;
	
	/**
	 * Queries MySQL using the conditions in whereArray (columnName => value) and returns the first user, if found.
	 * If nothing is found, NULL is returned
	 * @param array $whereArray
	 * @return User
	 */
	public static function findOneBy( $whereArray ){
    $ret = current(self::$DB->selectWhere(self::$TABLE_NAME,$whereArray));
    if($ret)
    {
      
      $user = new User(self::$DB);
      $user->hydrate($ret);
      return $user;
    }

    return null;
	}
	
	/**
	 * Sets the DB property
	 * @param DB $db
	 */
	public static function setDB(DB $db){
		self::$DB=$db;
	}
	
}