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
</head>
<body>
<nav class="navbar">
    <ul class="nav-links">
        <li class="nav-item"><a href="index.php">Question Bank</a></li>
        <li class="nav-item"><a href="exams.html">Exams</a></li>
        <li class="nav-item"><a href="../logout.php">Logout</a></li>
    </ul>
</nav>

<form method="post" action="question.php" autocomplete="off">
    <input type="hidden" name="questionID" value="<?php if (isset($questionID)) echo $questionID ?>">

    <label for="question">Question</label>
    <input type="text" name="question" id="question" value="<?php if (isset($question)) echo $question ?>"><br>

    <label for="questionType">Question Type</label>
    <input type="text" name="questionType" id="questionType" value="<?php if (isset($questionType)) echo $questionType ?>"><br>

    <label for="difficulty">Difficulty</label>
    <input type="text" name="difficulty" id="difficulty" value="<?php if (isset($difficulty)) echo $difficulty ?>"><br>

    <label for="parameterCount">Number of Parameters</label>
    <input type="text" name="parameterCount" id="parameterCount" value="<?php if (isset($parameterCount)) echo $parameterCount ?>"><br>

    <label for="functionToCall">Function Name</label>
    <input type="functionToCall" name="functionToCall" id="functionToCall" value="<?php if (isset($functionToCall)) echo $functionToCall ?>"><br>

    <button type="submit" name="submitQuestion" value="submitQuestion">Save</button>
</form>
</body>
</html>
