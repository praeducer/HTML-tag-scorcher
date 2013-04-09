MarkupProject
=============

Info
----
Create a class in the langauge of your choice that will read HTML content input and score and give 
an arbitrary score based our set of rules. The content should be assigned a key/name so changes to the content can be reran over time
to determine improvement/regression of the score. Each score and run should be stored in a persistant
storage that can be used to generate some basic reporting. 

You can use external libraries if you feel they will aid you but you must include them in your class.

Code Requirements
-----------------
* Accept HTML Content Input
* Accept unique id/name for HTML Content to score (filename, url, supplied id)
* Score HTML content based on score rules
* Save results to MySQL database
* Method: Retrieve score for a unique id
* Method: Retrieve all content runs per date range
* Method: Retrieve highest scored unique id
* Method: Retrieve lowest scored unique id
* Additionally you should write one query that will find the average score for all runs **__see project layout below__**

## Bonus
* tag names are case-insensitive (ie: Html is the same as html)
* Parses multiple sections of the HTML content at the same time 

Scoring Rules
-------------
Only starting tags should be counted. (We will assume for this project our html code creator
put all the ending tags in place.)

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

Project Layout
--------------
_/data_*

contains test html data you should can use to test your class each filename represents 
the iterations over time of their html code.

ie: dougs_2012_02_04.html 
    dougs_2012_04_01.html 
    dougs_2012_07_01.html

_/src_*

where you should put you should commit your class. 

_/schema_*

your create table(s) statements that would setup your mysql tables.
your query to find the average score across each key

ie: 
|  key  | avgScore |
| dougs | 10.35    |
| bobs  | 8.03     |

_/vendor__*

if you didn't write it put it in here and are using it put it in here.

Instructions
------------


