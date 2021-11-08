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
    <link rel="stylesheet"  href="../css/teacher/questionBank.css">
</head>
<body>
    <div class="center">
        <label for="typeFilter">Question Type</label><br>
        <select name="typeFilter" id="typeFilter" onchange="filterApplied()">
            <option value="allTypes" selected>All</option>
            <option value="default">General</option>
            <option value="forLoop" >For Loop</option>
            <option value="whileLoop">While Loop</option>
            <option value="recursion">Recursion</option>
        </select><br>

        <label for="diffFilter">Difficulty</label><br>
        <select name="diffFilter" id="diffFilter" onchange="filterApplied()">
            <option value="allDifficulties">All</option>
            <option value="Easy">Easy</option>
            <option value="Medium">Medium</option>
            <option value="Hard">Hard</option>
        </select><br>
    </div>

    <div id="questionDiv"></div>

    <script>
        var text = <?php echo $json ?>;;
        printAll("allTypes", "allDifficulties");

        function printAll(typeFilter, diffFilter) {     
            questionDivClear = document.getElementById("questionDiv");
            if(questionDivClear) { 
                alert("Clearing questions");
                questionDivClear.innerHTML = "" }

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

                if(typeFilter != "allTypes" && typeFilter != obj.questionType) { continue; }
                if(diffFilter != "allDifficulties" && diffFilter != difficulty) { continue; }

                questionDiv = document.getElementById("questionDiv");

                //Create row
                row = document.createElement("div");
                row.classList.add("row");

                //Create column
                column = document.createElement("div");
                column.classList.add("column");

                question = document.createElement("p");
                question.classList.add("center-question");
                question.innerHTML = obj.question;

                typeAndDif = document.createElement("p");
                typeAndDif.classList.add("center-question");
                typeAndDif.innerHTML = "Type: " + obj.questionType + "&emsp;" + "Difficulty: " + difficulty;

                /********** Used to make the question clickable to edit **********/
                //aTag = document.createElement("a");
                //aTag.setAttribute("href", "question.php?questionID=" + obj.questionID);
                //aTag.appendChild(question);
                //aTag.appendChild(typeAndDif);
                //column.appendChild(aTag);
                column.appendChild(question);
                column.appendChild(typeAndDif);
                row.appendChild(column);
                questionDiv.appendChild(row);
                document.body.appendChild(questionDiv); //Appends the div to the body of the HTML page
            }

            if (text.length == 0) {
                emptiness = document.createElement("div");
                emptiness.classList.add("center-column-text");
                emptiness.innerHTML = "NO QUESTIONS EXIST YET!";
                document.body.appendChild(emptiness);
            }
        }

        function filterApplied() {
            typeFil = document.getElementById("typeFilter");
            difficultyFil = document.getElementById("diffFilter");
            printAll(typeFil.value, difficultyFil.value);
        }
    </script>
</body>
</html>
