<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

if (!isset($_GET["saveChanges"]) || !isset($_GET["examID"]) || !isset($_GET["studentID"])) {
    header("Location: exams.php");
    exit();
}

$studentID = $_GET["studentID"];
$examID = $_GET["examID"];

unset($_GET["saveChanges"]);
unset($_GET["studentID"]);
unset($_GET["examID"]);

$sqlstmt = "UPDATE questiongrade SET achievedPoints = :achievedPoints, teacherComment = :teacherComment WHERE studentID = :studentID AND examID = :examID AND questionID = :questionID";
$params = array();
$tempArray = array();
foreach ($_GET as $key => $value) {
    $splitKey = explode("-", $key);
    $field = ":" . $splitKey[0];
    $qID = $splitKey[1];
    $tempArray[$qID][$field] = $value;
    $tempArray[$qID][":questionID"] = $qID;
}
foreach ($tempArray as $questionInfo) {
    $questionInfo[":studentID"] = $studentID;
    $questionInfo["examID"] = $examID;
    array_push($params, $questionInfo);
}

db_execute_query_multiple_times($sqlstmt, $params);

header("Location: reviewExam.php?examID=$examID&studentID=$studentID");
exit();

