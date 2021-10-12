<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

$sqlstmt = "SELECT * FROM exams WHERE teacherID = :teacherID";
$params = array(":teacherID" => $_SESSION["teacherID"]);
$result = db_execute($sqlstmt, $params);

$json = "[]";
if ($result) {
    $json = json_encode($result);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet"  href="../css/menu.css">
    <link rel="stylesheet"  href="../css/main.css">
    <link rel="stylesheet"  href="../css/teacher/exams.css">
</head>
<body>
    <nav class="navbar">
        <ul class="nav-links">
            <li class="nav-item"><a href="index.php">Question Bank</a></li>
            <li class="nav-item"><a href="examList.php">Exams</a></li>
            <li class="nav-item"><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <script>
        var text = <?php echo $json ?>;

        for (i = 0; i < text.length; i++) {

            const obj = JSON.parse(JSON.stringify(text[i]));

            //Create row
            row = document.createElement("div");
            row.classList.add("row");

            //Create column
            column = document.createElement("div");
            column.classList.add("column");

            exam = document.createElement("p");
            exam.classList.add("center-column-text");
            exam.innerHTML = obj.examName;

            points = document.createElement("p");
            points.classList.add("center-column-text");
            points.innerHTML = "Points: " + obj.points;

            if(obj.released == false) {
                column.appendChild(exam);
                column.appendChild(points);
                //Add button to release the exam
                //column.appendChild(releaseButton)
            } else {
                //Add how many student have taken the exam over how many students are in the class
                aTag = document.createElement("a");
                aTag.setAttribute("href", "exam.php?examID=" + obj.examID);
                aTag.appendChild(exam);
                aTag.appendChild(points);
                column.appendChild(aTag);
            }

            row.appendChild(column);

            document.body.appendChild(row); //Appends the div to the body of the HTML page
        }

        if (text.length == 0) {
            emptiness = document.createElement("div");
            emptiness.classList.add("center-column-text");
            emptiness.innerHTML = "NO EXAMS EXIST YET!";
            document.body.appendChild(emptiness);
        }
    </script>
</body>
</html>