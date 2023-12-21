<?php
	if (count(get_included_files()) <= 1) {
		exit (); // do not remove, prevents direct access
	}
?>
<div class="hs_sidebar">
	<h3>Skills</h3>
	<div class="news_divider"></div>
	<div class="list-group">
  		<?php
			foreach (getSkills() as $skillz) {
				echo '<a class="list-group-item" href="?skill='.$skillz.'"><img src="assets/img/skill_icons/'.$skillz.'-icon.png"/> '.$skillz.'</a>';
			}
		?>
	</div>
</div>
<div class="hs_sidebar">
	<h3>Search</h3>
	<div class="news_divider"></div>
  	<div class="panel-body">
	  	<form action="index.php" method="get">
			<div class="form-group">
				<input type="text" class="form-control" name="user" placeholder="Username" required>
			</div>
			<div style="text-align:right">
				<button class="btn btn-default btn-block game-mode-btn-s" type="submit"><i class="fa fa-search"></i> Search</button>
			</div>
		</form>
	</div>
</div>

<div class="hs_sidebar">
	<h3>Compare</h3>
	<div class="news_divider"></div>
  	<div class="panel-body">
	  	<form action="index.php" method="get">
			<div class="form-group">
				<input type="text" class="form-control" name="player" placeholder="Player One" required>
			</div>
			<div class="form-group">
				<input type="text" class="form-control" name="other" placeholder="Player Two (Optional)">
			</div>
			<div style="text-align:right">
				<button class="btn btn-default btn-block game-mode-btn-s" type="submit"><i class="fa fa-search"></i> Compare</button>
			</div>
		</form>
	</div>
</div>

					