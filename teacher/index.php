<?php
session_start();
require("./teacherutil/teacher_functions.php");

redirect_to_login_if_not_valid_teacher();

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
            <li class="nav-item"><a href="index.html">Question Bank</a></li>
            <li class="nav-item"><a href="exams.html">Exams</a></li>
            <li class="nav-item"><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <script>
        for (let i = 0; i < 5; i++) {
            let make = document.createElement("div"); //Creates the div
            make.id = i //Adds an id to the div
            make.classList.add("TestClass"); //Adds a class to the div
            make.innerHTML = i; //Adds text to the div
            document.body.appendChild(make); //Appends the div to the body of the HTML page
        }
    </script>
</body>
</html>