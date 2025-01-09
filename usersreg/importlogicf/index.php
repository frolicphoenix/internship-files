<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Upload</title>
</head>
<body>
    <div>
        <h2>Upload CSV File</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="csvFile" accept=".csv" required>
            <input type="submit" value="Upload CSV">
        </form>
    </div>
    <div style="padding-top: 50px;">
        <h2>Download CSV File</h2>
        <form action="download.php" method="post" enctype="multipart/form-data">
            <!-- <input type="file" name="csvFile" accept=".csv" required> -->
            <input type="submit" value="Download latest">
        </form>
    </div>
    <!-- <div style="padding-top: 50px;">
        <form action="dwnldformcontents.php" method="post" enctype="multipart/form-data">
            Name <input type="text" name="name" />
            Email <input type="text" name="email" />
            Phone <input type="text" name="phone" />
            <input type="submit" name="submit" value="Submit">
        </form>
    </div> -->
</body>
</html>
