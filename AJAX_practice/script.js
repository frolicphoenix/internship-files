//ajax request start
function getCarsByColor(color) {

    //creating a new object
    var xhttp = new XMLHttpRequest();

    //defining what happens when state changes
    xhttp.onreadystatechange = function() {

        //check if the request is complete
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("result").innerHTML = this.responseText;
        }
    };

    //the GET request is prepared
    xhttp.open("GET", "get_cars.php?color=" + color, true);
    xhttp.send();
}