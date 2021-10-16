<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

if (!isset($_GET["examID"])) {
    header("Location: exams.php");
    exit();
}

$examID = $_GET["examID"];

$sqlstmt = "SELECT studentID FROM studentexam WHERE examID = :examID";
$params = array(":examID" => $examID);
$studentIDs = db_execute($sqlstmt, $params);

$studentID = null;
if (isset($_GET["studentID"])) {
    $studentID = $_GET["studentID"];
} else {
    header("Location: reviewExam.php?examID=$examID&studentID=" . $studentIDs[0]["studentID"]);
    exit();
}

// Generate array of JSON objects with student's information for each question
if ($studentID) {
    $sqlstmt = "SELECT questiongrade.*, questionbank.question FROM questiongrade LEFT JOIN questionbank ON questiongrade.questionID = questionbank.questionID WHERE studentID = :studentID AND examID = :examID";
    $params = array(":studentID" => $studentID,
        ":examID" => $examID);
    $studentAnswers = db_execute($sqlstmt, $params);
    $json = "[]";
    if ($studentAnswers) {
        $json = str_replace("\\r\\n", "<br>", json_encode($studentAnswers));
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet"  href="../css/menu.css">
        <link rel="stylesheet"  href="../css/reviewExam.css">
    </head>
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="index.php">Question Bank</a></li>
                <li class="nav-item"><a href="exams.php">Exams</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>

        <form method="get" action="reviewExam.php">
            <div class="center">
                <input type="hidden" name="examID" value="<?php echo $examID ?>">
                <select name="studentID" id="student">
                    <?php

                    foreach ($studentIDs as $student) {
                        $sqlstmt = "SELECT student.studentName FROM student WHERE student.studentID IN (SELECT studentexam.studentID FROM studentexam WHERE student.studentID = :studentID AND examID = :examID)";
                        $params = array(":studentID" => $student["studentID"],
                            ":examID" => $examID);
                        $studentName = db_execute($sqlstmt, $params)[0]["studentName"];
                        $optionString = '<option value="' . $student["studentID"] . '"';
                        if ($student["studentID"] == $studentID) {
                            $optionString .= " selected";
                        }
                        $optionString .= ">$studentName</option>";
                        echo $optionString;
                    }
                    ?>
                </select><br>
                <input type="submit" class="submitButton"></input>
            <div>
        </form>

        <div class="row">
            <div class="columnHeader">
                <h1 class="center-column-text-font-size">
                    Question
                </h1>
            </div>
            <div class="columnHeader">
                <h1 class="center-column-text-font-size">
                    Answer
                </h1>
            </div>
            <div class="columnHeader">
                <h1 class="center-column-text-font-size">
                    Points
                </h1>
            </div>
            <div class="columnHeader">
                <h1 class="center-column-text-font-size">
                    Comment
                </h1>
            </div>
        </div>

        <script>
            var text = <?php echo $json ?>;
            form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "saveChanges.php");
    
            for (i = 0; i < text.length; i++) {
    
                const obj = JSON.parse(JSON.stringify(text[i]));
    
                //Create row
                row = document.createElement("div");
                row.classList.add("row");
    
                //Create column
                questionCol = document.createElement("div");
                questionCol.classList.add("column");
                questionCol.classList.add("center-column-text");
                questionCol.innerHTML = obj.question;

                answerCol = document.createElement("div");
                answerCol.classList.add("column");
                answerCol.classList.add("center-column-text");
                answerCol.innerHTML = obj.studentAnswer;

                pointsCol = document.createElement("div");
                pointsCol.classList.add("columnHeader");
                points = document.createElement("input");
                points.classList.add("input-text-field");
                points.setAttribute("type", "input");
                points.setAttribute("name", "achievedPoints-" + obj.questionID);
                points.value = obj.achievedPoints;
                pointsCol.appendChild(points);

                commentCol = document.createElement("div");
                commentCol.classList.add("columnHeader");
                comment = document.createElement("input");
                comment.classList.add("input-text-field");
                comment.setAttribute("type", "input");
                comment.setAttribute("name", "teacherComment-" + obj.questionID);
                if (obj.teacherComment == "") {
                    comment.value = "No Comment";
                } else {
                    comment.value = obj.teacherComment;
                }
                commentCol.appendChild(comment);
    
                row.appendChild(questionCol);
                row.appendChild(answerCol);
                row.appendChild(pointsCol);
                row.appendChild(commentCol);
                form.appendChild(row);
            }
            buttonDiv = document.createElement("div");
            buttonDiv.classList.add("center");
            createExamButton = document.createElement("input");
            createExamButton.setAttribute("type", "submit");
            createExamButton.setAttribute("class", "submitButton");
            createExamButton.setAttribute("name", "saveChanges");
            createExamButton.value = "Save Changes";
            buttonDiv.appendChild(createExamButton);
            form.appendChild(buttonDiv);

            studentIDInfo = document.createElement("input");
            studentIDInfo.setAttribute("type", "hidden");
            studentIDInfo.setAttribute("name", "studentID");
            studentIDInfo.setAttribute("value", "<?php echo $studentID ?>");
            form.appendChild(studentIDInfo);

            examIDInfo = document.createElement("input");
            examIDInfo.setAttribute("type", "hidden");
            examIDInfo.setAttribute("name", "examID");
            examIDInfo.setAttribute("value", "<?php echo $examID ?>");
            form.appendChild(examIDInfo)
    
            document.body.appendChild(form); //Appends the div to the body of the HTML page
        </script>
    </body>
</html>