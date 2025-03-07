<?php session_start();

require_once 'api/DB.php';
require_once 'api/auth/AuthCheck.php';

if (AuthCheck('', 'login.php', $DB)) {
    header('Location: clients.php');
    exit;
}

?>