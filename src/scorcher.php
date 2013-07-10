<?php
class Scorcher
	{

		// Configuraiton
		date_default_timezone_set('UTC'); // set the default timezone to use. Available since PHP 5.1
		private const string $DEFAULTDATE = COOKIE; // Predefined Constant for date format: Default HTTP Cookies (example: Monday, 15-Aug-05 15:52:01 UTC)
		private static $DEFAULTRULESARRAY = array(
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
	    private $date; // Date and time of the last run. Defaults to date and time property is instantiated
	    private $tagCountArray; // An array that will store tag names as keys and the instance count as the value
	    private $rulesArray; // An array that will store tag names as keys and the Score Modifier as the value
	    private $scorecardArray; // An array that will store tag names as keys and the accumulated score given for each Score Modifier
	    private $totalScore; // Integer: This will store the cumulative score. It will be a sum of all values from $scorecardArray

	    // Constructors
	    public function __construct() {
	       	$this->$contentId = '';
		    $this->$contentPath = '';
		    $this->$date = date($DATE);
		    $this->$tagCountArray = array();
		    $this->$rulesArray = $DEFAULTRULESARRAY;
		    $this->$scorecardArray = array();
		    $this->$totalScore = 0;
	    }

	    // Methods
	    public function displayVar() {
	        echo $this->var;
	    }

	    // Setters


	    // Getters
	    

	}
?>