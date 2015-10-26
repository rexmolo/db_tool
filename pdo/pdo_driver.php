<?php
namespace mh;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/22
 * Time: 17:36
 */

class pdo_driver {

//	private $host     = '122.4.79.213';
//	private $db       = 'zhiliao_usersinfo';//zhiliao_comsinfo
//	private $userName = 'xzhiliao';
//	private $dbPass   = '^YHNmju7';

	private $host;
	private $db;
	private $userName;
	private $dbPass;
	private $dsn;
	private $pdoDB;

	public function __construct() {}

	/**
	 * 初始化数据库连接参数
	 * @param $dbParams
	 */
	public function initDBParams( $dbParams ) {
		foreach( $dbParams as $k => $v ){
			$this->$k = $v;
		}
		$this->dsn = 'mysql:dbname=' . $this->db .';host='. $this->host .';';
		$this->pdoDB = $this->connect();
		$this->querySQL('SET NAMES utf8');
		return $this->pdoDB;
	}

	/**
	 * 执行连接数据库
	 * @return bool|\PDO
	 */
	public function connect() {
		try {
			return new \PDO( $this->dsn, $this->userName, $this->dbPass );
		} catch ( PDOException $e ) {
			echo 'Connection failed: '. $e->getMessage();
			return false;
		}
	}

	/**
	 * 执行一条SQL语句,返回所有结果集
	 * @param $sql
	 * @return mixed
	 */
	public function querySQL( $sql ) {
		$statement = $this->pdoDB->prepare($sql);
		$statement->execute();
		return $re = $statement->fetchAll(\PDO::FETCH_ASSOC);
	}
}
