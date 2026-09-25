<?php
$host = 'mysqlsrv.dcs.bbk.ac.uk';
$db = 'abutt20db';
$user = 'abutt20';
$pass = 'bbkmysql';
$charset = 'utf8mb4';
		
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	
$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
];
	
#Database connection is set and configured to my own database
	
try {
		$pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
		$errorCode = $e->getCode();
		$errorMessage = $e->getMessage();
		echo htmlParagraph($errorCode . " : ".  $errorMessage);
}
?>