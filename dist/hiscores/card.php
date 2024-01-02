<?php
	include 'assets/constants.php';
	include 'assets/classes/class.database.php';
	include 'assets/classes/class.user.php';

	if (!isset($_GET['user']) || !is_string($_GET['user'])) {
		echo 'nope';
		exit;
	}
	
	$name = preg_replace('/[^A-Za-z0-9 _]/', '', $_GET['user']);
	$fname = strtolower(str_replace(" ", "_", $name));
	$filePath = 'assets/img/cache/'.$fname.'.png';
	
	if (file_exists($filePath)) {
		$curTime = explode(" ", microtime())[1];
		$timeDif = $curTime - filemtime($filePath);
		
		if ($timeDiff < cache_time) {
			$image = imagecreatefrompng($filePath);
			header("Content-type: image/png");
			header("Content-Disposition: filename=".$fname.".png");
			imagepng($image);
			exit;
		}
	}
	
	include 'assets/connect.php';
	
	$result = $db->getUser($name);
	
	if ($result == null) {
		exit;
	}
	
	$user = new User($result);
	
	$image = imagecreatefrompng("assets/sigbg.png");
	$white = imagecolorallocate($image, 255, 255, 255);
	$black = imagecolorallocate($image, 0, 0, 0);
	$rights = array('1', '5', '6', '7', '8', '9');
	$modes = array(1, 2, 3, 4, 5);
	imagettftext($image, 18, 0, !in_array($result['rights'], $rights) && !in_array($result['mode'], $modes) ? 428 : 440, 38, $white, font, $user->getUsername().' #'.$db->getRank($result['username'], "Overall", $result['mode']));
	imagettftext($image, 14, 0, 430, 65, $white, font, "Level: ".$user->getRealLevel());
	imagettftext($image, 14, 0, 430, 87, $white, font, "Exp: ".number_format($user->getTotalXp()));
	
	$skills = explode(",", skills);
	
	imagettftext($image, 14, 0, 430, 109, $white, font, "Total Lvl: ".number_format($user->getTotalLevel($skills)));
	
	$baseX = 43; 
	$baseY = 25;
	
	for ($i = 0; $i < count($skills); $i++) {
		if ($i == 5 || $i == 10 || $i == 15 || $i == 20) {
			$baseX += 81;
			$baseY = 25;
		}
		
		$level = $user->getLevel($skills[$i + 1]);
		imagettftext($image, 13, 0, $baseX + 1, $baseY + 1, $black, font, $level);
		imagettftext($image, 13, 0, $baseX, $baseY, $white, font, $level);
		$baseY += 28;
	};
	
	list($width, $height) = getimagesize('https://dawnps.com/hiscores/assets/img/5.png');
  
    $new_width = $width * 1.4;
    $new_height = $height * 1.4;
   
    switch ($result['rights']) {
         case 1:
			$image1 = imagecreatefromgif('https://dawnps.com/hiscores/assets/img/mod.png');
            imagecopyresampled($image, $image1, 420, 20, 0, 0, $new_width, $new_height, $width, $height);
			break;
		case 5:
			$image1 = imagecreatefrompng('https://dawnps.com/hiscores/assets/img/5.png');
            imagecopyresampled($image, $image1, 420, 20, 0, 0, $new_width, $new_height, $width, $height);
			break;
		case 6:
			$image1 = imagecreatefrompng('https://dawnps.com/hiscores/assets/img/6.png');
            imagecopyresampled($image, $image1, 420, 20, 0, 0, $new_width, $new_height, $width, $height);
			break;
		case 7:
			$image1 = imagecreatefrompng('https://dawnps.com/hiscores/assets/img/7.png');
            imagecopyresampled($image, $image1, 420, 20, 0, 0, $new_width, $new_height, $width, $height);
			break;
		case 8:
		    $image1 = imagecreatefrompng('https://dawnps.com/hiscores/assets/img/8.png');
            imagecopyresampled($image, $image1, 420, 20, 0, 0, $new_width, $new_height, $width, $height);
			break;
		case 9:
			$image1 = imagecreatefrompng('https://dawnps.com/hiscores/assets/img/9.png');
            imagecopyresampled($image, $image1, 420, 20, 0, 0, $new_width, $new_height, $width, $height);
            break;
		default: 
			break;	 
    }
    if (!in_array($result['rights'], $rights)) {
        switch($result['mode']) {
            case 3: 
                $image1 = imagecreatefrompng('https://dawnps.com/hiscores/assets/img/classic.png');
                imagecopyresampled($image, $image1, 420, 20, 0, 0, $new_width, $new_height, $width, $height);
                break;
            case 1:
                case 4:
		    	$image1 = imagecreatefrompng('https://dawnps.com/hiscores/assets/img/ironman.png');
                imagecopyresampled($image, $image1, 420, 20, 0, 0, $new_width, $new_height, $width, $height);                
                break;
            case 2:
                case 5:
                $image1 = imagecreatefrompng('https://dawnps.com/hiscores/assets/img/hardcore.png');
                imagecopyresampled($image, $image1, 420, 20, 0, 0, $new_width, $new_height, $width, $height);
                break;
            default:
                break;
        }
    }

	header("Content-type: image/png");
	header("Content-Disposition: filename=".$fname.".png");
	
	imagepng($image, $filePath);
	imagepng($image);
?>