<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/22
 * Time: 17:36
 */

class pdo_driver {

	private $host     = '127.0.0.1';
	private $db       = 'laravel5';
	private $userName = 'root';
	private $dbPass   = '';

	private $dsn;

	public function __construct() {
		$this->dsn = 'mysql:dbname=' . $this->db .';host='. $this->host .';';
	}

	public function connect() {
		try {
			return $pdoDB = new PDO( $this->dsn, $this->userName, $this->dbPass );
		} catch ( PDOException $e ) {
			echo 'Connection failed: '. $e->getMessage();
			return false;
		}
	}


//	$sql = 'SHOW TABLES';
//	$statement = $pdoDB->prepare($sql);
//	$statement->execute();
//	$re = $statement->fetchAll();
//	var_dump($re);

}