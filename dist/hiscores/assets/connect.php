<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}
	

	$db = new Database($sql_host, $sql_user, $sql_pass, $sql_data);
	$db->setTable($table);
	$db->connect()
?>