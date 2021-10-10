<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

if (isset($_POST["submitQuestion"])) {
    $params = array(
        ":question" => htmlentities($_POST["question"]),
        ":questionType" => htmlentities($_POST["questionType"]),
        ":difficulty" => $_POST["difficulty"],
        ":parameterCount" => $_POST["parameterCount"],
        ":functionToCall" => $_POST["functionToCall"]
    );
    if ($_POST["questionID"] != "") {
        $sqlstmt = "UPDATE questionbank SET question = :question, questionType = :questionType, difficulty = :difficulty, parameterCount = :parameterCount, functionToCall = :functionToCall WHERE questionID = " . $_POST["questionID"];
    } else {
        $sqlstmt = "INSERT INTO questionbank (teacherID, question, questionType, difficulty, parameterCount, functionToCall) VALUES (:teacherID, :question, :questionType, :difficulty, :parameterCount, :functionToCall)";
        $params[":teacherID"] = $_SESSION["teacherID"];
    }
    db_execute($sqlstmt, $params);
    header("Location: ./");
    exit();
} elseif (isset($_GET["questionID"])) {
    $questionID = $_GET["questionID"];
    $sqlstmt = "SELECT * FROM questionbank WHERE questionID = :questionID AND teacherID = :teacherID";
    $params = array(":questionID" => $questionID, ":teacherID" => $_SESSION["teacherID"]);
    $result = db_execute($sqlstmt, $params)[0];

    $question = str_replace('"', "&#34;", $result["question"]);
    $questionType = str_replace('"', "&#34;", $result["questionType"]);
    $difficulty = $result["difficulty"];
    $parameterCount = $result["parameterCount"];
    $functionToCall = $result["functionToCall"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet"  href="../css/menu.css">
    <!--<link rel="stylesheet"  href="../css/main.css">-->
    <link rel="stylesheet"  href="../css/teacher/question.css">
</head>
<body>
<nav class="navbar">
    <ul class="nav-links">
        <li class="nav-item"><a href="index.php">Question Bank</a></li>
        <li class="nav-item"><a href="exams.php">Exams</a></li>
        <li class="nav-item"><a href="../logout.php">Logout</a></li>
    </ul>
</nav>

    <div style="text-align:center">
        <form method="post" action="question.php" autocomplete="off">
            <input type="hidden" name="questionID" value="<?php if (isset($questionID)) echo $questionID ?>"><br>

            <label for="question">Question</label><br>
            <textarea style="width: 189px; height: 58px;"><?php if (isset($question)) echo $question ?></textarea><br>
            <!--<input type="text" class="textbox" name="question" id="question" value="<?php if (isset($question)) echo $question ?>"><br>-->

            <label for="questionType">Question Type</label>
            <input type="text" name="questionType" id="questionType" value="<?php if (isset($questionType)) echo $questionType ?>"><br>

            <!--
            <label for="difficulty" style="margin-top:30px">Difficulty</label>
            <select name="difficulty" id="difficulty" style="margin-top:30px">
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
                <option selected="selected">Medium</option>
            </select><br>
            -->
            <label for="difficulty" style="margin-top:30px">Difficulty (Easy, Medium, Hard)</label>
            <input type="text" name="difficulty" id="difficulty" value="<?php if (isset($difficulty)) echo $difficulty ?>"><br>

            <label for="parameterCount">Number of Parameters</label>
            <input type="text" name="parameterCount" id="parameterCount" value="<?php if (isset($parameterCount)) echo $parameterCount ?>"><br>

            <label for="testCasesCount">Number of Test Cases</label>
            <input type="text" name="testCasesCount" id="testCasesCount" value="<?php if (isset($parameterCount)) echo $parameterCount ?>"><br>

            <label for="functionToCall">Function Name</label>
            <input type="functionToCall" name="functionToCall" id="functionToCall" value="<?php if (isset($functionToCall)) echo $functionToCall ?>"><br>

            <div id="masterParent">
            </div>

            <input type="submit" class="submitButton" name="submitQuestion" value="Save Question"></input>
        </form>
    </div>
        
    <script>

        masterDiv = document.getElementById("masterParent");
        //masterDiv.id = "masterParent";

        let columnCount = document.getElementById('parameterCount');
        //Get number of columns when key event is triggered in number of parameters field
        columnCount.addEventListener('keyup', (event) => {
            colVal = document.getElementById('parameterCount').value;
            if(Number.isInteger(parseInt(colVal))) {
                document.getElementById('parameterCount').value = parseInt(colVal);
                //alert(parseInt(entireVal) + " is an int");
            } else {
                document.getElementById('parameterCount').value = "";
            }
        });

        //Get number of rows when key event is triggered in number of parameters field
        let rowCount = document.getElementById('testCasesCount');
        rowCount.addEventListener('keyup', (event) => {
            rowVal = document.getElementById('testCasesCount').value;
            if(Number.isInteger(parseInt(rowVal))) {
                document.getElementById('testCasesCount').value = parseInt(rowVal);
                alert("test1"); //This prints
                document.getElementById("masterParent").innerHTML = ""; //********HELP*******//
                alert("test2"); //This does not
                makeBoxes(rowVal, colVal);
            } else {
                document.getElementById('testCasesCount').value = "";
            }
        });

        function makeBoxes(rowVal, colVal) {
            for(var i = 0; i < parseInt(rowVal); i++)
                {
                    //Create ROW
                    row = document.createElement("div");
                    row.classList.add("row");
                    
                    //Create COLUMNS and add them to the row
                    colVal = document.getElementById('parameterCount').value;
                    
                    for (var y = 0; y < parseInt(colVal) + 1; y++) {
                        //Create column
                        column = document.createElement("div");
                        column.classList.add("column");
                        
                        p = document.createElement("p");
                        p.classList.add("center-column-text");
                        if(y == parseInt(colVal)) {
                            p.innerHTML = "Answer";
                        } else {
                            p.innerHTML = "Parameter";
                        }

                        input = document.createElement("input");
                        input.setAttribute("type", "text");
                        input.classList.add("inputStyle");

                        column.appendChild(p);
                        column.appendChild(input);
                        row.appendChild(column);
                        masterDiv.appendChild(row);
                    }
                    form = document.getElementById("functionToCall");
                    form.parentNode.insertBefore(masterDiv, form.nextSibling);
                }
        }
    </script>
</body>
</html>
