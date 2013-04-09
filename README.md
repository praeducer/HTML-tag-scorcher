MarkupProject
=============

Info
----
Create a class in the langauge of your choice that will read HTML content input and score and give 
a score. The content should be assigned a key/name so changes to the content can be reran over time
to determine improvement/regression of the score. Each score and run should be stored in a persistant
storage that can be used to generate some basic reporting. 

You can use external libraries if you feel they will aid you but you must include them in your class

Code Requirements
------------
* Accept HTML Content Input
* Accept unique id/name for HTML Content to score (filename, url, supplied id)
* Score HTML content based on score rules
* Persist Score for later retrieval
* Method: Retrieve score for a unique id
* Method: Retrieve all content runs per date range
* Method: Retrieve highest scored unique id
* Method: Retrieve lowest scored unique id

