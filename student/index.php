<?php
session_start();
require("./studentutil/student_functions.php");
require ("../util/functions.php");
redirect_to_login_if_not_valid_student();

$sqlstmt = "SELECT exams.* FROM exams WHERE exams.examID IN (SELECT studentexam.examID FROM studentexam WHERE studentID = :studentID AND completedByStudent = 0)";
$params = array(":studentID" => $_SESSION["studentID"]);
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
        <link rel="stylesheet"  href="../css/student/stuIndex.css">
        <title>Outstanding Exams</title>
    </head>
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="./">Oustanding Exams</a></li>
                <li class="nav-item"><a href="grades.php">Grades</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </body>

    <script>
        var text = <?php echo $json ?>;

        for (i = 0; i < text.length; i++) {
            const obj = text[i];

            //Create row
            row = document.createElement("div");
            row.classList.add("row");

            //Create column
            column = document.createElement("div");
            column.classList.add("column");

            examName = document.createElement("p");
            examName.classList.add("center-column-text");
            examName.innerHTML = obj.examName;

            //Create release exam button
            centerDiv = document.createElement("div");
            centerDiv.classList.add("center");
            form = document.createElement("form");
            form.setAttribute("method", "get");
            form.setAttribute("action", "takeExam.php");
            submit = document.createElement("button");
            submit.setAttribute("type", "submit");
            submit.setAttribute("name", "examID");
            submit.setAttribute("class", "takeExam");
            submit.setAttribute("value", obj.examID);
            submit.innerHTML = "Take Exam";

            form.appendChild(submit);
            centerDiv.appendChild(form);

            column.appendChild(examName);
            column.appendChild(centerDiv);
            row.appendChild(column);

            document.body.appendChild(row);
        }

        if (text.length == 0) {
            emptiness = document.createElement("div");
            emptiness.classList.add("center-column-text");
            emptiness.innerHTML = "YOU HAVE NO OUTSTANDING EXAMS TO BE TAKEN!";
            document.body.appendChild(emptiness);
        }
    </script>
</html>