MarkupProject
=============

Info
----
Create a class in the langauge of your choice that will read HTML content input and score and give 
an arbitrary score based on a set of rules. The content should be assigned a unique id based on the prefix described below. 
Changes to the content can be re-ran over time to determine improvement/regression of the score. Each unique run should be stored with the
date and time it was ran along with the score received for the content.

You can use external libraries if you feel they will aid you but you must place them in the appropiate folder based on the project layout section.

Code Requirements
-----------------
* Accept HTML Content Input
* Accept unique id for HTML Content to score (filename prefix)
* Score HTML content using the scoring guide
* Save results to a MySQL database
* Method: Retrieve scores for a unique id
* Method: Retrieve all scores run in the system for a custom date range
* Method: Retrieve highest scored unique id 
* Method: Retrieve lowest scored unique id
* Additionally you should write one query that will find the average score for all runs **__see project layout below__**

## Bonus
* tag names are case-insensitive (ie: Html is the same as html)
* Parse multiple sections of the HTML content at the same time for performance

Scoring Rules
-------------
Each starting tag should below has been assigned a score. Each tag in the content should be added/subtracted to the total score.

(We will assume for this project our html code creator created valid html)

| TagName | Score Modifier | TagName | Score Modifier |
| ------- | :------------: | ------- | -------------- |
| div     | 3              | font    | -1             |
| p       | 1              | center  | -2             |
| h1      | 3              | big     | -2             |
| h2      | 2              | strike  | -1             |
| html    | 5              | tt      | -2             |
| body    | 5              | frameset| -5             |
| header  | 10             | frame   | -5             |
| footer  | 10             |

example:

````
<html>
    <body>
      <p>foo</p>
      <p>bar</p>
      <div text-align='center'>
        <big>hello world</big>
      </div>
    </body>
</html>
````

2 p tags = 2 x 1 <br>
1 body tag = 1 x 5 <br> 
1 html tag = 1 x 5 <br>
1 div tag = 1 x 3 <br>
1 big tag = 1 x -2 <br>
**Total Score: 13**


Project Layout
--------------
####/data

* Contains the HTML content data to parse, format: (keyname_yyyy_mm_dd)

ie: 
* dougs_2012_02_04.html 
* dougs_2012_04_01.html 
* dougs_2012_07_01.html

####/src

* Your code goes in here.

####/schema

* Your create table statements for MySQL. 
* Your query to find the average score across each key. (see data example below)

ie: 

key | avgScore 
|---|--------|
dougs | 10.35 
bobs  | 8.03    

####/vendor

* If you didn't write it put it in here and are using it put it in here.

Instructions
------------
* Fork this repo into your own github account.  
* Begin working on the project and commit your code to the repo
* When you are finished. Email your RedVentures recruiter or submit a pull request on the project. 


