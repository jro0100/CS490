<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

if (isset($_POST["saveExam"])) {
    unset($_POST["saveExam"]);

    // Save and unset exam name from POST array to easily iterate over array to pull out question ID's
    $examName = $_POST["examName"];
    unset($_POST["examName"]);

    $examID = "holder";
    $sqlstmt = "INSERT INTO exams (teacherID, examName, released, gradedByTeacher) VALUES (:teacherID, :examName, 0, 0)";
    $params = array(":teacherID" => $_SESSION["teacherID"],
        "examName" => $examName);
    db_execute($sqlstmt, $params, $examID);

    // Save list of all question and student ID's in this exam
    $questionIDArray = array();
    $studentIDArray = array();

    $sqlstmt = "INSERT INTO questionsonexam (questionID, examID, maxPoints) VALUES (:questionID, :examID, :maxPoints)";
    $params = array();
    foreach ($_POST as $key => $val) {
        $questionID = explode("-", $key)[0];
        array_push($params, array(
            ":questionID" => $questionID,
            ":examID" => $examID,
            ":maxPoints" => $val
        ));
        array_push($questionIDArray, $questionID);
    }
    db_execute_query_multiple_times($sqlstmt, $params);


    // Create entry for each student in studentexam table
    $sqlstmt = "SELECT studentID FROM student WHERE teacherID = :teacherID";
    $params = array(":teacherID" => $_SESSION["teacherID"]);
    $result = db_execute($sqlstmt, $params);

    $sqlstmt = "INSERT INTO studentexam (studentID, examID, completedByStudent) VALUES (:studentID, :examID, 0)";
    $params = array();
    foreach ($result as $student) {
        array_push($params, array(":studentID" => $student["studentID"],
            ":examID" => $examID));
        array_push($studentIDArray, $student["studentID"]);
    }
    db_execute_query_multiple_times($sqlstmt, $params);


    // Create default grade and answer of 0 for each student in questiongrade table, to be updated when they actually
    // take and submit exam
    $sqlstmt = "INSERT INTO questiongrade (studentID, examID, questionID) VALUES (:studentID, :examID, :questionID)";
    $params = array();
    foreach ($questionIDArray as $questionID) {
        foreach ($studentIDArray as $studentID) {
            array_push($params, array(
                ":studentID" => $studentID,
                ":examID" => $examID,
                ":questionID" => $questionID));
        }
    }
    db_execute_query_multiple_times($sqlstmt, $params);
}

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
    <link rel="stylesheet"  href="../css/teacher/exams.css">
</head>
<body>
    <nav class="navbar">
        <ul class="nav-links">
            <li class="nav-item"><a href="index.php">Question Bank</a></li>
            <li class="nav-item"><a href="exams.php">Exams</a></li>
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

            //Create review exam button
            cDiv = document.createElement("div");
            cDiv.classList.add("center");
            fo = document.createElement("form");
            fo.setAttribute("method", "get");
            fo.setAttribute("action", "reviewExam.php");
            sub = document.createElement("button");
            sub.setAttribute("type", "submit");
            sub.setAttribute("name", "examID");
            sub.setAttribute("class", "reviewExam");
            sub.setAttribute("value", obj.examID);
            sub.innerHTML = "Review Exam";

            //Create release exam button
            centerDiv = document.createElement("div");
            centerDiv.classList.add("center");
            form = document.createElement("form");
            form.setAttribute("method", "get");
            form.setAttribute("action", "exams.php");
            submit = document.createElement("button");
            submit.setAttribute("type", "submit");
            submit.setAttribute("name", "releaseExam");
            submit.setAttribute("class", "releaseExam");
            submit.setAttribute("value", obj.examID);
            submit.innerHTML = "Release Exam";

            //Add review Exam Button
            fo.appendChild(sub);
            cDiv.appendChild(fo);

            //Add release Exam Button
            form.appendChild(submit);
            centerDiv.appendChild(form);

            column.appendChild(exam);
            column.appendChild(cDiv);
            column.appendChild(centerDiv); //Add button to release the exam

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