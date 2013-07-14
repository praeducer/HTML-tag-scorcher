Documentation
---------------

main.php:
+ This file can build out MySQL for you and test every function in scorcher.php.
+ This file exemplifies the methods that will most concern you for testing functionality.

scorcher.php:
+ This is the main class.
+ All project requirements are met in this document.
+ To find a specific requirement, simply search the document for the text from
	the original project definition.
+ To see how to use this class, see the comments in 'main.php'. They are short.
+ To see more details about this class, see the comments in the document.


 * UNRESOLVED BUGS:
 * 1. When I convert the document to a DOMDocument object, PHP adds in new tags.
 *	This occures in the countTags() function when 'loadHTMLFile' is called.
 *	'body' and 'html' tags will be added if they already do not exist.
 * 	The function tries correcting the document if it does not meet certain standards:
 *	http://stackoverflow.com/questions/4800459/php-domdocument-adds-extra-tags

