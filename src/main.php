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
            $test = "paul_1986_02_03";
            $scorcher = new Scorcher($test);
            $scorcher->scorch();
            $scorcher->displayVars();
            Scorcher::goodbyeWorld("...");

        ?>

</body>