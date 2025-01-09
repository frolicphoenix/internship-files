<?php

session_start();
require_once __DIR__ . '/../db_connect.php';

// Check if user is logged in and is a superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['admin_type_id'] != 1) {
    header("Location: ../loginform/login.php");
    exit();
}

$uploadDir = "uploads/";
$downloadDir = "downloads/";

// Function to list files in a directory
function listFiles($directory) {
    $files = array_diff(scandir($directory), array('..', '.')); // Get files excluding '.' and '..'
    $fileList = [];

    foreach ($files as $file) {
        $filePath = $directory . $file;
        $fileList[] = [
            'name' => $file,
            'date' => date("Y-m-d", filemtime($filePath)),
            'time' => date("H:i:s", filemtime($filePath)) 
        ];
    }

    return $fileList;
}

$uploads = listFiles($uploadDir);
$downloads = listFiles($downloadDir);
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

        <h3>Uploaded Files</h3>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Date Created</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
            <?php foreach ($uploads as $upload): ?>
                <tr>
                    <td><?php echo htmlspecialchars($upload['name']); ?></td>
                    <td><?php echo htmlspecialchars($upload['date']); ?></td>
                    <td><?php echo htmlspecialchars($upload['time']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($uploadDir . $upload['name']); ?>" download>Download</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div style="padding-top: 50px;">
        <h2>Download CSV File</h2>
        <form action="download.php" method="post" enctype="multipart/form-data">
            <input type="submit" value="Download latest">
        </form>

        <h3>Downloadable Files</h3>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Date Created</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
            <?php foreach ($downloads as $download): ?>
                <tr>
                    <td><?php echo htmlspecialchars($download['name']); ?></td>
                    <td><?php echo htmlspecialchars($download['date']); ?></td>
                    <td><?php echo htmlspecialchars($download['time']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($downloadDir . $download['name']); ?>" download>Download</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>
