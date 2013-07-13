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
		private $uniqueId; // String: Unique content ID prefix. i.e. keyname from contentId Format: (keyname_yyyy_mm_dd)
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
		public function resetVars() {
			$this->contentId = '';
			$this->contentPath = '';
			$this->uniqueId = '';
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
		public function setPathVars($path) {
			$pathParts = pathinfo($path);
			$this->contentPath = $path;
			$this->contentDirectory = $pathParts['dirname'];
			$this->contentId = $pathParts['filename'];
			// Parse the contentId to extract the Unique ID and Content Published Date
			$explosionArray = explode("_", $this->contentId);
			$this->uniqueId = $explosionArray[0];
			$this->contentDate->setDate($explosionArray[1], $explosionArray[2], $explosionArray[3]);
		}	
		/**
		 * 
		 * Count how many times each tag is in the content. Store each tag count individually.
		 *
		 */
		public function countTags() {
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
		public function databaseConnect() {
			$this->mysqli = mysqli_connect($this->DBSERVER, $this->DBUSER, $this->DBPASS, $this->DB);
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
		public function createDatabase() {
			$localMysqli=mysqli_connect(Scorcher::DBSERVER, Scorcher::DBUSER, Scorcher::DBPASS);
			// Check connection
			if (mysqli_connect_errno())
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			$createDatabaseQuery = "CREATE DATABASE " . Scorcher::DB;//!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci
			if (mysqli_query($localMysqli,$createDatabaseQuery)){
				echo "Database " . Scorcher::DB . " created successfully<br />\n";
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
		public function createTable() {
			$localMysqli = mysqli_connect(Scorcher::DBSERVER, Scorcher::DBUSER, Scorcher::DBPASS, Scorcher::DB);
			if (mysqli_connect_errno($localMysqli)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error() . "<br />\n";
			}else { echo "MySQL connection successful.<br />\n"; }
			$createTableQuery = 
				"CREATE TABLE " . Scorcher::DBTABLE . " (
					run_id int(11) NOT NULL AUTO_INCREMENT,
					run_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					content_unique_id varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					content_date DATE NOT NULL,
					score int(11) NOT NULL,
					PRIMARY KEY (run_id),
					UNIQUE KEY run_id (run_id)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			if (mysqli_query($localMysqli, $createTableQuery)){
				echo "Table " . Scorcher::DBTABLE . " created successfully<br />\n";
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
		public function saveScorch() {
			$contentDate = $this->contentDate->format('Y-m-d');
			if(!mysqli_query($this->mysqli, "INSERT INTO '$this->DBTABLE' (content_unique_id, content_date, score)
				VALUES ('$this->uniqueId', '$contentDate', '$this->totalScore')")){
				die('Error: ' . mysqli_error($this->mysqli) . "<br />\n");
			} echo "New run added for '$this->contentId' with a score of '$this->totalScore'.<br />\n";
		}
		/**
		 * 
		 * Save the relevant information to the current run to the database
		 *
		 */
		public function retrieveScores($uniqueId) {



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
			$this->displayVar("uniqueId", $this->uniqueId);
			echo "<b>contentDate</b>"  .":	" . $this->contentDate->format('Y-m-d') . "<br />\n";
			$this->displayVarray("tagCountArray", $this->tagCountArray);
			$this->displayVarray("rulesArray", $this->rulesArray);
			$this->displayVarray("scorecardArray", $this->scorecardArray);
			$this->displayVar("totalScore", $this->totalScore);
		}

		public function displayVar($name, $var){
			echo "<b>" . $name . "</b>"  .":	" . $var . "<br />\n";
		}

		public function displayVarray($name, $arrayOK){
			echo "<b>" . $name . "</b>" . ":	" . "<br />\n";
			foreach ($arrayOK as $key => $value) {
				echo "<b>|</b> Key: $key <b>=></b> Value: $value <br />\n";
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