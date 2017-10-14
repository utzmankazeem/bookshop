<?php # test.php sandbox

define('DBNAME', 'bookshop');
define('DBUSER', 'root');
define('DBPASS', 'usman222');

try {
	# prepare a pdo instance
	$conn = new PDO('mysql:host=localhost;dbname='.DBNAME, DBUSER, DBPASS);

	#set verbose error modes
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

} catch(PDOException $e) {
	echo $e->getMessage();
}