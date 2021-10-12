<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

// Redirect to question bank if user did not come from the exam creation form
if (!isset($_POST["createExam"])) {
    header("Location: ./");
    exit();
}

$sqlstmt = "SELECT * FROM questionbank WHERE questionID = :questionID";
$params = array();

// Unset the createExam flag so that we can iterate over POST array and access questionID's without issue
unset($_POST["createExam"]);
foreach ($_POST as $q) {
    array_push($params, array(":questionID" => $q));
}

$result = db_execute_query_multiple_times($sqlstmt, $params);

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
    <link rel="stylesheet"  href="../css/teacher/index.css">
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

        form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "exams.php");

        examTag = docuemnt.createElement("div");
        examTag.classList.add("center-column-text");
        examTag.HTML = "Exam Name;"
        input = document.createElement("input");
        input.setAttribute("type", "text");
        input.setAttribute("name", "examName");
        input.classList.add("center-column-text");
        input.required = true;

        form.appendChild(examTag);
        form.appendChild(input);

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

            question = document.createElement("p");
            question.classList.add("center-column-text");
            question.innerHTML = obj.question;

            typeAndDif = document.createElement("p");
            typeAndDif.classList.add("center-column-text");
            typeAndDif.innerHTML = "Type: " + obj.questionType + "&emsp;" + "Difficulty: " + difficulty;
            
            input = document.createElement("input");
            input.setAttribute("type", "text");
            input.setAttribute("name", obj.questionID + "-pointvalue");
            input.setAttribute("placeholder", "Point Value");
            input.setAttribute("pattern", "^[1-9][0-9]*$");
            input.classList.add("center-column-text");
            input.required = true;

            column.appendChild(question);
            column.appendChild(typeAndDif);
            column.appendChild(input);
            row.appendChild(column);
            form.appendChild(row);
        }
        buttonDiv = document.createElement("div");
        buttonDiv.classList.add("center");
        createExamButton = document.createElement("input");
        createExamButton.setAttribute("type", "submit");
        createExamButton.setAttribute("class", "submitButton");
        createExamButton.setAttribute("name", "saveExam");
        createExamButton.value = "Save Exam";
        buttonDiv.appendChild(createExamButton);
        form.appendChild(buttonDiv);

        if (text.length == 0) {
            emptiness = document.createElement("div");
            emptiness.classList.add("center-column-text");
            emptiness.innerHTML = "NO QUESTIONS SELECTED TO MAKE EXAM!";
            document.body.appendChild(emptiness);
        } else {
            document.body.appendChild(form);
        }
    </script>
</body>
</html>