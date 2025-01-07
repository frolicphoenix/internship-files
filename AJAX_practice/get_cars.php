<?php

//array of cars with their respective colors
$cars = array(
    array("Volvo", "black"),
    array("BMW", "green"),
    array("Saab", "blue"),
    array("Land Rover", "red"),
    array("Ferrari", "red"),
    array("Lamborghini", "yellow"),
    array("Porsche", "black"),
    array("Tesla", "blue"),
    array("Suzuki", "pink")
);

//getting the colors from GET request and covert to lowercase
$color = strtolower($_GET['color']);

//filtering the cars array to find cars matching the selected color
$matchingCars = array_filter($cars, function($car) use ($color) {
    return strtolower($car[1]) === $color;
});

//checking if any missing cars were found
if (count($matchingCars) > 0) {

    // header output with selected color
    echo "<h2>Cars in " . ucfirst($color) . "</h2>";

    //ul start
    echo "<ul>";

    //looping through matching cars and outputting each of them as a list item
    foreach ($matchingCars as $car) {
        echo "<li>" . $car[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "No cars found in " . ucfirst($color);
}

exit();