<?php
session_start();
require("./studentutil/student_functions.php");
require ("../util/functions.php");
redirect_to_login_if_not_valid_student();

if (!isset($_GET["examID"])) {
    header("Location: grades.php");
    exit();
}

$examID = $_GET["examID"];

generate_student_outputs($studentAnswers, $_SESSION["studentID"], $examID);

$examName = get_exam_name($examID);
array_unshift($studentAnswers, array("examName" => $examName));

$json = "[]";
if ($studentAnswers) {
    $json = json_encode($studentAnswers);
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
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="./">Oustanding Exams</a></li>
                <li class="nav-item"><a href="grades.php">Grades</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>

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

            document.title = "Review - " + text[0].examName;
    
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

                //Create column
                answerCol = document.createElement("div");
                answerCol.classList.add("column");
                //answerCol.classList.add("center-column-text");
                //answerCol.innerHTML = obj.studentAnswer;
                answerTextArea = document.createElement("textarea");
                answerTextArea.readOnly = true;
                answerTextArea.style.width = "100%";
                answerTextArea.style.height = "100%";
                answerTextArea.style.resize = "none";
                answerTextArea.value = obj.studentAnswer;
                answerCol.appendChild(answerTextArea);

                //Create column
                /*
                pointsCol = document.createElement("div");
                pointsCol.classList.add("columnHeader");
                points = document.createElement("input");
                points.classList.add("input-text-field");
                points.setAttribute("type", "input");
                points.disabled = true;
                points.value = obj.achievedPoints;
                pointsCol.appendChild(points);
                */
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
                    pointsAchieved.disabled = true;
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


                //Create column
                commentCol = document.createElement("div");
                commentCol.classList.add("columnHeader");
                comment = document.createElement("input");
                comment.classList.add("input-text-field");
                comment.setAttribute("type", "input");
                comment.setAttribute("name", "comment-" + obj.questionID);
                comment.disabled = true;
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
                document.body.appendChild(row);
            }
        </script>
    </body>
</html>