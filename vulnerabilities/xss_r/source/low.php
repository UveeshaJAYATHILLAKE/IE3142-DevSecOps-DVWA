<?php

header ("X-XSS-Protection: 1; mode=block");

// Is there any input?
if( array_key_exists( "name", $_GET ) && $_GET[ 'name' ] != NULL ) {
	// Feedback for end user
	$html .= '<pre>Hello ' . htmlspecialchars( $_GET[ 'name' ] ) . '</pre>';
}

?>
