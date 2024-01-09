<?php
	session_start();
	include 'assets/classes/class.database.php';
	include 'assets/constants.php';
	include 'assets/connect.php';

	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$skill = isset($_GET['skill']) ? $_GET['skill'] : "Overall";

	if (!is_numeric($page) || $page < 1)
		$page = 1;

	if (!isValidSkill($skill))
		$skill = "Overall";

	$mode = isset($_COOKIE['mode']) && is_numeric($_COOKIE['mode']) ? cleanInt($_COOKIE['mode']) : 0;
	$rate = isset($_COOKIE['rate']) && is_numeric($_COOKIE['rate']) ? cleanInt($_COOKIE['rate']) : 0;

	if (isset($_GET['mode']) && is_numeric($_GET['mode'])) {
		setcookie("mode", $_GET['mode']);
		header("Location: index.php?skill=".$skill."");
		exit;
	}
	if (isset($_GET['rate']) && is_numeric($_GET['rate'])) {
		setcookie("rate", $_GET['rate']);
		header("Location: index.php?skill=".$skill);
		exit;
	}

	if (!isset($_GET['user']) && !isset($_GET['other'])) {
		$pages =  ceil($db->countUsers() / 25);

		if ($page > $pages) {
			$page = $pages;
		}
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $skill; ?>Dawn - Hiscores</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/css/font-awesome.css" />
		<link rel="stylesheet" href="assets/css/style.css" />
		<link href="../resources/css/normalize.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/components.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/styles.css" rel="stylesheet" type="text/css">
	</head>
<body>
	
	 <?php require_once("../resources/navigation.php"); ?>
	
	<div class="container body-wrapper" style="margin-bottom: 20px;">
	   
		<div class="col-xs-9">
			<?php
				if (isset($_GET['user'])) {
					include 'assets/temps/highscores/lookup.php';
				} else if (isset($_GET['player']) && isset($_GET['other'])) {
					include 'assets/temps/highscores/compare.php';
				} else {
					include 'assets/temps/highscores/main_table.php';
				}
			?>
		</div>
		<div class="col-xs-3">
			<?php include 'assets/temps/highscores/sidebar.php'; ?>
		</div>
	</div>
	
	<?php require_once("../resources/footer.php"); ?>

</body>
<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
<script src="assets/js/smoothscroll.js"></script>
<script src="assets/js/custom.js"></script>
</html>