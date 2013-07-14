<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Example</title>
</head>
<body>

        <?php
            
            require_once("scorcher.php");
            // Test that class was loaded...
            Scorcher::helloWorld("!");
            // Build out the database and table if you have not already.
            //Scorcher::buildDatabase();

            // MAIN CLASS METHODS
            //$content = "../data/bob_2013_03_01.html";
            //$directory = "../data/";
            // Oh yeah, instantiate the masterpiece. 
            //$scorcher = new Scorcher();
            // Score only one piece of content and save it to the database.
            //$scorcher->scorch($content);
            // Score all content in the directory and save all of the results to the database.
            //$scorcher->scorchDirectory($directory);
            // See what's in the current object.
            //$scorcher->displayVars();
            
            //DATABASE METHODS
            //Scorcher::retrieveScores('bob');
            //Scorcher::retrieveDateRange('2013_02_01','2013_03_01');
            //Scorcher::retrieveHighest();
            //Scorcher::retrieveLowest();
            Scorcher::retrieveAvgPerKey();
            // Let 'em know we're done for now.
            Scorcher::goodbyeWorld("...");

        ?>

</body>