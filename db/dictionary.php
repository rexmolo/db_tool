<?php
use mh\pdo_driver;
include '../pdo/pdo_driver.php';
include '../config/db_dictionary.php';

class dictionary {

	private $tables = array();
	private $tablesDetail = array();
	private $column_detail= array();

	private $database;

	private $pdoDB;

	public function __construct() {
	}

	public function initPdo( $dbConfig ) {
		$this->pdoDB = new pdo_driver();
		$this->pdoDB->initDBParams($dbConfig);
		$this->database = $dbConfig['db'];
	}


	/**
	 * 选择数据库
	 */
	public function selectDB() {
		$html  = '<!DOCTYPE html><html>';
		$html .= '<head>';
		$html .= '</head>';
		$html .= '<body>';
		$html .= '<form method="post" action="/db/dictionary.php">';
		$html .= '<select name="db">';
		$html .= '<option value="1">你的select菜单</option>';
		$html .= '<option value="0">本地数据库</option>';
		$html .= '</select>';
		$html .= '	 <input type="submit" value="生成" />';
		$html .= '</form>';
		$html .= '</body></html>';
		echo $html;
	}

	/**
	 * 选择数据库
	 * @param $selectedDB
	 */
	public function switchDB( $selectedDB ) {
		switch( $selectedDB ) {
			case 1:
				$dbConfig = 'usersinfo';
				break;
			case 2:
				$dbConfig = 'comsinfo';
				break;
			case 3:
				$dbConfig = 'chart';
				break;
			case 4:
				$dbConfig = 'hr_ucenter';
				break;
			case 0:
				$dbConfig = 'local';
				break;
		}
		return $dbConfig;
	}

	/**
	 * 取出表名
	 */
	public function fetchTablesDetail() {
		$sql = "SELECT
					TABLE_NAME,
					ENGINE,
					CREATE_TIME,
					TABLE_COMMENT
				FROM
					information_schema. TABLES
				WHERE
					TABLE_SCHEMA = '$this->database'";
		$this->tables = $this->pdoDB->querySQL($sql);
	}

	/**
	 * 获取表详情
	 */
	public function fetchColumnDetail() {
		foreach( $this->tables as $t ) {
			$sql = "SELECT
							COLUMN_NAME,
							COLUMN_TYPE,
							COLUMN_DEFAULT,
							IS_NULLABLE,
							DATA_TYPE,
							CHARACTER_MAXIMUM_LENGTH,
							EXTRA,
							COLUMN_COMMENT
						FROM
							information_schema. COLUMNS
						WHERE
							TABLE_NAME = '{$t['TABLE_NAME']}'
						AND TABLE_SCHEMA = '$this->database'";
			$re[$t['TABLE_NAME']] = $this->pdoDB->querySQL($sql);
			$this->column_detail = $re;
		}
	}


	/**
	 * 生成每一个table的html
	 * @return string
	 */
	public function generateTable() {
		$html = '';
		foreach ( $this->tables as $k => $v ) {
			// $html .= '<p><h2>'. $v['TABLE_COMMENT'] . '&nbsp;</h2>';
			$html .= '<table  border="1" cellspacing="0" cellpadding="0" align="center">';
			$html .= '<caption>' . $v ['TABLE_NAME'] . '  ' . $v ['TABLE_COMMENT'] . '</caption>';
			$html .= '<tbody><tr><th>字段名</th><th>数据类型</th><th>默认值</th>
							<th>允许非空</th>
							<th>自动递增</th><th>备注</th></tr>';
			$html .= '';

			foreach ( $this->column_detail[$v['TABLE_NAME']] as $f ) {
				$html .= '<tr><td class="c1">' . $f ['COLUMN_NAME'] . '</td>';
				$html .= '<td class="c2">' . $f ['COLUMN_TYPE'] . '</td>';
				$html .= '<td class="c3">&nbsp;' . $f ['COLUMN_DEFAULT'] . '</td>';
				$html .= '<td class="c4">&nbsp;' . $f ['IS_NULLABLE'] . '</td>';
				$html .= '<td class="c5">' . ($f ['EXTRA'] == 'auto_increment' ? '是' : '&nbsp;') . '</td>';
				$html .= '<td class="c6">&nbsp;' . $f ['COLUMN_COMMENT'] . '</td>';
				$html .= '</tr>';
			}
			$html .= '</tbody></table></p>';
		}

		return $html;
	}


	/**
	 * 生成数据库手册
	 */
	public function db_handbook() {
		$html = $this->generateTable();
		echo '<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<style>
						body,td,th {font-family:"宋体"; font-size:12px;}
						table{border-collapse:collapse;border:1px solid #CCC;background:#6089D4;}
						table caption{text-align:left; background-color:#fff; line-height:2em; font-size:16px; font-weight:bold; }
						table th{text-align:left; font-weight:bold;height:26px; line-height:25px; font-size:16px; border:3px solid #fff; color:#ffffff; padding:5px;}
						table td{height:25px; font-size:12px; border:3px solid #fff; background-color:#f0f0f0; padding:5px;}
						.c1{ width: 150px;}
						.c2{ width: 130px;}
						.c3{ width: 70px;}
						.c4{ width: 80px;}
						.c5{ width: 80px;}
						.c6{ width: 300px;}
					</style>
					</head>
					<body>';
//		echo '<h1 style="text-align:center;">' . 数据库字典 . '</h1>';
		echo $html;
		echo '</body></html>';

	}

}

$b = new dictionary();
//选择数据库
$b->selectDB();
if( isset($_POST['db']) ) {
	$selectedValue = $_POST['db'];
	$selectedDBName= $b->switchDB($selectedValue);
	$b->initPdo( $db[$selectedDBName]);
	$b->fetchTablesDetail();
	$b->fetchColumnDetail();
	$b->db_handbook();
}

