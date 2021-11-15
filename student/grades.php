<?php
session_start();
require("./studentutil/student_functions.php");
require ("../util/functions.php");
redirect_to_login_if_not_valid_student();

$sqlstmt = "SELECT * FROM exams WHERE teacherID = :teacherID AND released = 1";
$params = array(":teacherID" => $_SESSION["teacherID"]);
$exams = db_execute($sqlstmt, $params);

for ($i = 0; $i < count($exams); $i++) {
    // Get student's score for exam
    $sqlstmt = "SELECT studentGrade FROM studentexam WHERE studentID = :studentID AND studentexam.examID = :examID";
    $params = array(
        ":studentID" => $_SESSION["studentID"],
        ":examID" => $exams[$i]["examID"]
    );
    $exams[$i]["studentTotalPoints"] = db_execute($sqlstmt, $params)[0]["studentGrade"];

    // Get maximum score for exam
    $sqlstmt = "SELECT SUM(maxPoints) AS maxPoints FROM questionsonexam WHERE examID = :examID";
    $params = array(
        ":examID" => $exams[$i]["examID"]
    );
    $exams[$i]["examMaxPoints"] = db_execute($sqlstmt, $params)[0]["maxPoints"];
}

$json = "[]";
if ($exams) {
    $json = json_encode($exams);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet"  href="../css/menu.css">
        <link rel="stylesheet"  href="../css/student/stuIndex.css">
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

        for (i = 0; i < text.length; i++) {
            const obj = JSON.parse(JSON.stringify(text[i]));

            //Create row
            row = document.createElement("div");
            row.classList.add("row");

            //Create column
            column = document.createElement("div");
            column.classList.add("column");

            aTag = document.createElement("a");
            aTag.setAttribute("href", "reviewExam.php?examID=" + obj.examID);

            examName = document.createElement("p");
            examName.classList.add("center-column-text");
            examName.innerHTML = obj.examName;

            points = document.createElement("p");
            points.classList.add("center-column-text");
            points.innerHTML = "GRADE: " + obj.studentTotalPoints + "/" + obj.examMaxPoints;

            aTag.appendChild(examName);
            aTag.appendChild(points);
            column.appendChild(aTag);
            row.appendChild(column);

            document.body.appendChild(row);
        }

        if (text.length == 0) {
            emptiness = document.createElement("div");
            emptiness.classList.add("center-column-text");
            emptiness.innerHTML = "YOU CURRENTLY HAVE NO GRADED EXAMS!";
            document.body.appendChild(emptiness);
        }
    </script>
</html>