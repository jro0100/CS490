<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet"  href="../css/menu.css">
    <link rel="stylesheet"  href="../css/teacher/index.css">
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
        <iframe src="question.php" id="makeQuestionFrame"></iframe>
    </div>
    <div class="iframe-container">
        <iframe src="questionbank.php" id="questionbankFrame"></iframe>
    </div>

</body>
</html>