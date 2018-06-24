<?php
require('./lib/connexion.php');
require('./lib/db.php');
$C 	= BDD_Connection();
User_event($C, 2);
unset($C);
session_destroy();
header('Location: index.php');
?>