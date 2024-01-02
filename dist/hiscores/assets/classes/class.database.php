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

	public function __construct($host, $user, $pass, $data, $port, $cert) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->data = $data;
		$this->port = $port;
		$this->cert = $cert;
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
	
		$result = $stmt->get_result(); // Get the result set
	
		// Fetch all rows as an associative array
		$rows = [];
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
	
		// Return the count of rows
		return count($rows);
	}
	
	public function getUser($name) {
		$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE username = ?");
		$stmt->bind_param("s", $name); // "s" represents a string, adjust if needed
	
		$stmt->execute();
		$result = $stmt->get_result();
	
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			return $row;
		} else {
			return false;
		}
	}
	
    public function getAllUsers($skill, $min, $mode = null, $rate = null) {
		$skill = $skill == "overall_xp" ? "total_level" : $skill;
	
		if ($mode != null && $rate != null) {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE mode = ? AND combatrate = ? ORDER BY $skill DESC LIMIT ?, 25");
			$stmt->bind_param("sii", $mode, $rate, $min);
		} else if ($mode != null && $rate == null) {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE mode = ? ORDER BY $skill DESC LIMIT ?, 25");
			$stmt->bind_param("si", $mode, $min);
		} else if ($mode == null && $rate != null) {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE combatrate = ? ORDER BY $skill DESC LIMIT ?, 25");
			$stmt->bind_param("ii", $rate, $min);
		} else {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table ORDER BY $skill DESC LIMIT ?, 25");
			$stmt->bind_param("i", $min);
		}
	
		$stmt->execute();
		$result = $stmt->get_result();
	
		$rows = [];
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
	
		return $rows;
	}
	

	public function getRank($user, $skill, $mode) {
		$skill = strtolower($skill) . "_xp";
	
		$query = "SELECT (SELECT COUNT(*) FROM " . $this->table . " WHERE mode = ? AND ($skill) >= (u.$skill)) AS `rank` FROM " . $this->table . " u WHERE username = ? AND mode = ? LIMIT 1";
	
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("sss", $mode, $user, $mode);
		$stmt->execute();
		$stmt->bind_result($rank);
		$stmt->fetch();
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