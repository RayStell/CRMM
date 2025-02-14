<?php session_start();

require_once 'api/auth/AuthCheck.php';

AuthCheck('clients.php', 'login.php');

?>