<?php

// The page we wish to display
$file = $_GET[ 'page' ];

// Input validation - only allow specific files
$allowed_files = array('file1.php', 'file2.php', 'file3.php', 'include.php');

if( !in_array( $file, $allowed_files ) ) {
    echo "ERROR: File not allowed!";
    exit;
}

?>