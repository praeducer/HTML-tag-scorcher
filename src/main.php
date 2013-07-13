<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Example</title>
</head>
<body>

        <?php
            
            require_once("scorcher.php");
            Scorcher::helloWorld("!");
            $content = "../data/paul_1986_02_03.html";
            $directory = "../data/";
            Scorcher::buildDatabase();
            //$scorcher = new Scorcher();
            //$scorcher->prepareMySQL();
            //$scorcher->scorch($content);
            //$scorcher->scorchDirectory($directory);
            //$scorcher->displayVars();
            Scorcher::goodbyeWorld("...");

        ?>

</body>