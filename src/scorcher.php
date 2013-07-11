<?php
class Scorcher
	{
		
		// Configuraiton
		//date_default_timezone_set('UTC'); // set the default timezone to use. Available since PHP 5.1
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
		
	    // Properties (default values set)
	    private $contentId; // String: Unique ID for the content. Format: (keyname_yyyy_mm_dd)
	    private $contentPath; // String: URL or Directory Path to content
	    private $dateRan; // Date and time of the last run. date(DATE_COOKIE) Predefined Constant for date format: Default HTTP Cookies (example: Monday, 15-Aug-05 15:52:01 UTC)
	    private $tagCountArray; // An array that will store tag names as keys and the instance count as the value
	    private $rulesArray; // An array that will store tag names as keys and the Score Modifier as the value
	    private $scorecardArray; // An array that will store tag names as keys and the accumulated score given for each Score Modifier
	    private $totalScore; // Integer: This will store the cumulative score. It will be a sum of all values from $scorecardArray

	    // Constructors
	    /** Oops! Can't overload in PHP (easily).
		 * Default. Uses default set of rules. Will do nothing if ran as is.
		 
	    public function __construct() {

	       	$this->contentId = '';
		    $this->contentPath = '';
		    $this->dateRan = ''; 
		    $this->tagCountArray = array();
		    $this->rulesArray = $this->DEFAULTRULESARRAY;
		    $this->scorecardArray = array();
		    $this->totalScore = 0;
	    		    
	    }
	    */
	    /**
		 * 
		 * Build an object instance given a unique ID. Uses default rule set.
		 *
		 * @param string $newContentId the content ID for the content that needs to be parsed
		 */
	    public function __construct($newContentId) {

	       	$this->contentId = $newContentId;
		    $this->contentPath = '../data/' . $newContentId . '.html';
		    $this->dateRan = '';
		    $this->tagCountArray = array();
		    $this->rulesArray = $this->DEFAULTRULESARRAY;
		    $this->scorecardArray = array();
		    $this->totalScore = 0;
	    		    
	    }

	    // Methods
	    /**
		 * Prints out the object's properties. 
		 */
	    public function displayVar() {
	       	echo 'contentId: ' . $this->contentId;
		    echo '<br>' . 'contentPath: ' . $this->contentPath;
		    echo '<br>' . 'date: ' . $this->date;
		    echo '<br>' . 'tagCountArray: ';
		    print_r($this->tagCountArray);
		    echo '<br> rulesArray: ';
		    print_r($this->rulesArray);
		    echo '<br> scorecardArray: ';
		    print_r($this->scorecardArray);
		    echo '<br> totalScore: ' . $this->totalScore . '<br>';
	    }
	    public function helloWorld(){
	    	echo "Hello World!";
	    }
	    // Setters


	    // Getters
	    

	}

?>