<?php
session_start();
require("./studentutil/student_functions.php");
require ("../util/functions.php");
redirect_to_login_if_not_valid_student();

if (!isset($_POST["submitExam"])) {
    header("Location: ./");
    exit();
}

unset($_POST["submitExam"]);

if (isset($_POST["examID"])) {
    $examID = $_POST["examID"];
    unset($_POST["examID"]);
} else {
    header("Location: ./");
    exit();
}

$sqlstmt = "INSERT INTO questiongrade (studentID, examID, questionID, studentAnswer) VALUES (:studentID, :examID, :questionID, :studentAnswer)";
$params = array();
foreach ($_POST as $questionID => $studentAnswer) {
    array_push($params, array(":studentID" => $_SESSION["studentID"],
        ":examID" => $examID,
        ":questionID" => $questionID,
        ":studentAnswer" => $studentAnswer));
}
db_execute_query_multiple_times($sqlstmt, $params);

// Mark exam as completed for student
$sqlstmt = "UPDATE studentexam SET completedByStudent = 1 WHERE studentID = :studentID AND examID = :examID";
$params = array(":studentID" => $_SESSION["studentID"],
    ":examID" => $examID);
db_execute($sqlstmt, $params);
