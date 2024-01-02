<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}
	

	$db = new Database($sql_host, $sql_user, $sql_pass, $sql_data, $sql_port, $sql_cert);
	$db->setTable($table);
	$db->connectWithSSL()
?>