<?php
	if (count(get_included_files()) <= 1) {
		exit; // do not remove
	}

	$user1 = $db->getUser(cleanString($_GET['player']));
	$user2 = $db->getUser(cleanString($_GET['other']));

	if ($user1 == null && $user2 != null) {
		echo '<div class="alert alert-danger">No results found for '.cleanString($_GET['other']).'</div>';
	} else if ($user1 != null && $user2 == null) {
		echo '<div class="alert alert-danger">No results found for '.cleanString($_GET['other']).'</div>';
	} else if ($user1 == null && $user2 == null) {
		echo '<div class="alert alert-danger">No results found for either players</div>';
	} else {

	$username1 = ucwords(str_replace("_", " ", $user1['username']));
	$username2 = ucwords(str_replace("_", " ", $user2['username']));
?>

	<h1 class="skill-title text-center"><?php echo $username1; ?> vs. <?php echo $username2; ?></h1>
	<div class="news_divider" style="margin-top:15px;margin-bottom:15px;"></div>

	<table class="table table-striped">
		<tr>
			<td style="width:160px;" class="text-right">Exp</td>
			<td style="width:90px;" class="text-right small-header">Lvl</td>
			<td style="width:90px;" class="text-right small-header">Rank</td>

			<td style="width:50px;" class="text-center small-header"></td>

			<td style="width:90px;" class="small-header">Rank</td>
			<td style="width:90px;" class="small-header">Lvl</td>
			<td  style="width:160px;" class="small-header">Exp</td>
		</tr>
	<?php
		foreach (getSkills() as $skill) {
			$exp1 = $user1[strtolower($skill).'_xp'];
			$exp2 = $user2[strtolower($skill).'_xp'];

			$rank1 = $db->getRank($user1['username'], $skill, $user1['mode']);
			$rank2 = $db->getRank($user2['username'], $skill, $user2['mode']);

			$level1 = strtolower($skill) == "overall" ? getTotalLevel($user1) : getLevelForXp($exp1, $skill);
			$level2 = strtolower($skill) == "overall" ? getTotalLevel($user2) : getLevelForXp($exp2, $skill);

			$class1 = $rank1 < $rank2 ? "success" : "danger";
			$class2 = $rank1 > $rank2 ? "success" : "danger";

			if (!is_numeric($rank1) || !is_numeric($rank2)) {
				$class1 = is_numeric($rank1) && !is_numeric($rank2) ? "success" : "warning";
				$class2 = is_numeric($rank2) && !is_numeric($rank1) ? "success" : "warning";
			}
 
			echo '
			<tr>
				<td style="width:160px;" class="text-right">'.number_format($exp1).'</td>
				<td style="width:90px;" class="text-right">'.$level1.'</td>
				<td style="width:90px;" class="text-right '.$class1.'">'.$rank1.'</td>

				<td class="text-center" style="width:50px;"><img src="assets/img/skill_icons/'.$skill.'-icon.png"/></td>

				<td style="width:90px;" class="'.$class2.'">'.$rank2.'</td>
				<td style="width:90px;">'.$level2.'</td>
				<td style="width:160px;">'.number_format($exp2).'</td>
			</tr>
			';
		}
	?>
	</table>
	<?php } ?>