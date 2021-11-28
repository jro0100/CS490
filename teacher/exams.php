<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

if (isset($_POST["releaseExam"])) {
    $examID = $_POST["releaseExam"];
    $sqlstmt = "UPDATE exams SET released = 1 WHERE examID = :examID";
    $params = array(":examID" => $examID);
    db_execute($sqlstmt, $params);

    $sqlstmt = "UPDATE studentexam SET completedByStudent = 1 WHERE examID = :examID";
    $params = array(":examID" => $examID);
    db_execute($sqlstmt, $params);

} elseif (isset($_POST["saveExam"])) {
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
    foreach ($_POST as $questionID => $pointVal) {
        array_push($params, array(
            ":questionID" => $questionID,
            ":examID" => $examID,
            ":maxPoints" => $pointVal
        ));
        array_push($questionIDArray, $questionID);
    }
    db_execute_query_multiple_times($sqlstmt, $params);


    // Create entry for each student in studentexam table
    $sqlstmt = "SELECT studentID FROM student WHERE teacherID = :teacherID";
    $params = array(":teacherID" => $_SESSION["teacherID"]);
    $result = db_execute($sqlstmt, $params);

    $sqlstmt = "INSERT INTO studentexam (studentID, examID, completedByStudent, studentGrade) VALUES (:studentID, :examID, 0, 0)";
    $params = array();
    foreach ($result as $student) {
        array_push($params, array(":studentID" => $student["studentID"],
            ":examID" => $examID));
        array_push($studentIDArray, $student["studentID"]);
    }
    db_execute_query_multiple_times($sqlstmt, $params);


    // Create default grade and answer of 0 for each student in questiongrade table, to be updated when they actually
    // take and submit exam
    $sqlstmt = "INSERT INTO questiongrade (studentID, examID, questionID, achievedScore) VALUES (:studentID, :examID, :questionID, :achievedScore)";
    $params = array();
    foreach ($questionIDArray as $questionID) {
        foreach ($studentIDArray as $studentID) {
            array_push($params, array(
                ":studentID" => $studentID,
                ":examID" => $examID,
                ":questionID" => $questionID,
                ":achievedScore" => 0));
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
    <title>Exams</title>
</head>
<body <?php if (isset($_POST["releaseExam"])) echo 'onload="releaseExamToast()"' ?>>
    <nav class="navbar">
        <ul class="nav-links">
            <li class="nav-item"><a href="./">Question Bank</a></li>
            <li class="nav-item"><a href="exams.php">Exams</a></li>
            <li class="nav-item"><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <form action="createExam.php">
        <div class="center">
            <input type="submit" class="submitButton" name="createExam" value="Create Exam">
        </div>
    </form>

    <div class="float-container">
        <div class="float-child" id="leftCol">
            <div class="title">
                Unreleased Exams
            </div>
        </div>
        <div class="float-child" id="rightCol">
            <div class="title">
                Released Exams
            </div>
        </div>
    </div>

    <div id="snackbar">Exam Released!</div>

    <script>
        var text = <?php echo $json ?>;

        for (i = 0; i < text.length; i++) {

            const obj = text[i];

            createdExamBox = document.createElement("div");
            createdExamBox.classList.add("exam-layout");

            createdExam = document.createElement("div");
            createdExam.classList.add("center-column-text");


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
            form.setAttribute("method", "post");
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

            createdExam.appendChild(exam);
            createdExam.appendChild(cDiv);
            if(obj.released === "0") {
                createdExam.appendChild(centerDiv);
            }
            createdExamBox.appendChild(createdExam);

            leftCol = document.getElementById("leftCol");
            rightCol = document.getElementById("rightCol");
            if(obj.released === "1") { rightCol.appendChild(createdExamBox); }
            else { leftCol.appendChild(createdExamBox); }
        }

        if (text.length == 0) {
            emptiness = document.createElement("div");
            emptiness.classList.add("center-column-text");
            emptiness.innerHTML = "NO EXAMS EXIST YET!";
            document.body.appendChild(emptiness);
        }
    </script>

    <!-- The following is the JS for alerting the user that the exams have been released -->
    <script>
        function releaseExamToast() {
            // Get the toast div
            var toast = document.getElementById("snackbar");

            // Add the "show" class to div
            toast.className = "show";

            // After 3 seconds, remove the show class from div
            setTimeout(function(){ toast.className = toast.className.replace("show", ""); }, 3000);
        }
    </script>
</body>
</html>