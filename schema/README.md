Documentation
--------------

+ All SQL Queries are scripted in 'scorcher.php'.
+ To change database configuration, edit the constants in 'scorcher.php'.
+ To build out your database, run:
<?php
	require_once("scorcher.php");
	Scorcher::buildDatabase();
?>


+ This will call the queries:
$createDatabaseQuery = "CREATE DATABASE " . Scorcher::DB;

$createTableQuery = 
				"CREATE TABLE " . Scorcher::DBTABLE . " (
					run_id int(11) NOT NULL AUTO_INCREMENT,
					run_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					keyname varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					content_date DATE NOT NULL,
					score int(11) NOT NULL,
					PRIMARY KEY (run_id),
					UNIQUE KEY run_id (run_id)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";


+ Query to find the average score across each key is executed by the function:
Scorcher::retrieveAvgPerKey()

+ This will call the query:
$query =
				"SELECT keyname, AVG(score)
				FROM " . Scorcher::DBTABLE . "
				GROUP BY keyname"

