<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

$sqlstmt = "SELECT * FROM questionbank WHERE teacherID = :teacherID";
$params = array(":teacherID" => $_SESSION["teacherID"]);
$result = db_execute($sqlstmt, $params);

$json = "{}";

if ($result) {
    $json = htmlentities(json_encode($result));
}
echo "Teacher ID: " . $_SESSION["teacherID"] . "<br>";
echo $json;

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
            <li class="nav-item"><a href="index.php">Question Bank</a></li>
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