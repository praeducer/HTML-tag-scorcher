<?php
class Scorcher
	{	
		// Configuraiton
 		private $DEFAULTRULESARRAY = array(
			"div" => 3,
			"p" => 1,
			"h1" => 3,
			"h2" => 2,
			"html" => 5,
			"body" => 5,
			"header" => 10,
			"footer" => 10,
			"font" => -1,
			"center" => -2,
			"big" => -2,
			"strike" => -1,
			"tt" => -2,
			"frameset" => -5,
			"frame" => -5,
		); // The default set of rules from the project definition
		const DBSERVER = "localhost"; // server hosting the MySql database
		const DBUSER = "root"; // user name to login to database
		const DBPASS = ""; // A really safe way to secure the database
		const DB = "scorcher"; // name of the database that holds all of the runs
		const DBTABLE = "runs";
		private $mysqli; // Connection to MySQL

		// Properties
		private $contentId; // String: ID for the content. Format: (keyname_yyyy_mm_dd)
		private $contentPath; // String: URL or Directory Path to content
		private $contentDirectory; // String: Parent directory of content
		private $keyname; // String: Unique content ID prefix. i.e. keyname from contentId Format: (keyname_yyyy_mm_dd)
		private $contentDate; // Date content was published. i.e. yyyy_mm_dd keyname from contentId Format: (keyname_yyyy_mm_dd)
		private $tagCountArray; // An array that will store tag names as keys and the instance count as the value
		private $rulesArray; // An array that will store tag names as keys and the Score Modifier as the value
		private $scorecardArray; // An array that will store tag names as keys and the accumulated score given for each Score Modifier
		private $totalScore; // Integer: This will store the cumulative score. It will be a sum of all values from $scorecardArray

		// Constructor
		/**
		 * 
		 * Build an object instance given a unique ID. Uses default rule set.
		 *
		 * 
		 */
		public function __construct() {
			$this->resetVars();
			$this->databaseConnect();
					
		}
		public function __destruct() {
			mysqli_close($this->mysqli);
		}

		// Methods
		/**
		 * 
		 * Reset all properties to default values.
		 *
		 */
		private function resetVars() {
			$this->contentId = '';
			$this->contentPath = '';
			$this->keyname = '';
			$this->contentDate = new DateTime();
			$this->contentDirectory = '';
			$this->tagCountArray = array();
			$this->rulesArray = $this->DEFAULTRULESARRAY;
			$this->scorecardArray = array();
			$this->totalScore = 0;
		}
		/**
		 * 
		 * Score content. Map the rules to the amount of each tag. Multiply them by eachother.
		 *
		 * @param string $path Full working directory to the content that needs to be parsed. Can be relative to the location of this file.
		 */
		public function scorch($path) {
			$this->setPathVars($path);
			$this->countTags();
			// Get all of the keys from the rules array
			$keyArray = array_keys($this->rulesArray);
			for( $i = 0 ; $i < count($keyArray); $i++){
				// If the tag for the rule exists in the content
				if(array_key_exists($keyArray[$i], $this->tagCountArray)){
					// Multiple the score modifier by the number of tag instances. Accumulate total score.
					$this->totalScore += $this->scorecardArray[$keyArray[$i]] =
						($this->tagCountArray[$keyArray[$i]] * $this->rulesArray[$keyArray[$i]]);		
				}
			}
			$this->saveScorch();
		}
		/**
		 * 
		 * Score all content in a directory. Map the rules to the amount of each tag. Multiply them by eachother.
		 *
		 * @param string $directory Directory to the content that needs to be parsed. Format must include slash at the beginning and end.
		 */
		public function scorchDirectory($directory) {
			$files = scandir($directory);
			foreach ($files as $file) {
				if(!is_dir($file)){
					$this->scorch($directory . $file);
					$this->resetVars();
				}
			}
		}	
		/**
		 * 
		 * Break path into useful parts.
		 *
		 */
		private function setPathVars($path) {
			$pathParts = pathinfo($path);
			$this->contentPath = $path;
			$this->contentDirectory = $pathParts['dirname'];
			$this->contentId = $pathParts['filename'];
			// Parse the contentId to extract the Unique ID and Content Published Date
			$explosionArray = explode("_", $this->contentId);
			$this->keyname = $explosionArray[0];
			$this->contentDate->setDate($explosionArray[1], $explosionArray[2], $explosionArray[3]);
		}	
		/**
		 * 
		 * Count how many times each tag is in the content. Store each tag count individually.
		 *
		 */
		private function countTags() {
			$content = new DOMDocument();
			$content->loadHTMLFile($this->contentPath);
			// Create an array with all tags that exist in content.
			$tags = $content->getElementsByTagName('*');
			// Count the occurence of each tag.
			foreach($tags as $tag) {
				if(array_key_exists($tag->tagName, $this->tagCountArray)) {
					$this->tagCountArray[$tag->tagName] += 1;
				} else {
					$this->tagCountArray[$tag->tagName] = 1;
				}
			}
		}

		//Database methods
		/**
		 * 
		 * Connect to the MySQL database and table for storing the runs
		 *
		 */
		private function databaseConnect() {
			$this->mysqli = mysqli_connect(Scorcher::DBSERVER, Scorcher::DBUSER, Scorcher::DBPASS, Scorcher::DB);
			if (mysqli_connect_errno($this->mysqli)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error() . "<br />\n";
			}else { echo "MySQL connection successful.<br />\n"; }			
		}
		/**
		 * 
		 * Build out MySQL environment to handle database methods
		 *
		 */
		public function buildDatabase() {
			Scorcher::createDatabase();
			Scorcher::createTable();
		}
		/**
		 * 
		 * Create the database to store data
		 *
		 */
		private function createDatabase() {
			$localMysqli=mysqli_connect(Scorcher::DBSERVER, Scorcher::DBUSER, Scorcher::DBPASS);
			// Check connection
			if (mysqli_connect_errno())
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}else { echo "MySQL connection successful.<br />\n"; }
			$createDatabaseQuery = "CREATE DATABASE " . Scorcher::DB;//!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci
			if (mysqli_query($localMysqli,$createDatabaseQuery)){
				echo "Database " . Scorcher::DB . " created successfully.<br />\n";
			}else{
				echo "Error creating database: " . mysqli_error($localMysqli) . "<br />\n";
			}
			mysqli_close($localMysqli);
		}
		/**
		 * 
		 * Create the table to store all of the scorch runs
		 *
		 */
		private function createTable() {
			$localMysqli = mysqli_connect(Scorcher::DBSERVER, Scorcher::DBUSER, Scorcher::DBPASS, Scorcher::DB);
			if (mysqli_connect_errno($localMysqli)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error() . "<br />\n";
			}else { echo "MySQL connection successful.<br />\n"; }
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
			if (mysqli_query($localMysqli, $createTableQuery)){
				echo "Table " . Scorcher::DBTABLE . " created successfully.<br />\n";
			}else{
				echo "Error creating table: " . mysqli_error($localMysqli) . "<br />\n";
			}
			mysqli_close($localMysqli);
		}
		/**
		 * 
		 * Save the relevant information to the current run to the database
		 *
		 */
		private function saveScorch() {
			$contentDate = $this->contentDate->format('Y-m-d');
			if(!mysqli_query($this->mysqli, "INSERT INTO " . Scorcher::DBTABLE . " (keyname, content_date, score)
				VALUES ('$this->keyname', '$contentDate', '$this->totalScore')")){
				die('Error: ' . mysqli_error($this->mysqli) . "<br />\n");
			} echo "New run added for '$this->contentId' with a score of '$this->totalScore'.<br />\n";
		}
		/**
		 * 
		 * "Method: Retrieve scores for a unique id"
		 *
		 * @param string $key unqiue ID keyname for the content.
		 */
		public function retrieveScores($key) {
			$localMysqli = mysqli_connect(Scorcher::DBSERVER, Scorcher::DBUSER, Scorcher::DBPASS, Scorcher::DB);
			if (mysqli_connect_errno($localMysqli)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error() . "<br />\n";
			}
			$result = mysqli_query($localMysqli,
				"SELECT run_timestamp, content_date, score
				FROM " . Scorcher::DBTABLE . "
				WHERE keyname = '$key'
				ORDER BY content_date ASC"
			);
			if(!$result){ die('Error: ' . mysqli_error($localMysqli) . "<br />\n"); }
			echo "'$key' content retrieved: <br />\n";
			Scorcher::printRows($result);
			mysqli_close($localMysqli);
		}
		/**
		 * 
		 * "Method: Retrieve all scores run in the system for a custom date range"
		 *
		 * @param string $start inclusive start date of range to be retrieved. Format MySQL DateTime
		 * @param string $end inclusive end date of range to be retrieved. Format MySQL DateTime
		 */
		public function retrieveDateRange($start, $end) {
			$localMysqli = mysqli_connect(Scorcher::DBSERVER, Scorcher::DBUSER, Scorcher::DBPASS, Scorcher::DB);
			if (mysqli_connect_errno($localMysqli)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error() . "<br />\n";
			}
			$result = mysqli_query($localMysqli,
				"SELECT run_timestamp, keyname, content_date, score
				FROM " . Scorcher::DBTABLE . "
				WHERE content_date
				BETWEEN '$start'
				AND '$end'
				ORDER BY content_date ASC"
			);
			if(!$result){ die('Error: ' . mysqli_error($localMysqli) . "<br />\n"); }
			echo "Date range retrieved: <br />\n";
			Scorcher::printRows($result);
			mysqli_close($localMysqli);
		}
		/**
		 * 
		 * "Method: Retrieve highest scored unique id"
		 *
		 */
		public function retrieveHighest() {
			$localMysqli = mysqli_connect(Scorcher::DBSERVER, Scorcher::DBUSER, Scorcher::DBPASS, Scorcher::DB);
			if (mysqli_connect_errno($localMysqli)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error() . "<br />\n";
			}
			$result = mysqli_query($localMysqli,
				"SELECT MAX(score) AS max
				FROM " . Scorcher::DBTABLE
			);
			if(!$result){ die('Error: ' . mysqli_error($localMysqli) . "<br />\n"); }
			$max = mysqli_fetch_array($result)['max'];
			$result = mysqli_query($localMysqli,
				"SELECT run_timestamp, keyname, content_date, score
				FROM " . Scorcher::DBTABLE . "
				WHERE score = $max"
			);
			if(!$result){ die('Error: ' . mysqli_error($localMysqli) . "<br />\n"); }
			echo "Highest scored Unique ID retrieved: <br />\n";
			Scorcher::printRows($result);
			mysqli_close($localMysqli);
		}


		// Printers
		/**
		 * Prints out the object's properties. 
		 */
		public function displayVars() {
			echo "<br />\n";
			$this->displayVar("contentId", $this->contentId);
			$this->displayVar("contentPath", $this->contentPath);
			$this->displayVar("contentDirectory", $this->contentDirectory);
			$this->displayVar("keyname", $this->keyname);
			echo "<b>contentDate</b>"  .":	" . $this->contentDate->format('Y-m-d') . "<br />\n";
			$this->displayVarray("tagCountArray", $this->tagCountArray);
			$this->displayVarray("rulesArray", $this->rulesArray);
			$this->displayVarray("scorecardArray", $this->scorecardArray);
			$this->displayVar("totalScore", $this->totalScore);
		}

		private function displayVar($name, $var){
			echo "<b>" . $name . "</b>"  .":	" . $var . "<br />\n";
		}

		private function displayVarray($name, $arrayOK){
			echo "<b>" . $name . "</b>" . ":	" . "<br />\n";
			foreach ($arrayOK as $key => $value) {
				echo "<b>|</b> Key: $key <b>=></b> Value: $value <br />\n";
			}
		}

		private function printRows($result){
			while($row = mysqli_fetch_array($result)) {
				echo "\tOn " . $row['content_date'] . " the keyname '" . $row['keyname'] . "' scored '" . $row['score'] .
					"' according to the run with a timestamp of '" . $row['run_timestamp'] . "'.<br />\n";
			}
		}

		public function helloWorld($w){
			echo "Hello, World$w<br />\n<br />\n";
		}

		public function goodbyeWorld($w){
			echo "<br />\nGoodbye, World$w<br />\n";
		}

	}

?>