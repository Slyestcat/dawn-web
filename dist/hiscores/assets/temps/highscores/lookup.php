<?php
	if (count(get_included_files()) <= 1) {
		exit; // do not remove
	}

	$user = $db->getUser(cleanString($_GET['user']));

	if ($user == null) {
		echo '<div class="alert alert-danger">No results could be found for that user.</div>';
	} else {
		$username = strip_tags($user['username']);
		$username = str_replace("_", " ", $username);
		$username = ucwords($username);

		echo '<div class="news_divider" style="margin-top:15px;margin-bottom:15px;"></div>';

		echo '<div class="page-btns text-center">
				<div class="skill-title">Statistics for '.$db->getImage($user['mode'], $user['rights']).'<span class="text-danger">'.ucwords($username).'</span></div>
				<br /><a target="_blank" href="card.php?user='.(str_replace(" ", "_", $username)).'" class="btn btn-default game-mode-btn next">View Playercard</a>
			  </div>';
?>

<div class="news_divider" style="margin-top:15px;margin-bottom:15px;"></div>
	<table class="table">
		<tr>
			<td style="width:50px;" class="text-center" style="width:50px;">Icon</td>
			<td style="width:120px;">Rank</td>
			<td style="width:120px;" class="text-right">Experience</td>
			<td style="width:120px;" class="text-right">Level</td>
			<td style="width:120px;" class="text-right">To Next Level</td>
			<td style="width:120px;" class="text-right">Until Maxed</td>
		</tr>
	<?php
		foreach (getSkills() as $skill) {

			$experience = $user[strtolower($skill).'_xp'] == 1 ? 0 : $user[strtolower($skill).'_xp'];
			$max = strtolower($skill) == "overall" ? 5000000000 : 200000000;

			$curLevel =  getLevelForXp($experience, $skill);
			
			$nextLevel = $curLevel == 99 || $curLevel == 120 ? 0 : getXpForLevel($curLevel + 1);
			$remaining = $curLevel == 99 || $curLevel == 120 ? 0 : ($nextLevel - $experience);

			echo '<tr>';
			echo '<td style="width:50px;" class="text-center"><img src="assets/img/skill_icons/'.$skill.'-icon.png"/></td>';
			echo '<td style="width:120px;">'.$db->getRank($user['username'], $skill, $user['mode']).'</td>';
			echo '<td class="text-right" style="width:120px;">'.number_format($experience).'</td>';
			echo '<td style="width:120px;" class="text-right">'.number_format(strtolower($skill) == "overall" ? getTotalLevel($user) : $curLevel).'</td>';
			echo '<td style="width:120px;" class="text-right">'.number_format($remaining).'</td>';
			echo '<td style="width:120px;" class="text-right">'.number_format($max - $experience).'</td>';
			echo '</tr>';

		}
	?>
	</table>

<?php } ?>