<?php

// default skin 
$defaultSkin = "hdrlabs";

//default sort
$defaultSort = "date";
// default sort order
$defSortDirection = "ascending"; 

// default time to show each image in a slideshow (in seconds)
$defaultSSSpeed = 6;

// Show "send to Flickr" links
$flickr = false;

// any files you don't want visible to the file browser add into this 
// array...
$ignoreFiles = array(	"index.php",
						"fComments.txt"
						);
						
// any folders you don't want visible to the file browser add into this 
// array...						
$ignoreFolders = array("fileNice"
						);

// file type handling, add file extensions to these array to have the 
// file types handled in certain ways
$imgTypes 	= array("gif","jpg","jpeg","bmp","png");
$embedTypes = array();
$htmlTypes 	= array("html","htm","txt","css","siblt");
$phpTypes 	= array("php","php3","php4","asp","js");
$miscTypes 	= array("pdf","zip");

// date format - see http://php.net/date for details
$dateFormat = "F d Y ";

?>
