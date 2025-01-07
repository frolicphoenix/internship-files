<?php 

$colors = array(
    "black",
    "green",
    "blue",
    "red",
    "yellow",
    "pink"
);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color-based Car Selector</title>
    <script src="script.js"></script>
</head>
<body>
<h1>Select a Color</h1>
    <select onchange="getCarsByColor(this.value)">
        <option value="">Choose a color</option>
        <?php foreach ($colors as $color): ?>
            <option value="<?php echo strtolower($color); ?>"><?php echo ucfirst($color); ?></option>
        <?php endforeach; ?>
    </select>
    <div id="result"></div>
</body>
</html>

