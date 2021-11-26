<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

$sqlstmt = "SELECT * FROM questionbank WHERE teacherID = :teacherID";
$params = array(":teacherID" => $_SESSION["teacherID"]);
$result = db_execute($sqlstmt, $params);

$json = "[]";

if ($result) {
    $json = str_replace("\\r\\n", "<br>", json_encode($result));
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet"  href="../css/menu.css">
    <link rel="stylesheet"  href="../css/teacher/index.css">
    <link rel="stylesheet"  href="../css/teacher/createExam.css">
    <title>Create Exam</title>
</head>
<body>

    <nav class="navbar">
        <ul class="nav-links">
            <li class="nav-item"><a href="./">Question Bank</a></li>
            <li class="nav-item"><a href="exams.php">Exams</a></li>
            <li class="nav-item"><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="float-container">
        <div class="float-child" id="leftCol">
            <div class="center-column-text-header" id="header">
                Question Bank
            </div>
            <div class = "dropdowns">
                <label for="typeFilter">Question Type</label><br>
                <select name="typeFilter" id="typeFilter" style="font-size:17px;" onchange="filterApplied()">
                    <option value="allTypes" selected>All</option>
                    <option value="general">General</option>
                    <option value="forLoop" >For Loop</option>
                    <option value="whileLoop">While Loop</option>
                    <option value="recursion">Recursion</option>
                    <option value="conditionals">Conditionals</option>
                    <option value="strings">Strings</option>
                </select><br>
            </div>
            <div class = "dropdowns" id="lastDiffFilter">
                <label for="diffFilter">Difficulty</label><br>
                <select name="diffFilter" id="diffFilter" style="font-size:17px;" onchange="filterApplied()">
                    <option value="allDifficulties">All</option>
                    <option value="Easy">Easy</option>
                    <option value="Medium">Medium</option>
                    <option value="Hard">Hard</option>
                </select><br>
            </div>
        </div>
            <div class="float-child" id="rightCol">
                    <div class="center-column-text-header">
                        Selected Questions
                    </div>
            </div>
    </div>


    <script>
        var text = <?php echo $json ?>;

        rightForm = document.createElement("form");
        rightForm.setAttribute("id", "rightForm");
        rightForm.setAttribute("method", "post");
        rightForm.setAttribute("action", "exams.php");
        rightColumn = document.getElementById("rightCol");
        rightColumn.appendChild(rightForm);

        cDiv = document.createElement("div");
        cDiv.classList.add("center");
        input = document.createElement("input");
        input.setAttribute("type", "text");
        input.setAttribute("name", "examName");
        input.setAttribute("placeholder", "Exam Name");
        input.classList.add("exam-name-light-background");
        input.required = true;
        cDiv.appendChild(input);
        rightForm.appendChild(cDiv);

        const filters = ["allTypes", "allDifficulties"];

        const objArr = [];
        const objArrRight = []
        for (i = 0; i < text.length; i++) {
            const obj = text[i];
            objArr.push(obj);

        }

        /* This still contains a slight bug. Almost ready to implement filter feature.*/

        printQuestionBank(filters[0], filters[1]);

        function printQuestionBank(typeFilter, diffFilter) {
            leftColClear = document.getElementById("leftCol");
            while(leftColClear.lastChild.id !== "lastDiffFilter") {
                leftColClear.removeChild(leftColClear.lastChild);
            }
            for (i = 0; i < text.length; i++) {

                difficulty = "";
                if(objArr[i].difficulty == 0) {
                    difficulty = "Easy";
                } else if (objArr[i].difficulty == 1) {
                    difficulty = "Medium";
                } else {
                    difficulty = "Hard";
                }

                if(typeFilter != "allTypes" && typeFilter != objArr[i].questionType) { continue; }
                if(diffFilter != "allDifficulties" && diffFilter != difficulty) { continue; }

                leftQuestion = document.createElement("div");
                leftQuestion.setAttribute("id", "l" + i);

                centerDiv = document.createElement("div");
                centerDiv.classList.add("center-column-text");

                question = document.createElement("p");
                question.classList.add("center-text");
                question.innerHTML = objArr[i].question;

                typeAndDif = document.createElement("p");
                typeAndDif.classList.add("center-text");
                typeAndDif.innerHTML = "Type: " + objArr[i].questionType + "&emsp;" + "Difficulty: " + difficulty;

                checkBox = document.createElement("input");
                checkBox.setAttribute("type", "checkbox");
                checkBox.setAttribute("class", "check");
                checkBox.setAttribute("onclick", "checkBoxClicked();")
                checkBox.setAttribute("id", objArr[i].questionID);
                checkBox.setAttribute("value", objArr[i].questionID);
                checkBox.setAttribute("name", objArr[i].questionID);

                leftCol = document.getElementById("leftCol");
                
                centerDiv.appendChild(question);
                centerDiv.appendChild(typeAndDif);
                centerDiv.appendChild(checkBox);
                leftQuestion.appendChild(centerDiv);
                leftCol.appendChild(leftQuestion);
            }
        }

        function checkBoxClicked() {
            leftChildren = document.getElementById("leftCol").children;
            for(i = 1; i < leftChildren.length; i++) {
                input = leftChildren[i].getElementsByTagName("input");
                for(y = 0; y < input.length; y++) {
                    if(input[y].checked) {
                        pointVal = document.createElement("input");
                        pointVal.setAttribute("type", "text");
                        pointVal.setAttribute("name", input[y].name);
                        pointVal.setAttribute("placeholder", "Point Value");
                        pointVal.setAttribute("pattern", "^[1-9][0-9]*$");
                        pointVal.classList.add("point-val-field");
                        rightCol = document.getElementById("rightCol");
                        leftChildren[i].firstChild.appendChild(pointVal);
                        rightChildren = document.getElementById("rightForm").children;
                        for(z = 0; z < objArr.length; z++) {
                            if(objArr[z].questionID == input[y].id) {
                                objArrRight.push(objArr[z]);
                                objArr.splice(z, 1);
                                break;
                            }
                        }
                        if(rightChildren.length == 1) {
                            rightForm.appendChild(leftChildren[i]);
                            addSubmitButton();
                        } else {
                            rightForm.lastChild.previousSibling.after(leftChildren[i])
                        }
                    }
                }
            }

            rightChildren = document.getElementById("rightForm").children;
            for(i = 1; i < rightChildren.length; i++) {
                input = rightChildren[i].getElementsByTagName("input");
                if(input[0].checked == false && input[0].type == "checkbox") {
                    for(j = 0; j < objArrRight.length; j++) {
                        if(objArrRight[j].questionID == input[0].id) {
                            objArr.push(objArrRight[j]);
                            objArrRight.splice(j, 1);
                            break;
                        }
                    }
                    input[1].parentNode.removeChild(input[1]);
                    leftCol = document.getElementById("leftCol");
                    leftCol.appendChild(rightChildren[i]);
                    if(rightChildren.length == 2) {
                        form = document.getElementById("rightForm");
                        form.removeChild(form.lastChild);
                    }
                    printQuestionBank(filters[0], filters[1]);
                }
            }
        }

        function addSubmitButton() {
            centerSubmit = document.createElement("div");
            centerSubmit.classList.add("center");

            createExamButton = document.createElement("input");
            createExamButton.setAttribute("type", "submit");
            createExamButton.setAttribute("class", "submitButton");
            createExamButton.setAttribute("name", "saveExam");
            createExamButton.value = "Save Exam";
            
            centerSubmit.appendChild(createExamButton);
            rightForm.appendChild(centerSubmit);
        }

        function filterApplied() {
            typeFil = document.getElementById("typeFilter");
            difficultyFil = document.getElementById("diffFilter");
            filters[0] = typeFil.value;
            filters[1] = difficultyFil.value;
            printQuestionBank(filters[0], filters[1]);
        }
    </script>
</body>
</html>