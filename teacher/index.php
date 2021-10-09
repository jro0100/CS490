<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

$sqlstmt = "SELECT * FROM questionbank WHERE teacherID = :teacherID";
$params = array(":teacherID" => $_SESSION["teacherID"]);
$result = db_execute($sqlstmt, $params);

$json = "[]";

if ($result) {
    $json = json_encode($result);
}
//echo "Teacher ID: " . $_SESSION["teacherID"] . "<br>";
//echo $json;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet"  href="../css/menu.css">
    <link rel="stylesheet"  href="../css/main.css">
</head>
<body>
    <nav class="navbar">
        <ul class="nav-links">
            <li class="nav-item"><a href="index.php">Question Bank</a></li>
            <li class="nav-item"><a href="exams.html">Exams</a></li>
            <li class="nav-item"><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <!--
    <div class="row">
        <div class="column">
            <form action="#" method="get" style="cursor:pointer">
                <input type="hidden" value="value">
                <a onclick="this.parentNode.submit();">
                    <p class="center-column-text">
                        Investing is an effective way to put your money to work and 
                        build wealth. Smart investing allows your money to 
                        outpace inflation and increase in value. <br>
                    </p>
                    <p class="center-column-text">
                        Type: For Loop &emsp; Difficulty: Easy
                    </p>
                </a>
        </form>
        </div>
    </div>
    -->

    
    <script>
        var text = [{"questionID":"5","teacherID":"5","question":"Write a function called &quotadd&quot that takes 2 numbers, adds them together, and returns the result","questionType":"Basic Function","difficulty":"0","parameterCount":"2","functionToCall":"add"},{"questionID":"15","teacherID":"5","question":"Write a function called &quotsubtract&quot that takes 2 numbers and subtracts the second number from the first one, then returns the result","questionType":"Basic Function","difficulty":"0","parameterCount":"2","functionToCall":"subtract"}];

        for (i = 0; i < text.length; i++) {

            const obj = JSON.parse(JSON.stringify(text[i]));

            difficulty = "";
            if(obj.difficulty == 0) {
                difficulty = "Easy";
            } else if (obj.difficulty == 1) {
                difficulty = "Medium";
            } else {
                difficulty = "Hard";
            }

            //Create row
            row = document.createElement("div");
            row.classList.add("row");

            //Create column
            column = document.createElement("div");
            column.classList.add("column");

            aTag = document.createElement("a");
            aTag.setAttribute("href", "question.php?questionID=" + obj.questionID);

            question = document.createElement("p");
            question.classList.add("center-column-text");
            question.innerHTML = obj.question;

            typeAndDif = document.createElement("p");
            typeAndDif.classList.add("center-column-text");
            typeAndDif.innerHTML = "Type: " + obj.questionType + "&emsp;" + "Difficulty: " + difficulty;

            aTag.appendChild(question);
            aTag.appendChild(typeAndDif);
            column.appendChild(aTag);
            row.appendChild(column);

            document.body.appendChild(row); //Appends the div to the body of the HTML page
        }
    </script>
</body>
</html>