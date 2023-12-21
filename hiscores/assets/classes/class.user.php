<?php
class User {
	
	private $user;
	
	public function __construct($user) {
		$this->user = $user;
	}

	public function getUsername() {
		return ucwords(str_replace("_", " ", $this->user['username']));
	}
	
	public function getPlainUsername() {
		return $this->user['username'];
	}
	
	public function getTotalXp() {
		return $this->user['overall_xp'];
	}

	public function getLevel($skill) {
		return $this->getLevelForXp($this->user[strtolower($skill).'_xp'], $skill);
	}

	public function getExp($skill) {
		return $this->user[strtolower($skill).'_xp'];
	}

	public function getOverallExp() {
		return $this->user['overall_xp'];
	}

	public function getTotalLevel($skills) {
		$total = 0;
		foreach ($skills as $skill) {
			$total += $this->getLevel($skill);
		}
		return $total;
	}

	public function isSkiller($skills) {
		foreach ($skills as $skill) {
			if (strtolower($skill) == "constitution") {
				if ($this->getLevel($skill) > 10)
					return false;
			} else {
				if ($this->getLevel($skill) > 1)
					return false;
			}
		}
		return true;
	}

	public function getLevelForXp($exp, $skill) {
		$points = 0;
		$output = 0;
		for ($lvl = 1; $lvl <= (strtolower($skill) == 'dungeoneering' ? 120 : 99); $lvl++) {
			$points += floor($lvl + 300.0 * pow(2.0, $lvl / 7.0));
			$output = (int) floor($points / 4);
			if (($output - 1) >= $exp) {
				return $lvl;
			}
		}
		return (strtolower($skill) == 'dungeoneering' ? 120 : 99);
	}
	
	public function getCombatLevel() {
		$attack = $this->getLevelForXp($this->user['attack_xp'], "");
		$defence = $this->getLevelForXp($this->user['defence_xp'], "");
		$strength = $this->getLevelForXp($this->user['strength_xp'], "");
		$hp = $this->getLevelForXp($this->user['constitution_xp'], "");
		$prayer = $this->getLevelForXp($this->user['prayer_xp'], "");
		$ranged = $this->getLevelForXp($this->user['ranged_xp'], "");
		$magic = $this->getLevelForXp($this->user['magic_xp'], "");
		$combatLevel = (int) (($defence + $hp + floor($prayer / 2)) * 0.25) + 1;
		$melee = ($attack + $strength) * 0.325;
		$ranger = floor($ranged * 1.5) * 0.325;
		$mage = floor($magic * 1.5) * 0.325;
	
		if ($melee >= $ranger && $melee >= $mage) {
			$combatLevel += $melee;
		} else if ($ranger >= $melee && $ranger >= $mage) {
			$combatLevel += $ranger;
		} else if ($mage >= $melee && $mage >= $ranger) {
			$combatLevel += $mage;
		}
		
		return (int)$combatLevel;
	}
	
	function getRealLevel() {
		return (int)($this->getCombatLevel() + $this->getSummoningCombatLevel());
	}
	
	function getSummoningCombatLevel() {
		return $this->getLevelForXp($this->user['summoning_xp'], "") / 8;
	}
	

}