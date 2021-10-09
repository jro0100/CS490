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
// echo "Teacher ID: " . $_SESSION["teacherID"] . "<br>";
// echo $json;

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
    
    <script>
        var text = <?php echo $json ?>;

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

        if (text.length == 0) {
            emptiness = document.createElement("div");
            emptiness.classList.add("center-column-text");
            emptiness.innerHTML = "NO QUESTIONS EXIST YET!";
            document.body.appendChild(emptiness);
        }
    </script>
    
    <div style="text-align:center">
        <form action="question.php">
            <input type="submit" value="Create a question"/>
        </form>
    </div>

</body>
</html>