<?php

$CONFIG = parse_ini_file('../conf/config.ini');
$DATA_DIR = $CONFIG['DATA_DIR'];

print "Database writer user: ";
$DB_WRITE_USER = trim(fgets(STDIN));
print "Database writer password: ";
$DB_WRITE_PASSWORD = trim(fgets(STDIN));

// Connect to database
$DB = null;
$dsn = sprintf("%s:host=%s;dbname=%s",
		$CONFIG['DB_DRIVER'],
		$CONFIG['DB_HOST'],
		$CONFIG['DB_NAME']);
try {
	$DB = new PDO($dsn, $DB_WRITE_USER, $DB_WRITE_PASSWORD);
} catch (PDOException $e) {
	// Couldn't connect to database
	trigger_error("Problem connecting to the database: " . $e->getMessage());
	exit();
}

$SCHEMA = strtoupper($CONFIG['DB_SCHEMA']);	

// Quick check for existing tables (verify overwrite)
try {
	$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$results = $DB->query('SELECT 1 FROM ' . $SCHEMA . '.data LIMIT 1');
	print "\nIt appears that the application data objects already exist.  Do " .
			"you want to recreate all application objects (cannot be undone)?\n";
	print 'Enter "Yes" or "No" [No]: ';
	$verify = trim(fgets(STDIN));
	print "\n";
	if (strtoupper($verify) != "YES") {
		print "Build of application data objects cancelled\n";
		$DB = null;
		exit();
	}
} catch (PDOException $e) {}

print "(Re)building application data objects ...\n";
$DATA_DIR = $CONFIG['DATA_DIR'];

// First the DDL commands (convert to upper case for ease of comparisons below)
$sql = strtoupper(file_get_contents($DATA_DIR . '/objects.sql'));

// If schema is not the default, modify it in the commands.
if ($SCHEMA !== 'US_DESIGN') {
	$sql = str_replace('US_DESIGN', $SCHEMA, $sql);
}

$tok = strtok($sql, ";");
while ($tok !== false) {
	$command = strtoupper(trim($tok));
	$drop_flag = strpos($command, "DROP ") === 0;
	if (strpos($command, "CREATE ") === 0 || $drop_flag ||
		strpos($command, "ALTER ") === 0) {
		try {
			$DB->exec($command);
		}
		catch (PDOException $e) {
			if (!$drop_flag) {
				trigger_error("DDL error: " . $e->getMessage());
			}
		}
	}
	$tok = strtok(";");
}
$DB = null;
?>