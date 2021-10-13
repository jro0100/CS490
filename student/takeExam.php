<?php
session_start();
require("./studentutil/student_functions.php");
require ("../util/functions.php");
redirect_to_login_if_not_valid_student();

if (!isset($_GET["examID"])) {
    header("Location: ./");
    exit();
}

// Verify that the exam ID given actually exists, can be taken by the current student, and has not been grade/released yet
$stmtstring = "SELECT * FROM exams WHERE examID = :examID AND teacherID = :teacherID AND released = 0 AND gradedByTeacher = 0";
$params = array(":examID" => $_GET["examID"],
    ":teacherID" => $_SESSION["teacherID"]);
$result = db_execute($stmtstring, $params)[0];
if (!$result) {
    header("Location: ./");
    exit();
}

$stmtstring = "SELECT questionID, maxPoints from questionsonexam WHERE examID = :examID";
$params = array(":examID" => $_GET["examID"]);
$questionIDScoreArray = db_execute($stmtstring, $params);

$stmtstring = "SELECT questionID, question, questionType, difficulty FROM questionbank WHERE questionID = :questionID";
$params = array();
foreach ($questionIDScoreArray as $question) {
    array_push($params, array(":questionID" => $question["questionID"]));
}
$questionArray = db_execute_query_multiple_times($stmtstring, $params);

// Populate questionArray with point values
for ($i = 0; $i < count($questionIDScoreArray); $i++) {
    $questionArray[$i]["points"] = $questionIDScoreArray[$i]["maxPoints"];
}

$json = "[]";
if ($questionArray) {
    $json = json_encode($questionArray);
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet"  href="../css/menu.css">
        <link rel="stylesheet"  href="../css/main.css">
        <link rel="stylesheet"  href="../css/student/stuExams.css">
    </head>
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="index.php">Oustanding Exams</a></li>
                <li class="nav-item"><a href="grades.php">Grades</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </body>

    <script>
        var text = <?php echo $json ?>;

        form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "index.php");

        for (i = 0; i < text.length; i++) {
            const obj = JSON.parse(JSON.stringify(text[i]));

            //Create row
            row = document.createElement("div");
            row.classList.add("row");

            //Create column
            column = document.createElement("div");
            column.classList.add("column");

            question = document.createElement("p");
            question.classList.add("center-column-text");
            question.innerHTML = (i + 1) + ".) " + obj.question;

            points = document.createElement("p");
            points.classList.add("center-column-text");
            points.innerHTML = "(Points: " + obj.points + ")";

            centerDiv = document.createElement("div");
            centerDiv.classList.add("center");
            textArea = document.createElement("textarea");
            textArea.classList.add("text-area");
            textArea.setAttribute("name", obj.questionID);
            
            textArea.setAttribute("id", obj.questionID);
            centerDiv.appendChild(textArea);
            column.appendChild(question);
            column.appendChild(points);
            column.appendChild(centerDiv);
            row.appendChild(column);
            form.appendChild(row);
        }

        centerButton = document.createElement("div");
        centerButton.classList.add("center");
        submitExam = document.createElement("input");
        submitExam.setAttribute("type", "submit");
        submitExam.setAttribute("name", "submitExam");
        submitExam.classList.add("submitButton");
        centerButton.appendChild(submitExam);

        form.appendChild(centerButton);
        document.body.appendChild(form);
    </script>
</html>