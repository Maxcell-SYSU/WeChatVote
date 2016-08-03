<?php

$db_connect = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS);
if (!$db_connect) {
	die('Counld not connect:'.mysql_error());
}
else {
    mysql_select_db(SAE_MYSQL_DB, $db_connect);
}
