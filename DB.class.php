<?php 

class DB {
	
	private $configArray;
	private $pdo = null;
	private $lastInsertId = null;
	
	public function __construct(array $configArray){
		$this->configArray = $configArray;
	}
	
	/**
	 * Returns the current PDO connection. Will create it if it doesn't exist.
	 * Uses this->configArray for configuration parameters.
	 * @return PDO
	 */
	public function getConnection(){

		if($this->pdo){
			//connection is already established";
			return $this->pdo;
		} else
			//connection is not established, try to connect to database, if not show error";
			try{
			    $this->pdo = new PDO($this->configArray['dsn'],$this->configArray['username'],$this->configArray['password']);
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return $this->pdo;
			} catch(PDOException $e) {
	            echo $e->getMessage();
	            exit();
			}
	    }
	
	/**
	 * Inserts a row into $TABLE with data from $dataArray.
	 * $dataArray is an associative array containing column name => value, assume column names and table is safe.
	 * Also, sets the lastInsertId with the autoincrement value for the created row
	 * @param string $table
	 * @param array $dataArray
	 * @return DB
	 */
	public function insertRow($table, array $dataArray){

		$columns = '';
        foreach ($dataArray as $key => $value) {
            $columns .= $key . ',';
        }
        $columns = rtrim($columns, ',');
        
        $placeholders = '';
        foreach ($dataArray as $key => $value) {
            $placeholders .= ":$key,";
        }
        $placeholders = rtrim($placeholders, ',');
        
        $stmt = $this->pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        
        foreach ($dataArray as $key => $value) {
            $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
        }
        
        if ($result = $stmt->execute($dataArray)) {
            $this->lastInsertId = $this->pdo->lastInsertId();
            return $result;
        } else {
            return FALSE;
        }

	}
	
	/**
	 * Performs a SQL UPDATE using whereArray (columnName => value) 
	 * to set dataArray (columnName => value) on $table
	 * @param unknown_type $table
	 * @param array $whereArray
	 * @param array $dataArray
	 * @return DB
	 */
	public function updateRow($table, array $whereArray, array $dataArray){
       	
       	$pairs = '';
        foreach ($dataArray as $key => $value) {
            $pairs .= "$key=:$key,";
        }
        $pairs = rtrim($pairs, ',');
        
        foreach ($whereArray as $key => $value) {
            $column = $key;
        }

        $stmt = $this->pdo->prepare("UPDATE $table SET $pairs WHERE $column = :value");
        
        foreach ($whereArray as $key => $value) {
            $stmt->bindValue(":value", $value);
        }
       
        foreach ($dataArray as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();

	}
	
	/**
	 * Inserts several rows into $table. 
	 * $dataArray is assumed to be an array of k => v arrays for the values to insert, assume table and column name values are safe, and consistent across all of the array. 
	 * @param unknown_type $table
	 * @param array $dataArray
	 */
	public function bulkInsertRows($table, array $dataArray){
		
		$columns = '';
        
		foreach ($dataArray[0] as $key => $value){
	            $columns .= $key . ',';
	    }

        $columns = rtrim($columns, ',');
        
        $placeholders = '';
        foreach ($dataArray[0] as $key => $value){
            $placeholders .= ":$key,";
        }
        $placeholders = rtrim($placeholders, ',');
        
        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");

	    foreach($dataArray as $insertRow){
  		   //now loop through each inner array to match binded values
   		   foreach($insertRow as $key => $value){
       			$stmt->bindValue(":$key", $value, PDO::PARAM_STR);
   			 }
   			$result = $stmt->execute();
		}

		$this->pdo->commit();

        if ($result) {
            $this->lastInsertId = $this->pdo->lastInsertId();
            return $result;
        } else {
            return FALSE;
        }
	}
	
	/**
	 * Finds and returns a row by primary key in $table
	 * Returns null if no results are found
	 * @param unknown_type $id
	 */
	public function findById( $table, $id ){
		$stmt = $this->pdo->prepare("SELECT * FROM $table WHERE id = :id LIMIT 1");
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
	}
	
	/**
	 * Deletes a row in $table with id equal to $id
	 * @param unknown_type $table
	 * @param unknown_type $id
	 */
	public function deleteById( $table, $id ){
		$stmt = $this->pdo->prepare("DELETE FROM $table WHERE id = :id");
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
	}

	/**
	 * Performs a SELECT * WHERE on $table and returns the rows
	 * $whereArray is assumsed to be a associative array with column name => column value for there WHERE
	 * NOTE: You'll want to use PDO::FETCH_ASSOC truuuust me
	 * @return array
	 */	
	public function selectWhere($table, array $whereArray){
		
		foreach ($whereArray as $key => $value) {
            $column = $key;
        }

		$stmt = $this->pdo->prepare("SELECT * FROM $table WHERE $column = :value");

		foreach ($whereArray as $key => $value) {
            $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        }

        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Returns the $this->lastInsertId value
	 */
	public function getLastInsertId(){
      return $this->lastInsertId;
	}
}
