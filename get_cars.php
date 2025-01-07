<?php
$cars = array(
    array("Volvo", "black"),
    array("BMW", "green"),
    array("Saab", "blue"),
    array("Land Rover", "red"),
    array("Ferrari", "red"),
    array("Lamborghini", "yellow"),
    array("Porsche", "black"),
    array("Tesla", "blue")
);

$color = strtolower($_GET['color']);

$matchingCars = array_filter($cars, function($car) use ($color) {
    return strtolower($car[1]) === $color;
});

if (count($matchingCars) > 0) {
    echo "<h2>Cars in " . ucfirst($color) . "</h2>";
    echo "<ul>";
    foreach ($matchingCars as $car) {
        echo "<li>" . $car[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "No cars found in " . ucfirst($color);
}
?>
