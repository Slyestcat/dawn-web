<?php
class Database {
	
	private $conn;
	private $host;
	private $user;
	private $pass;
	private $data;
	private $port;
	private $cert;
	
	private $table;

	public function __construct($host, $user, $pass, $data){
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->data = $data;
	}

	public function connectWithSSL() {
		$this->conn = new mysqli(
			$this->host,
			$this->user,
			$this->pass,
			$this->data,
			$this->port
		);
	
		if ($this->conn->connect_error) {
			// Log the connection error
			error_log("Connection Error: " . $this->conn->connect_error);
			return false;
		}
	
		// Set SSL options
		mysqli_ssl_set(
			$this->conn,
			null,   // SSL key file (not needed)
			$this->cert, // Path to your SSL certificate file
			null,   // SSL certificate authority file (not needed)
			null,   // SSL key passphrase (not needed)
			null    // SSL cipher list (not needed)
		);
	
		return true;
	}
	
	
	
	public function connect() {
		try {
			$this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->data.';charset=utf8', $this->user, $this->pass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function setTable($table) {
		$this->table = $table;
	}
	
	public function countUsers() {
		$stmt = $this->conn->prepare("SELECT * FROM $this->table LIMIT 500");
		$stmt->execute();
	
		// Fetch all rows as an associative array
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		// Return the count of rows
		return count($rows);
	}
	
	public function getUser($name) {
		$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE username = ?");
		$stmt->bindParam(1, $name, PDO::PARAM_STR);
		
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
		return ($row !== false) ? $row : false;
	}
	
	public function getAllUsers($skill, $min, $mode = null, $rate = null) {
		$skill = ($skill == "overall_xp") ? "total_level" : $skill;
	
		if ($mode != null && $rate != null) {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE mode = ? AND combatrate = ? ORDER BY $skill DESC LIMIT ?, 25");
			$stmt->bindParam(1, $mode, PDO::PARAM_STR);
			$stmt->bindParam(2, $rate, PDO::PARAM_INT);
			$stmt->bindParam(3, $min, PDO::PARAM_INT);
		} else if ($mode != null && $rate == null) {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE mode = ? ORDER BY $skill DESC LIMIT ?, 25");
			$stmt->bindParam(1, $mode, PDO::PARAM_STR);
			$stmt->bindParam(2, $min, PDO::PARAM_INT);
		} else if ($mode == null && $rate != null) {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE combatrate = ? ORDER BY $skill DESC LIMIT ?, 25");
			$stmt->bindParam(1, $rate, PDO::PARAM_INT);
			$stmt->bindParam(2, $min, PDO::PARAM_INT);
		} else {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table ORDER BY $skill DESC LIMIT ?, 25");
			$stmt->bindParam(1, $min, PDO::PARAM_INT);
		}
	
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		return $rows;
	}
	
	public function getRank($user, $skill, $mode) {
		$skill = strtolower($skill) . "_xp";
	
		$query = "SELECT (SELECT COUNT(*) FROM " . $this->table . " WHERE mode = ? AND ($skill) >= (u.$skill)) AS `rank` FROM " . $this->table . " u WHERE username = ? AND mode = ? LIMIT 1";
	
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $mode, PDO::PARAM_STR);
		$stmt->bindParam(2, $user, PDO::PARAM_STR);
		$stmt->bindParam(3, $mode, PDO::PARAM_STR);
		$stmt->execute();
		$rank = $stmt->fetchColumn();
		$stmt->close();
	
		return $rank;
	}
	
	
	function getImage($mode = NULL, $rank = NULL) {
	    switch($mode) {
            case 3:
			    $mode = '<img src="assets/img/classic.png" width="16" height="16"/>';
				break;
			case 1:
				$mode =  '<img src="assets/img/ironman.png" width="16" height="16"/>';
				break;
			case 2:
				$mode =  '<img src="assets/img/hardcore.png"width="16" height="16"/>';
				break;
			case 4:
				$mode =  '<img src="assets/img/classic.png" width="16" height="16"/><img src="assets/img/ironman.png" width="16" height="16"/>';
				break;
			case 5:
				$mode =  '<img src="assets/img/classic.png" width="16" height="16"/> <img src="assets/img/hardcore.png" width="16" height="16" />';
				break;
			default:
			    $mode = '';
				break;	        
	    }
	    
	    switch($rank) {
            case 1:
			    $rank = '<img src="assets/img/mod.gif" width="16" height="16"/>';
				break;
			case 5:
				$rank =  '<img src="assets/img/5.png" width="16" height="16"/>';
				break;
			case 6:
				$rank =  '<img src="assets/img/6.png" width="16" height="16"/>';
				break;
			case 7:
				$rank =  '<img src="assets/img/7.png" width="16" height="16" />';
				break;
			case 8:
				$rank =  '<img src="assets/img/8.png" width="16" height="16" />';
				break;
			case 9:
				$rank =  '<img src="assets/img/9.png" width="16" height="16" />';
				break;
			default: 
			    $rank = '';
				break;	        
	    }
	    
	    return $rank.' '.$mode;
	}
	
}
?>