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
		private $dbServer = "localhost"; // server hosting the MySql database
		private $dbUser = "root"; // user name to login to database
		private $dbPass = ""; // A really safe way to secure the database
		private $db = "scorcher"; // name of the database that holds all of the runs
		private $mysqli;


		// Properties (default values set)
		private $contentId; // String: Unique ID for the content. Format: (keyname_yyyy_mm_dd)
		private $contentPath; // String: URL or Directory Path to content
		private $contentDirectory; // Parent directory of content. Used for scoring entire folders.
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
		 * Score content. Map the rules to the amount of each tag. Multiply them by eachother.
		 *
		 * @param string $path Directory to the content that needs to be parsed.
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
		// Methods
		/**
		 * 
		 * Reset all properties to default values.
		 *
		 */
		public function resetVars() {
			$this->contentId = '';
			$this->contentPath = '';
			$this->contentDirectory = '';
			$this->tagCountArray = array();
			$this->rulesArray = $this->DEFAULTRULESARRAY;
			$this->scorecardArray = array();
			$this->totalScore = 0;
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
		}

		//Database access methods
		/**
		 * 
		 * Save the relevant information to the current run to the database
		 *
		 */
		public function databaseConnect() {
			$this->mysqli = mysqli_connect($this->dbServer, $this->dbUser, $this->dbPass, $this->db);
			if (mysqli_connect_errno($this->mysqli)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error() . "<br />\n";
			}else { echo "MySQL connection successful.<br />\n"; }			
		}
		/**
		 * 
		 * Save the relevant information to the current run to the database
		 *
		 */
		public function saveScorch() {

			if(!mysqli_query($this->mysqli, "INSERT INTO runs (content_id, score)
				VALUES ('$this->contentId', '$this->totalScore')")){
				die('Error: ' . mysqli_error($this->mysqli));
			} echo "New run added for $this->contentId with a score of $this->totalScore.";
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