<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

if (isset($_POST["submitQuestion"])) {
    $params = array(
        ":question" => htmlentities($_POST["question"]),
        ":questionType" => $_POST["questionType"],
        ":questionConstraint" => $_POST["constraint"],
        ":difficulty" => $_POST["difficulty"],
        ":parameterCount" => $_POST["parameterCount"],
        ":functionToCall" => $_POST["functionToCall"]
    );

    if ($_POST["questionID"] != "") {
        $sqlstmt = "UPDATE questionbank SET question = :question, questionType = :questionType, difficulty = :difficulty, parameterCount = :parameterCount, functionToCall = :functionToCall WHERE questionID = " . $_POST["questionID"];
    } else {
        $sqlstmt = "INSERT INTO questionbank (teacherID, question, questionType, questionConstraint, difficulty, parameterCount, functionToCall) VALUES (:teacherID, :question, :questionType, :questionConstraint, :difficulty, :parameterCount, :functionToCall)";
        $params[":teacherID"] = $_SESSION["teacherID"];
    }

    $questionID = "holder";
    db_execute($sqlstmt, $params, $questionID);

    $parameterCount = intval($_POST["parameterCount"]);
    $testCasesCount = intval($_POST["testCasesCount"]);

    // Add the correct function name and constraint matching as test cases to compare student answers to
    $sqlstmt = "INSERT INTO testcases (questionID, answer) VALUES (:questionID, :answer)";
    $params = array(array(
        ":questionID" => $questionID,
        ":answer" => $_POST["functionToCall"]
    ));
    if ($_POST["constraint"] != "none") {
        array_push($params, array(
            ":questionID" => $questionID,
            ":answer" => "matchConstraint: true"
        ));
    }
    db_execute_query_multiple_times($sqlstmt, $params);

    // Add each test case and associated parameters to database
    for ($i = 0; $i < $testCasesCount; $i++) {
        $sqlstmt = "INSERT INTO testcases (questionID, answer)  VALUES (:questionID, :answer)";
        $params = array(
            ":questionID" => $questionID,
            ":answer" => $_POST[$i . "-" . $parameterCount]
        );

        $testCaseID = "holder";
        db_execute($sqlstmt, $params, $testCaseID);

        for ($j = 0; $j < $parameterCount; $j++) {
            $sqlstmt = "INSERT INTO parameters (testCaseID, parameter) VALUES (:testCaseID, :parameter)";
            $params = array(
                ":testCaseID" => $testCaseID,
                ":parameter" => $_POST[$i . "-" . $j]
            );

            db_execute($sqlstmt, $params);
        }
    }
    //$reloadFramesScript = "<script>window.top.location.reload()</script>";
    //header("Location: ./");
    //exit();
} elseif (isset($_GET["questionID"])) {
    $questionID = $_GET["questionID"];
    $sqlstmt = "SELECT * FROM questionbank WHERE questionID = :questionID AND teacherID = :teacherID";
    $params = array(":questionID" => $questionID, ":teacherID" => $_SESSION["teacherID"]);
    $result = db_execute($sqlstmt, $params)[0];

    $question = $result["question"];
    $questionType = $result["questionType"];
    $questionConstraint = $result["questionConstraint"];
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
    <link rel="stylesheet"  href="../css/teacher/index.css">
    <link rel="stylesheet"  href="../css/teacher/question.css">
    <title>Questions</title>
</head>
<body>
    <nav class="navbar">
        <ul class="nav-links">
            <li class="nav-item"><a href="index.php">Question Bank</a></li>
            <li class="nav-item"><a href="exams.php">Exams</a></li>
            <li class="nav-item"><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="iframe-container">
        <div class="center-to-page">
            <form method="post" action="./" autocomplete="off">
                <input type="hidden" name="questionID" value="<?php if (isset($questionID)) echo $questionID ?>"><br>

                <label for="question">Question</label><br>
                <textarea name="question" id="question"><?php if (isset($question)) echo $question ?></textarea><br>

                <label for="questionType">Question Type</label>
                <select name="questionType" id="questionType" class="dropdowns">
                    <option value="general" selected>General</option>
                    <option value="forLoop" >For Loop</option>
                    <option value="whileLoop">While Loop</option>
                    <option value="recursion">Recursion</option>
                    <option value="conditionals">Conditionals</option>
                    <option value="strings">Strings</option>
                </select><br>

                <label for="constraint">Constraint</label>
                <select name="constraint" id="constraint" class="dropdowns">
                    <option value="none" selected>None</option>
                    <option value="forLoop" >For Loop</option>
                    <option value="whileLoop">While Loop</option>
                    <option value="recursion">Recursion</option>
                </select><br>

                <label for="difficulty">Difficulty</label>
                <select name="difficulty" id="difficulty" class="dropdowns">
                    <option value="0" <?php if (isset($difficulty) && $difficulty == 0) echo "selected"; ?>>Easy</option>
                    <option value="1" <?php if (isset($difficulty) && $difficulty == 1) echo "selected"; ?>>Medium</option>
                    <option value="2" <?php if (isset($difficulty) && $difficulty == 2) echo "selected"; ?>>Hard</option>
                </select><br>

                <label for="parameterCount">Number of Parameters</label>
                <input type="text" name="parameterCount" id="parameterCount" pattern="^[1-9][0-9]*$" value="<?php if (isset($parameterCount)) echo $parameterCount ?>" required><br>

                <label for="testCasesCount">Number of Test Cases</label>
                <input type="text" name="testCasesCount" id="testCasesCount" pattern="^[1-9][0-9]*$" value="<?php if (isset($parameterCount)) echo $parameterCount ?>" required><br>

                <label for="functionToCall">Function Name</label>
                <input type="functionToCall" name="functionToCall" id="functionToCall" value="<?php if (isset($functionToCall)) echo $functionToCall ?>" required><br>

                <div id="masterParent">
                </div>
                <br>
                <button type="submit" class="submitButton" name="submitQuestion" value="Save Question">Save Question</button>
            </form>
        </div>

        <script>

            masterDiv = document.getElementById("masterParent");

            let columnCount = document.getElementById('parameterCount');
            //Get number of columns when key event is triggered in number of parameters field
            columnCount.addEventListener('keyup', (event) => {
                colVal = document.getElementById('parameterCount').value;
                if(Number.isInteger(parseInt(colVal))) {
                    document.getElementById("masterParent").innerHTML = "";
                    makeBoxes(rowVal, colVal);
                } else if(columnCount.value === "") {
                    document.getElementById("masterParent").innerHTML = "";
                }
            });

            //Get number of rows when key event is triggered in number of parameters field
            let rowCount = document.getElementById('testCasesCount');
            rowCount.addEventListener('keyup', (event) => {
                rowVal = document.getElementById('testCasesCount').value;
                if(Number.isInteger(parseInt(rowVal))) {
                    document.getElementById("masterParent").innerHTML = "";
                    makeBoxes(rowVal, colVal);
                } else if(rowCount.value === "") {
                    document.getElementById("masterParent").innerHTML = "";
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
                        input.setAttribute("name", i + "-" + y);
                        input.setAttribute("id", i + "-" + y);
                        input.required = true;

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
    </div>
    <div class="iframe-container">
        <iframe src="questionbank.php" id="questionbankFrame"></iframe>
    </div>

</body>
</html>