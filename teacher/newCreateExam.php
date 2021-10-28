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
    $json = json_encode($result);
}
// echo "Teacher ID: " . $_SESSION["teacherID"] . "<br>";
// echo $json;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet"  href="../css/menu.css">
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

    <div class="float-container">
        <div class="float-child" id="leftCol">
            <div class="center-column-text-header">
                Question Bank
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
        rightColumn = document.getElementById("rightCol");
        rightColumn.appendChild(rightForm);

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

            leftQuestion = document.createElement("div");
            leftQuestion.setAttribute("id", "l" + i);

            centerDiv = document.createElement("div");
            centerDiv.classList.add("center-column-text");

            question = document.createElement("p");
            question.classList.add("center-text");
            question.innerHTML = obj.question;

            typeAndDif = document.createElement("p");
            typeAndDif.classList.add("center-text");
            typeAndDif.innerHTML = "Type: " + obj.questionType + "&emsp;" + "Difficulty: " + difficulty;

            checkBox = document.createElement("input");
            checkBox.setAttribute("type", "checkbox");
            checkBox.setAttribute("class", "check");
            checkBox.setAttribute("id", "checkBox");
            checkBox.setAttribute("onclick", "checkBoxClicked();")
            checkBox.setAttribute("value", obj.questionID);
            checkBox.setAttribute("name", obj.questionID);

            leftCol = document.getElementById("leftCol");
            
            centerDiv.appendChild(question);
            centerDiv.appendChild(typeAndDif);
            centerDiv.appendChild(checkBox);
            leftQuestion.appendChild(centerDiv);
            leftCol.appendChild(leftQuestion);
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
                        rightForm.appendChild(leftChildren[i]);
                    }
                }
            }

            rightChildren = document.getElementById("rightForm").children;
            for(i = 0; i < rightChildren.length; i++) {
                input = rightChildren[i].getElementsByTagName("input");
                if(input[0].checked == false && input[0].id == "checkBox") {
                    input[1].parentNode.removeChild(input[1]);
                    leftCol = document.getElementById("leftCol");
                    leftCol.appendChild(rightChildren[i]);
                }
            }
        }
    </script>
</body>
</html>
