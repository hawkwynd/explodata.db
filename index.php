<?php 
// file upload for db files
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Your explodata.db</title>
</head>
<body>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <h2>Upload a File</h2>
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload File" name="submit">
    </form>
</body>
</html>