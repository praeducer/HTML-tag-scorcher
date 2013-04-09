MarkupProject
=============

Info
----
Create a class in the langauge of your choice that will read HTML content input and score and give 
an arbitrary score based our set of rules. The content should be assigned a key/name so changes to the content can be reran over time
to determine improvement/regression of the score. Each score and run should be stored in a persistant
storage that can be used to generate some basic reporting. 

You can use external libraries if you feel they will aid you but you must include them in your class

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

## Bonus
* handles tags depsite 
* Parses multiple sections of the HTML content at the same time 

Scoring Rules
-------------
| *Good Tags*              | *BadTags*                |
| :---------:              | :-------:                |
| TagName | Score Modifier | TagName | Score Modifier |
| ------- | :------------: | ------- | -------------- |
| div     | 3              | font    | -1             |
| p       | 1              | center  | -2             |
| table   | 1              | frame   | -5             |
| span    | 1              | strike  | -1             |
| h1      | 3              | big     | -2             |
| h2      | 2              | applet  | -10            |
| input   | 1              | frameset| -5             |
| html    | 5              | tt      | -2             |
| body    | 5              |
| header  | 10             |
| footer  | 10             |

 

