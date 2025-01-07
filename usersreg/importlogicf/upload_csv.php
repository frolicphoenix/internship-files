<?php

    $display_table='';

    if(isset($_POST['upload']) && $_POST['upload']=='Upload CSV') {
        $display_table='test';
    }

    // exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="csv" />
        <input type="submit" name="upload" value="Upload CSV" />
    <form>
    <div>
        <?php echo $display_table; ?>
    </div>
</body>
</html>