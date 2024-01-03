<?php
	$host = 'dawn-db-do-user-1648532-0.c.db.ondigitalocean.com';
	$user = 'dawn_web';
	$pass = 'AVNS_i6EjdrHsvluB9k5W1WA';
	$data = 'dawn_web';
	$port = 25060;
	$ssl_cert = __DIR__ . '/secure/ca-certificate.crt';

	// Create connection
	$conn = mysqli_init();

	// Set SSL options
	mysqli_ssl_set($conn, null, null, $ssl_cert, null, null);

	// Check connection
	if (!$conn->real_connect($host, $user, $pass, $data, $port)) {
		die("Connection failed: " . $conn->connect_error);
	}

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$query = "SELECT * FROM hs_users ORDER BY overall_xp DESC LIMIT 10 "; //You don't need a ; like you do in SQL
	$result = $conn->query($query);

	if ($result->num_rows > 0) {
  // output data of each row
		while($row = $result->fetch_assoc()) {
		    switch($row["mode"]) {
            case 3:
			    $mode = '<img src="highscores/assets/img/classic.png" width="16" height="16"/>';
				break;
			case 1:
				$mode =  '<img src="highscores/assets/img/ironman.png" width="16" height="16"/>';
				break;
			case 2:
				$mode =  '<img src="highscores/assets/img/hardcore.png"width="16" height="16"/>';
				break;
			case 4:
				$mode =  '<img src="highscores/assets/img/classic.png" width="16" height="16"/><img src="assets/img/ironman.png" width="16" height="16"/>';
				break;
			case 5:
				$mode =  '<img src="highscores/assets/img/classic.png" width="16" height="16"/> <img src="assets/img/hardcore.png" width="16" height="16" />';
				break;
			default:
			    $mode = '';
				break;	        
	        }
	        switch($row["rights"]) {
            case 1:
			    $rank = '<img src="highscores/assets/img/mod.gif" width="16" height="16"/>';
				break;
			case 5:
				$rank =  '<img src="highscores/assets/img/5.png" width="16" height="16"/>';
				break;
			case 6:
				$rank =  '<img src="highscores/assets/img/6.png" width="16" height="16"/>';
				break;
			case 7:
				$rank =  '<img src="highscores/assets/img/7.png" width="16" height="16" />';
				break;
			case 8:
				$rank =  '<img src="highscores/assets/img/8.png" width="16" height="16" />';
				break;
			case 9:
				$rank =  '<img src="highscores/assets/img/9.png" width="16" height="16" />';
				break;
			default:
			    $rank = '';
				break;	        
	        }
	    
			echo "<li class='hiscores-list-item'><a href='https://dawnps.com/hiscores/index.php?user=" . $row["username"] . "' class='hiscores-username'>".$mode." ".$rank." " . $row["username"] . "</a> <div>" . number_format($row["overall_xp"]) . "</div></li>";
		}
	} else {
		echo "<li class='hiscores-list-item'><a href='#' '>Calum</a><div>1234567891011</div></li>";
	}
	$conn->close();
?>
