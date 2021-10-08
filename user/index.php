<?php
session_start();
require("./userutil/userfunctions.php");

redirect_to_login_if_not_valid_user();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet"  href="../css/menu.css">
    </head>
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="index.php">Oustanding Exams</a></li>
                <li class="nav-item"><a href="grades.html">Grades</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </body>

    <script>
        for (let i = 0; i < 5; i++) {
            let make = document.createElement("div"); //Creates the div
            make.id = i //Adds an id to the div
            make.classList.add("TestClass"); //Adds a class to the div
            make.innerHTML = i; //Adds text to the div
            document.body.appendChild(make); //Appends the div to the body of the HTML page
        }
    </script>
</html>