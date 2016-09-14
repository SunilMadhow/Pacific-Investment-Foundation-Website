<?php
define("HEADER_FILE", "header.html");
define("FOOTER_FILE", "footer.html");

define("UTILS", "utilities_head.html");

function getHeaderBody(){
	echo file_get_contents (HEADER_FILE);
}


function getFooterBody(){
	echo file_get_contents (FOOTER_FILE);
}

//Like title, icon and boostraps (yes i realize name is humorous)
function getHead(){
	echo file_get_contents (UTILS);
}


?>