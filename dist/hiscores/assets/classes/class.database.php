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

	// Connect to the database with SSL using only .crt file
	public function connectWithSSL()
	{
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->data = $data;
		$this->port = $port;
		$this->cert = $cert;

		$dsn = "mysql:host={$this->host};dbname={$this->data};charset=utf8;port={$this->$port};";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES   => false,
			PDO::MYSQL_ATTR_SSL_CERT     => $this->$cert,
		];

		try {
			$this->conn = new PDO($dsn, $this->user, $this->pass, $options);
			return true;
		} catch (PDOException $e) {
			return false;
		}
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
		return count($stmt->fetchAll(PDO::FETCH_ASSOC));
	}
	
	public function getUser($name) {
		$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE username=:name");
		$stmt->bindParam(":name", $name);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
    public function getAllUsers($skill, $min, $mode = null, $rate = null) {
       $skill = $skill == "overall_xp" ? "total_level" : $skill;
		if ($mode != null && $rate != null) {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE mode=:mode AND combatrate=:rate ORDER BY $skill DESC LIMIT $min, 25");
			$stmt->bindParam(":mode", $mode);
			$stmt->bindParam(":rate", $rate);
		} else if ($mode != null && $rate == null) {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE mode=:mode ORDER BY $skill DESC LIMIT $min, 25");
			$stmt->bindParam(":mode", $mode);
		} else if ($mode == null && $rate != null) {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE combatrate=:rate ORDER BY $skill DESC LIMIT $min, 25");
			$stmt->bindParam(":rate", $rate);
		} else {
			$stmt = $this->conn->prepare("SELECT * FROM $this->table ORDER BY $skill DESC LIMIT $min, 25");
		}
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getRank($user, $skill, $mode) {
		$skill = strtolower($skill)."_xp";
		$stmt = $this->conn->prepare("SELECT (SELECT COUNT(*) FROM hs_users WHERE mode = :mode AND ($skill) >= (u.$skill)) AS rank FROM hs_users u WHERE username = :user AND mode = :mode2 LIMIT 1");
        $stmt->bindParam(":user", $user);
		$stmt->bindParam(":mode", $mode);
		$stmt->bindParam(":mode2", $mode);
		$stmt->execute();
		return $stmt->fetchColumn();
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