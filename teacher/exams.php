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

    $sqlstmt = "INSERT INTO questionsonexam (questionID, examID, maxPoints) VALUES (:questionID, :examID, :maxPoints)";
    $params = array();
    foreach ($_POST as $key => $val) {
        $questionID = explode("-", $key)[0];
        array_push($params, array(
            ":questionID" => $questionID,
            ":examID" => $examID,
            ":maxPoints" => $val
        ));
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
    <link rel="stylesheet"  href="../css/main.css">
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

            centerDiv = document.createElement("div");
            centerDiv.classList.add("center");
            form = document.createElement("form");
            form.setAttribute("method", "get");
            form.setAttribute("action", "exams.php");
            submit = document.createElement("button");
            submit.setAttribute("type", "submit");
            submit.setAttribute("name", "releaseExam");
            submit.setAttribute("value", obj.examID);
            submit.innerHTML = "Release Exam";
            
            //Add review exam button

            column.appendChild(exam);
            form.appendChild(submit);
            centerDiv.appendChild(form);
            column.appendChild(centerDiv) //Add button to release the exam

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