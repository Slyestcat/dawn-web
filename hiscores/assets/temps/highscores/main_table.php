<?php
	if (count(get_included_files()) <= 1) {
		exit; # do not remove
	}
	
	$skill_sort = strtolower($skill).'_xp';
	$pages =  ceil($db->countUsers() / 25);
	
	if ($pages > 20)
		$pages = 20;
	
	echo '<div class="page-btns">';
	if (!isset($_GET['user']) && !isset($_GET['other']) && enable_modes) {
		$mode_keys = array_keys($modes);
		echo '<div class="dropdown pull-right">
              <a class="game-mode btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">Mode '.$modes[$mode].'</a>
              <ul class="dropdown-menu mode-dropdown" aria-labelledby="dropdownMenuLink">';
		for ($i = 0; $i < count($modes); $i++) {
			$active = $mode == $mode_keys[$i] ? "active" : "";
			echo '<li class="game-mode"><a href="index.php?mode='.$mode_keys[$i].'&skill='.$skill.'" style="width:100px;">'.$modes[$mode_keys[$i]].'</a></li>';
		}
	echo '</ul></div>';
	//echo '<hr style="border-color:#333;">';
	}
	echo '<div class="skill-title" style="color: #e2ca96;"><img src="assets/img/skill_icons/'.ucwords($skill).'-icon.png"/> '.ucwords($skill).' Highscores - Page '.$page.'</div>';
		

	
	echo '</div>';
?>
<table class="table">
	<tr>
		<td style="width:30px;"></td>
		<td style="width:30px;"></td>
		<?php if (in_array($mode, $modes_double)) { echo '<td style="width:30px;"></td><td style="width:30px;"></td>'; }?>
		<?php if (in_array($mode, $modes_single)) { echo '<td style="width:30px;"></td>'; }?>
		<td style="width:130px;">Username</td>
		<td style="width:130px;"class="text-right">Combat</td>
		<td style="width:130px;"class="text-right">Total Level</td>
		<td style="width:130px;"class="text-right">Experience</td>
		<td style="width:130px;"class="text-right">Completion</td>
	</tr>
	<?php
		$min = $page == 1 ? 0 : ($page * 25) - 25;

		$users = null;
		$rate  = $rate == 1 ? null : $rate;
		if (enable_modes && enable_xp) {
			$users = $db->getAllUsers($skill_sort, $min, $mode, $rate);
		} else if (enable_modes) {
			$users = $db->getAllUsers($skill_sort, $min, $mode, null);
		} else if (enable_xp) {
			$users = $db->getAllUsers($skill_sort, $min, null, $rate);
		} else {
			$users = $db->getAllUsers($skill_sort, $min);
		}
        
        $rank = $min + 1;
        
		foreach($users as $user) {

			$username = ucwords(strip_tags($user['username']));
			$username = str_replace("_", " ", $username);

			echo '<tr>';

			switch ($user['rights']) {
				case 1:
					echo '<td style="width:30px;text-align:right;"><img src="assets/img/mod.gif" style="vertical-align:middle"/></td>';
					break;
				case 5:
					echo '<td style="width:30px;text-align:right;"><img src="assets/img/5.png" width="16" height="16" style="vertical-align:middle"/></td>';
					break;
				case 6:
					echo '<td style="width:30px;text-align:right;"><img src="assets/img/6.png" style="vertical-align:middle"/></td>';
					break;
				case 7:
					echo '<td style="width:30px;text-align:right;"><img src="assets/img/7.png" style="vertical-align:middle"/></td>';
					break;
				case 8:
					echo '<td style="width:30px;text-align:right;"><img src="assets/img/8.png" style="vertical-align:middle"/></td>';
					break;
				case 9:
					echo '<td style="width:30px;text-align:right;"><img src="assets/img/8.png" style="vertical-align:middle"/></td>';
					break;
				default:
					echo '<td style="width:30px;text-align:right;"></td>';
					break;
			}
			if ($mode != 0) {
    			switch ($user['mode']) {
    				case 3:
    					echo '<td style="width:30px;text-align:right;"><img src="assets/img/classic.png" style="vertical-align:middle"/></td>';
    					break;
    				case 1:
    					echo '<td style="width:30px;text-align:right;"><img src="assets/img/ironman.png" width="16" height="16" style="vertical-align:middle"/></td>';
    					break;
    				case 2:
    					echo '<td style="width:30px;text-align:right;"><img src="assets/img/hardcore.png" style="vertical-align:middle"/></td>';
    					break;
    				case 4:
    					echo '<td style="width:30px;text-align:right;"><img src="assets/img/classic.png" style="vertical-align:middle"/></td><td style="width:30px;text-align:right;"><img src="assets/img/ironman.png" width="16" height="16" style="vertical-align:middle"/></td>';
    					break;
    				case 5:
    					echo '<td style="width:30px;text-align:right;"><img src="assets/img/classic.png" style="vertical-align:middle"/></td><td style="width:30px;text-align:right;"><img src="assets/img/hardcore.png" style="vertical-align:middle"/></td>';
    					break;
    				default:
    					break;
    			}
			}
			$exp = $user[$skill_sort];
			$max = strtolower($skill) == "overall" ? 5000000000 : 200000000;
			$perc = (($exp / $max) * 100);
			
			$level = (strtolower($skill) == "overall" ? getTotalLevel($user) : getLevelForXp($exp, $skill));
			echo '<td style="width:30px;">'.$rank.'</td>';
			echo '<td style="width:130px;"><a href="?user='.$user['username'].'">'.$username.'</a></td>';
			echo '<td style="width:130px;" class="text-right">'.number_format(getLevel($user)).'</td>';
			echo '<td style="width:130px;" class="text-right">'.number_format($level).'</td>';
			echo '<td style="width:130px;" class="text-right">'.number_format($exp).'</td>';
			echo '<td style="width:160px;" class="text-right">
					<div class="progress progress-striped active" style="margin:0;text-align:center;">
						<div class="percent">'.number_format($perc, 0).'%</div>
						<div class="progress-bar" style="width: '.$perc.'%;"></div>
					</div>
				  </td>';
			echo '</tr>';
			$rank++;
		}
	?>
</table>

<?php
	if ($page != 1) {
		echo '<a href="?skill='.$skill.'&page='.($page - 1).'" class="btn btn-default game-mode-btn">Prev Page</a>';
	}
	if ($pages > 1 && $page < $pages) {
		echo '<a href="?skill='.$skill.'&page='.($page + 1).'" class="btn btn-default game-mode-btn">Next Page</a>';
	}
?>