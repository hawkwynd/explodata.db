<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("America/Chicago");

// print_r( $_FILES ); 
// echo $upload_max_size = ini_get('upload_max_filesize') . " max_upload_size<br/>";  
// echo $post_max_size=ini_get('post_max_size') . " post_max_size<br/>"; 
// echo $_FILES["fileToUpload"]["size"] . " upload file size</br>";


$target_dir     = "./databases/"; // Directory where uploaded files will be stored
$target_file    = $target_dir . basename( $_FILES["fileToUpload"]["name"] );
$uploadOk       = 1;
$fileType       = strtolower( pathinfo($target_file, PATHINFO_EXTENSION ));

// Check if file already exists
if (file_exists( $target_file )) {
    echo "Sorry, $target_file already exists.<br>";
    $uploadOk = 0;
}

// Check file size (example: limit to 5MB)
// if ($_FILES["fileToUpload"]["size"] > $upload_max_size ) {
//     echo "Sorry, your file is too large.<br>";
//     $uploadOk = 0;
// }

// Allow certain file formats (example: JPG, JPEG, PNG, GIF)
if($fileType != "db" ) {
    echo "Sorry, only " . $fileType. "  files are allowed.<br>";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.<br>";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.<br>";
        echo "<a href='helium.php'>Click here to extract your data in csv format</a><br/>";
    } else {
        echo "Sorry, there was an error uploading your file.<br>";
    }
}

?>