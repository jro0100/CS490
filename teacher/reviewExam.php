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

    generate_student_outputs($studentAnswers, $studentID, $examID);

    $examName = get_exam_name($examID);
    array_unshift($studentAnswers, array("examName" => $examName));

    $json = "[]";
    if ($studentAnswers) {
        $json = json_encode($studentAnswers);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet"  href="../css/menu.css">
        <link rel="stylesheet"  href="../css/reviewExam.css">
        <title>Review Exam</title>
    </head>
    <body <?php if (isset($_GET["saved"]) && $_GET["saved"] == "1") echo 'onload="saveChangesToast()"'; ?>>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="./">Question Bank</a></li>
                <li class="nav-item"><a href="exams.php">Exams</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>

        <form method="get" action="reviewExam.php">
            <div class="center">
                <input type="hidden" name="examID" value="<?php echo $examID ?>">
                <select name="studentID" id="student" onchange=selectNewStudent(this)>
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
            <div>
        </form>

        <!--<div class="row">
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
        </div>-->

        <div id="snackbar">Changes Saved!</div>

        <script>
            var text = <?php echo $json ?>;

            form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "saveChanges.php");

            document.title = "Review - " + text[0].examName;

            row = document.createElement("div");
            row.classList.add("row");

            quesionTitle = document.createElement("div");
            quesionTitle.classList.add("column");
            quesionTitle.classList.add("center-column-text");
            quesionTitle.innerHTML = "Question";

            answerTitle = document.createElement("div");
            answerTitle.classList.add("column");
            answerTitle.classList.add("center-column-text");
            answerTitle.innerHTML = "Answer";

            pointsTitle = document.createElement("div");
            pointsTitle.classList.add("column");
            pointsTitle.classList.add("center-column-text");
            pointsTitle.innerHTML = "Points";

            commentTitle = document.createElement("div");
            commentTitle.classList.add("column");
            commentTitle.classList.add("center-column-text");
            commentTitle.innerHTML = "Comment";

            row.appendChild(questionTitle);
            row.appendChild(answerTitle);
            row.appendChild(pointsTitle);
            row.appendChild(commentTitle);

            document.appendChild(row);
            
    
            for (i = 1; i < text.length; i++) {
    
                const obj = text[i];
    
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
                //answerCol.classList.add("center-column-text");
                answerTextArea = document.createElement("textarea");
                answerTextArea.readOnly = true;
                answerTextArea.style.width = "100%";
                answerTextArea.style.height = "100%";
                answerTextArea.style.resize = "none";
                answerTextArea.value = obj.studentAnswer;
                answerCol.appendChild(answerTextArea);


                centerTable = document.createElement("div");
                centerTable.classList.add("center-table");

                table = document.createElement("table");
                table.setAttribute("border", "1");
                tr = document.createElement("tr");
                th1 = document.createElement("th");
                th1.innerHTML = "Test Case";
                th2 = document.createElement("th");
                th2.innerHTML = "Output";
                th3 = document.createElement("th");
                th3.innerHTML = "Auto";
                th4 = document.createElement("th");
                th4.innerHTML = "Worth";
                th5 = document.createElement("th");
                th5.innerHTML = "Final";

                tr.appendChild(th1);
                tr.appendChild(th2);
                tr.appendChild(th4);
                tr.appendChild(th3);
                tr.appendChild(th5);
                table.appendChild(tr);

                scoreTotal = 0;
                autogradeTotal = 0;
                worthTotal = 0;

                for(y = 0; y < obj.autogradeOutputs.length; y++) {
                    tr = document.createElement("tr");
                    td1 = document.createElement("td");
                    td1.classList.add("test-case");
                    td1.innerHTML = obj.autogradeOutputs[y].correctOutput;
                    td2 = document.createElement("td");
                    td2.classList.add("output");
                    td2.innerHTML = obj.autogradeOutputs[y].studentOutput;
                    td3 = document.createElement("td");
                    td3.innerHTML = obj.autogradeOutputs[y].autoGradeScore;
                    td4 = document.createElement("td");
                    td4.innerHTML = obj.autogradeOutputs[y].maxPoints;
                    td5 = document.createElement("td");

                    pointsAchieved = document.createElement("input");
                    pointsAchieved.setAttribute("type", "text");
                    pointsAchieved.classList.add("points-achieved");
                    pointsAchieved.setAttribute("name", "teacherScore-" + obj.autogradeOutputs[y].studentTestCaseID);
                    pointsAchieved.setAttribute("size", "1");
                    pointsAchieved.value = obj.autogradeOutputs[y].teacherScore;

                    scoreTotal += parseInt(obj.autogradeOutputs[y].teacherScore, 10);
                    autogradeTotal += parseInt(obj.autogradeOutputs[y].autoGradeScore, 10);
                    worthTotal += parseInt(obj.autogradeOutputs[y].maxPoints, 10);

                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    tr.appendChild(td4);
                    tr.appendChild(td3);
                    td5.appendChild(pointsAchieved);
                    tr.appendChild(td5);
                    table.appendChild(tr);
                }

                let scoreRow = document.createElement("tr");
                elem = document.createElement("td");
                elem.innerHTML = "Total";
                scoreRow.appendChild(elem);
                for (let c = 0; c < 4; c++) {
                    elem = document.createElement("td");
                    switch (c) {
                        case 1:
                            elem.innerHTML = worthTotal;
                            break;
                        case 2:
                            elem.innerHTML = autogradeTotal;
                            break;
                        case 3:
                            elem.innerHTML = scoreTotal;
                            break;
                        default:
                            elem.innerHTML = "-";
                            break;
                    }
                    scoreRow.appendChild(elem);
                }
                table.appendChild(scoreRow);

                commentCol = document.createElement("div");
                commentCol.classList.add("columnHeader");
                comment = document.createElement("input");
                comment.classList.add("input-text-field");
                comment.setAttribute("type", "text");
                comment.setAttribute("name", "teacherComment-" + obj.questionID);
                if (obj.teacherComment == "") {
                    comment.value = "-";
                } else {
                    comment.value = obj.teacherComment;
                }
                commentCol.appendChild(comment);
    
                row.appendChild(questionCol);
                row.appendChild(answerCol);
                centerTable.appendChild(table);
                row.appendChild(centerTable);
                row.appendChild(commentCol);
                form.appendChild(row);
            }
            buttonDiv = document.createElement("div");
            buttonDiv.classList.add("center");
            saveChanges = document.createElement("input");
            saveChanges.setAttribute("type", "submit");
            saveChanges.setAttribute("class", "submitButton");
            saveChanges.setAttribute("name", "saveChanges");
            //saveChanges.setAttribute("onclick", "releaseExamToast()");
            saveChanges.value = "Save Changes";
            buttonDiv.appendChild(saveChanges);
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
            form.appendChild(examIDInfo);
    
            document.body.appendChild(form); //Appends the div to the body of the HTML page

            // Redirect review exam page to next student when student selector changes
            function selectNewStudent(selector) {
                const examID = document.getElementsByName("examID")[0].value;
                const studentID = selector.value;
                location.href = "reviewExam.php?examID=" + examID + "&studentID=" + studentID;
            }
        </script>

        <!-- The following is the JS for alerting the user that the exams have been released -->
        <script>
            function saveChangesToast() {
                // Get the toast div
                var toast = document.getElementById("snackbar");

                // Add the "show" class to div
                toast.className = "show";

                // After 3 seconds, remove the show class from DIV
                setTimeout(function(){ toast.className = toast.className.replace("show", ""); }, 3000);
            }
        </script>
    </body>
</html>